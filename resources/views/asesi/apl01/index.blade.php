@extends('layouts.admin')

@section('title', 'Riwayat APL 01')

@section('content')
<div class="main-card">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0">INBOX</h4>
        </div>
    </div>
<div class="card m-3">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="statusFilter" class="form-label">Filter Status</label>
                <select class="form-select" id="statusFilter" name="status">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Confirm</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Re Open</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="searchInput" class="form-label">Cari APL</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" name="search" 
                           placeholder="Cari berdasarkan nama, nomor APL, atau skema..." 
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="applyFilter">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="resetFilter">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- APL 01 List -->
    <div class="card m-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            {{-- <h6 class="mb-0">List Task</h6> --}}
        </div>
        <div class="card-body p-0">
            @if($apls->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="aplTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>No. APL / Skema</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                                <th>Reviewer</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apls as $apl)
                            <tr data-status="{{ $apl->status }}">
                                <td>
                                    <div class="small">
                                        <h6 class="text-bold">{{ $apl->nama_lengkap }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold">
                                            {{ $apl->nomor_apl_01 ?: 'DRAFT-' . $apl->id }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $apl->certificationScheme->nama ?? 'Tidak ada skema' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $apl->certificationScheme->jenjang ?? '' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        {{ $apl->created_at->format('d F Y') }}
                                        <br>
                                        <span class="text-muted">{{ $apl->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                
                                <td>
                                    @php
                                        $statusConfig = [
                                            'draft' => ['color' => 'secondary', 'text' => 'Draft', 'icon' => 'pencil-square'],
                                            'open' => ['color' => 'warning', 'text' => 'Re Open', 'icon' => 'unlock'],
                                            'submitted' => ['color' => 'info', 'text' => 'Submitted', 'icon' => 'send'],
                                            'review' => ['color' => 'primary', 'text' => 'Review', 'icon' => 'eye'],
                                            'approved' => ['color' => 'success', 'text' => 'Disetujui', 'icon' => 'check-circle'],
                                            'rejected' => ['color' => 'danger', 'text' => 'Ditolak', 'icon' => 'x-circle'],
                                            'returned' => ['color' => 'warning', 'text' => 'Dikembalikan', 'icon' => 'arrow-return-left']
                                        ];
                                        $status = $statusConfig[$apl->status] ?? ['color' => 'secondary', 'text' => ucfirst($apl->status), 'icon' => 'question-circle'];
                                    @endphp
                                    <span class="badge bg-{{ $status['color'] }}">
                                        <i class="bi bi-{{ $status['icon'] }}"></i>
                                        {{ $status['text'] }}
                                    </span>
                                    @if($apl->status === 'rejected' && $apl->notes)
                                        <div class="small text-danger mt-1">
                                            <i class="bi bi-info-circle"></i>
                                            {{ Str::limit($apl->notes, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($apl->reviewer)
                                        <div class="small">
                                            {{ $apl->reviewer->name }}
                                            @if($apl->reviewed_at)
                                                <br>
                                                <span class="text-muted">{{ $apl->reviewed_at->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        
                                        <!-- Edit Button - only for draft, open, or rejected -->
                                        @if(in_array($apl->status, ['draft', 'open', 'rejected']))
                                        <a href="{{ route('asesi.apl01.edit', $apl) }}" 
                                           class="btn btn-outline-warning btn-sm" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                        
                                        <!-- Delete Button - only for draft -->
                                        @if($apl->status === 'draft')
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteApl({{ $apl->id }})" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                        
                                        @if($apl->status === 'approved')
                                        <a href="#" class="btn btn-outline-success btn-sm" 
                                           title="Download Sertifikat" disabled>
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                    <h5 class="mt-3">No Pending Task</h5>
                </div>
            @endif
        </div>
    </div>

    <!-- Next Steps Card -->
    @if($apls->where('status', 'approved')->count() > 0)
    <div class="card mt-4 border-success">
        <div class="card-header bg-success bg-opacity-25">
            <h6 class="mb-0 text-success">
                <i class="bi bi-arrow-right-circle-fill"></i> 
                Langkah Selanjutnya
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-3">APL 01 Anda telah disetujui! Berikut adalah langkah-langkah selanjutnya:</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-file-earmark-plus display-6 text-primary"></i>
                        <h6 class="mt-2">APL 02</h6>
                        <p class="small text-muted">Self Assessment Portfolio</p>
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            Segera Tersedia
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-calendar-check display-6 text-info"></i>
                        <h6 class="mt-2">Jadwal Asesmen</h6>
                        <p class="small text-muted">Penjadwalan uji kompetensi</p>
                        <button class="btn btn-outline-info btn-sm" disabled>
                            Segera Tersedia
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-award display-6 text-success"></i>
                        <h6 class="mt-2">Sertifikat</h6>
                        <p class="small text-muted">Download sertifikat kompetensi</p>
                        <button class="btn btn-outline-success btn-sm" disabled>
                            Segera Tersedia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus APL 01</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus APL 01 ini?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.badge {
    font-size: 0.75rem;
}

.progress {
    background-color: #e9ecef;
}

.btn-group .btn {
    border-radius: 4px;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.025);
}

.alert {
    border-radius: 8px;
}

.display-6 {
    font-size: 3rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 2px;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const applyFilter = document.getElementById('applyFilter');
    const resetFilter = document.getElementById('resetFilter');
    const clearSearch = document.getElementById('clearSearch');

    // Apply filter function
    function applyFilters() {
        const status = statusFilter.value;
        const search = searchInput.value.trim();
        
        const params = new URLSearchParams();
        if (status) params.append('status', status);
        if (search) params.append('search', search);
        
        const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    }

    // Event listeners
    applyFilter.addEventListener('click', applyFilters);
    
    resetFilter.addEventListener('click', function() {
        statusFilter.value = '';
        searchInput.value = '';
        window.location.href = window.location.pathname;
    });
    
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
    });
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
    
    statusFilter.addEventListener('change', function() {
        if (this.value !== '') {
            applyFilters();
        }
    });
});
let deleteAplId = null;

// Status filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('#aplTable tbody tr');
    
    rows.forEach(row => {
        if (!filterValue || row.dataset.status === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Delete APL functionality
function deleteApl(aplId) {
    deleteAplId = aplId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', async function() {
    if (!deleteAplId) return;
    
    const button = this;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghapus...';
    
    try {
        const response = await fetch(`{{ route('asesi.apl01.index') }}/${deleteAplId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            // Show success message
            showAlert('success', result.message);
            
            // Remove row from table
            const row = document.querySelector(`#aplTable tbody tr:has(button[onclick="deleteApl(${deleteAplId})"])`);
            if (row) {
                row.remove();
            }
            
            // Reload page to update stats
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(result.message || 'Gagal menghapus APL');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showAlert('danger', 'Gagal menghapus APL: ' + error.message);
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
});

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Auto-refresh data every 30 seconds
setInterval(() => {
    // Only refresh if not in modal
    if (!document.querySelector('.modal.show')) {
        const currentFilter = document.getElementById('statusFilter').value;
        
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update statistics if needed
            // This would require additional implementation
        })
        .catch(error => {
            console.error('Auto-refresh error:', error);
        });
    }
}, 30000);
</script>
@endpush