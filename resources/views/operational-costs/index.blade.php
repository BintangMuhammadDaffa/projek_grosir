@extends('layouts.app')

@section('title', 'Biaya Operasional')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Biaya Operasional</h1>
    @if(auth()->user()->canAccessOperationalCosts())
    <a href="{{ route('operational-costs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Biaya
    </a>
    @endif
</div>

<!-- Costs Table -->
<div class="card">
    <div class="card-body">
        @if($costs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Biaya</th>
                            <th>Deskripsi</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costs as $cost)
                        <tr>
                            <td>
                                <code>{{ $cost->cost_code }}</code>
                            </td>
                            <td>
                                <strong>{{ $cost->description }}</strong>
                                @if($cost->notes)
                                    <br><small class="text-muted">{{ Str::limit($cost->notes, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($cost->category) }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                            <td>{{ $cost->formatted_cost_date ?? $cost->cost_date }}</td>
                            <td>{{ $cost->user->name }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('operational-costs.show', $cost) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->canAccessOperationalCosts())
                                    <a href="{{ route('operational-costs.edit', $cost) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('operational-costs.destroy', $cost) }}" method="POST" class="d-inline"
                                          onsubmit="return confirmDelete('Hapus biaya ini?')">
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
                {{ $costs->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calculator" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">Belum ada biaya operasional</h4>
                <p class="text-muted">Tambahkan biaya operasional pertama</p>
                @if(auth()->user()->canAccessOperationalCosts())
                <a href="{{ route('operational-costs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Biaya
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
