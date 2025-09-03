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

    .stats-icon.asesi { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stats-icon.sertifikat { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .stats-icon.lembaga { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .stats-icon.skema { background: linear-gradient(135deg, #43e97b, #38f9d7); }

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

    .table th {
        background: var(--sidebar-bg);
        border: none;
        font-weight: 600;
        color: var(--text-primary);
    }

    .badge-created { background: #d4edda; color: #155724; }
    .badge-updated { background: #d1ecf1; color: #0c5460; }
    .badge-deleted { background: #f8d7da; color: #721c24; }
    .badge-default { background: var(--sidebar-bg); color: var(--text-secondary); }

    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    }

    .live-time {
        color: white !important;
        font-weight: 500;
    }

    .live-date {
        color: white !important;
        opacity: 0.9;
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
                    Selamat datang di Dashboard Super Admin. 
                    Kelola dan pantau seluruh aktivitas sertifikasi dengan mudah.
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
                    <option value="all" {{ ($year ?? 'all') == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2010; $i--)
                        <option value="{{ $i }}" {{ ($year ?? 'all') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                    <div class="stats-icon asesi mx-auto mb-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalAsesi ?? 0 }}">{{ $totalAsesi ?? 0 }}</div>
                    <div class="text-muted">Total Asesi</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon sertifikat mx-auto mb-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalSertifikat ?? 0 }}">{{ $totalSertifikat ?? 0 }}</div>
                    <div class="text-muted">Sertifikat Diterbitkan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon lembaga mx-auto mb-3">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalLembagaPelatihan ?? 0 }}">{{ $totalLembagaPelatihan ?? 0 }}</div>
                    <div class="text-muted">Lembaga Pelatihan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <div class="stats-icon skema mx-auto mb-3">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="stats-value counter-number" data-target="{{ $totalSkema ?? 0 }}">{{ $totalSkema ?? 0 }}</div>
                    <div class="text-muted">Skema Sertifikasi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-card p-4 mb-4">
        <h5 class="card-title mb-3">
            <i class="bi bi-bar-chart me-2"></i>Statistik Peserta per Skema
        </h5>
        <div style="position: relative; height: 400px;">
            <canvas id="schemeChart"></canvas>
            <div id="chartLoading" class="loading-spinner d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Certificates Section -->
    <div class="chart-card p-4 mb-4">
    <h5 class="card-title mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
        Sertifikat Akan Expired (3 Bulan ke Depan)
        <span class="badge bg-primary ms-2" id="totalExpiredBadge">Loading...</span>
    </h5>
    
    <div class="table-responsive">
        <table id="expiredCertificatesTable" class="table table-hover" style="width: 100%;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peserta</th>
                    <th>Sertifikasi</th>
                    <th>Tanggal Expired</th>
                    <th>Hari Tersisa</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

    <!-- Activity Log Section -->
    <div class="activity-card p-4">
        <h5 class="card-title mb-3">
            <i class="bi bi-activity me-2"></i>Activity Log
        </h5>
        
        <!-- Activity Log Filter -->
        <form method="GET" class="row g-3 mb-4">
            <input type="hidden" name="year" value="{{ $year ?? 'all' }}">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal Mulai</label>
                <input type="date" class="form-control" name="start_date" 
                       value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal Selesai</label>
                <input type="date" class="form-control" name="end_date" 
                       value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-filter">
                        <i class="bi bi-search me-2"></i>Filter Log
                    </button>
                </div>
            </div>
        </form>

        <!-- Activity Log Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Tipe Log</th>
                        <th>Deskripsi</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activityLogs as $log)
                    <tr>
                        <td>
                            <div class="small">
                                <div class="fw-semibold">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                            </div>
                        </td>
                        <td>
                            @if($log->causer)
                                <div>
                                    <div class="fw-semibold">{{ $log->causer->name }}</div>
                                    <div class="small text-muted">{{ $log->causer->email }}</div>
                                </div>
                            @else
                                <div>
                                    <div class="fw-semibold">System</div>
                                    <div class="small text-muted">Automated</div>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $logType = $log->log_name ?? 'default';
                                $badgeClass = 'badge-' . $logType;
                            @endphp
                            <span class="badge {{ $badgeClass }} px-2 py-1">{{ $logType }}</span>
                        </td>
                        <td>{{ Str::limit($log->description, 50) }}</td>
                        <td>
                            @if($log->subject_type && $log->subject_id)
                                <div class="small">
                                    <div class="fw-semibold">{{ class_basename($log->subject_type) }}</div>
                                    <div class="text-muted">ID: {{ $log->subject_id }}</div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <h6>Tidak ada activity log ditemukan</h6>
                                <p class="small">Untuk periode {{ $startDate }} - {{ $endDate }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activityLogs->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $activityLogs->firstItem() }} - {{ $activityLogs->lastItem() }} 
                dari {{ $activityLogs->total() }} log
            </div>
            <div>
                {{ $activityLogs->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>

    $(document).ready(function() {
    // Initialize DataTable with server-side processing
    const expiredTable = $('#expiredCertificatesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.dashboard.expired-certificates") }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('Error loading expired certificates:', error);
            }
        },
        columns: [
            { 
                data: null,
                name: 'row_number',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'nama', 
                name: 'nama',
                render: function(data, type, row) {
                    return `<div class="fw-semibold">${data}</div>`;
                }
            },
            { 
                data: 'sertifikasi', 
                name: 'sertifikasi' 
            },
            { 
                data: 'tanggal_exp', 
                name: 'tanggal_exp',
                render: function(data, type, row) {
                    const expDate = new Date(data);
                    const formattedDate = expDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    return `<div class="fw-semibold">${formattedDate}</div>`;
                }
            },
            { 
                data: 'days_left', 
                name: 'days_left',
                orderable: true,
                render: function(data, type, row) {
                    if (data > 0) {
                        return `<div class="text-muted small">${data} hari lagi</div>`;
                    } else if (data == 0) {
                        return `<div class="text-danger small fw-bold">Hari ini</div>`;
                    } else {
                        return `<div class="text-danger small fw-bold">${Math.abs(data)} hari lalu</div>`;
                    }
                }
            }
        ],
        order: [[4, 'asc']], // Order by days_left ascending (most critical first)
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ sertifikat",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 sertifikat",
            infoFiltered: "(disaring dari _MAX_ total sertifikat)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            emptyTable: "Tidak ada sertifikat yang akan expired dalam 3 bulan ke depan",
            zeroRecords: "Tidak ditemukan data yang sesuai"
        },
        responsive: true,
        drawCallback: function(settings) {
            // Update total badge after data loaded
            const totalRecords = settings.json ? settings.json.recordsTotal : 0;
            $('#totalExpiredBadge').text(`${totalRecords} total`);
        },
        initComplete: function() {
            console.log('Expired certificates table initialized');
        }
    });

    // Custom styling for status badges
    $('#expiredCertificatesTable').on('draw.dt', function() {
        // Add hover effects or additional styling if needed
    });
});
document.addEventListener('DOMContentLoaded', function() {
    let schemeChart = null;
    const ctx = document.getElementById('schemeChart').getContext('2d');
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
    
    // Update time every second
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

    // Update Statistics
    function updateStatistics(year) {
        return fetch(`{{ route('admin.dashboard.statistics-data') }}?year=${year}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update data-target attributes
                    const counters = document.querySelectorAll('.counter-number');
                    counters[0].setAttribute('data-target', data.totalAsesi);
                    counters[1].setAttribute('data-target', data.totalSertifikat);
                    counters[2].setAttribute('data-target', data.totalLembagaPelatihan);
                    counters[3].setAttribute('data-target', data.totalSkema);
                    
                    // Reset and animate counters
                    counters.forEach(counter => {
                        counter.textContent = '0';
                    });
                    setTimeout(animateCounters, 100);
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error updating statistics:', error);
                // Use fallback values if error
                const counters = document.querySelectorAll('.counter-number');
                counters.forEach(counter => {
                    const fallback = counter.getAttribute('data-target') || '0';
                    counter.textContent = parseInt(fallback).toLocaleString('id-ID');
                });
            });
    }

    // Create Chart
    function createChart(labels, data) {
        if (schemeChart) {
            schemeChart.destroy();
        }

        // Professional color palette
        const colors = [
            'rgba(54, 162, 235, 0.8)',   // Blue
            'rgba(255, 99, 132, 0.8)',   // Red
            'rgba(75, 192, 192, 0.8)',   // Teal
            'rgba(255, 206, 86, 0.8)',   // Yellow
            'rgba(153, 102, 255, 0.8)',  // Purple
            'rgba(255, 159, 64, 0.8)',   // Orange
            'rgba(199, 199, 199, 0.8)',  // Grey
            'rgba(83, 102, 255, 0.8)',   // Indigo
            'rgba(255, 99, 255, 0.8)',   // Pink
            'rgba(99, 255, 132, 0.8)'    // Green
        ];

        const borderColors = colors.map(color => color.replace('0.8', '1'));

        schemeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Peserta',
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderColor: borderColors.slice(0, labels.length),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
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
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Total: ${context.parsed.y.toLocaleString('id-ID')} peserta`;
                            }
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
                }
            }
        });
    }

    // Load Chart Data - Fixed version
    function loadChartData(year) {
        const loading = document.getElementById('chartLoading');
        loading.classList.remove('d-none');
        
        fetch(`{{ route('admin.dashboard.chart-data') }}?year=${year}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                loading.classList.add('d-none'); // Hide loading
                
                if (data.success) {
                    // Check if we have data
                    if (data.labels && data.values && data.labels.length > 0) {
                        createChart(data.labels, data.values);
                    } else {
                        // Show no data message
                        createChart(['Tidak ada data'], [0]);
                    }
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                loading.classList.add('d-none'); // Hide loading on error
                
                // Use fallback data
                const fallbackLabels = @json($chartLabels ?? ['Tidak ada data']);
                const fallbackValues = @json($chartValues ?? [0]);
                createChart(fallbackLabels, fallbackValues);
            });
    }

    // Apply Filter Event
    applyFilterBtn.addEventListener('click', function() {
        const year = yearSelect.value;
        
        // Show loading state
        const originalHTML = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
        this.disabled = true;
        
        // Update both statistics and chart
        Promise.all([
            updateStatistics(year),
            loadChartData(year)
        ]).finally(() => {
            // Reset button state
            this.innerHTML = originalHTML;
            this.disabled = false;
        });
        
        // Update URL without reload
        const url = new URL(window.location);
        if (year !== 'all') {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }
        
        window.history.pushState({}, '', url);
    });

    // Reset Filter Event
    resetFilterBtn.addEventListener('click', function() {
        yearSelect.value = 'all';
        
        // Trigger apply filter
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
@endsection