@extends('layouts.admin')

@section('content')
<style>
    .welcome-card {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border-radius: 15px;
        color: white;
        margin-bottom: 2rem;
    }

    .stats-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: all 0.3s ease;
        height: 100%;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stats-icon.purple { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stats-icon.pink { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .stats-icon.green { background: linear-gradient(135deg, #43e97b, #38f9d7); }
    .stats-icon.orange { background: linear-gradient(135deg, #fa709a, #fee140); }

    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 2rem;
    }

    .content-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 2rem;
    }

    .btn-filter {
        background: var(--primary-color);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 500;
    }

    .btn-filter:hover {
        background: var(--primary-dark);
        color: white;
    }

    .live-time {
        color: white !important;
        font-weight: 500;
    }

    .live-date {
        color: white !important;
        opacity: 0.9;
    }

    .assessment-item {
        background: var(--sidebar-bg);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
    }

    .assessment-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .assessment-item.today {
        border-left-color: #f093fb;
        background: linear-gradient(to right, #fff5f7 0%, var(--sidebar-bg) 100%);
    }

    .assessment-item.completed {
        border-left-color: #43e97b;
        background: linear-gradient(to right, #f0fff4 0%, var(--sidebar-bg) 100%);
        opacity: 0.8;
    }

    .badge-custom {
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-online {
        background: #4299e1;
        color: white;
    }

    .badge-offline {
        background: #9f7aea;
        color: white;
    }

    .badge-today {
        background: #fa709a;
        color: white;
    }

    .badge-upcoming {
        background: #4299e1;
        color: white;
    }

    .badge-completed {
        background: #48bb78;
        color: white;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .btn-detail {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-detail:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        color: white;
    }
</style>

<div class="container-fluid p-4">
    <!-- Welcome Section -->
    <div class="welcome-card p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="bi bi-sun me-2"></i>
                    Selamat {{ 
                        date('H') < 12 ? 'Pagi' : 
                        (date('H') < 17 ? 'Siang' : 'Malam') 
                    }}, {{ auth()->user()->name }}!
                </h2>
                <p class="mb-0 opacity-90">
                    Selamat datang di Dashboard Asesor. 
                    Kelola jadwal assessment Anda dengan mudah.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="live-date">
                    <i class="bi bi-calendar3 me-2"></i><span id="liveDate"></span>
                </div>
                <div class="live-time">
                    <i class="bi bi-clock me-2"></i><span id="liveTime"></span>
                </div>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon purple mx-auto mb-3">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalAssessments }}">{{ $totalAssessments }}</div>
                    <div class="text-muted">Total Assessment</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon pink mx-auto mb-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $upcomingAssessments }}">{{ $upcomingAssessments }}</div>
                    <div class="text-muted">Akan Datang</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon green mx-auto mb-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $completedAssessments }}">{{ $completedAssessments }}</div>
                    <div class="text-muted">Selesai</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon orange mx-auto mb-3">
                        <i class="bi bi-star"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $todayAssessments }}">{{ $todayAssessments }}</div>
                    <div class="text-muted">Hari Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card p-4">
        <h5 class="card-title mb-3">
            <i class="bi bi-funnel me-2"></i>Filter Data
        </h5>
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Bulan</label>
                <input type="month" class="form-select" name="month" value="{{ $month }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="status">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="today" {{ $status == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="upcoming" {{ $status == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-filter">
                        <i class="bi bi-search me-2"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <!-- Assessment List -->
        <div class="col-lg-8">
            <div class="content-card p-4">
                <h5 class="card-title mb-3">
                    <i class="bi bi-calendar-week me-2"></i>Jadwal Assessment
                </h5>

                @forelse($scheduledAssessments as $schedule)
                    @php
                        $scheduleDate = \Carbon\Carbon::parse($schedule->tanggal_pelaksanaan_asesmen);
                        $isToday = $scheduleDate->isToday();
                        $isCompleted = $schedule->status_assessment === 'completed';
                        $itemClass = $isToday ? 'today' : ($isCompleted ? 'completed' : '');
                    @endphp

                    <div class="assessment-item {{ $itemClass }}">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-2 fw-bold">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    {{ $schedule->asesi->name ?? 'Unknown' }}
                                </h6>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-mortarboard me-1"></i>
                                    {{ $schedule->certificationScheme->code_1 ?? 'N/A' }} -
                                    {{ Str::limit($schedule->certificationScheme->nama ?? 'N/A', 50) }}
                                </div>
                                <div>
                                    <span class="badge badge-custom badge-{{ $schedule->jenis_ujian }} me-2">
                                        <i class="bi bi-{{ $schedule->jenis_ujian === 'online' ? 'laptop' : 'building' }} me-1"></i>
                                        {{ ucfirst($schedule->jenis_ujian) }}
                                    </span>
                                    <span class="badge badge-custom badge-{{ $isCompleted ? 'completed' : ($isToday ? 'today' : 'upcoming') }}">
                                        {{ $isCompleted ? 'Selesai' : ($isToday ? 'Hari Ini' : 'Akan Datang') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="fw-bold text-primary mb-2">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($schedule->waktu_mulai)->format('H:i') }} WIB
                                </div>
                                <div class="small text-muted mb-2">
                                    {{ $scheduleDate->isoFormat('dddd, D MMM Y') }}
                                </div>
                                <button class="btn btn-sm btn-detail" onclick="showDetail({{ $schedule->id }})">
                                    <i class="bi bi-eye me-1"></i>Detail
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada jadwal assessment</h5>
                        <p class="text-muted small">Untuk periode yang dipilih</p>
                    </div>
                @endforelse

                @if(method_exists($scheduledAssessments, 'hasPages') && $scheduledAssessments->hasPages())
                    <div class="mt-4">
                        {{ $scheduledAssessments->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Info -->
        <div class="col-lg-4">
            <div class="info-card p-4">
                <h5 class="card-title mb-3">
                    <i class="bi bi-info-circle me-2"></i>Info Cepat
                </h5>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Progress Bulan Ini</span>
                        <span class="fw-bold">{{ $totalAssessments > 0 ? number_format(($completedAssessments / $totalAssessments) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $totalAssessments > 0 ? ($completedAssessments / $totalAssessments) * 100 : 0 }}%; background: linear-gradient(90deg, #43e97b, #38f9d7);"
                            aria-valuenow="{{ $totalAssessments > 0 ? ($completedAssessments / $totalAssessments) * 100 : 0 }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold mb-3">Ringkasan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Assessment</span>
                        <span class="fw-bold">{{ $totalAssessments }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Akan Datang</span>
                        <span class="fw-bold text-primary">{{ $upcomingAssessments }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Selesai</span>
                        <span class="fw-bold text-success">{{ $completedAssessments }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Hari Ini</span>
                        <span class="fw-bold text-warning">{{ $todayAssessments }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="border-bottom: 2px solid #f7fafc;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Detail Assessment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live Time and Date
    function updateDateTime() {
        const now = new Date();
        const timeOptions = { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        const dateOptions = { 
            weekday: 'long',
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            timeZone: 'Asia/Jakarta'
        };
        
        document.getElementById('liveTime').textContent = now.toLocaleTimeString('id-ID', timeOptions) + ' WIB';
        document.getElementById('liveDate').textContent = now.toLocaleDateString('id-ID', dateOptions);
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Counter Animation
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-number');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            let current = 0;
            const increment = Math.ceil(target / 20);
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target.toLocaleString('id-ID');
                    clearInterval(timer);
                } else {
                    counter.textContent = current.toLocaleString('id-ID');
                }
            }, 50);
        });
    }

    setTimeout(animateCounters, 500);
});

function showDetail(assessmentId) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();

    const url = "{{ route('asesor.assessment.detail', ':id') }}".replace(':id', assessmentId);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('detailContent');

            if (!data.success) {
                container.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Gagal memuat detail assessment: ${data.message || 'Terjadi kesalahan tidak diketahui.'}
                    </div>
                `;
                return;
            }

            const a = data.data;
            const scheduleDate = new Date(a.tanggal_pelaksanaan_asesmen);

            container.innerHTML = `
                <div class="row g-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-person me-2"></i>Informasi Asesi
                        </h6>
                        <table class="table table-borderless">
                            <tr><td width="30%" class="text-muted">Nama</td><td>: ${a.asesi.name}</td></tr>
                            <tr><td class="text-muted">Email</td><td>: ${a.asesi.email}</td></tr>
                        </table>
                    </div>

                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-clipboard-check me-2"></i>Detail Assessment
                        </h6>
                        <table class="table table-borderless">
                            <tr><td width="30%" class="text-muted">Skema</td><td>: ${a.certification_scheme.nama}</td></tr>
                            <tr><td class="text-muted">Tanggal</td><td>: ${scheduleDate.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</td></tr>
                            <tr><td class="text-muted">Waktu</td><td>: ${a.waktu_mulai} WIB</td></tr>
                            <tr><td class="text-muted">Jenis Ujian</td><td>: <span class="badge bg-secondary">${a.jenis_ujian.toUpperCase()}</span></td></tr>
                            <tr><td class="text-muted">MET Asesor</td><td>: ${a.asesor_met}</td></tr>
                        </table>
                    </div>

                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-people me-2"></i>Tim Assessment</h6>
                        <table class="table table-borderless">
                            <tr><td width="30%" class="text-muted">Verifikator TUK</td><td>: ${a.verifikator_tuk.name}</td></tr>
                            <tr><td class="text-muted">Observer</td><td>: ${a.observer.name}</td></tr>
                            <tr><td class="text-muted">Asesor</td><td>: ${a.asesor.name}</td></tr>
                        </table>
                    </div>

                    ${a.notes && a.notes !== '-' ? `
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-sticky me-2"></i>Catatan</h6>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>${a.notes}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('detailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Gagal memuat detail assessment: ${error.message}
                </div>
            `;
        });
}
</script>
@endsection