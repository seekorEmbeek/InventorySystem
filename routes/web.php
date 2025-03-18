<?php

use App\Exports\PurchasingExport;
use App\Exports\SalesExport;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

//AUTH
Route::get('/', function () {
    // Jika pengguna belum login, arahkan ke halaman login
    if (Auth::check()) {
        return redirect()->route('login');
    }
    // Jika sudah login, arahkan ke dashboard
    return redirect()->route('dashboard');
});


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

//AUTH
Route::get('/home', function () {
    // Jika sudah login, arahkan ke dashboard
    return redirect()->route('dashboard');
});

//dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//product
Route::resource('/product', ProductController::class);

//purchasing
Route::resource('/purchasing', PurchasingController::class);

//stock
Route::resource('/stock', StockController::class);
Route::get('/stock/conversion/{id}', [StockController::class, 'editConversion'])->name('stock.conversion');
Route::put('/stock/conversion/{id}', [StockController::class, 'updateConversion'])->name('stock.conversion');

//sales
Route::resource('/sales', SalesController::class);
Route::get('/debt', [SalesController::class, 'debt'])->name('sales.debt');

//export
Route::get('/export-purchasing', function (Request $request) {
    $dateFrom = $request->dateFrom ?? 'ALL';
    $dateTo = $request->dateTo ?? 'ALL';
    $productName = $request->productName ?? 'ALL';

    // Format filename dynamically
    $fileName = "Rekap_Pembelian_{$productName}_{$dateFrom}_to_{$dateTo}.xlsx";
    return Excel::download(new PurchasingExport(
        $request->dateFrom,
        $request->dateTo,
        $request->productName
    ), $fileName);
})->name('export.purchasing');

Route::get('/export-sales', function (Request $request) {
    $dateFrom = $request->dateFrom ?? 'ALL';
    $dateTo = $request->dateTo ?? 'ALL';
    $productName = $request->productName ?? 'ALL';

    $fileName = "Rekap_Penjualan_{$productName}_{$dateFrom}_to_{$dateTo}.xlsx";

    return Excel::download(new SalesExport(
        $request->dateFrom,
        $request->dateTo,
        $request->productName
    ), $fileName);
})->name('export.sales');
