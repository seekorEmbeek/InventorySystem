<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    //
    public function index()
    {
        $data = Sales::latest()->paginate(10);
        return view('sales.list', compact('data'))
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
            $remainingPayment = $request->totalPrice - $request->totalPrice <= 0 ? 0 : $request->totalPrice - $request->totalPayment;

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
        $stocks = Stock::where('remainingStock', '>', 0)->where('sellingPricePerUnit', '>', 0)->get();
        return view('sales.edit', compact('sales', 'stocks'));
    }

    //create update function
    public function update(Request $request, $id)
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
            $remainingPayment = $request->totalPrice - $request->totalPrice <= 0 ? 0 : $request->totalPrice - $request->totalPayment;

            //update sales data
            Sales::find($id)->update([
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

                //update sales item data
                SalesItem::find($item['id'])->update([
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
                InventoryMovement::where('salesItem_id', $item['id'])->update([
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


            // Commit Transaction
            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Data penjualan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
