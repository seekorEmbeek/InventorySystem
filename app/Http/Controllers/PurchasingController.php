<?php

namespace App\Http\Controllers;

use App\Models\Purchasing;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchasingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Purchasing::latest()->paginate(10);
        return view('purchasing.list', compact('data'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $product = Product::all();
        return view('purchasing.create', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //validasi data yang diterima
            $request->validate([
                'supplierName'  => 'required|string',
                'date'          => 'required|date',
                'productName'   => 'required|string',
                'productId'     => 'required|integer',
                'purchaseQty'   => 'required|numeric|min:1',
                'purchaseUom'   => 'required|string',
                'purchasePrice' => 'required|numeric|min:1',
            ]);

            // mengambil data yang diterima dan disimpan pada variable $request
            $request['pricePerUnit'] = $request->smallPrice / $request->smallQty;
            $request['purchaseStatus'] = 'LUNAS';

            // menjalankan fungsi insert pada table customer 
            Purchasing::create($request->all());
            // redirect ke halaman list customer
            return redirect()->route('purchasing.index')->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            //throw $th;
            //munculkan error tanpa meredirect ke halaman manapun dan jangan menghapus data yang sudah diinput
            return back()->with('error', $th->getMessage());
            // return redirect()->route('purchasing.index')->with('error',$th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchasing $purchasing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $product = Product::all();
        $data = Purchasing::where('id', $id)->firstOrFail();
        $data['hasConversion'] = $data->purchaseUom == $data->smallUom ? false : true;
        if ($data) {
            return view('purchasing.edit', compact('data', 'product'));
        } else {
            return redirect()->route('purchasing.index')->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        try {
            //validasi data yang diterima
            $request->validate([
                'supplierName'  => 'required|string',
                'date'          => 'required|date',
                'productName'   => 'required|string',
                'productId'     => 'required|integer',
                'purchaseQty'   => 'required|numeric|min:1',
                'purchaseUom'   => 'required|string',
                'purchasePrice' => 'required|numeric|min:1',
            ]);

            // mengambil data yang diterima dan disimpan pada variable $request
            $request['pricePerUnit'] =  $request->smallPrice / $request->smallQty;
            $request['purchaseStatus'] = 'LUNAS';

            // menjalankan fungsi insert pada table customer 
            Purchasing::where('id', $id)->update($request->except(['_token', '_method']));
            // redirect ke halaman list customer
            return redirect()->route('purchasing.index')->with('success', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            //throw $th;
            //munculkan error tanpa meredirect ke halaman manapun dan jangan menghapus data yang sudah diinput
            return back()->with('error', $th->getMessage());
            // return redirect()->route('purchasing.index')->with('error',$th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchasing $purchasing)
    {
        //
        Purchasing::where('id', $purchasing->id)->delete();

        return redirect()->route('purchasing.index')->with('success', 'Berhasil menghapus data');
    }
}
