<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class SalesController extends Controller
{
    //
    public function index()
    {
        $data = Sales::latest()->paginate(10);
        $product = Product::all();
        return view('sales.list', compact('data', 'product'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        //retrive all stock data where remainingStock > 0 and sellingPricePerUnit > 0
        $stocks = Stock::where('remainingStock', '>', 0)->where('sellingPricePerUnit', '>', 0)->get();

        return view('sales.create', compact('stocks'));
    }

    public function store(Request $request)
    {
        try {
            // Start Transaction
            DB::beginTransaction();
            //validasi data yang diterima
            $request->validate([
                'buyerName'    => 'required|string',
                'date'         => 'required|date',
                'totalPayment' => 'required|numeric|min:1',
                'totalPrice'   => 'required|numeric|min:1',
                'status'       => 'required|string',
                'items'        => 'required|array', // Ensure items are provided
                'items.*.stockId'   => 'required|integer',
                'items.*.productId'   => 'required|integer',
                'items.*.productName' => 'required|string',
                'items.*.uom'         => 'required|string',
                'items.*.qty'         => 'required|numeric|min:1',
                'items.*.pricePerUnit' => 'required|numeric|min:1',
                'items.*.sellingPricePerUnit' => 'required|numeric|min:1',
                'items.*.totalPrice'  => 'required|numeric|min:1',
            ]);

            //calculate remaining payment
            $remainingPayment = max(0, $request->totalPrice - $request->totalPayment);

            //insert sales data
            Sales::create([
                'buyerName' => $request->buyerName,
                'date' => $request->date,
                'totalPayment' => $request->totalPayment,
                'totalPrice' => $request->totalPrice,
                'status' => $request->status,
                'remainingPayment' => $remainingPayment
            ]);

            //update remainingStock in stock table by stockId
            foreach ($request->items as $item) {
                //get stock data by stockId
                $stock = Stock::find($item['stockId']);
                $stock->remainingStock -= $item['qty'];
                $stock->totalStock -= $item['qty'];
                $stock->totalPrice -= $item['pricePerUnit'] * $item['qty'];

                $stock->save();

                //insert sales item data
                SalesItem::create([
                    'sales_id' => Sales::latest()->first()->id,
                    'productId' => $item['productId'],
                    'productName' => $item['productName'],
                    'uom' => $item['uom'],
                    'qty' => $item['qty'],
                    'pricePerUnit' => $item['pricePerUnit'],
                    'sellingPricePerUnit' => $item['sellingPricePerUnit'],
                    'totalSellingPrice' => $item['totalPrice'],
                    'stock_id' => $item['stockId'],
                ]);

                //insert inventory movement data
                InventoryMovement::create([
                    'productId' => $item['productId'],
                    'productName' => $item['productName'],
                    'uom' => $item['uom'],
                    'qty' => $item['qty'],
                    'movementType' => 'OUT',
                    'date' =>  $request->date,
                    'pricePerUnit' => $item['sellingPricePerUnit'],
                    'totalPrice' => $item['totalPrice'],
                    'salesItem_id' => SalesItem::latest()->first()->id,
                ]);
            }

            // Commit Transaction
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        //get sales data by id include sales item data
        $sales = Sales::with('items')->find($id);

        //retrive all stock data where remainingStock > 0 and sellingPricePerUnit > 0
        $stocks = Stock::where('sellingPricePerUnit', '>', 0)->get();

        //match stock data with sales item data
        foreach ($sales->items as $item) {
            //add remainingStock in stocks data from sales item data
            $matchedStock = $stocks->where('id', $item->stock_id)->first();
            $matchedStock->remainingStock += $item->qty;
        }

        //filter stock data where remainingStock > 0
        $stocks = $stocks->where('remainingStock', '>', 0);

        return view('sales.edit', compact('sales', 'stocks'));
    }

    //create update function
    public function update(Request $request, $id)
    {
        try {
            // dd($request->all());
            // Start Transaction
            DB::beginTransaction();
            //validasi data yang diterima
            $request->validate([
                'buyerName'    => 'required|string',
                'date'         => 'required|date',
                'totalPayment' => 'required|numeric|min:1',
                'totalPrice'   => 'required|numeric|min:1',
                'status'       => 'required|string',
                'items'        => 'required|array', // Ensure items are provided
                'items.*.stockId'   => 'required|integer',
                'items.*.productId'   => 'required|integer',
                'items.*.productName' => 'required|string',
                'items.*.uom'         => 'required|string',
                'items.*.qty'         => 'required|numeric|min:1',
                'items.*.pricePerUnit' => 'required|numeric|min:1',
                'items.*.sellingPricePerUnit' => 'required|numeric|min:1',
                'items.*.totalPrice'  => 'required|numeric|min:1',
            ]);


            //calculate remaining payment
            $remainingPayment = max(0, $request->totalPrice - $request->totalPayment);

            //update sales data
            Sales::find($id)->update([
                'buyerName' => $request->buyerName,
                'date' => $request->date,
                'totalPayment' => $request->totalPayment,
                'totalPrice' => $request->totalPrice,
                'status' => $request->status,
                'remainingPayment' => $remainingPayment
            ]);

            // Get existing sales items
            $existingSalesItems = SalesItem::where('sales_id', $id)->get()->keyBy('id');

            //delete sales item data where old sales item data not in new sales item data
            // **Delete Removed Items**
            foreach ($existingSalesItems as $oldItem) {
                if (!in_array($oldItem->id, array_column($request->items, 'id'))) {
                    // Restore stock for removed item
                    $stock = Stock::find($oldItem->stock_id);
                    if ($stock) {
                        $stock->remainingStock += $oldItem->qty;
                        $stock->totalStock += $oldItem->qty;
                        $stock->totalPrice += $oldItem->pricePerUnit * $oldItem->qty;
                        $stock->save();
                    }

                    // Delete related records
                    InventoryMovement::where('salesItem_id', $oldItem->id)->delete();
                    $oldItem->delete();
                }
            }

            //update sales item data where old sales item data in new sales item data or insert new sales item data
            foreach ($request->items as $item) {
                //if item id is null then insert new item
                if (!isset($item['id']) || empty($item['id'])) {
                    //get stock data by stockId
                    $stock = Stock::find($item['stockId']);

                    // Ensure stock is not negative
                    if ($stock->remainingStock < $item['qty']) {
                        throw new \Exception("Stock untuk {$item['productName']} tidak mencukupi");
                    }


                    $stock->remainingStock -= $item['qty'];
                    $stock->totalStock -= $item['qty'];
                    $stock->totalPrice  = $item['pricePerUnit'] * $stock->totalStock;

                    $stock->save();

                    //insert sales item data
                    $salesItem  = SalesItem::create([
                        'sales_id' => $id,
                        'productId' => $item['productId'],
                        'productName' => $item['productName'],
                        'uom' => $item['uom'],
                        'qty' => $item['qty'],
                        'pricePerUnit' => $item['pricePerUnit'],
                        'sellingPricePerUnit' => $item['sellingPricePerUnit'],
                        'totalSellingPrice' => $item['totalPrice'],
                        'stock_id' => $item['stockId'],
                    ]);

                    //insert inventory movement data
                    InventoryMovement::create([
                        'productId' => $item['productId'],
                        'productName' => $item['productName'],
                        'uom' => $item['uom'],
                        'qty' => $item['qty'],
                        'movementType' => 'OUT',
                        'date' =>  $request->date,
                        'pricePerUnit' => $item['sellingPricePerUnit'],
                        'totalPrice' => $item['totalPrice'],
                        'salesItem_id' => $salesItem->id, // Now correctly referenced
                    ]);
                    //if item id is not null then update item
                } else {
                    // **Existing Item - Update**
                    if (!isset($existingSalesItems[$item['id']])) {
                        throw new \Exception("Sales Item with ID {$item['id']} not found.");
                    }

                    $salesItem = $existingSalesItems[$item['id']];
                    $stock = Stock::findOrFail($item['stockId']);

                    //update stock data
                    $stock->remainingStock += $salesItem->qty - $item['qty'];
                    $stock->totalStock += $salesItem->qty - $item['qty'];
                    $stock->totalPrice = $stock->totalStock * $stock->pricePerUnit;

                    $stock->save();

                    //update sales item data
                    $salesItem->update([
                        'productId' => $item['productId'],
                        'productName' => $item['productName'],
                        'uom' => $item['uom'],
                        'qty' => $item['qty'],
                        'pricePerUnit' => $item['pricePerUnit'],
                        'sellingPricePerUnit' => $item['sellingPricePerUnit'],
                        'totalSellingPrice' => $item['totalPrice'],
                        'stock_id' => $item['stockId'],
                    ]);

                    //update inventory movement data
                    InventoryMovement::where('salesItem_id', $salesItem->id)->update([
                        'productId' => $item['productId'],
                        'productName' => $item['productName'],
                        'uom' => $item['uom'],
                        'qty' => $item['qty'],
                        'movementType' => 'OUT',
                        'date' =>  $request->date,
                        'pricePerUnit' => $item['sellingPricePerUnit'],
                        'totalPrice' => $item['totalPrice'],
                    ]);
                }
            }

            // Commit Transaction
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //create delete function
    public function destroy($id)
    {
        try {
            // Start Transaction
            DB::beginTransaction();
            //get sales data by id include sales item data
            $sales = Sales::with('items')->find($id);

            //update remainingStock in stock table by stockId
            foreach ($sales->items as $item) {
                //get stock data by stockId
                $stock = Stock::find($item->stock_id);
                $stock->remainingStock += $item->qty;
                $stock->totalStock += $item->qty;
                $stock->totalPrice += $item->pricePerUnit * $item->qty;

                $stock->save();

                //delete inventory movement data
                InventoryMovement::where('salesItem_id', $item->id)->delete();

                //delete sales item data    
                $item->delete();
            }

            //delete sales data
            $sales->delete();

            // Commit Transaction
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //create function to get debt data
    public function debt()
    {
        $data = Sales::where('remainingPayment', '>', 0)->latest()->paginate(10);
        return view('sales.debt', compact('data'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
}
