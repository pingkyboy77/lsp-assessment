{{-- resources/views/admin/kelompok-kerja/partials/unit-card.blade.php --}}
<div class="col-lg-6 col-xl-4 mb-4">
    <div class="card unit-card h-100">
        <div class="unit-header p-3" @if(isset($inactive) && $inactive) style="background: linear-gradient(135deg, #636e72 0%, #2d3436 100%);" @endif>
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1 text-white">{{ $unit->kode_unit }}</h6>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-{{ $unit->pivot->is_active ? 'success' : 'secondary' }}">
                            {{ $unit->pivot->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <span class="badge bg-light text-dark" style="font-size: 0.7rem; padding: 2px 6px;">
                            Urutan: {{ $unit->pivot->sort_order }}
                        </span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}">
                                <i class="bi bi-eye me-2"></i> Lihat Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}">
                                <i class="bi bi-pencil me-2"></i> Edit Unit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item" onclick="toggleUnitStatus({{ $unit->id }})">
                                <i class="bi bi-toggle-{{ $unit->pivot->is_active ? 'off' : 'on' }} me-2"></i>
                                {{ $unit->pivot->is_active ? 'Nonaktifkan' : 'Aktifkan' }} di Kelompok
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item text-danger" onclick="removeFromKelompok({{ $unit->id }})">
                                <i class="bi bi-unlink me-2"></i> Lepas dari Kelompok
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body">
            <h6 class="card-title mb-2">{{ Str::limit($unit->judul_unit, 60) }}</h6>

            @if ($unit->standar_kompetensi_kerja)
                <p class="text-muted small mb-3">
                    {{ Str::limit($unit->standar_kompetensi_kerja, 80) }}
                </p>
            @endif

            <!-- Statistics -->
            <div class="row text-center mb-3">
                <div class="col-6">
                    <div class="stat-item">
                        <div class="fw-bold text-primary">
                            {{ $unit->elemenKompetensis->count() }}
                        </div>
                        <div class="text-muted small">Elemen</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-item">
                        <div class="fw-bold text-success">
                            {{ $unit->elemenKompetensis->sum(fn($e) => $e->kriteriaKerjas->count()) }}
                        </div>
                        <div class="text-muted small">Kriteria</div>
                    </div>
                </div>
            </div>

            <!-- Meta Information -->
            <div class="border-top pt-2">
                <div class="d-flex justify-content-between text-muted small">
                    <span>
                        <i class="bi bi-calendar-plus"></i>
                        {{ $unit->pivot->created_at->format('d M Y') }}
                    </span>
                    <span class="badge bg-{{ $unit->is_active ? 'success' : 'secondary' }}" style="font-size: 0.7rem;">
                        {{ $unit->is_active ? 'Unit Aktif' : 'Unit Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-footer bg-transparent">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.schemes.unit-kompetensi.show', [$scheme, $unit]) }}" class="btn btn-outline-primary btn-sm flex-fill">
                    <i class="bi bi-eye"></i> Lihat
                </a>
                <a href="{{ route('admin.schemes.unit-kompetensi.edit', [$scheme, $unit]) }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil"></i>
                </a>
            </div>
        </div>
    </div>
</div>