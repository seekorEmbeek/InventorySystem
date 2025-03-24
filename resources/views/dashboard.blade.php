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
        <h3 class="card-title">Laporan Penjualan & Laba</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Pie Chart untuk Total Penjualan per Produk -->
            <div class="col-md-6">
                <h5 class="text-center">Total Penjualan Per Produk (Bulan Ini)</h5>
                <canvas id="salesChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Line Chart untuk Laba Per Bulan -->
            <div class="col-md-6">
                <h5 class="text-center">Progress Laba Per Bulan</h5>
                <canvas id="profitChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sales Chart
        var ctx = document.getElementById("salesChart").getContext("2d");
        var salesData = @json($salesData);

        var labels = salesData.map(item => item.productName);
        var values = salesData.map(item => item.total_sold);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Barang Terjual',
                    data: values,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });

    //PROFIT CHART
    var ctxLine = document.getElementById("profitChart").getContext("2d");

    var monthlyProfit = @json($monthlyProfit); // Ambil data dari controller

    console.log("Monthly Profit Data:", monthlyProfit); // Debugging

    var months = monthlyProfit.map(item => item.month); // Ambil bulan
    var profits = monthlyProfit.map(item => item.total_profit); // Ambil laba

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: months, // Nama Bulan (Jan, Feb, Mar...)
            datasets: [{
                label: 'Laba per Bulan (Rp)',
                data: profits, // Total Laba
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true, // Isi area di bawah garis
                tension: 0.3, // Buat garis lebih halus
                pointBackgroundColor: 'rgba(255, 99, 132, 1)', // Warna titik
                pointRadius: 4 // Ukuran titik
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Laba (Rp)"
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: "Bulan"
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top"
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `Rp ${tooltipItem.raw.toLocaleString('id-ID', { minimumFractionDigits: 0 })}`;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection