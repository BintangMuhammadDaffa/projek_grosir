@extends('layouts.app')

@section('title', 'Struk Pembayaran')

@section('content')
<div class="d-flex justify-content-center mt-4">
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
</div>
<div class="text-center mt-3 no-print">
            <button class="btn btn-sm btn-dark" onclick="printReceipt()">🖨️ Cetak Struk</button>
            <a href="{{ route('cashier.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>
@endsection

@section('styles')
<style>
    body {
        background: #f8f9fa;
    }

    @media print {
        body {
            background: white !important;
            margin: 0;
            padding: 0;
        }

        .receipt {
            box-shadow: none !important;
            border: none !important;
            width: 240px !important;
            margin: 0 auto;
            color: #000 !important;
            font-size: 11px !important;
        }

        .no-print {
            display: none !important;
        }

        hr {
            border-top: 1px dashed #000 !important;
        }

        @page {
            size: auto;
            margin: 5mm;
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
        location.reload();
    }
</script>