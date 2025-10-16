{{-- @dd($apl02->apl01) --}}
@extends('layouts.admin')

@section('title', 'Preview APL 02')

@section('content')
    <div class="main-card">
        <!-- Header Section -->
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="m-0">PREVIEW APL 02 - SELF ASSESSMENT</h4>
                <small class="text-muted">{{ $apl02->certificationScheme->nama }}</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-{{ $apl02->status_color }} fs-6">{{ $apl02->status_text }}</span>
                <a href="{{ route('asesi.apl02.export-pdf', $apl02) }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                </a>
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
                <button onclick="window.close()" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i>Close
                </button>
            </div>
        </div>

        <div class="preview-content">
            <!-- Document Header -->
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h5 class="mb-0">FORMULIR APL-02</h5>
                    <h6 class="mb-0">ASESMEN MANDIRI (SELF ASSESSMENT)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" width="30%">Nama Asesi:</td>
                                    <td>{{ $apl02->apl01->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Skema Sertifikasi:</td>
                                    <td>{{ $apl02->certificationScheme->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jenjang:</td>
                                    <td>{{ $apl02->certificationScheme->jenjang }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" width="30%">Nomor APL-01:</td>
                                    <td>{{ $apl02->apl01->nomor_apl_01 }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal:</td>
                                    <td>{{ $apl02->created_at->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $apl02->status_color }}">{{ $apl02->status_text }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Results by Unit -->
            @foreach ($assessmentData as $unitData)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <strong>{{ $unitData['unit']->kode_unit }}</strong> - {{ $unitData['unit']->judul_unit }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="20%">Elemen Kompetensi</th>
                                        <th width="40%">Kriteria Unjuk Kerja</th>
                                        <th width="15%">Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unitData['elements'] as $elementData)
                                        <tr>
                                            <td class="fw-bold text-primary">
                                                {{ $elementData['element']->kode_elemen }}
                                                <br>
                                                <small>{{ $elementData['element']->judul_elemen }}</small>
                                            </td>
                                            <td>
                                                @foreach ($elementData['criterias'] as $criteria)
                                                    <div class="mb-2">
                                                        <span class="badge bg-secondary me-1">{{ $criteria->kode_kriteria }}</span>
                                                        {{ $criteria->uraian_kriteria }}
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                @if (optional($elementData['assessment'])->assessment_result)
                                                    @if ($elementData['assessment']->assessment_result === 'kompeten')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Kompeten
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle me-1"></i>Belum Kompeten
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Belum Dinilai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Evidence Section -->
            @if ($evidenceSubmissions->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-paperclip me-2"></i>
                            Bukti Portfolio yang Disubmit
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Dokumen Portfolio</th>
                                        <th>Nama File</th>
                                        <th>Ukuran</th>
                                        <th>Tanggal Upload</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evidenceSubmissions as $index => $evidence)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $evidence->portfolioFile->document_name ?? 'N/A' }}</td>
                                            <td>{{ $evidence->file_name }}</td>
                                            <td>{{ $evidence->file_size_formatted }}</td>
                                            <td>{{ $evidence->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('asesi.apl02.download-evidence', [$apl02, $evidence->id]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>
                                Ringkasan Penilaian
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <div class="display-6 text-primary">{{ $apl02->total_elements }}</div>
                                        <small class="text-muted">Total Elemen</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <div class="display-6 text-success">{{ $apl02->kompeten_count }}</div>
                                        <small class="text-muted">Kompeten</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <div class="display-6 text-danger">{{ $apl02->belum_kompeten_count }}</div>
                                        <small class="text-muted">Belum Kompeten</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <div class="display-6 text-info">{{ number_format($apl02->competency_percentage, 1) }}%</div>
                                        <small class="text-muted">Persentase Kompetensi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            @if ($apl02->is_signed_by_asesi)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-pen me-2"></i>
                            Tanda Tangan Asesi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tanggal & Waktu:</strong> {{ $apl02->asesi_signed_at->format('d M Y, H:i:s') }}</p>
                                <p><strong>IP Address:</strong> {{ $apl02->asesi_signature_ip ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="signature-preview border rounded p-3" style="background: white;">
                                    <img src="{{ $apl02->tanda_tangan_asesi }}" 
                                         alt="Tanda tangan asesi" 
                                         class="img-fluid"
                                         style="max-height: 100px;">
                                </div>
                                <small class="text-muted mt-2 d-block">Tanda Tangan Digital Asesi</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-4 pt-4 border-top">
                <small class="text-muted">
                    Dokumen ini digenerate secara otomatis pada {{ now()->format('d M Y, H:i:s') }}
                </small>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .d-flex.justify-content-between {
                display: none !important;
            }
            
            .preview-content {
                margin: 0;
                padding: 0;
            }
            
            .card {
                border: 1px solid #333 !important;
                break-inside: avoid;
                margin-bottom: 20px !important;
            }
            
            .table {
                font-size: 12px !important;
            }
            
            .badge {
                border: 1px solid #333 !important;
                color: #333 !important;
            }
            
            .signature-preview {
                border: 1px solid #333 !important;
            }
        }
        
        .preview-content {
            background: white;
            padding: 20px;
        }
        
        .table th {
            background-color: #f8f9fa !important;
            font-weight: 600;
        }
        
        .signature-preview img {
            border: 1px solid #ddd;
        }
    </style>
@endsection