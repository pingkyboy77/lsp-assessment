@extends('layouts.lembaga-pelatihan')

@section('title', 'Dashboard Lembaga Pelatihan')

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

    .stats-icon.peserta { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stats-icon.apl01 { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .stats-icon.apl02 { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .stats-icon.approved { background: linear-gradient(135deg, #43e97b, #38f9d7); }

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

    .chart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 2rem;
    }

    .activity-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
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

    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    }

    .activity-item {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0;
    }

    .activity-item:last-child {
        border-bottom: none;
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
                    }}, {{ Auth::user()->name }}!
                </h2>
                <p class="mb-0 opacity-90">
                    Selamat datang di Dashboard Lembaga Pelatihan. 
                    Kelola dan pantau aktivitas pelatihan dengan mudah.
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

    <!-- Filter Section -->
    <div class="filter-card p-4">
        <h5 class="card-title mb-3">
            <i class="bi bi-funnel me-2"></i>Filter Data
        </h5>
        <form id="filterForm" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tahun Registrasi</label>
                <select class="form-select" id="yearSelect" name="year">
                    <option value="all" {{ $year == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2010; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="button" id="applyFilter" class="btn btn-filter">
                        <i class="bi bi-search me-2"></i>Terapkan Filter
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="button" id="resetFilter" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon peserta mx-auto mb-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalPeserta }}">{{ $totalPeserta }}</div>
                    <div class="text-muted">Total Peserta</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon apl01 mx-auto mb-3">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalApl01 }}">{{ $totalApl01 }}</div>
                    <div class="text-muted">APL 01 Terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon apl02 mx-auto mb-3">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalApl02 }}">{{ $totalApl02 }}</div>
                    <div class="text-muted">APL 02 Terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon approved mx-auto mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalApproved }}">{{ $totalApproved }}</div>
                    <div class="text-muted">Total Disetujui</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart and Recent Activities Row -->
    <div class="row">
        <!-- Chart Card -->
        <div class="col-xl-12 col-lg-12">
            <div class="chart-card p-4 mb-4">
                <h5 class="card-title mb-3">
                    <i class="bi bi-bar-chart me-2"></i>Grafik Pengajuan APL 01 per Bulan
                </h5>
                <div style="position: relative; height: 400px;">
                    <canvas id="aplChart"></canvas>
                    <div id="chartLoading" class="loading-spinner d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="chart-card p-4">
        <h5 class="card-title mb-3">
            <i class="bi bi-building me-2"></i>Informasi Lembaga
        </h5>
        <div class="row">
            <div class="col-md-6 d-flex">
                <p><strong>Nama Lembaga:</strong> {{ Auth::user()->lembaga->name }}</p>
            </div><div class="col-md-6 d-flex">
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let aplChart = null;
    const ctx = document.getElementById('aplChart').getContext('2d');
    const yearSelect = document.getElementById('yearSelect');
    const applyFilterBtn = document.getElementById('applyFilter');
    const resetFilterBtn = document.getElementById('resetFilter');

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

    // Create Chart - Navy blue with data labels
    function createChart(labels, data) {
        if (aplChart) {
            aplChart.destroy();
        }

        const navyBlue = '#1e3a8a';
        const navyBlueHover = '#1e40af';

        aplChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total APL 01',
                    data: data,
                    backgroundColor: navyBlue,
                    borderColor: navyBlue,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: navyBlueHover,
                    hoverBorderColor: navyBlueHover,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 58, 138, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Total: ${context.parsed.y.toLocaleString('id-ID')} pengajuan`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'top',
                        color: '#1e3a8a',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value) {
                            return value.toLocaleString('id-ID');
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 30
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    // Load Chart Data
    function loadChartData(year) {
        const loading = document.getElementById('chartLoading');
        loading.classList.remove('d-none');
        
        fetch(`{{ route('lembaga-pelatihan.chart-data') }}?year=${year}`)
            .then(response => response.json())
            .then(data => {
                loading.classList.add('d-none');
                
                if (data.success) {
                    if (data.labels && data.values && data.labels.length > 0) {
                        createChart(data.labels, data.values);
                    } else {
                        createChart(['Tidak ada data'], [0]);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                loading.classList.add('d-none');
                const fallbackLabels = @json($chartLabels ?? ['Tidak ada data']);
                const fallbackValues = @json($chartValues ?? [0]);
                createChart(fallbackLabels, fallbackValues);
            });
    }

    // Update Statistics
    function updateStatistics(year) {
        return fetch(`{{ route('lembaga-pelatihan.statistics.data') }}?year=${year}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const counters = document.querySelectorAll('.counter-number');
                    counters[0].setAttribute('data-target', data.totalPeserta);
                    counters[1].setAttribute('data-target', data.totalApl01);
                    counters[2].setAttribute('data-target', data.totalApl02);
                    counters[3].setAttribute('data-target', data.totalApproved);
                    
                    counters.forEach(counter => counter.textContent = '0');
                    setTimeout(animateCounters, 100);
                }
            });
    }

    // Apply Filter
    applyFilterBtn.addEventListener('click', function() {
        const year = yearSelect.value;
        const originalHTML = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
        this.disabled = true;
        
        Promise.all([
            updateStatistics(year),
            loadChartData(year)
        ]).finally(() => {
            this.innerHTML = originalHTML;
            this.disabled = false;
        });
        
        const url = new URL(window.location);
        if (year !== 'all') {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }
        window.history.pushState({}, '', url);
    });

    // Reset Filter
    resetFilterBtn.addEventListener('click', function() {
        yearSelect.value = 'all';
        applyFilterBtn.click();
    });

    // Initialize
    setTimeout(() => {
        animateCounters();
        const currentYear = yearSelect.value;
        loadChartData(currentYear);
    }, 500);
});
</script>
@endpush
@endsection