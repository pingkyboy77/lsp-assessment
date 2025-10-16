{{-- resources/views/admin/tuk-requests/partials/user-documents-section.blade.php --}}

@php
    $userDocuments = $apl->user->documents ?? collect();
    $documentTypes = [
        'ktp' => ['label' => 'KTP/Identitas', 'icon' => 'bi-card-heading', 'color' => 'primary'],
        'ijazah' => ['label' => 'Ijazah', 'icon' => 'bi-mortarboard', 'color' => 'success'],
        'sertifikat' => ['label' => 'Sertifikat', 'icon' => 'bi-award', 'color' => 'warning'],
        'cv' => ['label' => 'Curriculum Vitae', 'icon' => 'bi-file-person', 'color' => 'info'],
        'foto' => ['label' => 'Foto', 'icon' => 'bi-camera', 'color' => 'secondary'],
        'kk' => ['label' => 'Kartu Keluarga', 'icon' => 'bi-people', 'color' => 'primary'],
        'npwp' => ['label' => 'NPWP', 'icon' => 'bi-file-earmark-text', 'color' => 'success'],
        'bpjs' => ['label' => 'BPJS', 'icon' => 'bi-shield-check', 'color' => 'danger'],
        'surat_kerja' => ['label' => 'Surat Keterangan Kerja', 'icon' => 'bi-briefcase', 'color' => 'info'],
        'portofolio' => ['label' => 'Portofolio', 'icon' => 'bi-folder', 'color' => 'warning'],
        'lainnya' => ['label' => 'Dokumen Lainnya', 'icon' => 'bi-file-earmark', 'color' => 'secondary'],
    ];
@endphp

<div class="card mb-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">
            <i class="bi bi-folder2-open me-2"></i>Dokumen Pendukung Asesi
        </h6>
    </div>
    <div class="card-body">
        @if ($userDocuments->isEmpty())
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Belum ada dokumen pendukung yang diunggah oleh asesi
            </div>
        @else
            <div class="row g-3">
                @foreach ($userDocuments as $document)
                    @php
                        $docType = $documentTypes[$document->document_type] ?? [
                            'label' => ucwords(str_replace('_', ' ', $document->document_type)),
                            'icon' => 'bi-file-earmark',
                            'color' => 'secondary',
                        ];
                    @endphp

                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-{{ $docType['color'] }} shadow-sm hover-shadow">
                            <div class="card-body">
                                <!-- Document Icon & Type -->
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-{{ $docType['color'] }} bg-opacity-10 p-3">
                                            <i class="{{ $docType['icon'] }} text-{{ $docType['color'] }}"
                                                style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fw-semibold">{{ $docType['label'] }}</h6>
                                        <span
                                            class="badge bg-{{ $docType['color'] }} bg-opacity-10 text-{{ $docType['color'] }}">
                                            {{ $document->file_type_text }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Document Details -->
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-file-earmark me-1"></i>
                                        {{ Str::limit($document->original_name, 30) }}
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-hdd me-1"></i>
                                        {{ $document->file_size_formatted }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $document->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>

                                @if ($document->description)
                                    <div class="mb-3">
                                        <small class="text-muted fst-italic">
                                            "{{ Str::limit($document->description, 50) }}"
                                        </small>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    @if ($document->file_exists)
                                        @if ($document->isImage())
                                            <!-- Preview Image Button -->
                                            <button type="button"
                                                class="btn btn-sm btn-outline-{{ $docType['color'] }}"
                                                onclick="previewImage('{{ $document->file_url }}', '{{ $docType['label'] }}')">
                                                <i class="bi bi-eye me-1"></i>Lihat
                                            </button>
                                        @elseif($document->mime_type === 'application/pdf')
                                            <!-- Preview PDF Button -->
                                            <button type="button"
                                                class="btn btn-sm btn-outline-{{ $docType['color'] }}"
                                                onclick="previewPDF('{{ $document->file_url }}', '{{ $docType['label'] }}')">
                                                <i class="bi bi-eye me-1"></i>Lihat PDF
                                            </button>
                                        @else
                                            <!-- Download Button for other files -->
                                            <a href="{{ route('admin.tuk-requests.user-documents.download', $document->id) }}"
                                                class="btn btn-sm btn-outline-{{ $docType['color'] }}" target="_blank">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        @endif

                                        <!-- Always show download option -->
                                        <a href="{{ route('admin.tuk-requests.user-documents.download', $document->id) }}"
                                            class="btn btn-sm btn-secondary" download>
                                            <i class="bi bi-cloud-download me-1"></i>Download File
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-outline-danger" disabled>
                                            <i class="bi bi-exclamation-triangle me-1"></i>File Tidak Ditemukan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Document Statistics -->
            <div class="mt-4 pt-3 border-top">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="small text-muted">Total Dokumen</div>
                        <div class="h5 mb-0 fw-bold">{{ $userDocuments->count() }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Total Ukuran</div>
                        <div class="h5 mb-0 fw-bold">
                            @php
                                $totalSize = $userDocuments->sum('file_size');
                                echo \App\Models\UserDocument::formatFileSize($totalSize);
                            @endphp
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Tersedia</div>
                        <div class="h5 mb-0 fw-bold text-success">
                            {{ $userDocuments->where('file_exists', true)->count() }}/{{ $userDocuments->count() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modals hanya dibuat sekali di dalam parent modal -->
@once
    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewTitle">Preview Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagePreviewImg" src="" alt="Preview" class="img-fluid" style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewTitle">Preview PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="pdfPreviewFrame" src="" style="width: 100%; height: 80vh; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endonce

<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }
</style>

@push('scripts')
    <script>
        // Preview functions - hanya didefinisikan sekali
        if (typeof window.previewImage === 'undefined') {
            window.previewImage = function(url, title) {
                $('#imagePreviewTitle').text(title);
                $('#imagePreviewImg').attr('src', url);
                new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
            };
        }

        if (typeof window.previewPDF === 'undefined') {
            window.previewPDF = function(url, title) {
                $('#pdfPreviewTitle').text(title);
                $('#pdfPreviewFrame').attr('src', url);
                new bootstrap.Modal(document.getElementById('pdfPreviewModal')).show();
            };
        }
    </script>
@endpush
