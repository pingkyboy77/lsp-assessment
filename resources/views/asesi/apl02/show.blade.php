{{-- resources/views/asesi/apl02/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail APL 02 - Self Assessment')

@push('styles')
    @include('asesi.apl02.partials.styles.assessment-form')
    <style>
        .assessment-status {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-draft {
            background-color: #e9ecef;
            color: #495057;
        }

        .status-submitted {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-reviewed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-returned {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .assessment-result {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .result-kompeten {
            background-color: #d4edda;
            color: #155724;
        }

        .result-belum-kompeten {
            background-color: #f8d7da;
            color: #721c24;
        }

        .result-not-assessed {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .signature-display {
            max-width: 200px;
            max-height: 100px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background: white;
        }

        .evidence-document {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
            margin-bottom: 0.5rem;
        }

        .file-icon {
            font-size: 1.5rem;
            color: #6c757d;
        }

        .assessment-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .readonly-form .form-control,
        .readonly-form .form-check-input {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            cursor: default;
        }

        .readonly-form .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .selected-document {
            background-color: #e8f5e8;
            border-color: #28a745;
        }

        .document-status {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }

        .status-uploaded {
            background-color: #d4edda;
            color: #155724;
        }

        .status-not-uploaded {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                break-inside: avoid;
            }
        }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert-success-custom">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif
    <div class="main-card">
        <!-- Header Section -->
        <div class="card-header-custom d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="m-0">APL 02 - SELF ASSESSMENT</h4>
                <small class="text-light">{{ $apl02->certificationScheme->nama }}</small>
            </div>
            <div class="mt-2">
                <span class="assessment-status status-{{ $apl02->status }}">
                    {{ $apl02->status_text }}
                </span>
            </div>
        </div>
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
        <!-- Assessment Summary -->
        @if ($apl02->status !== 'draft')
            <div class="assessment-summary m-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-3">Ringkasan Assessment</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="progress-circle bg-success bg-opacity-25 me-3">
                                        {{ $apl02->elementAssessments->where('assessment_result', 'kompeten')->count() }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">Kompeten</div>
                                        <small class="opacity-75">Elemen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="progress-circle bg-danger bg-opacity-25 me-3">
                                        {{ $apl02->elementAssessments->where('assessment_result', 'belum_kompeten')->count() }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">Belum Kompeten</div>
                                        <small class="opacity-75">Elemen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="progress-circle bg-info bg-opacity-25 me-3">
                                        {{ $apl02->evidenceSubmissions->count() }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">Bukti Portfolio</div>
                                        <small class="opacity-75">Terupload</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        @if ($apl02->submitted_at)
                            <div class="mb-2">
                                <i class="bi bi-calendar-check display-4 opacity-75"></i>
                            </div>
                            <div class="fw-bold">Tanggal Submit</div>
                            <div>{{ $apl02->submitted_at->format('d M Y H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Basic Information -->
        <div class="card mb-4 m-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Dasar
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 180px;">Nomor APL 02:</td>
                                <td>{{ $apl02->nomor_apl_02 ?? 'Belum diset' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nama Asesi:</td>
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
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 180px;">Status:</td>
                                <td>
                                    <span class="assessment-status status-{{ $apl02->status }}">
                                        {{ $apl02->status_text }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal Dibuat:</td>
                                <td>{{ $apl02->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @if ($apl02->submitted_at)
                                <tr>
                                    <td class="fw-bold">Tanggal Submit:</td>
                                    <td>{{ $apl02->submitted_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                            @if ($apl02->reviewed_at)
                                <tr>
                                    <td class="fw-bold">Tanggal Review:</td>
                                    <td>{{ $apl02->reviewed_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Results by Unit with Element-Specific Documents -->
        <form class="readonly-form m-3">
            @foreach ($apl02->certificationScheme->activeUnits()->with([
                'activeElemenKompetensis.activeKriteriaKerjas',
                'activeElemenKompetensis.assessments' => function ($q) use ($apl02) {
                    $q->where('apl_02_id', $apl02->id);
                },
                'portfolioFiles' => function ($q) {
                    $q->where('portfolio_files.is_active', true)->orderBy('portfolio_files.sort_order')->orderBy('portfolio_files.document_name');
                },
            ])->ordered()->get() as $unit)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-book me-2"></i>
                            {{ $unit->kode_unit }} - {{ ucwords(strtolower($unit->judul_unit)) }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach ($unit->activeElemenKompetensis as $element)
                            @php
                                $assessment = $element->assessments->first();
                                $result = $assessment ? $assessment->assessment_result : null;

                                // Parse element documents from notes JSON
                                $elementDocuments = [];
                                if ($assessment && $assessment->notes) {
                                    $decoded = json_decode($assessment->notes, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $elementDocuments = $decoded;
                                    }
                                }

                                // FIXED: Get all evidence files grouped by document name for easy lookup
                                $allEvidenceFiles = $apl02
                                    ->evidenceSubmissions()
                                    ->with('portfolioFile')
                                    ->get()
                                    ->groupBy(function ($evidence) {
                                        return $evidence->portfolioFile
                                            ? $evidence->portfolioFile->document_name
                                            : null;
                                    })
                                    ->filter(function ($group, $documentName) {
                                        return $documentName !== null;
                                    })
                                    ->map(function ($evidenceGroup) {
                                        return $evidenceGroup->first(); // Take first evidence for this document name
                                    });
                            @endphp
                            <div class="element-assessment mb-4 p-3 border rounded bg-white shadow-sm">
                                <div class="row g-3">
                                    {{-- LEFT: Element + Criteria (col-md-6) --}}
                                    <div class="col-md-5">
                                        {{-- <h6 class="fw-bold mb-3"
                                            style="text-align: justify; font-size: 1.15rem; line-height:1.4;">
                                            {{ $element->judul_elemen }}
                                        </h6> --}}
                                        <h6 class="mb-3" style="text-align: justify; line-height: 1.5;">{{ $element->judul_elemen }}</h6>

                                        <div class="criteria-list ms-1">
                                            <h6 class="fw-semibold text-black mb-2">Kriteria Unjuk Kerja:</h6>
                                            <ol class="ps-3"
                                                style="text-align: justify; line-height: 1.6; font-size: 0.98rem;">
                                                @foreach ($element->activeKriteriaKerjas as $criteria)
                                                    <li class="mb-1"><p class=" text-black mb-2">{{ $criteria->uraian_kriteria }}</h6></li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>

                                    {{-- MIDDLE: Self Assessment Result (col-md-3) --}}
                                    <div class="col-md-3 border-start ps-4 d-flex flex-column justify-content-start">
                                        <label class="form-label fw-bold mb-3">Penilaian Diri</label>

                                        <div class="mb-3">
                                            @if ($result === 'kompeten')
                                                <span class="fw-bold text-success" style="font-size: 1rem;">Kompeten</span>
                                            @elseif ($result === 'belum_kompeten')
                                                <span class="fw-bold text-danger" style="font-size: 1rem;">Belum
                                                    Kompeten</span>
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 1rem;">Belum
                                                    dinilai</span>
                                            @endif
                                        </div>


                                        @if (!$result)
                                            <div class="text-muted small">
                                                <i class="bi bi-circle me-1"></i>Belum dinilai
                                            </div>
                                        @endif
                                    </div>

                                    {{-- RIGHT: Element-Specific Documents (col-md-3) --}}
                                    <div class="col-md-4 border-start ps-4">
                                        @if (count($elementDocuments) > 0)
                                            <h6 class="text-primary mb-3" style="font-size: 0.95rem;">
                                                <i class="bi bi-paperclip me-1"></i>
                                                Dokumen yang Dipilih:
                                            </h6>
                                            <div class="selected-documents-list">
                                                @foreach ($elementDocuments as $document)
                                                    @php
                                                        // Find the portfolio file by portfolio ID
                                                        $portfolioFile = $unit->portfolioFiles->firstWhere(
                                                            'id',
                                                            $document['portfolioId'],
                                                        );

                                                        // FIXED: Check if this document name has uploaded evidence (regardless of portfolio ID)
                                                        $uploadedEvidence = $allEvidenceFiles->get(
                                                            $document['documentName'],
                                                        );
                                                    @endphp

                                                    @if ($portfolioFile)
                                                        <div
                                                            class="evidence-document {{ $uploadedEvidence ? 'selected-document' : '' }}">
                                                            <div class="d-flex align-items-center">
                                                                <div class="file-icon me-2">
                                                                    @if ($uploadedEvidence)
                                                                        @switch(strtolower($uploadedEvidence->file_type))
                                                                            @case('pdf')
                                                                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                                                            @break

                                                                            @case('doc')
                                                                            @case('docx')
                                                                                <i class="bi bi-file-earmark-word text-primary"></i>
                                                                            @break

                                                                            @case('jpg')
                                                                            @case('jpeg')

                                                                            @case('png')
                                                                            @case('gif')
                                                                                <i
                                                                                    class="bi bi-file-earmark-image text-success"></i>
                                                                            @break

                                                                            @default
                                                                                <i class="bi bi-file-earmark text-secondary"></i>
                                                                        @endswitch
                                                                    @else
                                                                        <i class="bi bi-file-earmark-plus text-muted"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold" style="font-size: 0.85rem;">
                                                                        {{ $portfolioFile->document_name }}
                                                                    </div>

                                                                    @if ($uploadedEvidence)
                                                                        <div class="document-status status-uploaded">
                                                                            <i class="bi bi-check-circle-fill me-1"></i>
                                                                            Terupload: {{ $uploadedEvidence->file_name }}
                                                                        </div>
                                                                        <small class="text-muted d-block">
                                                                            {{ $uploadedEvidence->file_size_formatted }}
                                                                        </small>
                                                                    @else
                                                                        <div class="document-status status-not-uploaded">
                                                                            <i
                                                                                class="bi bi-exclamation-circle-fill me-1"></i>
                                                                            Belum diupload
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                @if ($uploadedEvidence)
                                                                    <div class="btn-group btn-group-sm no-print">
                                                                        <button type="button"
                                                                            class="btn btn-outline-primary btn-sm"
                                                                            onclick="downloadFile('{{ route('asesi.apl02.download-evidence', [$apl02->id, $uploadedEvidence->id]) }}')"
                                                                            title="Download">
                                                                            <i class="bi bi-download"></i>
                                                                        </button>
                                                                        @if (in_array(strtolower($uploadedEvidence->file_type), ['pdf', 'jpg', 'jpeg', 'png', 'gif']))
                                                                            <button type="button"
                                                                                class="btn btn-outline-info btn-sm"
                                                                                onclick="previewFile('{{ route('asesi.apl02.preview-evidence', [$apl02->id, $uploadedEvidence->id]) }}')"
                                                                                title="Preview">
                                                                                <i class="bi bi-eye"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted small">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Tidak ada dokumen yang dipilih untuk elemen ini.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </form>

        <!-- Digital Signature -->
        @if ($apl02->tanda_tangan_asesi || $apl02->status !== 'draft')
            <div class="card mb-4 m-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-pen me-2"></i>
                        TANDA TANGAN DIGITAL
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            @if ($apl02->tanda_tangan_asesi)
                                <div class="signature-container">
                                    <label class="form-label fw-bold">Tanda Tangan Asesi</label>
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($apl02->tanda_tangan_asesi) }}"
                                            alt="Tanda tangan asesi" class="signature-display">
                                    </div>

                                    @if ($apl02->tanggal_tanda_tangan_asesi)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            Ditandatangani pada:
                                            {{ $apl02->tanggal_tanda_tangan_asesi->format('d M Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Tanda tangan belum tersedia
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            @if ($apl02->tanda_tangan_asesor)
                                <div class="signature-container">
                                    <label class="form-label fw-bold">Tanda Tangan Asesor</label>
                                    <div class="mb-3">
                                        <img src="{{ $apl02->tanda_tangan_asesor }}" alt="Tanda tangan asesor"
                                            class="signature-display">
                                    </div>
                                    @if ($apl02->tanggal_tanda_tangan_asesor)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            Ditandatangani pada:
                                            {{ $apl02->tanggal_tanda_tangan_asesor->format('d M Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Menunggu tanda tangan asesor
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Review Notes (if any) -->
        @if ($apl02->catatan_reviewer || $apl02->catatan_asesor)
            <div class="card mb-4 m-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-chat-left-text me-2"></i>
                        CATATAN REVIEW
                    </h6>
                </div>
                <div class="card-body">
                    @if ($apl02->catatan_reviewer)
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary">Catatan Reviewer:</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $apl02->catatan_reviewer }}
                            </div>
                        </div>
                    @endif

                    @if ($apl02->catatan_asesor)
                        <div class="mb-3">
                            <h6 class="fw-bold text-success">Catatan Asesor:</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $apl02->catatan_asesor }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="card mb-4 no-print m-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('asesi.inbox.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke Pra Assessment
                        </a>
                    </div>

                    <div class="d-flex gap-2">
                        @if (in_array($apl02->status, ['draft', 'returned']))
                            <a href="{{ route('asesi.apl02.edit', $apl02) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>Edit Assessment
                            </a>
                        @endif

                        {{-- <a href="{{ route('asesi.apl02.preview', $apl02) }}" class="btn btn-outline-info"
                            target="_blank">
                            <i class="bi bi-eye me-1"></i>Preview
                        </a> --}}

                        {{-- <a href="{{ route('asesi.apl02.export-pdf', $apl02) }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i>Export PDF
                        </a> --}}
                    </div>
                </div>

                @if ($apl02->status === 'draft')
                    <div class="mt-3">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Status Draft:</strong> Assessment masih dalam tahap draft. Silakan lengkapi dan submit
                            untuk review.
                        </div>
                    </div>
                @elseif($apl02->status === 'returned')
                    <div class="mt-3">
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Dikembalikan untuk Revisi:</strong> Silakan perbaiki sesuai catatan reviewer dan submit
                            ulang.
                        </div>
                    </div>
                @elseif($apl02->status === 'submitted')
                    <div class="mt-3">
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Telah Disubmit:</strong> Assessment sedang menunggu review dari asesor.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="filePreviewContent" class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function downloadFile(url) {
            const link = document.createElement('a');
            link.href = url;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function previewFile(url) {
            const modal = document.getElementById('filePreviewModal');
            const modalBody = document.getElementById('filePreviewContent');

            // Show loading spinner
            modalBody.innerHTML = `
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;

            // Show modal
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            // Try to load the file
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('File cannot be previewed');
                    }

                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/pdf')) {
                        modalBody.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="600px">`;
                    } else if (contentType && contentType.includes('image/')) {
                        modalBody.innerHTML = `<img src="${url}" class="img-fluid" alt="Preview">`;
                    } else {
                        throw new Error('File type not supported for preview');
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            File tidak dapat dipreview di browser. 
                            <a href="${url}" target="_blank" class="btn btn-sm btn-primary ms-2">
                                <i class="bi bi-download me-1"></i>Download File
                            </a>
                        </div>
                    `;
                });
        }

        // Print handling
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });

        function requestVerification(id) {
            if (confirm('Apakah Anda yakin ingin mengajukan permohonan verifikasi TUK?')) {
                showToast('info', 'Fitur verifikasi TUK akan segera tersedia');
            }
        }
    </script>
@endpush
