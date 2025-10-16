@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="main-card">
        <div class="card-header-custom d-flex justify-content-center align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-center">{{ $pageTitle }}
                </h3>
                <p class="text-light mb-0 text-center">{{ $apl01->certificationScheme->nama ?? 'Skema Sertifikasi' }}</p>
            </div>
            
        </div>

        <!-- Success Alert for New TUK Request -->
        @if(!$isEdit)
        <div class="alert alert-info mb-4 m-3">
            <h5><i class="bi bi-info-circle me-2"></i>Permohonan TUK Baru</h5>
            <p class="mb-0">Anda akan membuat permohonan TUK baru untuk APL 01 yang sudah disetujui. Pastikan semua data yang Anda isi benar dan lengkap.</p>
        </div>
        @endif

        <!-- APL01 Info -->
        <div class="d-flex justify-content-end align-items-center m-4">
            <a href="{{ route('asesi.inbox.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Inbox
            </a>
            
        </div>
        
        <div class="row mb-4 m-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Informasi APL 01</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nomor APL 01:</strong><br>
                                {{ $apl01->nomor_apl_01 }}
                            </div>
                            <div class="col-md-3">
                                <strong>Nama Peserta:</strong><br>
                                {{ $apl01->nama_lengkap }}
                            </div>
                            <div class="col-md-3">
                                <strong>Skema Sertifikasi:</strong><br>
                                {{ $apl01->certificationScheme->nama ?? '-' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Status APL 01:</strong><br>
                                <span class="badge bg-success">{{ strtoupper($apl01->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TUK Request Form -->
        <div class="row m-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>{{ $isEdit ? 'Edit ' : 'Buat ' }}Permohonan TUK Sewaktu
                            @if($tukRequest && $tukRequest->kode_tuk)
                                <span class="badge bg-success ms-2">{{ $tukRequest->kode_tuk }}</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="tukRequestForm">
                            @csrf
                            
                            <!-- Alert for existing request -->
                            @if($tukRequest && $tukRequest->isComplete())
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Form TUK sudah lengkap dan menunggu rekomendasi admin.
                                    @if($tukRequest->hasRecommendation())
                                        <br><strong>Status:</strong> Sudah direkomendasi pada {{ $tukRequest->formatted_recommended_at }}
                                    @endif
                                </div>
                            @endif

                            <!-- Tanggal Assessment -->
                            <div class="mb-3">
                                <label for="tanggal_assessment" class="form-label">
                                    <strong>Tanggal Pelaksanaan Assessment</strong> 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_assessment" 
                                       name="tanggal_assessment"
                                       value="{{ $tukRequest ? $tukRequest->tanggal_assessment?->format('Y-m-d') : '' }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       required>
                                <div class="form-text">Pilih tanggal minimal H+1 dari hari ini</div>
                            </div>

                            <!-- Lokasi Assessment -->
                            <div class="mb-3">
                                <label for="lokasi_assessment" class="form-label">
                                    <strong>Lokasi TUK Sewaktu</strong> 
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="lokasi_assessment" 
                                          name="lokasi_assessment"
                                          rows="3" 
                                          placeholder="Masukkan alamat lengkap lokasi TUK sewaktu..."
                                          required>{{ $tukRequest ? $tukRequest->lokasi_assessment : '' }}</textarea>
                                <div class="form-text">Tulis alamat lengkap tempat pelaksanaan assessment</div>
                            </div>

                            <!-- Persyaratan TUK (Hardcoded) -->
                            <div class="mb-4">
                                <h4 class="text-black">Persyaratan TUK Sewaktu</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">No</th>
                                                <th>Ruang dan Peralatan</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>1</td><td>Luas ruangan memadai dan tidak ditempat umum</td><td>untuk 1 orang</td></tr>
                                            <tr><td>2</td><td>Meja dan kursi</td><td>1 unit</td></tr>
                                            <tr><td>3</td><td>Pencahayaan memadai</td><td>-</td></tr>
                                            <tr><td>4</td><td>Notebook / PC yang dilengkapi kamera, tidak menggunakan Ipad, tablet</td><td>1 unit dengan spec min RAM 4 GB</td></tr>
                                            <tr><td>5</td><td>Software Video Conference</td><td>Zoom</td></tr>
                                            <tr><td>6</td><td>Software Pengolah Kata, Worksheet, Presentasi</td><td>MS Office/sejenisi</td></tr>
                                            <tr><td>7</td><td>Colokan/socket Listrik</td><td>-</td></tr>
                                            <tr><td>8</td><td>Handphone</td><td>Android/IOS</td></tr>
                                            <tr><td>9</td><td>Jaringan internet utama</td><td>-</td></tr>
                                            <tr><td>10</td><td>Jaringan internet cadangan</td><td>-</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pernyataan -->
                            <div class="mb-4">
                                <h6 class="text-black">Pernyataan</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-2">Dengan ini menyatakan untuk:</p>
                                    <ol class="mb-0">
                                        <li><strong>Memenuhi persyaratan TUK Sewaktu LSP-PM</strong></li>
                                        <li><strong>Bersedia mematuhi dan mengikuti persyaratan dan prosedur Verifikasi TUK Sewaktu LSP-PM</strong></li>
                                        <li><strong>Tidak akan memberikan imbalan finansial kepada LSP-PM sehubungan dengan pelaksanaan verifikasi</strong></li>
                                        <li><strong>Bersedia melengkapi semua informasi yang dibutuhkan</strong></li>
                                    </ol>
                                </div>
                            </div>

                            <!-- Signature -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <strong>Tanda Tangan Peserta</strong> 
                                    <span class="text-danger">*</span>
                                </label>
                                
                                @if($tukRequest && $tukRequest->hasSignature())
                                    <div class="mb-3">
                                        <div class="alert alert-success">
                                            <i class="bi bi-check-circle me-2"></i>Tanda tangan sudah tersimpan
                                        </div>
                                        {{-- <img src="{{ asset($tukRequest->getSignatureUrl()) }}" 
                                             alt="Tanda Tangan" 
                                             class="img-fluid border" 
                                             style="max-height: 150px;"> --}}

                                             <img src="{{ Storage::url($tukRequest->tanda_tangan_peserta_path) }}"
                                            alt="Tanda tangan asesi" class="signature-display">
                                        <br>
                                        <small class="text-muted">Klik tombol di bawah untuk mengubah tanda tangan</small>
                                    </div>
                                @endif

                                <div id="signaturePad" class="border rounded p-3 bg-light" style="display: {{ $tukRequest && $tukRequest->hasSignature() ? 'none' : 'block' }};">
                                    <canvas id="signature-canvas" width="500" height="200" class="border bg-white rounded"></canvas>
                                    <div class="mt-2">
                                        <button type="button" id="clearSignature" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-eraser me-1"></i>Hapus
                                        </button>
                                        <span class="text-muted ms-3">Tanda tangan di area di atas</span>
                                    </div>
                                </div>

                                @if($tukRequest && $tukRequest->hasSignature())
                                    <button type="button" id="changeSignature" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil me-1"></i>Ubah Tanda Tangan
                                    </button>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>
                                    {{ $isEdit ? 'Update Permohonan TUK' : 'Simpan Permohonan TUK' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            {{-- <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($tukRequest && $tukRequest->kode_tuk)
                            <div class="mb-3">
                                <strong>Kode TUK:</strong><br>
                                <code>{{ $tukRequest->kode_tuk }}</code>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Mode:</strong><br>
                            <span class="badge bg-{{ $isEdit ? 'warning' : 'primary' }}">
                                {{ $isEdit ? 'Edit' : 'Create' }}
                            </span>
                        </div>

                        @if($tukRequest && $tukRequest->hasRecommendation())
                            <div class="alert alert-info">
                                <h6><i class="bi bi-clock me-2"></i>Rekomendasi Admin</h6>
                                <div class="mb-2">
                                    <strong>Tanggal:</strong> {{ $tukRequest->formatted_tanggal_assessment }}<br>
                                    <strong>Jam:</strong> {{ $tukRequest->formatted_jam_mulai }}
                                </div>
                                @if($tukRequest->catatan_rekomendasi)
                                    <div class="mb-2">
                                        <strong>Catatan:</strong><br>
                                        <small>{{ $tukRequest->catatan_rekomendasi }}</small>
                                    </div>
                                @endif
                                <small class="text-muted">
                                    Direkomendasi: {{ $tukRequest->formatted_recommended_at }}<br>
                                    Oleh: {{ $tukRequest->recommendedBy?->name }}
                                </small>
                            </div>
                        @endif

                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Perhatian</h6>
                            <ul class="mb-0 small">
                                <li>Pastikan semua data yang diisi benar</li>
                                <li>Tanda tangan diperlukan untuk validitas form</li>
                                <li>Form harus lengkap sebelum dapat diproses admin</li>
                                <li>Setelah disubmit, admin akan memberikan rekomendasi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
#signature-canvas {
    cursor: crosshair;
}

.card-header-custom {
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
}

.main-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
    padding: 0;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
}

.table td {
    font-size: 0.85rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize signature pad
    const canvas = document.getElementById('signature-canvas');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 1)',
        penColor: 'rgb(0, 0, 0)'
    });

    // Clear signature button
    $('#clearSignature').on('click', function() {
        signaturePad.clear();
    });

    // Change signature button (for existing signatures)
    $('#changeSignature').on('click', function() {
        $('#signaturePad').show();
        $(this).hide();
    });

    // Form submission
    $('#tukRequestForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        // Validate signature
        if (signaturePad.isEmpty() && !{{ $tukRequest && $tukRequest->hasSignature() ? 'true' : 'false' }}) {
            showToast('error', 'Harap buat tanda tangan terlebih dahulu');
            return;
        }

        // Prepare form data
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            tanggal_assessment: $('#tanggal_assessment').val(),
            lokasi_assessment: $('#lokasi_assessment').val(),
            tanda_tangan_peserta: signaturePad.isEmpty() ? null : signaturePad.toDataURL()
        };

        // Validate required fields
        if (!formData.tanggal_assessment || !formData.lokasi_assessment) {
            showToast('error', 'Harap lengkapi semua field yang required');
            return;
        }

        // Show loading
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

        // Submit via AJAX
        $.ajax({
            url: '{{ route("asesi.tuk.store", $apl01->id) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    
                    // Reload page after short delay to show updated data
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', response.error || 'Gagal menyimpan form TUK');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menyimpan form TUK';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showToast('error', errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Toast notification function
    function showToast(type, message) {
        const colors = {
            success: 'bg-success',
            error: 'bg-danger', 
            info: 'bg-info',
            warning: 'bg-warning'
        };

        const icons = {
            success: 'bi-check-circle',
            error: 'bi-exclamation-triangle',
            info: 'bi-info-circle',
            warning: 'bi-exclamation-triangle'
        };

        const toastHtml = `
            <div class="toast align-items-center text-white ${colors[type]}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icons[type]} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        container.insertAdjacentHTML('beforeend', toastHtml);
        const toast = new bootstrap.Toast(container.lastElementChild);
        toast.show();
    }
});
</script>
@endpush