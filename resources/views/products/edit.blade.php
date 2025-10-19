@extends('layouts.app')

@section('title', 'Edit Barang: ' . $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Barang</h1>
    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Detail
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <h5 class="mb-3">Informasi Dasar</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="product_code" class="form-label">Kode Barang <span class="text-muted">(Tidak dapat diubah)</span></label>
                            <input type="text" class="form-control"
                                   id="product_code" name="product_code" value="{{ old('product_code', $product->product_code) }}" readonly>
                            <div class="form-text">
                                <small class="text-muted">Kode barang tidak dapat diubah setelah produk dibuat</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="barcode" class="form-label">Barcode <span class="text-muted">(Auto-generated)</span></label>
                            <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                   id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" readonly>
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock Information -->
                    <h5 class="mb-3 mt-4">Informasi Stok & Harga</h5>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="stock_quantity" class="form-label">Stok Saat Ini <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="purchase_price" class="form-label">Harga Beli <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('purchase_price') is-invalid @enderror"
                                       id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" step="0.01" required>
                            </div>
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="selling_price" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                       id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="0" step="0.01" required>
                            </div>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Supplier Information -->
                    <h5 class="mb-3 mt-4">Informasi Supplier</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="supplier_name" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('supplier_name') is-invalid @enderror"
                                   id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $product->supplier_name) }}" required>
                            @error('supplier_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="supplier_contact" class="form-label">Kontak Supplier</label>
                            <input type="text" class="form-control @error('supplier_contact') is-invalid @enderror"
                                   id="supplier_contact" name="supplier_contact" value="{{ old('supplier_contact', $product->supplier_contact) }}" placeholder="No. HP atau Email">
                            @error('supplier_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Image Upload -->
                    <h5 class="mb-3">Gambar Barang</h5>
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Gambar Baru (Opsional)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.</div>
                    </div>

                    <!-- Current Image Preview -->
                    <div class="text-center mb-3">
                        <label class="form-label">Gambar Saat Ini:</label>
                        <div class="border rounded p-3 bg-light">
                            @if($product->image)
                                <img id="currentImage" src="{{ asset('images/products/' . $product->image) }}"
                                     alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 200px;">
                            @else
                                <div id="currentImage" class="d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- New Image Preview -->
                    <div class="text-center">
                        <div id="imagePreview" class="border rounded p-3 bg-light" style="min-height: 200px; display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 180px;">
                        </div>
                        <div id="imagePlaceholder" class="border rounded p-3 bg-light d-flex align-items-center justify-content-center" style="min-height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span id="submitBtnText">
                                <i class="bi bi-check2"></i> Update Barang
                            </span>
                            <span id="submitBtnLoading" style="display: none;">
                                <i class="bi bi-arrow-clockwise spinning"></i> Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePlaceholder');
    const previewImg = document.getElementById('previewImg');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
    }
}

// Auto-format currency inputs
document.getElementById('purchase_price').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9.]/g, '');
    e.target.value = value;
});

document.getElementById('selling_price').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9.]/g, '');
    e.target.value = value;
});

// Form submission with loading state
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnLoading = document.getElementById('submitBtnLoading');

    // Show loading state
    submitBtn.disabled = true;
    submitBtnText.style.display = 'none';
    submitBtnLoading.style.display = 'inline-block';

    // Re-enable button after 10 seconds as fallback
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtnText.style.display = 'inline-block';
        submitBtnLoading.style.display = 'none';
    }, 10000);
});
</script>
@endpush
