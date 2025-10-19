@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Riwayat Transaksi</h1>
    <a href="{{ route('cashier.index') }}" class="btn btn-primary">
        <i class="bi bi-cash-stack"></i> Ke Kasir
    </a>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Cari kode transaksi atau nama pelanggan..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary ms-2">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <div class="col-md-3">
        <select class="form-select" onchange="window.location.href=this.value">
            <option value="{{ route('transactions.index') }}" {{ !request('payment_method') ? 'selected' : '' }}>Semua Pembayaran</option>
            <option value="{{ route('transactions.index', ['payment_method' => 'cash']) }}" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
            <option value="{{ route('transactions.index', ['payment_method' => 'qris']) }}" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
            <option value="{{ route('transactions.index', ['payment_method' => 'transfer']) }}" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="{{ route('transactions.index', ['payment_method' => 'unpaid']) }}" {{ request('payment_method') == 'unpaid' ? 'selected' : '' }}>Utang</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" onchange="window.location.href=this.value">
            <option value="{{ route('transactions.index') }}" {{ !request('status') ? 'selected' : '' }}>Semua Status</option>
            <option value="{{ route('transactions.index', ['status' => 'completed']) }}" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="{{ route('transactions.index', ['status' => 'uncomplete']) }}" {{ request('status') == 'uncomplete' ? 'selected' : '' }}>Belum Selesai</option>
            <option value="{{ route('transactions.index', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="{{ route('transactions.index', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <code>{{ $transaction->transaction_code }}</code>
                            </td>
                            <td>{{ $transaction->formatted_transaction_date }}</td>
                            <td>{{ $transaction->customer_name ?: 'Umum' }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="text-success">
                                    Rp {{ number_format($transaction->total_profit, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : ($transaction->payment_method == 'qris' ? 'success' : ($transaction->payment_method == 'transfer' ? 'success' : 'secondary')) }}">
                                    {{ $transaction->payment_method == 'unpaid' ? 'Utang' : ucfirst($transaction->payment_method) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'uncomplete' ? 'danger' : ($transaction->status == 'pending' ? 'info' : 'danger')) }}">
                                    {{ $transaction->status == 'uncomplete' ? 'Belum Selesai' : ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                @if($transaction->status == 'uncomplete')
                                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-warning ms-1" title="Edit Pembayaran">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Belum ada transaksi</h4>
                <p class="text-muted">Transaksi pertama akan muncul di sini</p>
                <a href="{{ route('cashier.index') }}" class="btn btn-primary">
                    <i class="bi bi-cash-stack"></i> Mulai Transaksi
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
