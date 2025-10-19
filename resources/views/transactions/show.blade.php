@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . $transaction->transaction_code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detail Transaksi</h1>
    <div>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        @if($transaction->status == 'uncomplete')
        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning me-2">
            <i class="bi bi-pencil"></i> Edit Pembayaran
        </a>
        @endif
        <button onclick="printReceipt()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Cetak
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Transaction Details -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Kode Transaksi:</strong> <code>{{ $transaction->transaction_code }}</code></p>
                        <p><strong>Tanggal:</strong> {{ $transaction->formatted_transaction_date }}</p>
                        <p><strong>Pelanggan:</strong> {{ $transaction->customer_name ?: 'Umum' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Kasir:</strong> {{ $transaction->user->name }}</p>
                        <p><strong>Metode Pembayaran:</strong>
                            <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : ($transaction->payment_method == 'qris' ? 'info' : ($transaction->payment_method == 'transfer' ? 'warning' : 'secondary')) }}">
                                {{ $transaction->payment_method == 'unpaid' ? 'Utang' : ucfirst($transaction->payment_method) }}
                            </span>
                        </p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'uncomplete' ? 'danger' : ($transaction->status == 'pending' ? 'info' : 'warning')) }}">
                                {{ $transaction->status == 'uncomplete' ? 'Belum Selesai' : ucfirst($transaction->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Items -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Detail Barang</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Kode</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->transactionItems as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td><code>{{ $item->product->product_code }}</code></td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ringkasan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Belanja:</span>
                    <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Profit:</span>
                    <strong class="text-success">Rp {{ number_format($transaction->total_profit, 0, ',', '.') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span><strong>Grand Total:</strong></span>
                    <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="printArea" class="receipt bg-white p-3" style="width: 280px; font-family: monospace; color: #000;">

        <!-- Header -->
        <div class="text-center mb-2">
            <h6 class="mb-0" style="font-weight: bold;">Metro Grosir</h6>
            <div style="font-size: 12px;">
                JL. Dewi Sartika No.52,Pabaton, Kecamatan Bogor Tengah, Kota Bogor, Jawa Barat<br>
                087786254660
            </div>
        </div>

        <!-- Info Transaksi -->
        <div style="font-size: 12px;">
            <div class="d-flex justify-content-between">
                <span>Check No:</span>
                <span>{{ $transaction->transaction_code }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>{{ $transaction->transaction_date->format('d M y H:i:s') }}</span>
                <span>{{ strtoupper($transaction->customer_name) }}</span>
            </div>
        </div>

        <hr style="border-top: 1px dashed #000; margin: 6px 0;">

        <!-- Daftar Barang -->
        <div style="font-size: 12px;">
            @foreach($transaction->transactionItems as $item)
            <div class="d-flex justify-content-between">
                <span>{{ $item->product_name }}</span>
                <span>{{ number_format($item->unit_price, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <hr style="border-top: 1px dashed #000; margin: 6px 0;">

        <!-- Total -->
        <div style="font-size: 12px;">
            <div class="d-flex justify-content-between">
                <strong>Subtotal</strong>
                <strong>{{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span>Payment</span>
                <span>{{ ucfirst($transaction->payment_method) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Total</span>
                <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr style="border-top: 1px dashed #000; margin: 6px 0;">

        <!-- Footer -->
        <div class="text-center" style="font-size: 11px; line-height: 1.4;">
            Thank You<br>
            Please Come Again
        </div>
</div>
@endsection



@section('styles')
<style>
    /* Sembunyikan div receipt di tampilan web normal */
    #printArea {
        display: none;
    }

    /* Tampilkan dan atur saat print */
    @media print {
        body * {
            visibility: hidden;
        }
        #printArea, #printArea * {
            visibility: visible;
        }
        #printArea {
            display: block !important;  /* Pastikan tampil saat print */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;  /* Atau sesuaikan dengan lebar receipt, misal 280px untuk thermal printer */
            margin: 0;
            padding: 0;
            background: white;
            font-size: 12px;  /* Sesuaikan untuk receipt */
        }
        .btn, .card-header, .card {  /* Sembunyikan elemen lain saat print */
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

<script>
    function printReceipt() {
        var printContents = document.getElementById('printArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Reload the page to restore original state
    }
    
      document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('printArea').style.display = 'none';
  });
  
</script>