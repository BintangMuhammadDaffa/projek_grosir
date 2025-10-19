@extends('layouts.app')

@section('title', 'Detail Biaya Operasional')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detail Biaya Operasional</h1>
    <div>
        @if(auth()->user()->canAccessOperationalCosts())
        <a href="{{ route('operational-costs.edit', $cost) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif
        <a href="{{ route('operational-costs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Biaya</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Kode Biaya:</dt>
                            <dd class="col-sm-8">
                                <code>{{ $cost->cost_code }}</code>
                            </dd>

                            <dt class="col-sm-4">Kategori:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span>
                            </dd>

                            <dt class="col-sm-4">Jumlah:</dt>
                            <dd class="col-sm-8">
                                <strong class="text-danger">Rp {{ number_format($cost->amount, 0, ',', '.') }}</strong>
                            </dd>

                            <dt class="col-sm-4">Tanggal:</dt>
                            <dd class="col-sm-8">{{ $cost->formatted_cost_date }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Deskripsi:</dt>
                            <dd class="col-sm-8">{{ $cost->description }}</dd>

                            @if($cost->notes)
                            <dt class="col-sm-4">Catatan:</dt>
                            <dd class="col-sm-8">{{ $cost->notes }}</dd>
                            @endif

                            <dt class="col-sm-4">Dibuat oleh:</dt>
                            <dd class="col-sm-8">{{ $cost->user->name }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Riwayat</h5>
            </div>
            <div class="card-body">
                <dl class="row small">
                    <dt class="col-sm-5">Dibuat:</dt>
                    <dd class="col-sm-7">{{ $cost->created_at->format('d/m/Y H:i') }}</dd>

                    @if($cost->updated_at != $cost->created_at)
                    <dt class="col-sm-5">Diubah:</dt>
                    <dd class="col-sm-7">{{ $cost->updated_at->format('d/m/Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if(auth()->user()->canAccessOperationalCosts())
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Zona Berbahaya
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-3">Menghapus biaya operasional akan mempengaruhi laporan keuangan.</p>
                <form action="{{ route('operational-costs.destroy', $cost) }}" method="POST" id="deleteCostForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm" onclick="handleDeleteCost('Apakah Anda yakin ingin menghapus biaya ini?')">
                        <i class="bi bi-trash"></i> Hapus Biaya
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
async function handleDeleteCost(message) {
    const confirmed = await confirmDelete(message);
    if (confirmed) {
        document.getElementById('deleteCostForm').submit();
    }
}
</script>
@endpush
@endsection
