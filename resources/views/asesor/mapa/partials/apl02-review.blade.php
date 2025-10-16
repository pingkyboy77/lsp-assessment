<div class="review-section">
    <div class="review-header">
        <i class="bi bi-file-earmark-check me-2"></i>APL 02 - Portfolio & Assessment
    </div>
    <div class="review-body">

        <!-- Loop Per Kelompok Kerja -->
        @foreach ($kelompokKerjas as $kelompok)
            <div class="mb-4">
                <div class="kelompok-card">
                    <div class="kelompok-header">
                        <i class="bi bi-folder-fill me-2"></i>{{ $kelompok->nama_kelompok }}
                    </div>
                    <div class="p-0">
                        <!-- Accordion per Unit -->
                        <div class="accordion accordion-flush" id="accordion{{ $kelompok->id }}">
                            @foreach ($kelompok->unitKompetensis as $unitIndex => $unit)
                                @php
                                    $portfolioFiles = $unit->portfolioFiles->where('is_active', true);
                                    $availablePortfolios = $portfolioFiles->filter(function ($portfolio) use ($delegasi) {
                                        $submission = $delegasi->apl02->evidenceSubmissions
                                            ->where('portfolio_file_id', $portfolio->id)
                                            ->first();
                                        return $submission && $submission->is_submitted && $submission->file_path;
                                    });

                                    $totalElemen = $unit->elemenKompetensis->count();
                                    $kompeten = 0;
                                    foreach ($unit->elemenKompetensis as $el) {
                                        $ass = $delegasi->apl02->elementAssessments
                                            ->where('elemen_kompetensi_id', $el->id)
                                            ->first();
                                        if ($ass && $ass->assessment_result === 'kompeten') {
                                            $kompeten++;
                                        }
                                    }
                                @endphp

                                <div class="accordion-item border-0 border-top">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $unit->id }}">
                                            <div class="d-flex align-items-center w-100 gap-3">
                                                <span class="unit-code-badge">{{ $unit->kode_unit }}</span>
                                                <div class="unit-title">
                                                    <span class="unit-title-text" title="{{ $unit->judul_unit }}">
                                                        {{ $unit->judul_unit }}
                                                    </span>
                                                </div>
                                                <div class="d-flex gap-2 align-items-center flex-shrink-0">
                                                    <span class="status-badge {{ $kompeten == $totalElemen ? 'status-kompeten' : 'status-belum-kompeten' }}">
                                                        {{ $kompeten }}/{{ $totalElemen }}
                                                    </span>
                                                    @if ($availablePortfolios->count() > 0)
                                                        <span class="badge-bukti">
                                                            <i class="bi bi-files me-1"></i>{{ $availablePortfolios->count() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $unit->id }}"
                                        class="accordion-collapse collapse"
                                        data-bs-parent="#accordion{{ $kelompok->id }}">
                                        <div class="accordion-body p-4">

                                            <!-- Elemen Kompetensi -->
                                            <div class="mb-4">
                                                <h6 class="fw-bold mb-3" style="color: var(--primary-color);">
                                                    <i class="bi bi-list-check me-2"></i>Elemen Kompetensi
                                                </h6>
                                                @foreach ($unit->elemenKompetensis as $elemen)
                                                    @php
                                                        $assessment = $delegasi->apl02->elementAssessments
                                                            ->where('elemen_kompetensi_id', $elemen->id)
                                                            ->first();
                                                    @endphp
                                                    <div class="elemen-card">
                                                        <div class="elemen-card-body">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold" style="color: var(--gray-900);">
                                                                        {{ $elemen->judul_elemen }}
                                                                    </div>
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
                                                                    $submission = $delegasi->apl02->evidenceSubmissions
                                                                        ->where('portfolio_file_id', $portfolio->id)
                                                                        ->first();
                                                                @endphp

                                                                <div class="portfolio-item">
                                                                    <i class="bi bi-file-earmark-text portfolio-icon"></i>
                                                                    <span class="portfolio-name">{{ $portfolio->document_name }}</span>
                                                                    <button type="button"
                                                                        onclick="viewDocument('{{ Storage::url($submission->file_path) }}', '{{ $portfolio->document_name }}')"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-eye"></i> Lihat
                                                                    </button>
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
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>