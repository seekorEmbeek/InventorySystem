<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //
    public function index()
    {
        $data = Stock::latest()->paginate(10);
        return view('stock.list', compact('data'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function edit($id)
    {
        $data = Stock::find($id);
        return view('stock.edit', compact('data'));
    }
}
