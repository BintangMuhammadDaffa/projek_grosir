@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Dashboard</h1>
            <div>
                <span class="badge bg-primary fs-6">{{ date('l, d F Y') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Filter Berdasarkan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="filter_type" id="filterMonth" value="month" {{ $filterType == 'month' ? 'checked' : '' }}>
                                <label class="form-check-label" for="filterMonth">
                                    Bulan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="filter_type" id="filterYear" value="year" {{ $filterType == 'year' ? 'checked' : '' }}>
                                <label class="form-check-label" for="filterYear">
                                    Tahun
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3" id="monthSelect" style="{{ $filterType == 'month' ? '' : 'display: none;' }}">
                            <label for="month" class="form-label">Bulan</label>
                            <select class="form-select" id="month" name="month">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="form-label">Tahun</label>
                            <select class="form-select" id="year" name="year">
                                @for ($y = $currentYear - 5; $y <= $currentYear; $y++)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
@if($filterType == 'month')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Penjualan Hari Ini</h6>
                        <h3 class="mb-0 text-success">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                        <small class="text-muted">{{ $todayTransactions }} transaksi</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Profit Hari Ini</h6>
                        <h3 class="mb-0 text-info">Rp {{ number_format($todayProfit, 0, ',', '.') }}</h3>
                        <small class="text-muted">
                            @if($todaySales != 0)
                                {{ number_format(($todayProfit / $todaySales) * 100, 1) }}% margin
                            @else
                                0% margin
                            @endif
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Penjualan Bulan Ini</h6>
                        <h3 class="mb-0 text-primary">Rp {{ number_format($monthlySales, 0, ',', '.') }}</h3>
                        <small class="text-{{ $salesGrowth >= 0 ? 'success' : 'danger' }}">
                            <i class="bi bi-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($salesGrowth), 1) }}% dari bulan lalu
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-bar-chart text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Biaya Operasional</h6>
                        <h3 class="mb-0 text-warning">Rp {{ number_format($monthlyCosts, 0, ',', '.') }}</h3>
                        <small class="text-muted">Bulan ini</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calculator text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Penjualan Tahun Ini</h6>
                        <h3 class="mb-0 text-primary">Rp {{ number_format($yearlySales, 0, ',', '.') }}</h3>
                        <small class="text-muted">Tahun ini</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-bar-chart text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Profit Tahun Ini</h6>
                        <h3 class="mb-0 text-info">Rp {{ number_format($yearlyProfit, 0, ',', '.') }}</h3>
                        <small class="text-muted">
                            @if($yearlySales != 0)
                                {{ number_format(($yearlyProfit / $yearlySales) * 100, 1) }}% margin
                            @else
                                0% margin
                            @endif
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Biaya Operasional Tahun Ini</h6>
                        <h3 class="mb-0 text-warning">Rp {{ number_format($yearlyCosts, 0, ',', '.') }}</h3>
                        <small class="text-muted">Tahun ini</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calculator text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sales Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i>
                    {{ $chartTitle }}
                </h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="100%" height="400px"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Pie Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart"></i>
                    Produk Terlaris (Persentase Penjualan)
                </h5>
                <a href="{{ route('reports.index') }}" class="btn btn-primary">
                    Detail
                </a>
            </div>

            <div class="card-body">
                @if(count($topProductNames) > 0)
                    <canvas id="productPieChart" width="100%" height="400px"></canvas>
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-pie-chart" style="font-size: 3rem;"></i>
                        <p class="mt-2">Tidak ada data penjualan untuk periode ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Low Stock Alert -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    Stok Rendah
                </h5>
            </div>
            <div class="card-body">
                @if($lowStockProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $product->name }}</strong><br>
                                <small class="text-muted">Kode: {{ $product->product_code }}</small>
                            </div>
                            <span class="badge bg-warning">{{ $product->stock_quantity }} unit</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
                        <p class="mt-2">Semua stok dalam kondisi baik</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i>
                    Transaksi Terbaru
                </h5>
            </div>
            <div class="card-body">
                @if($recentTransactions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentTransactions as $transaction)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $transaction->transaction_code }}</strong><br>
                                <small class="text-muted">{{ $transaction->formatted_transaction_date }}</small>
                            </div>
                            <div class="text-end">
                                <div class="text-success">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                <small class="text-muted">{{ $transaction->user->name }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                        <p class="mt-2">Belum ada transaksi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('cashier.index') }}" class="btn btn-primary w-100">
                            <i class="bi bi-cash"></i><br>
                            Buka Kasir
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('products.create') }}" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle"></i><br>
                            Tambah Barang
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('reports.index') }}" class="btn btn-info w-100">
                            <i class="bi bi-bar-chart"></i><br>
                            Lihat Laporan
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('operational-costs.create') }}" class="btn btn-warning w-100">
                            <i class="bi bi-calculator"></i><br>
                            Tambah Biaya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter form toggle
    document.querySelectorAll('input[name="filter_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'month') {
                document.getElementById('monthSelect').style.display = '';
            } else {
                document.getElementById('monthSelect').style.display = 'none';
            }
        });
    });

    // Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Penjualan',
                data: {!! json_encode($chartData) !!},
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointBorderColor: 'rgb(54, 162, 235)',
                pointHoverBackgroundColor: 'rgb(54, 162, 235)',
                pointHoverBorderColor: 'rgb(54, 162, 235)',
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'category',
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        maxRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4
                }
            }
        }
    });

    @if(count($topProductNames) > 0)
    // Pie Chart for Top Products
    const pieCtx = document.getElementById('productPieChart').getContext('2d');
    const productPieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($topProductNames) !!},
            datasets: [{
                data: {!! json_encode($topProductPercentages) !!},
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ],
                hoverBackgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endsection
