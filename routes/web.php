<?php

use App\Models\CertificationScheme;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\DataPribadiController;
use App\Http\Controllers\Admin\AdminApl01Controller;
use App\Http\Controllers\Admin\KelompokKerjaController;
use App\Http\Controllers\Admin\KriteriaKerjaController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\NumberSequenceController;
use App\Http\Controllers\Admin\UnitKompetensiController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Asesi\DashboardAsesiController;
use App\Http\Controllers\Admin\BuktiPortofolioController;
use App\Http\Controllers\Admin\ElemenKompetensiController;
use App\Http\Controllers\Asesi\SkemaSertifikasiController;
use App\Http\Controllers\Admin\MonitoringProfileController;
use App\Http\Controllers\Admin\CertificationSchemeController;
use App\Http\Controllers\Admin\RequirementTemplateController;
use App\Http\Controllers\Admin\SuperAdminDashboardController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| Authenticated & Verified Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', fn() => view('dashboard'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes (Only for Superadmin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:superadmin'])
    ->name('admin.')
    ->group(function () {
        // Route::get('/dashboard', fn() => view('dashboard.superAdmin'))->name('dashboard');
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/scheme-stats/{id}', [SuperAdminDashboardController::class, 'getSchemeStats'])->name('dashboard.scheme-stats');
    Route::get('/dashboard/trending-schemes', [SuperAdminDashboardController::class, 'getTrendingSchemes'])->name('dashboard.trending');
    Route::get('/dashboard/activity-logs', [SuperAdminDashboardController::class, 'getActivityLogs'])->name('dashboard.activity-logs');
    Route::get('/dashboard/activity-stats', [SuperAdminDashboardController::class, 'getActivityStats'])->name('dashboard.activity-stats');

    Route::get('/dashboard/statistics-data', [SuperAdminDashboardController::class, 'getStatisticsData'])->name('dashboard.statistics-data');
    Route::get('/dashboard/chart-data', [SuperAdminDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/expiring-certificates', [SuperAdminDashboardController::class, 'getExpiringCertificates'])->name('dashboard.expiring-certificates');
    Route::get('/debug-expiring', [SuperAdminDashboardController::class, 'debugExpiringCertificates'])->name('debug.expiring');
    Route::get('/dashboard/expired-certificates', [SuperAdminDashboardController::class, 'getExpiringCertificatesDataTable'])->name('dashboard.expired-certificates');

        Route::resource('users', UserManagementController::class)->names('users');
        Route::resource('roles', RoleController::class)->names('roles');
        Route::resource('system-settings', SystemSettingController::class);
        Route::resource('number-sequences', NumberSequenceController::class);
        Route::post('number-sequences/{numberSequence}/test', [NumberSequenceController::class, 'test'])->name('number-sequences.test');
        Route::post('generate-number', [NumberSequenceController::class, 'generate'])->name('generate-number');
        Route::prefix('monitoring-profile')
            ->name('monitoring-profile.')
            ->group(function () {
                Route::get('/', [MonitoringProfileController::class, 'index'])->name('index');
                Route::get('/data', [MonitoringProfileController::class, 'getData'])->name('getData');
                Route::get('/{id}', [MonitoringProfileController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [MonitoringProfileController::class, 'edit'])->name('edit');
                Route::put('/{id}', [MonitoringProfileController::class, 'update'])->name('update');
            });

        Route::resource('lembaga', \App\Http\Controllers\Admin\LembagaPelatihanController::class);

        Route::resource('fields', FieldController::class)->except('show');
        Route::patch('fields/{field}/toggle-status', [FieldController::class, 'toggleStatus'])->name('fields.toggle-status');
        Route::get('api/fields', [FieldController::class, 'getFields'])->name('api.fields');

        // Existing certification schemes routes
        Route::resource('certification-schemes', CertificationSchemeController::class);

        // Additional routes for requirements management
        Route::get('certification-schemes/{certificationScheme}/requirements', [CertificationSchemeController::class, 'requirements'])->name('certification-schemes.requirements');
        Route::put('certification-schemes/{certificationScheme}/requirements', [CertificationSchemeController::class, 'updateRequirements'])->name('certification-schemes.update-requirements');
        Route::delete('certification-schemes/{certification_scheme}/reset-requirements', [CertificationSchemeController::class, 'resetRequirements'])->name('certification-schemes.reset-requirements');

        // Toggle status route
        Route::patch('certification-schemes/{certificationScheme}/toggle-status', [CertificationSchemeController::class, 'toggleStatus'])->name('certification-schemes.toggle-status');

        // API routes for AJAX functionality
        Route::get('api/schemes/by-field/{code2}', [CertificationSchemeController::class, 'getSchemesByField'])->name('certification-schemes.by-field');
        Route::get('api/templates/{template}/details', [CertificationSchemeController::class, 'getTemplateDetails'])->name('certification-schemes.get-template-details');

        Route::prefix('requirements')
            ->name('requirements.')
            ->group(function () {
                Route::get('/', [RequirementTemplateController::class, 'index'])->name('index');
                Route::get('/create', [RequirementTemplateController::class, 'create'])->name('create');
                Route::post('/', [RequirementTemplateController::class, 'store'])->name('store');
                Route::get('/{id}', [RequirementTemplateController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [RequirementTemplateController::class, 'edit'])->name('edit');
                Route::put('/{id}', [RequirementTemplateController::class, 'update'])->name('update');
                Route::delete('/{id}', [RequirementTemplateController::class, 'destroy'])->name('destroy');
                Route::patch('/{id}/toggle-status', [RequirementTemplateController::class, 'toggleStatus'])->name('toggle-status');
                Route::get('/{id}/template', [RequirementTemplateController::class, 'getTemplate'])->name('get-template');
            });

        // Unit Kompetensi System Routes
        Route::group(['prefix' => 'schemes/{scheme}'], function () {
            // Unit Kompetensi Routes
            Route::group(['prefix' => 'unit-kompetensi', 'as' => 'schemes.unit-kompetensi.'], function () {
                Route::get('/', [UnitKompetensiController::class, 'index'])->name('index');
                Route::get('/create', [UnitKompetensiController::class, 'create'])->name('create');
                Route::post('/', [UnitKompetensiController::class, 'store'])->name('store');
                Route::get('/{unitKompetensi}', [UnitKompetensiController::class, 'show'])->name('show');
                Route::get('/{unitKompetensi}/edit', [UnitKompetensiController::class, 'edit'])->name('edit');
                Route::put('/{unitKompetensi}', [UnitKompetensiController::class, 'update'])->name('update');
                Route::delete('/{unitKompetensi}', [UnitKompetensiController::class, 'destroy'])->name('destroy');

                // AJAX routes for Unit Kompetensi
                Route::post('/reorder', [UnitKompetensiController::class, 'reorder'])->name('reorder');
                Route::get('/{unitKompetensi}/duplicate', [UnitKompetensiController::class, 'duplicateForm'])->name('duplicate.form');
                Route::post('/{unitKompetensi}/duplicate', [UnitKompetensiController::class, 'duplicateStore'])->name('duplicate.store');
                Route::patch('/{unitKompetensi}/toggle-status', [UnitKompetensiController::class, 'toggleStatus'])->name('toggle-status');

                Route::get('/export', [UnitKompetensiController::class, 'export'])->name('export');

                // Elemen Kompetensi routes
                // Untuk Elemen Kompetensi
                Route::post('/{unitKompetensi}/elemen-kompetensi', [ElemenKompetensiController::class, 'store'])->name('elemen-kompetensi.store');
                Route::put('/{unitKompetensi}/elemen-kompetensi/{elemen}', [ElemenKompetensiController::class, 'update'])->name('elemen-kompetensi.update');
                Route::delete('/{unitKompetensi}/elemen-kompetensi/{elemen}', [ElemenKompetensiController::class, 'destroy'])->name('elemen-kompetensi.destroy');

                // Untuk Kriteria Kerja
                Route::post('/{unitKompetensi}/elemen-kompetensi/{elemen}/kriteria-kerja', [KriteriaKerjaController::class, 'store'])->name('elemen-kompetensi.kriteria-kerja.store');
                Route::put('/{unitKompetensi}/elemen-kompetensi/{elemen}/kriteria-kerja/{kriteria}', [KriteriaKerjaController::class, 'update'])->name('elemen-kompetensi.kriteria-kerja.update');
                Route::delete('/{unitKompetensi}/elemen-kompetensi/{elemen}/kriteria-kerja/{kriteria}', [KriteriaKerjaController::class, 'destroy'])->name('elemen-kompetensi.kriteria-kerja.destroy');
            });

            Route::group(['prefix' => 'kelompok-kerja', 'as' => 'schemes.kelompok-kerja.'], function () {
                Route::get('/', [KelompokKerjaController::class, 'index'])->name('index');
                Route::get('/create', [KelompokKerjaController::class, 'create'])->name('create');
                Route::post('/', [KelompokKerjaController::class, 'store'])->name('store');

                // Kelompok Kerja Management Routes (yang tidak butuh kelompokKerja ID)
                Route::post('/reorder', [KelompokKerjaController::class, 'reorder'])->name('reorder');
                Route::post('/bulk-toggle-status', [KelompokKerjaController::class, 'bulkToggleStatus'])->name('bulk-toggle-status');
                Route::get('/export', [KelompokKerjaController::class, 'export'])->name('export');

                // Routes yang membutuhkan kelompokKerja ID - PENTING: Harus di akhir!
                Route::get('/{kelompokKerja}', [KelompokKerjaController::class, 'show'])->name('show');
                Route::get('/{kelompokKerja}/edit', [KelompokKerjaController::class, 'edit'])->name('edit');
                Route::put('/{kelompokKerja}', [KelompokKerjaController::class, 'update'])->name('update');
                Route::delete('/{kelompokKerja}', [KelompokKerjaController::class, 'destroy'])->name('destroy');

                // Unit Kompetensi Management Routes
                Route::get('/{kelompokKerja}/manage-unit-kompetensi', [KelompokKerjaController::class, 'manageUnitKompetensi'])->name('manage-unit-kompetensi');
                Route::post('/{kelompokKerja}/update-unit-kompetensi', [KelompokKerjaController::class, 'updateUnitKompetensi'])->name('update-unit-kompetensi');
                Route::post('/{kelompokKerja}/add-unit-kompetensi', [KelompokKerjaController::class, 'addUnitKompetensi'])->name('add-unit-kompetensi');
                Route::delete('/{kelompokKerja}/remove-unit-kompetensi', [KelompokKerjaController::class, 'removeUnitKompetensi'])->name('remove-unit-kompetensi');
                Route::patch('/{kelompokKerja}/toggle-unit-status', [KelompokKerjaController::class, 'toggleUnitStatus'])->name('toggle-unit-status');
                Route::post('/{kelompokKerja}/reorder-unit-kompetensi', [KelompokKerjaController::class, 'reorderUnitKompetensi'])->name('reorder-unit-kompetensi');
                Route::patch('/{kelompokKerja}/bulk-toggle-unit-status', [KelompokKerjaController::class, 'bulkToggleUnitStatus'])->name('bulk-toggle-unit-status');

                // Bukti Portofolio routes - FIXED PATTERN
                Route::prefix('{kelompokKerja}/bukti-portofolio')
                    ->name('bukti-portofolio.')
                    ->group(function () {
                        // routes khusus (harus didefinisikan lebih dulu)
                        Route::get('/available', [BuktiPortofolioController::class, 'getAvailableBukti'])->name('available');
                        Route::post('/remove-from-group', [BuktiPortofolioController::class, 'removeGroup'])->name('remove-from-group');
                        Route::get('/dependency-options', [BuktiPortofolioController::class, 'getDependencyOptions'])->name('dependency-options');

                        // CRUD utama
                        Route::get('/', [BuktiPortofolioController::class, 'index'])->name('index');
                        Route::post('/', [BuktiPortofolioController::class, 'store'])->name('store');
                        Route::post('/batch', [BuktiPortofolioController::class, 'storeBatch'])->name('store-batch');
                        Route::post('/bulk-action', [BuktiPortofolioController::class, 'bulkAction'])->name('bulk-action');
                        Route::post('/manage-groups', [BuktiPortofolioController::class, 'manageGroups'])->name('manage-groups');
                        Route::post('/remove-from-group', [BuktiPortofolioController::class, 'removeFromGroup'])->name('remove-from-group');
                        Route::post('/reorder', [BuktiPortofolioController::class, 'reorder'])->name('reorder');

                        // yang pakai {bukti} TARUH PALING BAWAH
                        Route::get('/{bukti}', [BuktiPortofolioController::class, 'show'])->name('show');
                        Route::put('/{bukti}', [BuktiPortofolioController::class, 'update'])->name('update');
                        Route::delete('/{bukti}', [BuktiPortofolioController::class, 'destroy'])->name('destroy');
                        Route::patch('/{bukti}/toggle-status', [BuktiPortofolioController::class, 'toggleStatus'])->name('toggle-status');
                    });

                // Other kelompokKerja specific routes
                Route::post('/{kelompokKerja}/toggle-status', [KelompokKerjaController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/{kelompokKerja}/duplicate', [KelompokKerjaController::class, 'duplicate'])->name('duplicate');
            });
        });

        Route::prefix('apl01')
    ->name('apl01.')
    ->group(function () {
        
        // PENTING: Routes spesifik HARUS diletakkan SEBELUM routes dengan parameter {apl}
        
        // Statistics dan export routes (tanpa parameter)
        Route::get('/statistics', [AdminApl01Controller::class, 'getStatistics'])->name('statistics');
        Route::post('/export', [AdminApl01Controller::class, 'export'])->name('export');
        
        // Data endpoint untuk DataTables
        Route::post('/data', [AdminApl01Controller::class, 'data'])->name('data');
        
        // Bulk operations (tanpa parameter)
        Route::post('/bulk-action', [AdminApl01Controller::class, 'bulkAction'])->name('bulk-action');
        
        // Main listing page
        Route::get('/', [AdminApl01Controller::class, 'index'])->name('index');
        
        // Routes dengan parameter {apl} - LETAKKAN SETELAH routes spesifik
        Route::get('/{apl}', [AdminApl01Controller::class, 'show'])->name('show');
        Route::get('/{apl}/edit', [AdminApl01Controller::class, 'edit'])->name('edit');
        Route::put('/{apl}', [AdminApl01Controller::class, 'update'])->name('update');
        Route::delete('/{apl}', [AdminApl01Controller::class, 'destroy'])->name('destroy');
        
        // Review operations dengan parameter {apl}
        Route::post('/{apl}/reopen', [AdminApl01Controller::class, 'reopen'])->name('reopen');
        Route::get('/{apl}/review-data', [AdminApl01Controller::class, 'getReviewData'])->name('review-data');
        Route::post('/{apl}/approve', [AdminApl01Controller::class, 'approve'])->name('approve');
        Route::post('/{apl}/reject', [AdminApl01Controller::class, 'reject'])->name('reject');
        Route::post('/{apl}/set-review', [AdminApl01Controller::class, 'setUnderReview'])->name('set-review');
        Route::post('/{apl}/return-revision', [AdminApl01Controller::class, 'returnForRevision'])->name('return-revision');
    });
    });

// API Routes untuk Admin
Route::group(['prefix' => 'api/admin', 'as' => 'api.admin.', 'middleware' => ['auth', 'role:superadmin']], function () {
    // Quick stats for dashboard
    Route::get('schemes/{scheme}/stats', function (CertificationScheme $scheme) {
        return response()->json([
            'units' => $scheme->unitKompetensis()->count(),
            'active_units' => $scheme->activeUnitKompetensis()->count(),
            'kelompoks' => $scheme->kelompokKerjas()->count(),
            'active_kelompoks' => $scheme->activeKelompokKerjas()->count(),
            'total_elements' => $scheme->elemenKompetensis()->count(),
            'total_criterias' => $scheme->kriteriaKerjas()->count(),
            'total_portfolios' => $scheme->buktiPortofolios()->count(),
        ]);
    })->name('schemes.stats');

    // Quick search for units
    Route::get('schemes/{scheme}/units/search', function (Request $request, CertificationScheme $scheme) {
        $query = $request->get('q');
        $units = $scheme
            ->unitKompetensis()
            ->where(function ($q) use ($query) {
                $q->where('kode_unit', 'like', "%{$query}%")->orWhere('judul_unit', 'like', "%{$query}%");
            })
            ->active()
            ->limit(10)
            ->get(['id', 'kode_unit', 'judul_unit']);

        return response()->json($units);
    })->name('schemes.units.search');

    // Quick search for kelompok
    Route::get('schemes/{scheme}/kelompoks/search', function (Request $request, CertificationScheme $scheme) {
        $query = $request->get('q');
        $kelompoks = $scheme
            ->kelompokKerjas()
            ->where('nama_kelompok', 'like', "%{$query}%")
            ->active()
            ->limit(10)
            ->get(['id', 'nama_kelompok', 'deskripsi']);

        return response()->json($kelompoks);
    })->name('schemes.kelompoks.search');
});

// Public Routes (Optional - for certificate verification)
Route::prefix('certificates')
    ->name('certificates.')
    ->group(function () {
        Route::get('/verify/{certificateNumber}', function ($certificateNumber) {
            $certificate = \App\Models\Certificate::with(['certificationScheme.field'])
                ->where('certificate_number', strtoupper($certificateNumber))
                ->where('status', 'active')
                ->first();

            if (!$certificate) {
                return view('certificates.verify-result', [
                    'found' => false,
                    'message' => 'Sertifikat tidak ditemukan atau sudah tidak berlaku',
                ]);
            }

            return view('certificates.verify-result', [
                'found' => true,
                'certificate' => $certificate,
                'message' => 'Sertifikat valid dan masih berlaku',
            ]);
        })->name('verify');

        Route::get('/verify', function () {
            return view('certificates.verify-form');
        })->name('verify-form');
    });

/*
|--------------------------------------------------------------------------
| Asesor Routes
|--------------------------------------------------------------------------
*/

Route::prefix('asesor')
    ->middleware(['auth', 'role:asesor'])
    ->name('asesor.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('dashboard.asesor'))->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Asesi Routes
|--------------------------------------------------------------------------
*/
Route::prefix('asesi')
    ->middleware(['auth', 'role:asesi'])
    ->name('asesi.')
    ->group(function () {
        Route::get('/dashboard', [DashboardAsesiController::class, 'index'])->name('dashboard');

        Route::get('/data-pribadi', [DataPribadiController::class, 'index'])->name('data-pribadi.index');
        Route::post('/data-pribadi', [DataPribadiController::class, 'store'])->name('data-pribadi.store');

        // Document Management Routes
        Route::get('/data-pribadi/document/{id}/download', [DataPribadiController::class, 'downloadDocument'])->name('data-pribadi.download');
        Route::delete('/data-pribadi/document/{id}', [DataPribadiController::class, 'deleteDocument'])->name('data-pribadi.delete-document');

        Route::get('skema-sertifikasi', [SkemaSertifikasiController::class, 'index'])->name('skema-sertifikasi.index');

        Route::prefix('apl01')
            ->name('apl01.')
            ->group(function () {
                Route::get('/', [App\Http\Controllers\Asesi\Apl01Controller::class, 'index'])->name('index');
                Route::get('/create/{scheme}', [App\Http\Controllers\Asesi\Apl01Controller::class, 'create'])->name('create');
                Route::post('/store/{scheme}', [App\Http\Controllers\Asesi\Apl01Controller::class, 'store'])->name('store');
                Route::get('/{apl}/edit', [App\Http\Controllers\Asesi\Apl01Controller::class, 'edit'])->name('edit');
                Route::get('/{apl}/show', [App\Http\Controllers\Asesi\Apl01Controller::class, 'show'])->name('show');
                
                Route::put('/{apl}', [App\Http\Controllers\Asesi\Apl01Controller::class, 'update'])->name('update');
                Route::delete('/{apl}', [App\Http\Controllers\Asesi\Apl01Controller::class, 'destroy'])->name('destroy');
            });

        // Region API Routes
        Route::prefix('regions')
            ->name('regions.')
            ->group(function () {
                Route::get('/provinces', [App\Http\Controllers\Asesi\Apl01Controller::class, 'getProvinces'])->name('provinces');
                Route::get('/cities/{provinceId}', [App\Http\Controllers\Asesi\Apl01Controller::class, 'getCities'])->name('cities');
            });
    });

/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Register, etc.)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
