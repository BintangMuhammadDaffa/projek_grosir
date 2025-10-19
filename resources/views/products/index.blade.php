@extends('layouts.app')

@section('title', 'Stok Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Stok Barang</h1>
    @if(auth()->user()->canManageStock())
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Barang
    </a>
    @endif
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary ms-2">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <div class="col-md-3">
        <select class="form-select" onchange="window.location.href=this.value">
            <option value="{{ route('products.index') }}" {{ !request('supplier') ? 'selected' : '' }}>Semua Supplier</option>
            @foreach($products->pluck('supplier_name')->unique() as $supplier)
            <option value="{{ route('products.index', ['supplier' => $supplier]) }}" {{ request('supplier') == $supplier ? 'selected' : '' }}>
                {{ $supplier }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" onchange="window.location.href=this.value">
            <option value="{{ route('products.index') }}" {{ !request('stock_status') ? 'selected' : '' }}>Semua Status</option>
            <option value="{{ route('products.index', ['stock_status' => 'low']) }}" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Rendah</option>
            <option value="{{ route('products.index', ['stock_status' => 'out']) }}" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis</option>
        </select>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Barcode</th>
                            <th>Stok</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Profit</th>
                            <th>Supplier</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->name }}" class="rounded" width="50" height="50">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <code>{{ $product->product_code }}</code>
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                    <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                {!! DNS1D::getBarcodeHTML("$product->product_code",'C128') !!}
                                {{ $product->product_code}}
                            </td>
                            <td>
                                <span class="badge bg-{{ $product->stock_quantity <= 0 ? 'danger' : ($product->stock_quantity <= 10 ? 'warning' : 'success') }}">
                                    {{ $product->stock_quantity }} unit
                                </span>
                            </td>
                            <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="text-success">
                                    Rp {{ number_format($product->profit, 0, ',', '.') }}
                                    <br><small>({{ number_format($product->profit_margin, 1) }}%)</small>
                                </span>
                            </td>
                            <td>{{ $product->supplier_name }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->canManageStock())
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                          onsubmit="return confirmDelete('Hapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-boxes" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Belum ada barang</h4>
                <p class="text-muted">Tambahkan barang pertama untuk memulai</p>
                @if(auth()->user()->canManageStock())
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Barang
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
