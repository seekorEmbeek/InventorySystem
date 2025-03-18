<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockConversionHistories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    //
    public function index()
    {
        $product = Product::all();
        $data = Stock::latest()->paginate(10);
        return view('stock.list', compact('data', 'product'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function edit($id)
    {
        $data = Stock::find($id);
        return view('stock.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction(); // Start transaction

            $request->validate([
                'sellingPricePerUnit' => 'required|numeric|min:1',
            ]);

            // Find stock by ID
            $stock = Stock::find($id);

            // Update sellingPricePerUnit stock
            $stock->sellingPricePerUnit = $request->get('sellingPricePerUnit');
            $stock->remainingStock = $request->get('remainingStock');
            $stock->save();

            DB::commit(); // Commit transaction

            return redirect()->route('stock.index')
                ->with('success', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction
            return back()->with('error', $th->getMessage());
        }
    }

    public function editConversion($id)
    {
        $data = Stock::find($id);
        return view('stock.conversion', compact('data'));
    }

    public function updateConversion(Request $request, $id)
    {
        try {
            DB::beginTransaction(); // Start transaction

            $request->validate([
                'sellingPricePerUnit' => 'required|numeric|min:1',
            ]);

            // Find stock by ID
            $stock = Stock::find($id);

            // Update stock
            $stock->totalStock -= $request->get('conversionQty');
            $stock->remainingStock -= $request->get('conversionQty');
            $stock->totalPrice = $stock->remainingStock  *  $stock->pricePerUnit;
            $stock->save();

            //check new stock with same product and uom, if not exist then insert new stock with new uom if exist then update stock
            $newStock = Stock::where('productId', $stock->productId)
                ->where('uom', $request->get('smallUom'))
                ->first();

            //jika stock sudah ada
            if ($newStock) {
                //calculate average selling price per unit
                $totalSellingPrice = $newStock->sellingPricePerUnit * $newStock->remainingStock;
                $totalSellingPriceConversiom = $request->get('sellingPricePerUnit') * $request->get('smallQty');

                $newStock->totalStock += $request->get('smallQty');
                $newStock->remainingStock += $request->get('smallQty');
                $newStock->totalPrice += $request->get('smallQty') * $request->get('smallPrice');
                $newStock->pricePerUnit = $newStock->totalPrice / $newStock->totalStock;

                $sellingPricePerUnit = ($totalSellingPrice + $totalSellingPriceConversiom) / ($newStock->remainingStock);
                $newStock->sellingPricePerUnit = $sellingPricePerUnit;
                $newStock->save();
                //jika stock belum ada
            } else {
                $newStock = new Stock();
                $newStock->productId = $stock->productId;
                $newStock->productName = $stock->productName;
                $newStock->totalStock = $request->get('smallQty');
                $newStock->remainingStock = $request->get('smallQty');
                $newStock->pricePerUnit = $request->get('smallPrice');
                $newStock->uom = $request->get('smallUom');
                $newStock->totalPrice = $request->get('smallQty') * $request->get('smallPrice');
                $newStock->sellingPricePerUnit = $request->get('sellingPricePerUnit');
                $newStock->save();
            }

            //insert conversion history
            $conversionHistory = new StockConversionHistories();
            $conversionHistory->stockId = $stock->id;
            $conversionHistory->productId = $stock->productId;
            $conversionHistory->productName = $stock->productName;
            $conversionHistory->uomBefore = $stock->uom;
            $conversionHistory->qtyBefore = $request->get('conversionQty');
            $conversionHistory->pricePerUnitBefore = $stock->pricePerUnit;
            $conversionHistory->uomAfter = $request->get('smallUom');
            $conversionHistory->qtyAfter = $request->get('smallQty');
            $conversionHistory->pricePerUnitAfter = $request->get('smallPrice');
            $conversionHistory->save();

            DB::commit(); // Commit transaction

            return redirect()->route('stock.index')
                ->with('success', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction
            return back()->with('error', $th->getMessage());
        }
    }
}
