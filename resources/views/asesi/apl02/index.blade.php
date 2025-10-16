@extends('layouts.admin')

@section('title', 'APL 02 - Self Assessment')

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
                <h4 class="m-0">APL 02 - SELF ASSESSMENT</h4>
                <small class="text-muted">Asesmen Mandiri Portofolio Kompetensi</small>
            </div>
            <div>
                <a href="{{ route('asesi.apl02.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i>Buat APL 02 Baru
                </a>
            </div>
        </div>

        <!-- APL 02 List -->
        <div class="card m-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar APL 02</h6>
                <span class="badge bg-info">{{ $apl02s->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                @if ($apl02s->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nomor APL 02</th>
                                    <th>APL 01 / Skema</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Tanda Tangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apl02s as $apl02)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">
                                                {{ $apl02->nomor_apl_02 ?: 'DRAFT-' . $apl02->id }}
                                            </div>
                                            <small class="text-muted">{{ $apl02->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold small">{{ $apl02->apl01->nomor_apl_01 }}</div>
                                                <div class="text-muted small">{{ $apl02->certificationScheme->nama }}</div>
                                                <div class="text-muted small">{{ $apl02->certificationScheme->jenjang }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $apl02->competency_percentage >= 80 ? 'success' : ($apl02->competency_percentage >= 65 ? 'info' : ($apl02->competency_percentage >= 50 ? 'warning' : 'danger')) }}" 
                                                         style="width: {{ $apl02->competency_percentage }}%"></div>
                                                </div>
                                                <small class="fw-bold">{{ number_format($apl02->competency_percentage, 1) }}%</small>
                                            </div>
                                            <div class="small text-muted">
                                                {{ $apl02->kompeten_count }}/{{ $apl02->total_elements }} Kompeten
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusConfig = [
                                                    'draft' => ['color' => 'secondary', 'text' => 'Draft', 'icon' => 'pencil-square'],
                                                    'submitted' => ['color' => 'info', 'text' => 'Submitted', 'icon' => 'send'],
                                                    'review' => ['color' => 'primary', 'text' => 'Review', 'icon' => 'eye'],
                                                    'approved' => ['color' => 'success', 'text' => 'Disetujui', 'icon' => 'check-circle'],
                                                    'rejected' => ['color' => 'danger', 'text' => 'Ditolak', 'icon' => 'x-circle'],
                                                    'returned' => ['color' => 'warning', 'text' => 'Dikembalikan', 'icon' => 'arrow-return-left'],
                                                ];
                                                $status = $statusConfig[$apl02->status] ?? ['color' => 'secondary', 'text' => ucfirst($apl02->status), 'icon' => 'question-circle'];
                                            @endphp
                                            <span class="badge bg-{{ $status['color'] }}">
                                                <i class="bi bi-{{ $status['icon'] }}"></i>
                                                {{ $status['text'] }}
                                            </span>
                                            @if($apl02->status === 'rejected' && $apl02->reviewer_notes)
                                                <div class="small text-danger mt-1">
                                                    <i class="bi bi-info-circle"></i>
                                                    {{ Str::limit($apl02->reviewer_notes, 50) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                @if($apl02->submitted_at)
                                                    <strong>Submit:</strong> {{ $apl02->submitted_at->format('d/m/Y H:i') }}<br>
                                                @endif
                                                @if($apl02->completed_at)
                                                    <strong>Selesai:</strong> {{ $apl02->completed_at->format('d/m/Y H:i') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="signature-status">
                                                <div class="d-flex align-items-center mb-1">
                                                    @if($apl02->is_signed_by_asesi)
                                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                        <small>Asesi</small>
                                                    @else
                                                        <i class="bi bi-circle text-muted me-1"></i>
                                                        <small class="text-muted">Asesi</small>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    @if($apl02->is_signed_by_asesor)
                                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                        <small>Asesor</small>
                                                    @else
                                                        <i class="bi bi-circle text-muted me-1"></i>
                                                        <small class="text-muted">Asesor</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- View Button -->
                                                <a href="{{ route('asesi.apl02.show', $apl02) }}" 
                                                   class="btn btn-outline-info btn-sm" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <!-- Edit Button - only for draft or returned -->
                                                @if (in_array($apl02->status, ['draft', 'returned']))
                                                    <a href="{{ route('asesi.apl02.edit', $apl02) }}"
                                                        class="btn btn-outline-warning btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif

                                                <!-- Export PDF Button -->
                                                @if($apl02->status !== 'draft')
                                                    <a href="{{ route('asesi.apl02.export-pdf', $apl02) }}" 
                                                       class="btn btn-outline-secondary btn-sm" title="Download PDF">
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
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $apl02s->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-check display-1 text-muted"></i>
                        <h5 class="mt-3">Belum Ada APL 02</h5>
                        <p class="text-muted">Mulai dengan membuat APL 02 dari APL 01 yang sudah disetujui.</p>
                        <a href="{{ route('asesi.apl02.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-1"></i>Buat APL 02 Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Help Card -->
        <div class="card mt-4 border-info m-3">
            <div class="card-header bg-info bg-opacity-25">
                <h6 class="mb-0 text-info">
                    <i class="bi bi-info-circle-fill"></i>
                    Tentang APL 02
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>APL 02 (Self Assessment Portfolio)</strong> adalah dokumen yang berisi:</p>
                <ul class="mb-3">
                    <li>Penilaian mandiri terhadap kompetensi berdasarkan elemen-elemen dalam unit kompetensi</li>
                    <li>Upload bukti-bukti dokumen yang mendukung kompetensi Anda</li>
                    <li>Tanda tangan digital dari asesi dan asesor</li>
                </ul>
                <p class="mb-0"><small class="text-muted">
                    <i class="bi bi-lightbulb"></i> 
                    <strong>Tips:</strong> Pastikan Anda telah memiliki semua dokumen bukti sebelum mengisi APL 02.
                </small></p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .signature-status small {
            font-size: 0.7rem;
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
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-right: 0;
                margin-bottom: 2px;
            }
        }
    </style>
@endpush