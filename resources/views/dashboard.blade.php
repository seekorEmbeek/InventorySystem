@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Halaman Depan</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rp {{ number_format($totalPurchasing, 0, ',', '.') }}</h3>
                <p>Total Pembelian</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                <p>Total Penjualan</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                <p>Total Profit</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rp {{ number_format($totalDebt, 0, ',', '.') }}</h3>
                <p>Total Hutang</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<!-- Chart Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Total Penjulan Per Product (Bulan Ini)</h3>
    </div>
    <div class="card-body">
        <canvas id="salesChart"></canvas>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("salesChart").getContext("2d");
        var salesData = @json($salesData);

        var labels = salesData.map(item => item.productName);
        var values = salesData.map(item => item.total_sold);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Barang Terjual',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection