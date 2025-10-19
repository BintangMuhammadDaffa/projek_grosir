@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Laporan Penjualan</h1>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="completed" {{ ($status ?? 'all') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="uncomplete" {{ ($status ?? 'all') == 'uncomplete' ? 'selected' : '' }}>Belum Selesai</option>
                    <option value="pending" {{ ($status ?? 'all') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
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
                <h5 class="card-title">Total Profit</h5>
                <h3 class="text-info">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Transaksi</h5>
                <h3 class="text-primary">{{ $totalTransactions }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Transaksi ({{ $startDate }} - {{ $endDate }})</h5>
    </div>
    <div class="card-body">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Profit</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td><code>{{ $transaction->transaction_code }}</code></td>
                            <td>{{ $transaction->formatted_transaction_date }}</td>
                            <td>{{ $transaction->customer_name ?: 'Umum' }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($transaction->total_profit, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : ($transaction->payment_method == 'unpaid' ? 'danger' : 'success')}}">
                                    {{ ucfirst($transaction->payment_method) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Tidak ada transaksi</h4>
                <p class="text-muted">Tidak ada transaksi dalam periode ini</p>
            </div>
        @endif
    </div>
</div>

<!-- Unpaid Customers -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Pelanggan Belum Bayar</h5>
    </div>
    <div class="card-body">
        @if($unpaidCustomers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Pelanggan</th>
                            <th>Total Utang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unpaidCustomers as $customer)
                        <tr>
                            <td>{{ $customer->customer_name ?: 'Umum' }}</td>
                            <td>Rp {{ number_format($customer->total_debt, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-person-x" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Tidak ada pelanggan belum bayar</h4>
                <p class="text-muted">Pelanggan dengan utang akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>

<!-- Best Selling Products -->
<div class="card">
    <div class="card-header">
        <h5>Produk Terlaris</h5>
    </div>
    <div class="card-body">
        @if($bestSellingProducts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Kode Produk</th>
                            <th>Total Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bestSellingProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td><code>{{ $product->product_code }}</code></td>
                            <td>{{ $product->total_sold }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-graph-up" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Tidak ada data penjualan</h4>
                <p class="text-muted">Produk terlaris akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
@endsection
