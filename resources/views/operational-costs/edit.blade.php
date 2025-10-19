@extends('layouts.app')

@section('title', 'Edit Biaya Operasional')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Biaya Operasional</h1>
    <a href="{{ route('operational-costs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('operational-costs.update', $cost) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <h5 class="mb-3">Informasi Biaya</h5>

                    <div class="mb-3">
                        <label for="cost_code" class="form-label">Kode Biaya</label>
                        <input type="text" class="form-control"
                               id="cost_code" name="cost_code" value="{{ old('cost_code', $cost->cost_code) }}" readonly>
                        @error('cost_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">Kode biaya tidak dapat diubah</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3" required>{{ old('description', $cost->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror"
                                    id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="rent" {{ old('category', $cost->category) == 'rent' ? 'selected' : '' }}>Sewa</option>
                                <option value="utilities" {{ old('category', $cost->category) == 'utilities' ? 'selected' : '' }}>Utilitas</option>
                                <option value="salary" {{ old('category', $cost->category) == 'salary' ? 'selected' : '' }}>Gaji</option>
                                <option value="marketing" {{ old('category', $cost->category) == 'marketing' ? 'selected' : '' }}>Pemasaran</option>
                                <option value="maintenance" {{ old('category', $cost->category) == 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                                <option value="other" {{ old('category', $cost->category) == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                       id="amount" name="amount" value="{{ old('amount', $cost->amount) }}" min="0" step="0.01" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cost_date" class="form-label">Tanggal Biaya <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('cost_date') is-invalid @enderror"
                               id="cost_date" name="cost_date" value="{{ old('cost_date', $cost->cost_date->format('Y-m-d')) }}" required>
                        @error('cost_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan Tambahan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="2" placeholder="Catatan opsional">{{ old('notes', $cost->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Info Panel -->
                    <h5 class="mb-3">Informasi</h5>
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Informasi Biaya</h6>
                        <dl class="row mb-0 small">
                            <dt class="col-sm-5">Dibuat oleh:</dt>
                            <dd class="col-sm-7">{{ $cost->user->name }}</dd>
                            <dt class="col-sm-5">Dibuat pada:</dt>
                            <dd class="col-sm-7">{{ $cost->created_at->format('d/m/Y H:i') }}</dd>
                            @if($cost->updated_at != $cost->created_at)
                            <dt class="col-sm-5">Diubah pada:</dt>
                            <dd class="col-sm-7">{{ $cost->updated_at->format('d/m/Y H:i') }}</dd>
                            @endif
                        </dl>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle"></i> Perhatian</h6>
                        <small>
                            Pastikan data yang dimasukkan akurat karena akan mempengaruhi laporan keuangan.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('operational-costs.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span id="submitBtnText">
                                <i class="bi bi-check2"></i> Update Biaya
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
// Auto-format amount input
document.getElementById('amount').addEventListener('input', function(e) {
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
