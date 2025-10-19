@extends('layouts.app')

@section('title', 'Detail Barang: ' . $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detail Barang</h1>
    <div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        @if(auth()->user()->canManageStock())
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit Barang
        </a>
        @endif
    </div>
</div>

<div class="row">
    <!-- Product Image and Basic Info -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Gambar Barang</h5>
            </div>
            <div class="card-body text-center">
                @if($product->image)
                    <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 300px;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Barcode -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Barcode</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-2">
                    {!! DNS1D::getBarcodeHTML("$product->product_code",'C128') !!}
                </div>
                <code class="fs-5">{{ $product->product_code }}</code>
            </div>
        </div>
    </div>

    <!-- Product Details -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Barang</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Kode Barang:</strong></div>
                    <div class="col-sm-9"><code>{{ $product->product_code }}</code></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Nama Barang:</strong></div>
                    <div class="col-sm-9"><h5 class="mb-0">{{ $product->name }}</h5></div>
                </div>

                @if($product->description)
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Deskripsi:</strong></div>
                    <div class="col-sm-9">{{ $product->description }}</div>
                </div>
                @endif

                <hr>

                <h6 class="mb-3">Informasi Stok & Harga</h6>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Stok Saat Ini:</strong></div>
                    <div class="col-sm-9">
                        <span class="badge bg-{{ $product->stock_quantity <= 0 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning' : 'success') }} fs-6">
                            {{ $product->stock_quantity }} unit
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Harga Beli:</strong></div>
                    <div class="col-sm-9">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Harga Jual:</strong></div>
                    <div class="col-sm-9">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Profit:</strong></div>
                    <div class="col-sm-9">
                        <span class="text-success fw-bold">
                            Rp {{ number_format($product->profit, 0, ',', '.') }}
                            <small class="text-muted">({{ number_format($product->profit_margin, 1) }}%)</small>
                        </span>
                    </div>
                </div>

                <hr>

                <h6 class="mb-3">Informasi Supplier</h6>

                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Nama Supplier:</strong></div>
                    <div class="col-sm-9">{{ $product->supplier_name }}</div>
                </div>

                @if($product->supplier_contact)
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Kontak Supplier:</strong></div>
                    <div class="col-sm-9">{{ $product->supplier_contact }}</div>
                </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-sm-3"><strong>Dibuat:</strong></div>
                    <div class="col-sm-9">{{ $product->created_at->format('d M Y, H:i') }}</div>
                </div>

                <div class="row">
                    <div class="col-sm-3"><strong>Terakhir Diubah:</strong></div>
                    <div class="col-sm-9">{{ $product->updated_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if(auth()->user()->canManageStock())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <form action="{{ route('products.add-stock', $product) }}" method="POST" class="d-flex">
                            @csrf
                            <input type="number" name="quantity" class="form-control me-2" placeholder="Jumlah" min="1" required style="max-width: 120px;">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Tambah Stok
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('products.export-pdf', $product) }}" class="btn btn-info w-100 no-loading">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <form action="{{ route('products.destroy', $product) }}" method="POST" id="deleteForm{{ $product->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger w-100" onclick="handleDelete('{{ $product->id }}', 'Apakah Anda yakin ingin menghapus barang ini?')">
                                <i class="bi bi-trash"></i> Hapus Barang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
async function handleDelete(formId, message) {
    const confirmed = await confirmDelete(message);
    if (confirmed) {
        document.getElementById('deleteForm' + formId).submit();
    }
}
</script>
@endpush
@endsection
