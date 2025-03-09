<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Echo_;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::latest() -> paginate(10);
        return view('product.list',compact('product'))
        ->with('i',(request()->input('page',1)-1)*10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //validasi data yang diterima
            $request->validate([
                'name' => 'required',
                'uom' => 'required',
            ]);

            // menjalankan fungsi insert pada table customer 
            Product::create($request->all());
            // redirect ke halaman list customer
            return redirect()->route('product.index')->with('success','Berhasil menambahkan data');
        } catch (\Throwable $th) {
            //throw $th;
           // munculkan pesan error jika ada error
            return redirect()->route('product.index')->with('error',$th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
         //munculkan data customer sesuai parameter id dan ambil satu data
         $product =  Product::where('id',$id)->firstOrFail();
         // jika ada data customer
         if($product){
             // buka halaman view customer_edit dengan mengirim datanya
             return view('product.edit',compact('product'));
         }else{
             return redirect()->route('product.index')->with('error','Barang tidak ditemukan');
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         //tambahkan validasi
        //ambil data customer sesuai parameter id dan lakukan update pada modelnya
        Product::where('id',$id)->update([
            'name'=> $request->name,
            'uom'=> $request->uom,
        ]);


        return redirect()->route('product.index')->with('success','Berhasil update data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         //lakukan delete pada data customer sesuai parameter id
         Product::where('id',$id)->delete();

         return redirect()->route('product.index')->with('success','Berhasil hapus data');
    }
}
