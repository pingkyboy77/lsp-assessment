{{-- resources/views/admin/tuk-requests/partials/apl02-content.blade.php --}}

<div class="review-section">
    <div class="review-header">
        <i class="bi bi-file-earmark-check me-2"></i>APL 02 - Portfolio & Assessment
    </div>
    <div class="review-body">

        <!-- Status Info -->
        <div class="card mb-4" style="border: 1px solid var(--gray-100);">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informasi Asesmen</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="120" class="text-muted">Nomor APL 02</td>
                                <td><strong>{{ $apl02->nomor_apl_02 }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    @if ($apl02->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($apl02->status === 'open')
                                        <span class="badge bg-warning">Re Open</span>
                                    @elseif($apl02->status === 'submitted')
                                        <span class="badge bg-info">Submitted</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($apl02->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($apl02->submitted_at)
                                <tr>
                                    <td class="text-muted">Submitted</td>
                                    <td>{{ $apl02->submitted_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Progress Summary</h6>
                        @php
                            $totalElements = $apl02->elementAssessments->count();
                            $assessedElements = $apl02->elementAssessments->whereNotNull('assessment_result')->count();
                            $kompeten = $apl02->elementAssessments->where('assessment_result', 'kompeten')->count();
                            $progressPercentage = $totalElements > 0 ? ($assessedElements / $totalElements) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progress:</span>
                            <strong>{{ $assessedElements }} / {{ $totalElements }}</strong>
                        </div>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ $progressPercentage }}%">
                                {{ number_format($progressPercentage, 0) }}%
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success">Kompeten: {{ $kompeten }}</span>
                            <span class="badge bg-danger">Belum: {{ $totalElements - $kompeten }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($apl02->reviewer_notes)
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Catatan Review</h6>
            <p class="mb-0">{{ $apl02->reviewer_notes }}</p>
            @if($apl02->reviewed_at)
            <hr>
            <small class="text-muted">
                Review oleh: {{ $apl02->reviewer->name ?? 'N/A' }} - {{ $apl02->reviewed_at->format('d M Y H:i') }}
            </small>
            @endif
        </div>
        @endif

        <!-- Loop Per Unit Kompetensi -->
        @php
            $units = $apl02->certificationScheme->units;
        @endphp

        @foreach ($units as $unitIndex => $unit)
            <div class="mb-4">
                <div class="unit-card">
                    <div class="unit-card-header">
                        <button class="accordion-button-custom" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnit{{ $unit->id }}">
                            <div class="d-flex align-items-center w-100 gap-3">
                                <span class="unit-code-badge">{{ $unit->kode_unit }}</span>
                                <div class="unit-title">
                                    <span class="unit-title-text" title="{{ $unit->judul_unit }}">
                                        {{ $unit->judul_unit }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2 align-items-center flex-shrink-0">
                                    @php
                                        $unitElements = $unit->elemenKompetensis;
                                        $unitKompeten = $apl02->elementAssessments
                                            ->whereIn('elemen_kompetensi_id', $unitElements->pluck('id'))
                                            ->where('assessment_result', 'kompeten')
                                            ->count();
                                        $unitTotal = $unitElements->count();

                                        $portfolioFiles = $unit->portfolioFiles->where('is_active', true);
                                        $availablePortfolios = $portfolioFiles->filter(function ($portfolio) use ($apl02) {
                                            $submission = $apl02->evidenceSubmissions
                                                ->where('portfolio_file_id', $portfolio->id)
                                                ->first();
                                            return $submission && $submission->is_submitted && $submission->file_path;
                                        });
                                    @endphp
                                    <span class="status-badge {{ $unitKompeten == $unitTotal ? 'status-kompeten' : 'status-belum-kompeten' }}">
                                        {{ $unitKompeten }}/{{ $unitTotal }}
                                    </span>
                                    @if ($availablePortfolios->count() > 0)
                                        <span class="badge-bukti">
                                            <i class="bi bi-files me-1"></i>{{ $availablePortfolios->count() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </div>
                    <div id="collapseUnit{{ $unit->id }}" class="collapse {{ $unitIndex === 0 ? 'show' : '' }}">
                        <div class="unit-card-body">

                            <!-- Elemen Kompetensi -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3" style="color: var(--primary-color);">
                                    <i class="bi bi-list-check me-2"></i>Elemen Kompetensi
                                </h6>
                                @foreach ($unit->elemenKompetensis as $elemen)
                                    @php
                                        $assessment = $apl02->elementAssessments
                                            ->where('elemen_kompetensi_id', $elemen->id)
                                            ->first();
                                    @endphp
                                    <div class="elemen-card">
                                        <div class="elemen-card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold mb-1" style="color: var(--gray-900);">
                                                        {{ $elemen->kode_elemen }} - {{ $elemen->judul_elemen }}
                                                    </div>
                                                    @if ($elemen->kriteriaKerjas && $elemen->kriteriaKerjas->isNotEmpty())
                                                        <div class="mt-2">
                                                            <small class="text-muted fw-semibold">Kriteria Kerja:</small>
                                                            <ul class="small text-muted mb-0 mt-1">
                                                                @foreach ($elemen->kriteriaKerjas as $kriteria)
                                                                    <li>{{ $kriteria->kode_kriteria }}: {{ $kriteria->uraian_kriteria }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ms-3 flex-shrink-0">
                                                    @if ($assessment)
                                                        @if ($assessment->assessment_result === 'kompeten')
                                                            <span class="status-badge status-kompeten">
                                                                <i class="bi bi-check-circle-fill me-1"></i>Kompeten
                                                            </span>
                                                        @else
                                                            <span class="status-badge status-belum-kompeten">
                                                                <i class="bi bi-x-circle-fill me-1"></i>Belum Kompeten
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="status-badge status-belum-dinilai">
                                                            <i class="bi bi-circle me-1"></i>Belum Dinilai
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($assessment && $assessment->notes)
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="fw-semibold">Bukti Portofolio:</small>
                                                    @php
                                                        $buktiPortofolio = json_decode($assessment->notes, true);
                                                    @endphp
                                                    @if (is_array($buktiPortofolio))
                                                        <ul class="small mb-0 mt-1">
                                                            @foreach ($buktiPortofolio as $bukti)
                                                                <li>{{ $bukti['documentName'] ?? '-' }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="small mb-0">{{ $assessment->notes }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Persyaratan Bukti & Bukti Yang Dimiliki -->
                            <div class="row">
                                <!-- Daftar Persyaratan -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3" style="color: var(--primary-color);">
                                        <i class="bi bi-clipboard-check me-2"></i>Daftar Persyaratan Bukti
                                    </h6>
                                    @if ($portfolioFiles->count() > 0)
                                        <div class="list-group">
                                            @foreach ($portfolioFiles as $portfolio)
                                                <div class="list-group-item">
                                                    <i class="bi bi-file-text text-muted me-2"></i>
                                                    {{ $portfolio->document_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light border">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Tidak ada persyaratan bukti
                                        </div>
                                    @endif
                                </div>

                                <!-- Bukti Yang Dimiliki -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3" style="color: var(--primary-color);">
                                        <i class="bi bi-files me-2"></i>Bukti Yang Dimiliki
                                    </h6>
                                    @if ($availablePortfolios->count() > 0)
                                        <div>
                                            @foreach ($availablePortfolios as $portfolio)
                                                @php
                                                    $submission = $apl02->evidenceSubmissions
                                                        ->where('portfolio_file_id', $portfolio->id)
                                                        ->first();
                                                @endphp

                                                <div class="portfolio-item">
                                                    <i class="bi bi-file-earmark-text portfolio-icon"></i>
                                                    <span class="portfolio-name">{{ $portfolio->document_name }}</span>
                                                    <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Lihat
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light border">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Belum ada bukti yang diupload
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Tanda Tangan -->
        <div class="section-header-small">
            <h6 class="fw-bold mb-3 mt-4" style="color: var(--gray-900);">
                <span class="section-number-small">11</span>
                Tanda Tangan
            </h6>
        </div>

        <table class="info-table w-100 mb-4">
            <tr>
                <td>Tanda Tangan Asesi</td>
                <td>
                    @if ($apl02->tanda_tangan_asesi)
                        <div class="signature-display">
                            <img src="{{ Storage::url($apl02->tanda_tangan_asesi) }}" alt="Tanda tangan asesi"
                                style="max-width: 200px; max-height: 100px; border: 1px solid #dee2e6; border-radius: 4px;">
                            @if($apl02->tanggal_tanda_tangan_asesi)
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Ditandatangani: {{ $apl02->tanggal_tanda_tangan_asesi->format('d M Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="text-muted">Belum ditandatangani</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tanda Tangan Asesor</td>
                <td>
                    @if ($apl02->tanda_tangan_asesor)
                        <div class="signature-display">
                            <img src="{{ $apl02->getAsesorSignatureUrl() }}" alt="Tanda Tangan Asesor"
                                style="max-width: 200px; max-height: 100px; border: 1px solid #dee2e6; border-radius: 4px;">
                            @if($apl02->asesor)
                                <p class="small fw-semibold mt-2 mb-0">{{ $apl02->asesor->name }}</p>
                            @endif
                            @if($apl02->tanggal_tanda_tangan_asesor)
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Ditandatangani: {{ $apl02->tanggal_tanda_tangan_asesor->format('d M Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="text-muted">Belum ditandatangani asesor</span>
                    @endif
                </td>
            </tr>
        </table>

    </div>
</div>

<style>
    :root {
        --primary-color: #4f46e5;
        --primary-light: #818cf8;
        --gray-900: #111827;
        --gray-700: #374151;
        --gray-100: #f3f4f6;
    }

    .review-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .review-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .review-body {
        padding: 1.5rem;
    }

    .unit-card {
        border: 1px solid var(--gray-100);
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    .unit-card-header {
        background: #f9fafb;
        border-bottom: 1px solid var(--gray-100);
    }

    .accordion-button-custom {
        width: 100%;
        padding: 1rem 1.25rem;
        background: transparent;
        border: none;
        text-align: left;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .accordion-button-custom:hover {
        background: #f3f4f6;
    }

    .unit-code-badge {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .unit-title {
        flex-grow: 1;
        overflow: hidden;
    }

    .unit-title-text {
        display: block;
        font-weight: 600;
        color: var(--gray-900);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-kompeten {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-belum-kompeten {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-belum-dinilai {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-bukti {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .unit-card-body {
        padding: 1.5rem;
    }

    .elemen-card {
        background: #f9fafb;
        border: 1px solid var(--gray-100);
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }

    .elemen-card-body {
        padding: 1rem;
    }

    .portfolio-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border: 1px solid var(--gray-100);
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .portfolio-icon {
        color: var(--primary-color);
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .portfolio-name {
        flex-grow: 1;
        font-size: 0.9rem;
        color: var(--gray-700);
    }

    .list-group-item {
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-100);
        background: white;
    }

    .list-group-item:first-child {
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }

    .list-group-item:last-child {
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .info-table td:first-child {
        font-weight: 600;
        color: var(--gray-700);
        width: 200px;
    }

    .info-table td:last-child {
        color: var(--gray-900);
    }

    .section-header-small {
        margin-bottom: 1rem;
    }

    .section-number-small {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 0.75rem;
    }

    .signature-display {
        display: inline-block;
    }
</style>