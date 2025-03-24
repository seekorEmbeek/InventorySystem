<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Purchasing;
use App\Models\Sales;
use App\Models\SalesItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // Get total purchasing amount
        $totalPurchasing = Purchasing::whereMonth('date', Carbon::now()->month)
            ->whereNull('deleted_at') // Ensures soft deleted records are excluded
            ->sum('smallPrice');

        // Get total sales amount
        $totalSales = Sales::whereMonth('date', Carbon::now()->month)
            ->whereNull('deleted_at') // Ensures soft deleted records are excluded
            ->sum('totalPrice');

        // Get total profit sum(sellingPricePerUnit - pricePerUnit) * qty
        $totalProfit = Sales::where('status', 'LUNAS')
            ->whereMonth('date', Carbon::now()->month)
            ->whereNull('sales.deleted_at') // Ensures soft deleted records are excluded
            ->join('sales_items', function ($join) {
                $join->on('sales.id', '=', 'sales_items.sales_id')
                    ->whereNull('sales_items.deleted_at'); // Exclude soft-deleted sales items
            })
            ->sum(DB::raw('(sales_items.sellingPricePerUnit - sales_items.pricePerUnit) * sales_items.qty'));

        // Get total debt (unpaid sales)
        $totalDebt = Sales::whereMonth('date', Carbon::now()->month)
            ->whereNull('deleted_at') // Ensures soft deleted records are excluded
            ->where('remainingPayment', '>', 0)
            ->sum(DB::raw('remainingPayment'));

        // Get monthly profit
        $monthlyProfit = Sales::select(
            DB::raw('MONTH(sales.date) as month'),
            DB::raw('SUM((sales_items.sellingPricePerUnit - sales_items.pricePerUnit) * sales_items.qty) as total_profit')
        )
            ->join('sales_items', 'sales.id', '=', 'sales_items.sales_id')
            ->where('sales.status', 'LUNAS')
            ->whereYear('sales.date', Carbon::now()->year) // Ambil hanya data tahun ini
            ->whereNull('sales.deleted_at') // Ensures soft deleted records are excluded
            ->whereNull('sales_items.deleted_at') // Ensures soft deleted records are excluded
            ->groupBy(DB::raw('MONTH(sales.date)')) // Grouping berdasarkan bulan
            ->orderBy(DB::raw('MONTH(sales.date)')) // Urutkan berdasarkan bulan
            ->get();

        // Get sales data
        $salesData = Sales::join('sales_items', 'sales.id', '=', 'sales_items.sales_id')
            ->whereMonth('sales.date', Carbon::now()->month)
            ->whereNull('sales.deleted_at') // Ensures soft deleted records are excluded
            ->whereNull('sales_items.deleted_at') // Ensures soft deleted records are excluded
            ->select(
                'sales_items.productName',
                DB::raw('SUM(sales_items.qty) as total_sold')
            )
            ->groupBy('sales_items.productName') // Pastikan hasil dikelompokkan berdasarkan produk
            ->orderBy('total_sold', 'DESC') // Urutkan dari yang paling laris
            ->get();

        return view('dashboard', compact('totalPurchasing', 'totalSales', 'totalProfit', 'totalDebt', 'salesData', 'monthlyProfit'));
    }
}
