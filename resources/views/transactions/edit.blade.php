@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Edit Transaksi</h1>
        <p class="text-muted">Kode Transaksi: {{ $transaction->transaction_code }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>Detail Transaksi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran:</label>
                        <select id="payment_method" name="payment_method" class="form-select" required>
                            <option value="cash" {{ $transaction->payment_method == 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="qris" {{ $transaction->payment_method == 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="transfer" {{ $transaction->payment_method == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Ringkasan</h5>
            </div>
            <div class="card-body">
                <p><strong>Pelanggan:</strong> {{ $transaction->customer_name ?: 'N/A' }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                <p><strong>Profit:</strong> Rp {{ number_format($transaction->total_profit, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> <span class="badge bg-warning">{{ $transaction->status }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
