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
        $totalPurchasing = Purchasing::sum('smallPrice');

        // Get total sales amount
        $totalSales = Sales::sum('totalPrice');

        // Get total profit (sales - purchasing)
        $totalProfit = $totalSales - $totalPurchasing;

        // Get total debt (unpaid sales)
        $totalDebt = Sales::where('status', 'BELUM LUNAS')->sum(DB::raw('totalPrice - totalPayment'));

        // Get sales data for current month (group by product)
        $salesData = SalesItem::select('productName', DB::raw('SUM(totalSellingPrice) as total_sold'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('productName')
            ->get();

        return view('dashboard', compact('totalPurchasing', 'totalSales', 'totalProfit', 'totalDebt', 'salesData'));
    }
}
