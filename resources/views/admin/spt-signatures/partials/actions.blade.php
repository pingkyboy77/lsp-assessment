{{-- File: resources/views/admin/spt-signatures/partials/actions.blade.php --}}

<div class="btn-group" role="group">
    @if ($spt && $spt->is_signed)
        {{-- Sudah ditandatangani - tampilkan tombol download dan detail --}}
        @php
            // Database value: "Mandiri" dan "Sewaktu" (dengan huruf kapital di awal)
            $isMandiri = $delegasi->apl01 && strtolower($delegasi->apl01->tuk) === 'mandiri';
        @endphp
        
        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="bi bi-download me-1"></i>Download
        </button>
        <ul class="dropdown-menu">
            {{-- Hanya tampilkan SPT Verifikator jika BUKAN Mandiri --}}
            @if (!$isMandiri && $spt->spt_verifikator_file)
                <li>
                    <a class="dropdown-item" href="javascript:void(0)"
                        onclick="downloadSPT({{ $delegasi->id }}, 'verifikator')">
                        <i class="bi bi-file-earmark-pdf text-primary me-2"></i>SPT Verifikator
                    </a>
                </li>
            @endif
            
            {{-- Observer selalu ada --}}
            @if ($spt->spt_observer_file)
                <li>
                    <a class="dropdown-item" href="javascript:void(0)"
                        onclick="downloadSPT({{ $delegasi->id }}, 'observer')">
                        <i class="bi bi-file-earmark-pdf text-info me-2"></i>SPT Observer
                    </a>
                </li>
            @endif
            
            {{-- Asesor selalu ada --}}
            @if ($spt->spt_asesor_file)
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" 
                        onclick="downloadSPT({{ $delegasi->id }}, 'asesor')">
                        <i class="bi bi-file-earmark-pdf text-success me-2"></i>SPT Asesor
                    </a>
                </li>
            @endif
            
            {{-- Jika tidak ada file yang bisa didownload --}}
            @if ((!$isMandiri && !$spt->spt_verifikator_file) || !$spt->spt_observer_file || !$spt->spt_asesor_file)
                <li><span class="dropdown-item text-muted">Tidak ada file tersedia</span></li>
            @endif
        </ul>

        <a href="{{ route('admin.spt-signatures.show', $delegasi->id) }}" class="btn btn-sm btn-info"
            title="Lihat Detail SPT">
            <i class="bi bi-eye"></i>
        </a>
    @else
        {{-- Belum ditandatangani - tampilkan tombol sign --}}
        <div class="d-flex justify-content-start gap-2">
            <button type="button" class="btn btn-sm btn-outline-success d-flex align-items-center"
                onclick="signSingle({{ $delegasi->id }})" title="Tanda Tangan SPT">
                <i class="bi bi-pen-fill me-1"></i>
            </button>

            <a href="{{ route('admin.spt-signatures.show', $delegasi->id) }}"
                class="btn btn-sm btn-outline-info d-flex align-items-center" title="Lihat Detail Delegasi">
                <i class="bi bi-eye me-1"></i>
            </a>
        </div>
    @endif
</div>