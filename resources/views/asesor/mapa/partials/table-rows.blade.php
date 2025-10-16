{{-- resources/views/asesor/mapa/partials/table-rows.blade.php --}}
@forelse ($delegasiList as $index => $delegasi)
    <tr>
        <td class="text-center">{{ $delegasiList->firstItem() + $index }}</td>
        <td>
            <div class="asesi-info">
                <div class="asesi-details">
                    <div class="asesi-name">{{ $delegasi->asesi->name }}</div>
                    <div class="asesi-email">{{ $delegasi->asesi->email }}</div>
                </div>
            </div>
        </td>
        <td>
            <div class="fw-semibold" style="color: var(--gray-900);">
                {{ $delegasi->certificationScheme->nama }}
            </div>
        </td>
        <td>
            <div class="small">
                <i class="bi bi-calendar3 me-1 text-primary"></i>
                {{ $delegasi->tanggal_pelaksanaan_asesmen ? $delegasi->tanggal_pelaksanaan_asesmen->format('d M Y') : '-' }}
            </div>
        </td>
        <td>
            @if ($delegasi->mapa)
                @if ($delegasi->mapa->status === 'draft')
                    <span class="status-badge status-draft">
                        <i class="bi bi-file-earmark me-1"></i>Draft
                    </span>
                @elseif($delegasi->mapa->status === 'submitted')
                    <span class="status-badge status-submitted">
                        <i class="bi bi-send me-1"></i>Dikirim
                    </span>
                @elseif($delegasi->mapa->status === 'approved')
                    <span class="status-badge"
                        style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #FCD34D;">
                        <i class="bi bi-exclamation-triangle me-1"></i>Perlu Validasi
                    </span>
                @elseif($delegasi->mapa->status === 'rejected')
                    <span class="status-badge status-rejected">
                        <i class="bi bi-x-circle-fill me-1"></i>Ditolak
                    </span>
                @elseif($delegasi->mapa->status === 'validated')
                    <span class="status-badge status-approved">
                        <i class="bi bi-check-all me-1"></i>Tervalidasi
                    </span>
                @endif
            @else
                <span class="status-badge status-draft">
                    <i class="bi bi-dash-circle me-1"></i>Belum Dibuat
                </span>
            @endif
        </td>
        <td>
            <div class="d-flex flex-nowrap gap-1" style="min-width: 200px; overflow-x: auto;">
                @if ($delegasi->mapa)
                    {{-- Check if consultation is completed --}}
                    @php
                        $isConsultationDone = $delegasi->formKerahasiaan && $delegasi->formKerahasiaan->status === 'completed';
                    @endphp
                    
                    @if ($isConsultationDone)
                        {{-- ✅ CONSULTATION COMPLETED - ONLY SHOW THIS BUTTON --}}
                        <a href="{{ route('asesor.form-kerahasiaan.view', $delegasi->formKerahasiaan->id) }}"
                            class="btn btn-sm btn-outline-success"
                            title="Lihat Form Kerahasiaan">
                            <i class="bi bi-check-circle-fill me-1"></i> Konsultasi Selesai
                        </a>
                    @else
                        {{-- Show normal buttons if consultation not done yet --}}
                        <a href="{{ route('asesor.mapa.view', $delegasi->mapa->id) }}"
                            class="btn btn-sm btn-outline-info" title="Lihat MAPA">
                            <i class="bi bi-eye"></i> Lihat MAPA
                        </a>

                        {{-- STEP 1: MAPA DRAFT/SUBMITTED/REJECTED - Can Edit --}}
                        @if (in_array($delegasi->mapa->status, ['draft', 'submitted', 'rejected']))
                            @if ($delegasi->mapa->canBeEdited())
                                <a href="{{ route('asesor.mapa.edit', $delegasi->mapa->id) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Edit MAPA">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                        @endif

                        {{-- STEP 2: MAPA APPROVED - Need Assessor Validation --}}
                        @if ($delegasi->mapa->status === 'approved')
                            <a href="{{ route('admin.spt-signatures.preview', ['id' => $delegasi->id, 'type' => 'asesor']) }}"
                                class="btn btn-sm btn-outline-info" title="Lihat SPT Asesor" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> SPT
                            </a>
                            <a href="{{ route('asesor.mapa.validate', $delegasi->mapa->id) }}"
                                class="btn btn-sm btn-outline-warning" title="Validasi MAPA">
                                <i class="bi bi-pen-fill me-1"></i> Validasi MAPA
                            </a>
                        @endif

                        {{-- STEP 3: MAPA VALIDATED - Create AK.07 --}}
                        @if ($delegasi->mapa->status === 'validated')
                            @if (!$delegasi->mapa->ak07)
                                {{-- No AK.07 yet - Show Create AK.07 button --}}
                                <a href="{{ route('asesor.ak07.create', $delegasi->mapa->id) }}"
                                    class="btn btn-sm btn-outline-success" title="Buat FR.AK.07">
                                    <i class="bi bi-plus-circle-fill me-1"></i> Buat AK.07
                                </a>
                            @else
                                {{-- AK.07 exists - Check its status --}}
                                
                                {{-- STEP 4: AK.07 Waiting for Assessee Signature --}}
                                @if ($delegasi->mapa->ak07->status === 'waiting_asesi')
                                    <a href="{{ route('asesor.ak07.view', $delegasi->mapa->ak07->id) }}"
                                        class="btn btn-sm btn-outline-warning" title="Lihat AK.07">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Tanda Tangan Asesi
                                    </a>
                                @endif

                                {{-- STEP 5: AK.07 Completed - Create Final Recommendation --}}
                                @if ($delegasi->mapa->ak07->status === 'completed')
                                    @if ($delegasi->mapa->ak07->final_recommendation === null)
                                        {{-- No Final Recommendation yet --}}
                                        <a href="{{ route('asesor.ak07.final-recommendation', $delegasi->mapa->ak07->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Buat Rekomendasi Akhir">
                                            <i class="bi bi-clipboard-check-fill me-1"></i> Rekomendasi Akhir
                                        </a>
                                    @else
                                        {{-- Final Recommendation exists --}}
                                        
                                        @if ($delegasi->mapa->ak07->final_signed_at)
                                            <a href="{{ route('asesor.form-banding.show', $delegasi->id) }}"
                                                class="btn btn-sm btn-outline-primary" 
                                                title="Lihat Form Banding"
                                                target="_blank">
                                                <i class="bi bi-file-earmark-text me-1"></i> Form Banding
                                            </a>
                                        @endif
                                        
                                        @if ($delegasi->mapa->ak07->final_recommendation === 'continue')
                                            {{-- STEP 6: Continue to Confidentiality Form --}}
                                            <a href="{{ route('asesor.ak07.view', $delegasi->mapa->ak07->id) }}"
                                                class="btn btn-sm btn-outline-success" title="Lihat AK.07">
                                                <i class="bi bi-eye"></i> Lihat AK.07
                                            </a>
                                            
                                            @if (!$delegasi->formKerahasiaan)
                                                {{-- No Confidentiality Form yet --}}
                                                <a href="{{ route('asesor.form-kerahasiaan.create', $delegasi->id) }}"
                                                    class="btn btn-sm btn-outline-success" 
                                                    title="Buat Form Kerahasiaan">
                                                    <i class="bi bi-shield-lock-fill me-1"></i> Buat Form
                                                </a>
                                            @else
                                                {{-- Confidentiality Form exists but not completed --}}
                                                @if ($delegasi->formKerahasiaan->status === 'waiting_asesi')
                                                    {{-- STEP 7: Waiting for Assessee Signature on Form --}}
                                                    <a href="{{ route('asesor.form-kerahasiaan.view', $delegasi->formKerahasiaan->id) }}"
                                                        class="btn btn-sm btn-outline-warning"
                                                        title="Lihat Form Kerahasiaan">
                                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Tanda Tangan Form
                                                    </a>
                                                @endif
                                            @endif
                                        @elseif($delegasi->mapa->ak07->final_recommendation === 'not_continue')
                                            {{-- Not Continue (Reschedule) --}}
                                            <a href="{{ route('asesor.ak07.view', $delegasi->mapa->ak07->id) }}"
                                                class="btn btn-sm btn-outline-danger" title="Lihat AK.07 - Jadwal Ulang">
                                                <i class="bi bi-arrow-repeat me-1"></i> Jadwal Ulang
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif

                @else
                    {{-- No MAPA yet - STEP 0 --}}
                    <a href="{{ route('asesor.mapa.create', $delegasi->id) }}" 
                        class="btn btn-sm btn-outline-primary" title="Buat MAPA">
                        <i class="bi bi-plus-circle-fill me-1"></i> Buat MAPA
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: var(--gray-700);">Tidak Ada Data</h5>
                <p class="text-muted mb-3">
                    Tidak ada data yang sesuai dengan filter yang dipilih
                </p>
            </div>
        </td>
    </tr>
@endforelse