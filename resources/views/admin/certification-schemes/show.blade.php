@extends('layouts.admin')

@section('title', 'Detail Skema Sertifikasi - ' . $scheme->nama)

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
    }
    .glass-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .gradient-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px 20px 0 0;
    }
    .status-badge {
        background: linear-gradient(135deg, #4ade80, #22c55e);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    .status-badge.inactive {
        background: linear-gradient(135deg, #f87171, #ef4444);
    }
    .modern-badge {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 6px 14px;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .stats-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.3s ease;
    }
    .stats-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .section-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 16px 16px 0 0;
        padding: 1.25rem 1.5rem;
    }
    .modern-list-item {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }
    .modern-list-item:hover {
        transform: translateX(8px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
    }
    .floating-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 50px;
        /* padding: 12px 24px; */
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .floating-btn:hover {
        transform: translateY(-2px);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: rgba(255,255,255,0.9);
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
    }
    .breadcrumb-modern {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 0.5rem 1rem;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .hero-title { font-size: 1.8rem; }
        .d-flex.gap-3 { flex-direction: column; gap: 0.5rem !important; }
        .floating-btn { width: 100%; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    {{-- Hero Header --}}
    <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold text-dark d-flex justify-content-center align-items-center">
                        <i class="bi bi-award me-2"></i>{{ $scheme->nama }}
                        {{-- <span
                            class="m-2 badge bg-{{ $scheme->is_active ? 'success' : 'danger' }} fs-6">
                            <i class="bi bi-{{ $scheme->is_active ? 'check-circle' : 'x-circle' }}"></i>
                            {{ $scheme->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span> --}}
                    </h4>
                    <div class="d-flex align-items-center mb-3 gap-2">
                        <span class="badge bg-info fs-6">{{ $scheme->code_1 }}</span>
                        <span class="badge bg-{{ $scheme->jenjang_color }} fs-6">{{ $scheme->jenjang }}</span>
                        @if ($scheme->field)
                                    <strong>{{ $scheme->field->bidang }}</strong><br>
                        @endif
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.certification-schemes.index') }}">Skema
                                    Sertifikasi</a></li>
                            <li class="breadcrumb-item active">{{ $scheme->code_1 }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2 justify-content-center align-items-center my-2">
                    <a href="{{ route('admin.certification-schemes.edit', $scheme) }}"
                        class="btn btn-outline-warning text-center">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.certification-schemes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left mx-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

    {{-- Statistics Dashboard --}}
    <div class="glass-card mb-5">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-bar-chart-fill fs-3 text-primary me-3"></i>
                <h4 class="mb-0 fw-bold">Statistik Skema</h4>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-file-earmark-check-fill fs-1 text-success mb-3"></i>
                        <div class="stats-number text-success">{{ $scheme->requirement_templates_count ?? 0 }}</div>
                        <div class="text-muted small">Requirements</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-list-check fs-1 text-primary mb-3"></i>
                        <div class="stats-number text-primary">{{ $scheme->unit_kompetensi_count ?? 0 }}</div>
                        <div class="text-muted small">Unit Kompetensi</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-people-fill fs-1 text-info mb-3"></i>
                        <div class="stats-number text-info">{{ $scheme->kelompok_kerja_count ?? 0 }}</div>
                        <div class="text-muted small">Kelompok Kerja</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card text-center">
                        <i class="bi bi-clipboard-data fs-1 text-warning mb-3"></i>
                        <div class="stats-number text-warning">{{ $scheme->total_elemen_count ?? 0 }}</div>
                        <div class="text-muted small">Total Elemen</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Requirements Section --}}
    <div class="glass-card mb-5">
        <div class="section-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <h6 class="text-white mb-0"><i class="bi bi-file-earmark-check-fill me-2"></i>Requirements Template</h6>
        </div>
        <div class="card-body p-4">
            @if ($scheme->requirementTemplate || $scheme->requirementTemplates->count() > 0)
                @if ($scheme->requirementTemplate)
                    <div class="modern-list-item mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-star-fill text-warning fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $scheme->requirementTemplate->name }}</h6>
                                <p class="text-muted mb-0">{{ $scheme->requirementTemplate->description ?? 'Template utama untuk skema ini' }}</p>
                            </div>
                            <span class="ms-auto badge bg-primary">Template Utama</span>
                        </div>
                    </div>
                @endif

                <div class="row g-3 mb-4">
                    @foreach ($scheme->requirementTemplates as $template)
                        <div class="col-md-6 col-lg-4">
                            <div class="modern-list-item h-100">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-file-text-fill text-primary fs-4 me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-2">{{ $template->name }}</h6>
                                        <p class="text-muted small mb-2">{{ $template->description ?? 'Tidak ada deskripsi' }}</p>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>{{ $template->activeItems->count() }} item
                                            </span>
                                            <small class="text-muted">Aktif</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="text-center">
                    <a href="{{ route('admin.certification-schemes.requirements', $scheme) }}" 
                       class=" btn-primary-custom btn-sm text-light" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-gear-fill me-2"></i>Kelola Requirements
                    </a>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-file-earmark-x display-4 text-muted mb-3"></i>
                    <h5 class="text-muted mb-3">Belum Ada Requirements</h5>
                    <a href="{{ route('admin.certification-schemes.requirements', $scheme) }}" 
                       class=" btn-primary-custom btn-sm text-light" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Requirements
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Unit Kompetensi Section --}}
    <div class="glass-card mb-5">
        <div class="section-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <h6 class="text-white mb-0"><i class="bi bi-list-check me-2"></i>Unit Kompetensi</h6>
        </div>
        <div class="card-body p-4">
            @if ($scheme->unitKompetensis->count() > 0)
                <div class="row g-3 mb-4">
                    @foreach ($scheme->unitKompetensis->take(6) as $unit)
                        <div class="col-lg-6">
                            <div class="modern-list-item">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary bg-gradient rounded-3 p-3 me-3">
                                        <i class="bi bi-clipboard-check text-white fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-2">{{ $unit->kode_unit }}</h6>
                                        <p class="mb-2">{{ $unit->judul_unit }}</p>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary-subtle text-primary">
                                                <i class="bi bi-puzzle me-1"></i>{{ $unit->active_elemen_count }} Elemen
                                            </span>
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="bi bi-check-all me-1"></i>{{ $unit->active_kriteria_count }} Kriteria
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if ($scheme->unitKompetensis->count() > 6)
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle fs-5 me-3"></i>
                        <div>
                            <strong>{{ $scheme->unitKompetensis->count() - 6 }} unit kompetensi lainnya</strong><br>
                            <small>Klik "Lihat Semua" untuk melihat seluruh unit kompetensi</small>
                        </div>
                    </div>
                @endif
                
                <div class="text-center">
                    <a href="{{ route('admin.schemes.unit-kompetensi.index', $scheme) }}" 
                       class=" btn-primary-custom btn-sm text-light" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <i class="bi bi-eye-fill me-2"></i>Lihat Semua Unit
                    </a>
                </div>
            @else
                 <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x display-4 text-muted mb-3"></i>
                        <h5 class="text-muted mb-3">Belum Ada Unit Kompetensi</h5>
                        <a href="{{ route('admin.schemes.unit-kompetensi.create', $scheme) }}" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);"
                            class=" btn-primary-custom btn-sm text-light">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Unit Kompetensi
                        </a>
                    </div>
            @endif
        </div>
    </div>

    {{-- Kelompok Kerja Section --}}
    <div class="glass-card mb-5">
        <div class="section-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <h6 class="text-white mb-0"><i class="bi bi-people-fill me-2"></i>Kelompok Kerja</h6>
        </div>
        <div class="card-body p-4">
            @if ($scheme->kelompokKerjas->count() > 0)
                <div class="row g-3 mb-4">
                    @foreach ($scheme->kelompokKerjas->take(4) as $kelompok)
                        <div class="col-md-6">
                            <div class="modern-list-item">
                                <div class="d-flex align-items-start">
                                    <div class="rounded-3 p-3 me-3" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                        <i class="bi bi-people text-white fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-2">{{ $kelompok->nama_kelompok }}</h6>
                                        <div class="d-flex align-items-center justify-content-between">
                                            {{-- <span class="badge bg-warning-subtle text-warning">
                                                <i class="bi bi-folder me-1"></i>{{ $kelompok->active_bukti_count }} Bukti
                                            </span> --}}
                                            <small class="text-muted">Aktif</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if ($scheme->kelompokKerjas->count() > 4)
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle fs-5 me-3"></i>
                        <div>
                            <strong>{{ $scheme->kelompokKerjas->count() - 4 }} kelompok kerja lainnya</strong><br>
                            <small>Klik "Lihat Semua" untuk melihat seluruh kelompok kerja</small>
                        </div>
                    </div>
                @endif
                
                <div class="text-center">
                    <a href="{{ route('admin.schemes.kelompok-kerja.index', $scheme) }}" 
                       class=" btn-primary-custom btn-sm text-light" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="bi bi-eye-fill me-2"></i>Lihat Semua Kelompok
                    </a>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-people-fill display-4 text-muted mb-3"></i>
                    <h5 class="text-muted mb-3">Belum Ada Kelompok Kerja</h5>
                    <p class="text-muted mb-4">Buat kelompok kerja untuk mengorganisir bukti portofolio</p>
                    <a href="{{ route('admin.schemes.kelompok-kerja.create', $scheme) }}" 
                       class=" btn-primary-custom btn-sm text-light" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kelompok Kerja
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animations to cards
    const cards = document.querySelectorAll('.glass-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Hover effects
    const listItems = document.querySelectorAll('.modern-list-item');
    listItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Button loading states
    const buttons = document.querySelectorAll('.floating-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!this.href.includes('#')) {
                this.style.opacity = '0.7';
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Loading...';
            }
        });
    });
});

// Toggle status function
function toggleStatus() {
    if (confirm('Apakah Anda yakin ingin mengubah status skema ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.certification-schemes.toggle-status", $scheme) }}';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        window.location.href = '{{ route("admin.certification-schemes.edit", $scheme) }}';
    }
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.certification-schemes.index") }}';
    }
});
</script>
@endpush