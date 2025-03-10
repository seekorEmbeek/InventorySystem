<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//product
Route::resource('/product', ProductController::class);

//purchasing
Route::resource('/purchasing', PurchasingController::class);

//stock
Route::resource('/stock', StockController::class);
