@extends('layouts.admin')

@section('title', 'Detail Profil Pengguna')


@section('content')

    <!-- Navigation Buttons -->


    <!-- Paper Container -->
    <div class="d-flex justify-content-end row container">
        <div class="col-3 gap-2 justify-content-end d-flex">
            <a href="{{ route('admin.monitoring-profile.edit', $profile->id) }}" class="btn-nav btn-warning-custom">
                <i class="bi bi-pencil-square"></i>Edit Profil
            </a>
            <a href="{{ route('admin.monitoring-profile.index') }}" class="btn-nav btn-secondary-custom">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>
    </div>
    <div class="paper-container">
        <div class="paper-content">
            <!-- Paper Header -->
            <div class="paper-header">
                <h1 class="paper-title">Detail Profil Pengguna</h1>
                <p class="paper-subtitle">{{ $profile->nama_lengkap }}</p>
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
                                {{ $profile->jenis_kelamin == 'L' ? 'Laki-laki' : ($profile->jenis_kelamin == 'P' ? 'Perempuan' : 'Belum diisi') }}
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
                                {{ $profile->no_hp ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value {{ $profile->email ? '' : 'empty' }}">
                                {{ $profile->email ?? 'Belum diisi' }}
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
                            <div class="info-label">Alamat</div>
                            <div class="info-value {{ $profile->alamat_rumah ? '' : 'empty' }}">
                                {{ $profile->alamat_rumah ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-col">
                        <div class="info-item">
                            <div class="info-label">Kota</div>
                            <div class="info-value {{ $profile->kota_rumah ? '' : 'empty' }}">
                                {{ $profile->kota_rumah ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Provinsi</div>
                            <div class="info-value {{ $profile->provinsi_rumah ? '' : 'empty' }}">
                                {{ $profile->provinsi_rumah ?? 'Belum diisi' }}
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
                                {{ $profile->no_telp_rumah ?? 'Belum diisi' }}
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
                                {{ $profile->pendidikan_terakhir ?? 'Belum diisi' }}
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
                                {{ $profile->jabatan ?? 'Belum diisi' }}
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
                                {{ $profile->kategori_pekerjaan ?? 'Belum diisi' }}
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
                            <div class="info-label">Kota</div>
                            <div class="info-value {{ $profile->kota_kantor ? '' : 'empty' }}">
                                {{ $profile->kota_kantor ?? 'Belum diisi' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Provinsi</div>
                            <div class="info-value {{ $profile->provinsi_kantor ? '' : 'empty' }}">
                                {{ $profile->provinsi_kantor ?? 'Belum diisi' }}
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
                                {{ $profile->no_telp_kantor ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dokumen Section -->
            <div class="document-section">
                <h3 class="info-section-title">
                    <i class="bi bi-file-earmark-text me-2"></i>Dokumen yang Diupload
                </h3>

                @if ($documents->count() > 0)
                    <div class="document-table">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <th>Tanggal Upload</th>
                                    <th>Ukuran File</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $document->jenis_dokumen)) }}</td>
                                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if ($document->file_exists)
                                                <span class="file-size">{{ $document->file_size_kb }} KB</span>
                                            @else
                                                <span class="file-size">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->file_exists)
                                                <span class="file-status exists">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="file-status missing">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                    Tidak ditemukan
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->file_exists)
                                                <a href="{{ $document->file_url }}" target="_blank"
                                                    class="btn-doc-action btn-view-doc">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ $document->file_url }}" download
                                                    class="btn-doc-action btn-download-doc">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            @else
                                                <span class="text-danger small">
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
                @else
                    <div class="no-documents">
                        <i class="bi bi-file-earmark-upload"></i>
                        <p><strong>Belum ada dokumen yang diupload</strong></p>
                        <small>Pengguna belum mengupload dokumen apapun</small>
                    </div>
                @endif
            </div>



            <!-- Info Footer -->
            <div class="info-footer">
                <small>
                    <i class="bi bi-calendar-plus me-1"></i><strong>Dibuat:</strong>
                    {{ $profile->created_at->format('d/m/Y H:i') }}
                    @if ($profile->updated_at != $profile->created_at)
                        | <i class="bi bi-pencil-square me-1"></i><strong>Terakhir diperbarui:</strong>
                        {{ $profile->updated_at->format('d/m/Y H:i') }}
                    @endif
                </small>
            </div>
        </div>
    </div>

@endsection

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
                const url = $(this).attr('href');

                // Open in new window with specific dimensions
                const popup = window.open(url, 'document_preview',
                    'width=800,height=600,scrollbars=yes,resizable=yes');

                if (popup) {
                    popup.focus();
                } else {
                    // Fallback if popup blocked
                    window.location.href = url;
                }
            });

            // Tooltip initialization if using Bootstrap
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                    tooltipTriggerEl));
            }
        });
    </script>
@endpush
