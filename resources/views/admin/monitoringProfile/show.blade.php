@extends('layouts.admin')

@section('title', 'Detail Profil Pengguna')

@section('content')

    <!-- Navigation Buttons -->


    <!-- Paper Container -->
    <div class="paper-container">
        <div class="paper-content">
            <!-- Paper Header -->
            <div class="paper-header mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h1 class="paper-title mb-1">Detail Profil Pengguna</h1>

                    </div>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <a href="{{ route('admin.monitoring-profile.edit', $profile->id) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil-square me-1"></i>Edit Profil
                        </a>
                        <a href="{{ route('admin.monitoring-profile.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>


            <!-- Success Alert -->
            @if (session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Alert -->
            @if (session('error'))
                <div class="alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Profile Completion Status -->
            @php
                // Daftar field & label
                $fields = [
                    'nama_lengkap' => 'Nama Lengkap',
                    'nik' => 'NIK',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'jenis_kelamin' => 'Jenis Kelamin',
                    'kebangsaan' => 'Kebangsaan',
                    'no_hp' => 'No HP',
                    'email' => 'Email',
                    'alamat_rumah' => 'Alamat Rumah',
                    'kota_rumah' => 'Kota Rumah',
                    'provinsi_rumah' => 'Provinsi Rumah',
                    'kode_pos' => 'Kode Pos',
                    'no_telp_rumah' => 'No Telp Rumah',
                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                    'nama_sekolah_terakhir' => 'Nama Sekolah Terakhir',
                    'jabatan' => 'Jabatan',
                    'nama_tempat_kerja' => 'Nama Tempat Kerja',
                    'kategori_pekerjaan' => 'Kategori Pekerjaan',
                    'nama_jalan_kantor' => 'Nama Jalan Kantor',
                    'kota_kantor' => 'Kota Kantor',
                ];

                $totalFields = count($fields);

                // Hitung field terisi (anggap "0" valid)
                $filledCount = collect($fields)
                    ->filter(function ($label, $key) use ($profile) {
                        $val = $profile->$key ?? null;
                        return !is_null($val) && $val !== '';
                    })
                    ->count();

                // Field kosong
                $emptyFields = collect($fields)->filter(function ($label, $key) use ($profile) {
                    $val = $profile->$key ?? null;
                    return is_null($val) || $val === '';
                });

                $completeness = round(($filledCount / $totalFields) * 100);
            @endphp
            <div class="completion-status mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-graph-up me-2"></i>Kelengkapan Profil
                    </h6>
                    <span
                        class="badge bg-{{ $completeness >= 80 ? 'success' : ($completeness >= 50 ? 'warning' : 'danger') }}">
                        {{ $completeness }}%
                    </span>
                </div>

                <div class="progress w-100" style="height: 8px; border-radius:4px; overflow:hidden;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: {{ $completeness >= 100 ? 100 : $completeness }}%; border-radius:4px;">
                    </div>
                </div>
                <small class="text-muted d-block mt-1">
                    {{ $filledCount }} dari {{ $totalFields }} field telah diisi
                </small>
            </div>


            @if ($emptyFields->isNotEmpty())
                <div class="alert alert-warning mt-2">
                    <strong>Field yang belum diisi:</strong>
                    <ul class="mb-0">
                        @foreach ($emptyFields as $label)
                            <li>{{ $label }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <!-- Data Personal Section -->
            <div class="info-section">
                <h3 class="info-section-title">
                    <i class="bi bi-person-fill me-2"></i>Data Personal
                </h3>

                <div class="info-row">
                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value {{ $profile->nama_lengkap ? '' : 'empty' }}">
                                {{ $profile->nama_lengkap ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">NIK</div>
                            <div class="info-value {{ $profile->nik ? '' : 'empty' }}">
                                {{ $profile->nik ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Tempat Lahir</div>
                            <div class="info-value {{ $profile->tempat_lahir ? '' : 'empty' }}">
                                {{ $profile->tempat_lahir ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Tanggal Lahir</div>
                            <div class="info-value {{ $profile->tanggal_lahir ? '' : 'empty' }}">
                                {{ $profile->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d/m/Y') : 'Belum diisi' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Jenis Kelamin</div>
                            <div class="info-value {{ $profile->jenis_kelamin ? '' : 'empty' }}">
                                @if ($profile->jenis_kelamin)
                                    <span class="badge bg-{{ $profile->jenis_kelamin == 'L' ? 'primary' : 'pink' }}">
                                        {{ $profile->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                @else
                                    Belum diisi
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Kebangsaan</div>
                            <div class="info-value {{ $profile->kebangsaan ? '' : 'empty' }}">
                                {{ $profile->kebangsaan ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">No. HP</div>
                            <div class="info-value {{ $profile->no_hp ? '' : 'empty' }}">
                                @if ($profile->no_hp)
                                    <a href="tel:{{ $profile->no_hp }}" class="text-decoration-none">
                                        <i class="bi bi-telephone me-1"></i>{{ $profile->no_hp }}
                                    </a>
                                @else
                                    Belum diisi
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value {{ $profile->email ? '' : 'empty' }}">
                                @if ($profile->email)
                                    <a href="mailto:{{ $profile->email }}" class="text-decoration-none">
                                        <i class="bi bi-envelope me-1"></i>{{ $profile->email }}
                                    </a>
                                @else
                                    Belum diisi
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Rumah Section -->
            <div class="info-section">
                <h3 class="info-section-title">
                    <i class="bi bi-house-fill me-2"></i>Alamat Rumah
                </h3>

                <div class="info-row">
                    <div class="info-col-full">
                        <div class="info-item">
                            <div class="info-label">Alamat Lengkap</div>
                            <div class="info-value {{ $profile->alamat_rumah ? '' : 'empty' }}">
                                {{ $profile->alamat_rumah ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Kota/Kabupaten</div>
                            <div class="info-value {{ $profile->cityRumah->name ? '' : 'empty' }}">
                                {{ $profile->cityRumah->name ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Provinsi</div>
                            <div class="info-value {{ $profile->provinceRumah->name ? '' : 'empty' }}">
                                {{ $profile->provinceRumah->name ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Kode Pos</div>
                            <div class="info-value {{ $profile->kode_pos ? '' : 'empty' }}">
                                {{ $profile->kode_pos ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Telp. Rumah</div>
                            <div class="info-value {{ $profile->no_telp_rumah ? '' : 'empty' }}">
                                <a href="tel:{{ $profile->no_telp_rumah }}" class="text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i>{{ $profile->no_telp_rumah }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pendidikan Section -->
            <div class="info-section">
                <h3 class="info-section-title">
                    <i class="bi bi-mortarboard-fill me-2"></i>Pendidikan
                </h3>

                <div class="info-row">
                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Pendidikan Terakhir</div>
                            <div class="info-value {{ $profile->pendidikan_terakhir ? '' : 'empty' }}">
                                @if ($profile->pendidikan_terakhir)
                                    <span class="badge bg-info">{{ $profile->pendidikan_terakhir }}</span>
                                @else
                                    Belum diisi
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Nama Sekolah/Universitas</div>
                            <div class="info-value {{ $profile->nama_sekolah_terakhir ? '' : 'empty' }}">
                                {{ $profile->nama_sekolah_terakhir ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pekerjaan Section -->
            <div class="info-section">
                <h3 class="info-section-title">
                    <i class="bi bi-briefcase-fill me-2"></i>Pekerjaan
                </h3>

                <div class="info-row">
                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Jabatan</div>
                            <div class="info-value {{ $profile->jabatan ? '' : 'empty' }}">
                                @if ($profile->jabatan)
                                    <span class="badge bg-secondary">{{ $profile->jabatan }}</span>
                                @else
                                    Belum diisi
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Nama Tempat Kerja</div>
                            <div class="info-value {{ $profile->nama_tempat_kerja ? '' : 'empty' }}">
                                {{ $profile->nama_tempat_kerja ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Kategori Pekerjaan</div>
                            <div class="info-value {{ $profile->kategori_pekerjaan ? '' : 'empty' }}">
                                @if ($profile->kategori_pekerjaan)
                                    <span class="badge bg-warning">{{ $profile->kategori_pekerjaan }}</span>
                                @else
                                    Belum diisi
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Kantor Section -->
            <div class="info-section">
                <h3 class="info-section-title">
                    <i class="bi bi-building me-2"></i>Alamat Kantor
                </h3>

                <div class="info-row">
                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Nama Jalan</div>
                            <div class="info-value {{ $profile->nama_jalan_kantor ? '' : 'empty' }}">
                                {{ $profile->nama_jalan_kantor ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Kota/Kabupaten</div>
                            <div class="info-value {{ $profile->cityKantor->name ? '' : 'empty' }}">
                                {{ $profile->cityKantor->name ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Provinsi</div>
                            <div class="info-value {{ $profile->provinceKantor->name ? '' : 'empty' }}">
                                {{ $profile->provinceKantor->name ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Kode Pos</div>
                            <div class="info-value {{ $profile->kode_pos_kantor ? '' : 'empty' }}">
                                {{ $profile->kode_pos_kantor ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Negara</div>
                            <div class="info-value {{ $profile->negara_kantor ? '' : 'empty' }}">
                                {{ $profile->negara_kantor ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Telp. Kantor</div>
                            <div class="info-value {{ $profile->no_telp_kantor ? '' : 'empty' }}">
                                <a href="tel:{{ $profile->no_telp_kantor }}" class="text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i>{{ $profile->no_telp_kantor }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dokumen Section - Improved with UserDocument Model -->
            <div class="document-section">
                <h3 class="info-section-title">
                    <i class="bi bi-file-earmark-text me-2"></i>Dokumen yang Diupload
                    <span class="badge bg-primary ms-2">{{ $documents->count() }}</span>
                </h3>

                @if ($documents->count() > 0)
                    <!-- Document Statistics -->
                    @php
                        $docStats = [
                            'total' => $documents->count(),
                            'available' => $documents->where('file_exists', true)->count(),
                            'missing' => $documents->where('file_exists', false)->count(),
                            'total_size' => $documents->where('file_exists', true)->sum('file_size'),
                        ];
                    @endphp

                    <!-- Documents Table -->
                    <div class="document-table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="25%">Jenis Dokumen</th>
                                        <th width="15%">Tanggal Upload</th>
                                        <th width="15%">Ukuran File</th>
                                        <th width="15%">Status</th>
                                        <th width="30%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                        <tr class="{{ $document->file_exists ? '' : 'table-warning' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i
                                                        class="bi bi-{{ $document->file_exists ? $document->file_icon : 'exclamation-triangle' }} me-2 fs-5 text-{{ $document->file_exists ? 'primary' : 'warning' }}"></i>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ ucwords(str_replace('_', ' ', $document->document_type)) }}
                                                        </div>
                                                        @if ($document->original_name)
                                                            <small
                                                                class="text-muted">{{ Str::limit($document->original_name, 30) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted">{{ $document->created_at->format('d/m/Y') }}</span>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $document->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if ($document->file_exists && $document->file_size)
                                                    <span class="file-size badge bg-light text-dark">
                                                        {{ $document->file_size_formatted }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($document->file_exists)
                                                    <span class="file-status badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        Tersedia
                                                    </span>
                                                @else
                                                    <span class="file-status badge bg-warning">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                                        File Hilang
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($document->file_exists)
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary btn-view-doc"
                                                            data-url="{{ $document->file_url }}"
                                                            data-type="{{ $document->document_type }}"
                                                            data-bs-toggle="tooltip" title="Lihat Dokumen">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <a href="{{ $document->file_url }}"
                                                            download="{{ $document->original_name }}"
                                                            class="btn btn-sm btn-outline-success"
                                                            data-bs-toggle="tooltip" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @if ($document->isImage())
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-info btn-preview-image"
                                                                data-url="{{ $document->file_url }}"
                                                                data-name="{{ $document->original_name }}"
                                                                data-bs-toggle="tooltip" title="Preview Gambar">
                                                                <i class="bi bi-search"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-warning small">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        File tidak tersedia
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Document Types Summary -->
                    @php
                        $documentTypeGroups = $documents->groupBy('document_type');
                    @endphp
                @else
                    <div class="no-documents text-center py-5">
                        <i class="bi bi-file-earmark-upload display-1 text-muted"></i>
                        <h5 class="mt-3">Belum ada dokumen yang diupload</h5>
                        <p class="text-muted">Pengguna belum mengupload dokumen apapun</p>
                    </div>
                @endif
            </div>

            <!-- Info Footer -->
            <div class="info-footer">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-calendar-plus me-1"></i><strong>Dibuat:</strong>
                            {{ $profile->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        @if ($profile->updated_at != $profile->created_at)
                            <small class="text-muted">
                                <i class="bi bi-pencil-square me-1"></i><strong>Terakhir diperbarui:</strong>
                                {{ $profile->updated_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Preview Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a id="downloadImageBtn" href="" download="" class="btn btn-primary">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .completion-status {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid #dee2e6;
        }

        .stat-card {
            text-align: center;
            padding: 1rem;
            border-radius: 10px;
            color: white;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card i {
            font-size: 1.5rem;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .document-table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .document-types-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }

        .no-documents {
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }

        .info-value a {
            color: #0d6efd;
            transition: color 0.2s;
        }

        .info-value a:hover {
            color: #0a58ca;
        }

        @media print {

            .btn-nav,
            .modal,
            #imagePreviewModal {
                display: none !important;
            }

            .paper-container {
                box-shadow: none !important;
                margin: 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert-success-custom, .alert-danger-custom').fadeOut();
            }, 5000);

            // Print functionality
            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });

            // Document preview functionality
            $('.btn-view-doc').on('click', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const type = $(this).data('type');

                if (!url) {
                    alert('URL dokumen tidak tersedia');
                    return;
                }

                // Open in new window with specific dimensions
                const popup = window.open(url, 'document_preview',
                    'width=900,height=700,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,status=no');

                if (popup) {
                    popup.focus();
                } else {
                    // Fallback if popup blocked
                    alert('Popup diblokir. Dokumen akan dibuka di tab baru.');
                    window.open(url, '_blank');
                }
            });

            // Image preview functionality
            $('.btn-preview-image').on('click', function(e) {
                e.preventDefault();
                const imageUrl = $(this).data('url');
                const imageName = $(this).data('name') || 'Preview Gambar';

                if (!imageUrl) {
                    alert('URL gambar tidak tersedia');
                    return;
                }

                // Set modal content
                $('#previewImage').attr('src', imageUrl).attr('alt', imageName);
                $('#imagePreviewModalLabel').text(imageName);
                $('#downloadImageBtn').attr('href', imageUrl).attr('download', imageName);

                // Show modal
                $('#imagePreviewModal').modal('show');
            });

            // Handle modal image loading errors
            $('#previewImage').on('error', function() {
                $(this).attr('src',
                    'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdhbWJhciB0aWRhayBkYXBhdCBkaW11YXQ8L3RleHQ+PC9zdmc+'
                );
                $('.modal-footer #downloadImageBtn').hide();
            });

            // Reset modal when closed
            $('#imagePreviewModal').on('hidden.bs.modal', function() {
                $('#previewImage').attr('src', '').attr('alt', '');
                $('#imagePreviewModalLabel').text('Preview Gambar');
                $('#downloadImageBtn').attr('href', '#').attr('download', '').show();
            });

            // Download functionality with progress (if supported)
            $(document).on('click', '[data-bs-toggle="tooltip"]', function() {
                const $this = $(this);
                const originalTitle = $this.attr('title');

                if ($this.hasClass('btn-outline-success')) {
                    $this.attr('title', 'Mengunduh...');

                    setTimeout(function() {
                        $this.attr('title', originalTitle);
                    }, 2000);
                }
            });

            // Tooltip initialization for Bootstrap 5
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
                    new bootstrap.Tooltip(tooltipTriggerEl, {
                        delay: {
                            "show": 500,
                            "hide": 100
                        }
                    })
                );
            }

            // File size animation on hover
            $('.file-size').hover(
                function() {
                    $(this).addClass('badge-hover');
                },
                function() {
                    $(this).removeClass('badge-hover');
                }
            );

            // Document table row click handler
            $('.document-table-container tbody tr').on('click', function(e) {
                // Don't trigger if clicking on buttons
                if ($(e.target).closest('button, a').length) {
                    return;
                }

                $(this).toggleClass('table-active');
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl+F for search (if search functionality exists)
                if (e.ctrlKey && e.key === 'f') {
                    const searchInput = $('input[type="search"]');
                    if (searchInput.length) {
                        e.preventDefault();
                        searchInput.focus();
                    }
                }

                // Escape to close modals
                if (e.key === 'Escape') {
                    $('.modal.show').modal('hide');
                }
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));

                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });

            // Progress bar animation
            $('.progress-bar').each(function() {
                $(this).css('width', '0%').animate({
                    width: '100%'
                }, 1000);
            });


            // Lazy loading for document statistics
            function updateDocumentStats() {
                const totalDocs = $('.document-table tbody tr').length;
                const availableDocs = $('.file-status.exists').length;
                const missingDocs = $('.file-status.missing').length;

                // Update stats if elements exist
                $('.stat-card .stat-number').each(function(index) {
                    const $this = $(this);
                    let value = 0;

                    switch (index) {
                        case 0:
                            value = totalDocs;
                            break;
                        case 1:
                            value = availableDocs;
                            break;
                        case 2:
                            value = missingDocs;
                            break;
                    }

                    // Animate number counting
                    $({
                        Counter: 0
                    }).animate({
                        Counter: value
                    }, {
                        duration: 1000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.ceil(this.Counter));
                        }
                    });
                });
            }

            // Initialize stats animation
            updateDocumentStats();

            // Handle missing file warnings
            $('.file-status.missing').closest('tr').addClass('table-warning').each(function() {
                const $row = $(this);
                const docType = $row.find('td:first-child').text();

                // Add warning icon to document type
                $row.find('td:first-child .bi').removeClass('bi-file-earmark').addClass(
                    'bi-exclamation-triangle text-warning');
            });

            // Copy functionality for text fields (if needed)
            $(document).on('dblclick', '.info-value:not(.empty)', function() {
                const text = $(this).text().trim();

                if (text && text !== 'Belum diisi') {
                    // Try to copy to clipboard
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text).then(function() {
                            showToast('Teks disalin ke clipboard', 'success');
                        }).catch(function() {
                            // Fallback for older browsers
                            copyToClipboardFallback(text);
                        });
                    } else {
                        copyToClipboardFallback(text);
                    }
                }
            });

            // Fallback copy function
            function copyToClipboardFallback(text) {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    document.execCommand('copy');
                    showToast('Teks disalin ke clipboard', 'success');
                } catch (err) {
                    showToast('Gagal menyalin teks', 'error');
                }

                document.body.removeChild(textArea);
            }

            // Simple toast notification function
            function showToast(message, type = 'info') {
                const toast = $(`
            <div class="toast-notification toast-${type}" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
                color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
                padding: 10px 15px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 9999;
                opacity: 0;
            ">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
                ${message}
            </div>
        `);

                $('body').append(toast);

                toast.animate({
                    opacity: 1
                }, 300).delay(2000).animate({
                    opacity: 0
                }, 300, function() {
                    toast.remove();
                });
            }

            // Document table sorting (basic)
            $('.table thead th').on('click', function() {
                const $this = $(this);
                const index = $this.index();
                const $tbody = $this.closest('table').find('tbody');
                const rows = $tbody.find('tr').get();

                // Skip if no sortable content
                if (index === 4) return; // Skip actions column

                const isAscending = !$this.hasClass('sort-asc');

                // Clear previous sort indicators
                $('.table thead th').removeClass('sort-asc sort-desc');
                $this.addClass(isAscending ? 'sort-asc' : 'sort-desc');

                rows.sort(function(a, b) {
                    const aVal = $(a).find('td').eq(index).text().trim();
                    const bVal = $(b).find('td').eq(index).text().trim();

                    // Handle dates for column 1
                    if (index === 1) {
                        const aDate = new Date(aVal.split(' ')[0].split('/').reverse().join('-'));
                        const bDate = new Date(bVal.split(' ')[0].split('/').reverse().join('-'));
                        return isAscending ? aDate - bDate : bDate - aDate;
                    }

                    // Handle file sizes for column 2
                    if (index === 2) {
                        const getSizeInBytes = (sizeStr) => {
                            if (sizeStr === '-') return 0;
                            const match = sizeStr.match(/([0-9,.]+)\s*(KB|MB|GB|bytes)?/);
                            if (!match) return 0;

                            const size = parseFloat(match[1].replace(',', '.'));
                            const unit = match[2];

                            switch (unit) {
                                case 'GB':
                                    return size * 1024 * 1024 * 1024;
                                case 'MB':
                                    return size * 1024 * 1024;
                                case 'KB':
                                    return size * 1024;
                                default:
                                    return size;
                            }
                        };

                        const aSize = getSizeInBytes(aVal);
                        const bSize = getSizeInBytes(bVal);
                        return isAscending ? aSize - bSize : bSize - aSize;
                    }

                    // Default string comparison
                    return isAscending ?
                        aVal.localeCompare(bVal) :
                        bVal.localeCompare(aVal);
                });

                $.each(rows, function(index, row) {
                    $tbody.append(row);
                });
            });

            // Add loading state to buttons
            $('.btn-outline-primary, .btn-outline-success').on('click', function() {
                const $this = $(this);
                const originalHtml = $this.html();

                $this.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

                setTimeout(function() {
                    $this.prop('disabled', false).html(originalHtml);
                }, 1000);
            });

            // Console log for debugging (remove in production)
            console.log('Profile show page loaded successfully');
            console.log('Documents found:', $('.document-table tbody tr').length);
            console.log('Profile completion:', $('.progress-bar').attr('style'));
        });
    </script>
@endpush
