@extends('layouts.app')

@section('title', 'Laporan Profit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Laporan Profit</h1>
</div>

<!-- Month Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="month" class="form-label">Bulan</label>
                <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<!-- Row atas: 3 kolom -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Penjualan</h5>
                <h3 class="text-success">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Profit Dibayar</h5>
                <h3 class="text-info">Rp {{ number_format($paidProfit, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Profit Utang</h5>
                <h3 class="text-warning">Rp {{ number_format($unpaidProfit, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Row bawah: 2 kolom, ditengah -->
<div class="row mb-4 justify-content-center">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Profit Kotor</h5>
                <h3 class="text-primary">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Profit Bersih</h5>
                <h3 class="text-primary">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>


<!-- Daily Profit Breakdown Table -->
<div class="card">
    <div class="card-header">
        <h5>Rincian Harian ({{ \Carbon\Carbon::parse($month)->format('F Y') }})</h5>
    </div>
    <div class="card-body">
        @if(count($dailyData) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Penjualan</th>
                            <th>Profit</th>
                            <th>Biaya</th>
                            <th>Profit Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyData as $day)
                        <tr>
                            <td>{{ $day['date'] }}</td>
                            <td>Rp {{ number_format($day['sales'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($day['profit'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($day['costs'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($day['net'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-graph-up" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Tidak ada data profit</h4>
                <p class="text-muted">Data profit akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
@endsection
