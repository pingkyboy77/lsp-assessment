{{-- resources/views/asesor/mapa/partials/mapa-options.blade.php --}}
@php
    $totalKelompok = $kelompokKerjas->count();
    
    // Generate MAPA options
    $mapaOptions = [];
    for ($p = 0; $p <= $totalKelompok; $p++) {
        $tidakLangsung = $p;
        $langsung = $totalKelompok - $p;
        
        // Build description
        if ($p == 0) {
            $description = 'Semua kelompok menggunakan metode <strong>Langsung</strong> (Uji Tertulis & DIT)';
            $method = 'Langsung';
        } elseif ($p == $totalKelompok) {
            $description = 'Semua kelompok menggunakan metode <strong>Tidak Langsung</strong> (Portofolio & Wawancara)';
            $method = 'Tidak Langsung';
        } else {
            $parts = [];
            
            // Tidak Langsung
            if ($p == 1) {
                $parts[] = '<strong>Kelompok 1</strong>: Tidak Langsung (Portofolio & Wawancara)';
            } else {
                $parts[] = '<strong>Kelompok 1-' . $p . '</strong>: Tidak Langsung (Portofolio & Wawancara)';
            }
            
            // Langsung
            $start = $p + 1;
            $end = $totalKelompok;
            if ($start == $end) {
                $parts[] = '<strong>Kelompok ' . $start . '</strong>: Langsung (Tertulis & DIT)';
            } else {
                $parts[] = '<strong>Kelompok ' . $start . '-' . $end . '</strong>: Langsung (Tertulis & DIT)';
            }
            
            $description = implode(' + ', $parts);
            $method = 'Kombinasi';
        }
        
        $mapaOptions[] = [
            'p_level' => $p,
            'title' => 'MAPA P' . $p,
            'description' => $description,
            'method' => $method,
            'tidak_langsung' => $tidakLangsung,
            'langsung' => $langsung,
        ];
    }
    
    // Determine if we're in edit mode (validasi page with existing MAPA)
    $isEditMode = isset($currentPLevel) && $currentPLevel !== null;
    $readOnly = isset($readOnly) ? $readOnly : false;
@endphp

<div class="mb-4">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-clipboard-check me-2"></i>{{ $isEditMode ? 'MAPA yang Dipilih' : 'Pilih Level MAPA' }}
        @if(!$readOnly)
            <span class="text-danger">*</span>
        @endif
        @if($isEditMode && !$readOnly)
            <small class="text-muted ms-2" style="font-size: 0.85rem;">(dapat diganti jika diperlukan)</small>
        @endif
    </h5>

    @if($isEditMode && !$readOnly)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Mode Edit:</strong> MAPA saat ini adalah <strong>P{{ $currentPLevel }}</strong>. Anda dapat mengubahnya dengan memilih opsi lain di bawah.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @foreach ($mapaOptions as $option)
        @php
            $isSelected = $isEditMode && $currentPLevel === $option['p_level'];
        @endphp
        
        <div class="mapa-option-card {{ $isSelected ? 'selected' : '' }} {{ $readOnly ? 'readonly' : '' }}" 
             @if(!$readOnly) onclick="selectMapa({{ $option['p_level'] }}, this)" @endif
             style="{{ $readOnly ? 'cursor: default;' : '' }}">
            <div class="d-flex align-items-start">
                @if(!$readOnly)
                    <input type="radio" name="p_level" value="{{ $option['p_level'] }}"
                        class="mapa-option-radio me-3" id="mapa_p{{ $option['p_level'] }}"
                        {{ $isSelected ? 'checked' : '' }}
                        style="width: 20px; height: 20px; cursor: pointer;">
                @else
                    @if($isSelected)
                        <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 1.5rem;"></i>
                    @endif
                @endif
                
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="mapa-title">{{ $option['title'] }}</div>
                        <span class="mapa-badge {{ $option['method'] == 'Langsung' ? 'mapa-badge-langsung' : ($option['method'] == 'Tidak Langsung' ? 'mapa-badge-tidak-langsung' : 'mapa-badge-kombinasi') }}">
                            {{ $option['method'] }}
                        </span>
                    </div>
                    <div class="mapa-description mb-3">{!! $option['description'] !!}</div>

                    <!-- Detail Per Kelompok -->
                    <div class="row g-2">
                        @foreach ($kelompokKerjas as $index => $kelompok)
                            @php
                                $isNotLangsung = $index < $option['p_level'];
                                $statusText = $isNotLangsung
                                    ? 'Tidak Langsung (Portofolio & Wawancara)'
                                    : 'Langsung (Tertulis & DIT)';
                                $pNumber = $isNotLangsung ? ($index + 1) : 0;
                            @endphp
                            <div class="col-md-6">
                                <div class="kelompok-assignment">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-folder me-2" style="color: var(--primary-color);"></i>
                                        <div class="flex-grow-1">
                                            <small class="fw-semibold d-block" style="color: var(--gray-900);">{{ $kelompok->nama_kelompok }}</small>
                                            <div class="mt-1">
                                                <span class="p-level-badge {{ $pNumber == 0 ? 'p-level-0' : 'p-level-active' }}">
                                                    P{{ $pNumber }}
                                                </span>
                                                <small class="text-muted ms-2">{{ $statusText }}</small>
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

@if(!$readOnly)
    @push('scripts')
    <script>
        function selectMapa(pLevel, element) {
            // Remove selected class from all cards
            document.querySelectorAll('.mapa-option-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            element.classList.add('selected');
            
            // Check the radio button
            const radio = element.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
            
            console.log('MAPA Selected: P' + pLevel);
            
            // Optional: Show confirmation for edit mode
            @if($isEditMode)
                // You can add additional logic here if needed
                console.log('MAPA changed from P{{ $currentPLevel }} to P' + pLevel);
            @endif
        }
    </script>
    @endpush
@endif

<style>
    .mapa-option-card.readonly {
        opacity: 0.7;
    }
    
    .mapa-option-card.readonly:hover {
        transform: none;
        box-shadow: none;
    }
</style>