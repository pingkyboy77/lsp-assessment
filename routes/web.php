<?php

use App\Models\CertificationScheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Asesi\Apl02Controller;
use App\Http\Controllers\DataPribadiController;
use App\Http\Controllers\Admin\AdminTukController;
use App\Http\Controllers\Admin\AdminMapaController;
use App\Http\Controllers\Admin\AplReviewController;
use App\Http\Controllers\Asesi\AsesiAk07Controller;
use App\Http\Controllers\Admin\AdminApl01Controller;
use App\Http\Controllers\Asesi\AsesiInboxController;
use App\Http\Controllers\Asesor\AsesorAk07Controller;
use App\Http\Controllers\Asesor\AsesorMapaController;
use App\Http\Controllers\Admin\SPTSignatureController;
use App\Http\Controllers\Admin\KelompokKerjaController;
use App\Http\Controllers\Admin\KriteriaKerjaController;
use App\Http\Controllers\Admin\PortfolioFileController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\NumberSequenceController;
use App\Http\Controllers\Admin\UnitKompetensiController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Asesi\DashboardAsesiController;
use App\Http\Controllers\Admin\BuktiPortofolioController;
use App\Http\Controllers\Admin\DelegasiPersonilController;
use App\Http\Controllers\Admin\ElemenKompetensiController;
use App\Http\Controllers\Asesi\SkemaSertifikasiController;
use App\Http\Controllers\Asesor\AsesorDashboardController;
use App\Http\Controllers\Admin\MonitoringProfileController;
use App\Http\Controllers\Admin\CertificationSchemeController;
use App\Http\Controllers\Admin\RequirementTemplateController;
use App\Http\Controllers\Admin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\UnifiedAplMonitoringController;
use App\Http\Controllers\Asesi\AsesiFormKerahasiaanController;
use App\Http\Controllers\Asesor\AsesorFormKerahasiaanController;
use App\Http\Controllers\Admin\DelegasiPersonilAsesmenController;
use App\Http\Controllers\LembagaPelatihan\LembagaDashboardController;
use App\Http\Controllers\LembagaPelatihan\LembagaAplMonitoringController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('asesor')) {
            return redirect()->route('asesor.dashboard');
        } elseif ($user->hasRole('lembagaPelatihan')) {
            return redirect()->route('lembaga-pelatihan.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('asesi.dashboard');
        }
    }

    return view('welcome');
});

// Certificate Verification (Public)
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
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // General Dashboard
    // Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| asesor Routes (asesor Only)
|--------------------------------------------------------------------------
*/
Route::prefix('asesor')
    ->middleware(['auth', 'verified', 'checkPageAccess'])
    ->name('asesor.')
    ->group(function () {
        Route::get('/dashboard', [AsesorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/assessment/{id}/detail', [AsesorDashboardController::class, 'getAssessmentDetail'])->name('assessment.detail');

        Route::prefix('mapa')
            ->name('mapa.')
            ->group(function () {
                Route::get('/', [AsesorMapaController::class, 'index'])->name('index');
                Route::get('/delegasi/{delegasi}/create', [AsesorMapaController::class, 'create'])->name('create');
                Route::post('/delegasi/{delegasi}/store', [AsesorMapaController::class, 'store'])->name('store');
                Route::get('/{mapa}/edit', [AsesorMapaController::class, 'edit'])->name('edit');
                Route::post('/{mapa}/update', [AsesorMapaController::class, 'update'])->name('update');
                Route::get('/{mapa}/validate', [AsesorMapaController::class, 'validate'])->name('validate');

                Route::put('/{mapa}/update-validated', [AsesorMapaController::class, 'updateValidated'])->name('update-validated');

                Route::get('/{mapa}/view', [AsesorMapaController::class, 'view'])->name('view');
            });

        Route::prefix('ak07')
            ->name('ak07.')
            ->group(function () {
                Route::get('create/{mapa}', [AsesorAk07Controller::class, 'create'])->name('create');
                Route::post('store/{mapa}', [AsesorAk07Controller::class, 'store'])->name('store');
                Route::get('edit/{ak07}', [AsesorAk07Controller::class, 'edit'])->name('edit');
                Route::put('update/{ak07}', [AsesorAk07Controller::class, 'update'])->name('update');
                Route::get('view/{ak07}', [AsesorAk07Controller::class, 'view'])->name('view');

                // Final Recommendation Routes (tidak berubah)
                Route::get('{ak07Id}/final-recommendation', [AsesorAk07Controller::class, 'showFinalRecommendation'])->name('final-recommendation');
                Route::post('{ak07Id}/final-recommendation/store', [AsesorAk07Controller::class, 'storeFinalRecommendation'])->name('final-recommendation.store');
            });

        Route::prefix('form-kerahasiaan')
            ->name('form-kerahasiaan.')
            ->group(function () {
                Route::get('create/{delegasi}', [AsesorFormKerahasiaanController::class, 'create'])->name('create');
                Route::post('store/{delegasi}', [AsesorFormKerahasiaanController::class, 'store'])->name('store');
                Route::get('view/{id}', [AsesorFormKerahasiaanController::class, 'view'])->name('view');
            });

        // Form Banding Route (Embed Only)
        Route::get('form-banding/{delegasi}', [AsesorFormKerahasiaanController::class, 'showFormBanding'])->name('form-banding.show');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All admin routes with proper organization and grouping
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'checkPageAccess'])
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Dashboard Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('dashboard')
            ->name('dashboard.')
            ->group(function () {
                Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
                Route::get('/scheme-stats/{id}', [SuperAdminDashboardController::class, 'getSchemeStats'])->name('scheme-stats');
                Route::get('/trending-schemes', [SuperAdminDashboardController::class, 'getTrendingSchemes'])->name('trending');
                Route::get('/activity-logs', [SuperAdminDashboardController::class, 'getActivityLogs'])->name('activity-logs');
                Route::get('/activity-stats', [SuperAdminDashboardController::class, 'getActivityStats'])->name('activity-stats');
                Route::get('/statistics-data', [SuperAdminDashboardController::class, 'getStatisticsData'])->name('statistics-data');
                Route::get('/chart-data', [SuperAdminDashboardController::class, 'getChartData'])->name('chart-data');
                Route::get('/expiring-certificates', [SuperAdminDashboardController::class, 'getExpiringCertificates'])->name('expiring-certificates');
                Route::get('/expired-certificates', [SuperAdminDashboardController::class, 'getExpiringCertificatesDataTable'])->name('expired-certificates');
            });

        // Dashboard root (kept outside for backward compatibility)
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/debug-expiring', [SuperAdminDashboardController::class, 'debugExpiringCertificates'])->name('debug.expiring');

        /*
        |--------------------------------------------------------------------------
        | MAPA Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('mapa')
            ->name('mapa.')
            ->group(function () {
                Route::get('/', [AdminMapaController::class, 'index'])->name('index');
                Route::get('/{id}', [AdminMapaController::class, 'show'])->name('show');
                Route::get('/{id}/info', [AdminMapaController::class, 'getInfo'])->name('info');

                // Review Actions
                Route::post('/{id}/approve', [AdminMapaController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [AdminMapaController::class, 'reject'])->name('reject');

                // Bulk Actions
                Route::post('/bulk-approve', [AdminMapaController::class, 'bulkApprove'])->name('bulk-approve');
                Route::post('/bulk-reject', [AdminMapaController::class, 'bulkReject'])->name('bulk-reject');
                Route::get('/{id}/ak07', [AdminMapaController::class, 'viewAk07'])->name('view-ak07');
                Route::post('/{id}/unlock-ak07', [AdminMapaController::class, 'unlockAk07'])->name('unlock-ak07');
                Route::get('/{id}/form-kerahasiaan', [AdminMapaController::class, 'viewFormKerahasiaan'])->name('view-form-kerahasiaan');
            });
        /*
        |--------------------------------------------------------------------------
        | Page Management Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('pages')
            ->name('pages.')
            ->group(function () {
                Route::get('/', [PageController::class, 'index'])->name('index');
                Route::post('/bulk-action', [PageController::class, 'bulkAction'])->name('bulk-action');
                Route::post('/reorder', [PageController::class, 'reorder'])->name('reorder');
                Route::get('/export', [PageController::class, 'export'])->name('export');
                Route::post('/import', [PageController::class, 'import'])->name('import');
                Route::post('/{page}/toggle-status', [PageController::class, 'toggleStatus'])->name('toggle-status');
            });
        Route::resource('pages', PageController::class)
            ->names('pages')
            ->except(['index']);

        /*
        |--------------------------------------------------------------------------
        | User & Role Management Routes
        |--------------------------------------------------------------------------
        */
        // Custom user routes MUST come before resource routes
        Route::get('/users/by-roles', [UserManagementController::class, 'getUsersByRole'])->name('users.by-roles');

        Route::resource('users', UserManagementController::class)
            ->names('users')
            ->except(['show']);

        Route::resource('roles', RoleController::class)->names('roles');

        /*
        |--------------------------------------------------------------------------
        | System Settings Routes
        |--------------------------------------------------------------------------
        */
        Route::resource('system-settings', SystemSettingController::class)->names('system-settings');

        Route::prefix('number-sequences')
            ->name('number-sequences.')
            ->group(function () {
                Route::post('/{numberSequence}/test', [NumberSequenceController::class, 'test'])->name('test');
            });
        Route::resource('number-sequences', NumberSequenceController::class)->names('number-sequences');
        Route::post('/generate-number', [NumberSequenceController::class, 'generate'])->name('generate-number');

        /*
        |--------------------------------------------------------------------------
        | Lembaga Pelatihan Routes
        |--------------------------------------------------------------------------
        */
        Route::resource('lembaga', \App\Http\Controllers\Admin\LembagaPelatihanController::class)->names('lembaga');

        /*
        |--------------------------------------------------------------------------
        | Fields Management Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('fields')
            ->name('fields.')
            ->group(function () {
                Route::patch('/{field}/toggle-status', [FieldController::class, 'toggleStatus'])->name('toggle-status');
            });
        Route::resource('fields', FieldController::class)
            ->names('fields')
            ->except(['show']);

        Route::get('api/fields', [FieldController::class, 'getFields'])->name('api.fields');

        /*
        |--------------------------------------------------------------------------
        | Certification Schemes Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('certification-schemes')
            ->name('certification-schemes.')
            ->group(function () {
                Route::get('/{certificationScheme}/requirements', [CertificationSchemeController::class, 'requirements'])->name('requirements');
                Route::put('/{certificationScheme}/requirements', [CertificationSchemeController::class, 'updateRequirements'])->name('update-requirements');
                Route::delete('/{certification_scheme}/reset-requirements', [CertificationSchemeController::class, 'resetRequirements'])->name('reset-requirements');
                Route::patch('/{certificationScheme}/toggle-status', [CertificationSchemeController::class, 'toggleStatus'])->name('toggle-status');
            });
        Route::resource('certification-schemes', CertificationSchemeController::class)->names('certification-schemes');

        // Certification Schemes API
        Route::get('api/schemes/by-field/{code2}', [CertificationSchemeController::class, 'getSchemesByField'])->name('certification-schemes.by-field');
        Route::get('api/templates/{template}/details', [CertificationSchemeController::class, 'getTemplateDetails'])->name('certification-schemes.get-template-details');

        /*
        |--------------------------------------------------------------------------
        | Requirement Templates Routes
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Monitoring Profile Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('monitoring-profile')
            ->name('monitoring-profile.')
            ->group(function () {
                Route::get('/', [MonitoringProfileController::class, 'index'])->name('index');
                Route::get('/data', [MonitoringProfileController::class, 'getData'])->name('getData');
                Route::get('/{id}', [MonitoringProfileController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [MonitoringProfileController::class, 'edit'])->name('edit');
                Route::put('/{id}', [MonitoringProfileController::class, 'update'])->name('update');
            });

        /*
        |--------------------------------------------------------------------------
        | Unified APL Monitoring Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('monitoring/unified-apl')
            ->name('monitoring.unified.')
            ->group(function () {
                Route::get('/', [UnifiedAplMonitoringController::class, 'index'])->name('index');
                Route::get('/statistics', [UnifiedAplMonitoringController::class, 'getStatistics'])->name('statistics');
                Route::post('/data', [UnifiedAplMonitoringController::class, 'getData'])->name('data');

                // APL 01 Monitoring
                Route::prefix('apl01')
                    ->name('apl01.')
                    ->group(function () {
                        Route::get('/{apl}/review-data', [UnifiedAplMonitoringController::class, 'getApl01ReviewData'])->name('review-data');
                        Route::post('/{apl}/approve', [UnifiedAplMonitoringController::class, 'approveApl01'])->name('approve');
                        Route::post('/{apl}/reject', [UnifiedAplMonitoringController::class, 'rejectApl01'])->name('reject');
                        Route::post('/{apl}/reopen', [UnifiedAplMonitoringController::class, 'reopenApl01'])->name('reopen');
                        Route::post('/bulk-action', [UnifiedAplMonitoringController::class, 'bulkAction'])->name('bulk-action');
                    });

                // APL 02 Monitoring
                Route::prefix('apl02')
                    ->name('apl02.')
                    ->group(function () {
                        Route::get('/{apl02}/review-data', [UnifiedAplMonitoringController::class, 'getApl02ReviewData'])->name('review-data');
                        Route::post('/{apl02}/approve', [UnifiedAplMonitoringController::class, 'approveApl02'])->name('approve');
                        Route::post('/{apl02}/reject', [UnifiedAplMonitoringController::class, 'rejectApl02'])->name('reject');
                        Route::post('/{apl02}/reopen', [UnifiedAplMonitoringController::class, 'reopenApl02'])->name('reopen');
                        Route::get('/evidence/{evidenceId}/preview', [UnifiedAplMonitoringController::class, 'previewEvidence'])->name('evidence.preview');
                        Route::get('/evidence/{evidenceId}/download', [UnifiedAplMonitoringController::class, 'downloadEvidence'])->name('evidence.download');
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | APL 01 Management Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('apl01')
            ->name('apl01.')
            ->group(function () {
                Route::get('/', [AdminApl01Controller::class, 'index'])->name('index');
                Route::get('/statistics', [AdminApl01Controller::class, 'getStatistics'])->name('statistics');
                Route::post('/data', [AdminApl01Controller::class, 'data'])->name('data');
                Route::post('/export', [AdminApl01Controller::class, 'export'])->name('export');
                Route::post('/bulk-action', [AdminApl01Controller::class, 'bulkAction'])->name('bulk-action');

                Route::get('/{apl}', [AdminApl01Controller::class, 'show'])->name('show');
                Route::get('/{apl}/edit', [AdminApl01Controller::class, 'edit'])->name('edit');
                Route::put('/{apl}', [AdminApl01Controller::class, 'update'])->name('update');
                Route::delete('/{apl}', [AdminApl01Controller::class, 'destroy'])->name('destroy');

                Route::get('/{apl}/review-data', [AdminApl01Controller::class, 'getReviewData'])->name('review-data');
                Route::post('/{apl}/approve', [AdminApl01Controller::class, 'approve'])->name('approve');
                Route::post('/{apl}/reject', [AdminApl01Controller::class, 'reject'])->name('reject');
                Route::post('/{apl}/reopen', [AdminApl01Controller::class, 'reopen'])->name('reopen');
                Route::post('/{apl}/set-review', [AdminApl01Controller::class, 'setUnderReview'])->name('set-review');
                Route::post('/{apl}/return-revision', [AdminApl01Controller::class, 'returnForRevision'])->name('return-revision');
            });

        /*
        |--------------------------------------------------------------------------
        | APL 02 Admin Management Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('apl02')
            ->name('apl02.')
            ->group(function () {
                Route::get('/', [Apl02Controller::class, 'adminIndex'])->name('index');
                Route::get('/{apl02}', [Apl02Controller::class, 'adminShow'])->name('show');
                Route::post('/export', [Apl02Controller::class, 'export'])->name('export');
            });

        /*
        |--------------------------------------------------------------------------
        | TUK Management Routes
        |--------------------------------------------------------------------------
        */

        Route::prefix('tuk-requests')
            ->name('tuk-requests.')
            ->group(function () {
                // ==========================================
                // MAIN INDEX
                // ==========================================
                Route::get('/', [AdminTukController::class, 'index'])->name('index');

                // ==========================================
                // DATATABLES AJAX ENDPOINTS
                // ==========================================
                Route::get('/sewaktu-data', [AdminTukController::class, 'getTukSewaktuData'])->name('sewaktu-data');
                Route::get('/mandiri-data', [AdminTukController::class, 'getTukMandiriData'])->name('mandiri-data');

                // ==========================================
                // RESCHEDULE MONITORING (NEW)
                // ==========================================
                Route::get('/reschedule-monitoring', [AdminTukController::class, 'rescheduleMonitoring'])->name('reschedule-monitoring');
                Route::get('/reschedule-data', [AdminTukController::class, 'getRescheduleData'])->name('reschedule-data');
                Route::get('/reschedule-detail/{history}', [AdminTukController::class, 'getRescheduleDetail'])->name('reschedule-detail');

                // ==========================================
                // COMBINED REVIEW & REKOMENDASI LSP (APL01)
                // ==========================================
                Route::get('/combined-review/{apl01}', [AdminTukController::class, 'getCombinedReviewDetail'])->name('combined-review');
                Route::get('/ttd-rekomendasi-form/{apl01}', [AdminTukController::class, 'getTTDRekomendasiForm'])->name('ttd-rekomendasi-form');
                Route::get('/ttd-rekomendasi-combined/{apl01}', [AdminTukController::class, 'getTTDRekomendasiCombined'])->name('ttd-rekomendasi-combined');
                Route::get('/view-rekomendasi-lsp/{apl01}', [AdminTukController::class, 'viewRekomendasiLSP'])->name('view-rekomendasi-lsp');
                Route::post('/store-rekomendasi/{apl01}', [AdminTukController::class, 'storeRekomendasi'])->name('store-rekomendasi');

                // ==========================================
                // APL APPROVAL & REJECTION - COMBINED
                // ==========================================
                Route::post('/approve-combined/{apl01}', [AdminTukController::class, 'approveCombined'])->name('approve-combined');
                Route::post('/reject-combined/{apl01}', [AdminTukController::class, 'rejectCombined'])->name('reject-combined');

                // ==========================================
                // APL APPROVAL & REJECTION - INDIVIDUAL (Optional/Fallback)
                // ==========================================
                Route::post('/approve-apl01/{apl01}', [AdminTukController::class, 'approveApl01'])->name('approve-apl01');
                Route::post('/approve-apl02/{apl02}', [AdminTukController::class, 'approveApl02'])->name('approve-apl02');
                Route::post('/reject-apl01/{apl01}', [AdminTukController::class, 'rejectApl01'])->name('reject-apl01');
                Route::post('/reject-apl02/{apl02}', [AdminTukController::class, 'rejectApl02'])->name('reject-apl02');

                // ==========================================
                // APL01 SPECIFIC ROUTES (TUK Mandiri)
                // ==========================================
                Route::get('/apl01/{apl01}/view', [AdminTukController::class, 'viewApl01'])->name('view-apl01');
                Route::get('/apl01/{apl01}/delegasi-data', [AdminTukController::class, 'getDelegasiDataApl01'])->name('apl01.delegasi-data');
                Route::post('/apl01/{apl01}/reschedule', [AdminTukController::class, 'rescheduleMandiri'])->name('apl01.reschedule');
                Route::get('/apl01/{apl01}/tuk-mandiri', [AdminTukController::class, 'viewTukMandiri'])->name('view-tuk-mandiri');

                // ==========================================
                // USER DOCUMENTS
                // ==========================================
                Route::get('/user-documents/{document}/download', function ($documentId) {
                    $document = \App\Models\UserDocument::findOrFail($documentId);

                    if (!$document->file_exists) {
                        abort(404, 'File tidak ditemukan');
                    }

                    return $document->download();
                })->name('user-documents.download');

                // ==========================================
                // TUK REQUEST SPECIFIC ROUTES (TUK Sewaktu)
                // ==========================================
                Route::get('/{tukRequest}', [AdminTukController::class, 'show'])->name('show');
                Route::post('/{tukRequest}/recommend', [AdminTukController::class, 'recommend'])->name('recommend');
                Route::get('/{tukRequest}/delegasi-data', [AdminTukController::class, 'getDelegasiData'])->name('delegasi-data');
                Route::post('/{tukRequest}/reschedule', [AdminTukController::class, 'reschedule'])->name('reschedule');
            });

        Route::prefix('delegasi-personil')
            ->name('delegasi.')
            ->group(function () {
                Route::post('/store', [DelegasiPersonilAsesmenController::class, 'store'])->name('store');
                Route::get('/{delegasi}', [DelegasiPersonilAsesmenController::class, 'show'])->name('show');
                Route::get('/{delegasi}/view', [DelegasiPersonilAsesmenController::class, 'showView'])->name('show.view');
                Route::put('/{delegasi}', [DelegasiPersonilAsesmenController::class, 'update'])->name('update');
                Route::delete('/{delegasi}', [DelegasiPersonilAsesmenController::class, 'destroy'])->name('destroy');

                // ✅ TAMBAHAN: Generate SPT manually (jika diperlukan)
                Route::post('/{delegasi}/generate-spt', [SPTSignatureController::class, 'generateSPTs'])->name('generate-spt');
            });

        Route::prefix('apl-review')
            ->name('apl-review.')
            ->group(function () {
                // APL 01 Review
                Route::get('/apl01/{apl}/review-detail', [AplReviewController::class, 'getApl01ReviewDetail'])->name('apl01.review-detail');
                Route::post('/apl01/{apl}/approve', [AplReviewController::class, 'approveApl01'])->name('apl01.approve');
                Route::post('/apl01/{apl}/reject', [AplReviewController::class, 'rejectApl01'])->name('apl01.reject');

                // APL 02 Review
                Route::get('/apl02/{apl02}/review-detail', [AplReviewController::class, 'getApl02ReviewDetail'])->name('apl02.review-detail');
                Route::post('/apl02/{apl02}/approve', [AplReviewController::class, 'approveApl02'])->name('apl02.approve');
                Route::post('/apl02/{apl02}/reject', [AplReviewController::class, 'rejectApl02'])->name('apl02.reject');
            });

        Route::prefix('spt-signatures')
            ->name('spt-signatures.')
            ->group(function () {
                Route::get('/', [SPTSignatureController::class, 'index'])->name('index');
                Route::get('/data', [SPTSignatureController::class, 'getData'])->name('data');
                Route::get('/{id}', [SPTSignatureController::class, 'show'])->name('show');
                Route::get('/{id}/summary', [SPTSignatureController::class, 'getSummary'])->name('summary');

                // Signing routes
                Route::post('/sign-bulk', [SPTSignatureController::class, 'signBulk'])->name('sign-bulk');
                Route::post('/{id}/sign', [SPTSignatureController::class, 'sign'])->name('sign');

                // Download routes
                Route::get('/{id}/download/{type}', [SPTSignatureController::class, 'downloadSPT'])
                    ->where('type', 'verifikator|observer|asesor')
                    ->name('download');
                Route::get('/{id}/preview/{type}', [SptSignatureController::class, 'preview'])->name('preview');
            });

        // Generate SPT from delegation (called automatically)
        Route::post('/delegasi-personil/{id}/generate-spt', [SPTSignatureController::class, 'generateSPTs'])->name('delegasi.generate-spt');
        /*
        |--------------------------------------------------------------------------
        | Scheme-specific Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('schemes/{scheme}')
            ->name('schemes.')
            ->group(function () {
                // Unit Kompetensi Routes
                Route::prefix('unit-kompetensi')
                    ->name('unit-kompetensi.')
                    ->group(function () {
                        Route::get('/', [UnitKompetensiController::class, 'index'])->name('index');
                        Route::get('/create', [UnitKompetensiController::class, 'create'])->name('create');
                        Route::post('/', [UnitKompetensiController::class, 'store'])->name('store');
                        Route::get('/export', [UnitKompetensiController::class, 'export'])->name('export');
                        Route::post('/reorder', [UnitKompetensiController::class, 'reorder'])->name('reorder');

                        Route::get('/{unitKompetensi}', [UnitKompetensiController::class, 'show'])->name('show');
                        Route::get('/{unitKompetensi}/edit', [UnitKompetensiController::class, 'edit'])->name('edit');
                        Route::put('/{unitKompetensi}', [UnitKompetensiController::class, 'update'])->name('update');
                        Route::delete('/{unitKompetensi}', [UnitKompetensiController::class, 'destroy'])->name('destroy');
                        Route::get('/{unitKompetensi}/duplicate', [UnitKompetensiController::class, 'duplicateForm'])->name('duplicate.form');
                        Route::post('/{unitKompetensi}/duplicate', [UnitKompetensiController::class, 'duplicateStore'])->name('duplicate.store');
                        Route::patch('/{unitKompetensi}/toggle-status', [UnitKompetensiController::class, 'toggleStatus'])->name('toggle-status');
                        Route::post('/{unitKompetensi}/portfolio-files/upload', [UnitKompetensiController::class, 'uploadPortfolioFiles'])->name('portfolio-files.upload');

                        // Elemen Kompetensi
                        Route::post('/{unitKompetensi}/elemen-kompetensi', [ElemenKompetensiController::class, 'store'])->name('elemen-kompetensi.store');
                        Route::put('/{unitKompetensi}/elemen-kompetensi/{elemen}', [ElemenKompetensiController::class, 'update'])->name('elemen-kompetensi.update');
                        Route::delete('/{unitKompetensi}/elemen-kompetensi/{elemen}', [ElemenKompetensiController::class, 'destroy'])->name('elemen-kompetensi.destroy');

                        // Kriteria Kerja
                        Route::post('/{unitKompetensi}/elemen-kompetensi/{elemen}/kriteria-kerja', [KriteriaKerjaController::class, 'store'])->name('elemen-kompetensi.kriteria-kerja.store');
                        Route::put('/{unitKompetensi}/elemen-kompetensi/{elemen}/kriteria-kerja/{kriteria}', [KriteriaKerjaController::class, 'update'])->name('elemen-kompetensi.kriteria-kerja.update');
                        Route::delete('/{unitKompetensi}/elemen-kompetensi/{elemen}/kriteria-kerja/{kriteria}', [KriteriaKerjaController::class, 'destroy'])->name('elemen-kompetensi.kriteria-kerja.destroy');
                    });

                // Kelompok Kerja Routes
                Route::prefix('kelompok-kerja')
                    ->name('kelompok-kerja.')
                    ->group(function () {
                        Route::get('/', [KelompokKerjaController::class, 'index'])->name('index');
                        Route::get('/create', [KelompokKerjaController::class, 'create'])->name('create');
                        Route::post('/', [KelompokKerjaController::class, 'store'])->name('store');
                        Route::get('/export', [KelompokKerjaController::class, 'export'])->name('export');
                        Route::post('/reorder', [KelompokKerjaController::class, 'reorder'])->name('reorder');
                        Route::post('/bulk-toggle-status', [KelompokKerjaController::class, 'bulkToggleStatus'])->name('bulk-toggle-status');

                        Route::get('/{kelompokKerja}', [KelompokKerjaController::class, 'show'])->name('show');
                        Route::get('/{kelompokKerja}/edit', [KelompokKerjaController::class, 'edit'])->name('edit');
                        Route::put('/{kelompokKerja}', [KelompokKerjaController::class, 'update'])->name('update');
                        Route::delete('/{kelompokKerja}', [KelompokKerjaController::class, 'destroy'])->name('destroy');
                        Route::post('/{kelompokKerja}/toggle-status', [KelompokKerjaController::class, 'toggleStatus'])->name('toggle-status');
                        Route::post('/{kelompokKerja}/duplicate', [KelompokKerjaController::class, 'duplicate'])->name('duplicate');

                        // Unit Kompetensi Management
                        Route::get('/{kelompokKerja}/manage-unit-kompetensi', [KelompokKerjaController::class, 'manageUnitKompetensi'])->name('manage-unit-kompetensi');
                        Route::post('/{kelompokKerja}/update-unit-kompetensi', [KelompokKerjaController::class, 'updateUnitKompetensi'])->name('update-unit-kompetensi');
                        Route::post('/{kelompokKerja}/add-unit-kompetensi', [KelompokKerjaController::class, 'addUnitKompetensi'])->name('add-unit-kompetensi');
                        Route::delete('/{kelompokKerja}/remove-unit-kompetensi', [KelompokKerjaController::class, 'removeUnitKompetensi'])->name('remove-unit-kompetensi');
                        Route::patch('/{kelompokKerja}/toggle-unit-status', [KelompokKerjaController::class, 'toggleUnitStatus'])->name('toggle-unit-status');
                        Route::post('/{kelompokKerja}/reorder-unit-kompetensi', [KelompokKerjaController::class, 'reorderUnitKompetensi'])->name('reorder-unit-kompetensi');
                        Route::patch('/{kelompokKerja}/bulk-toggle-unit-status', [KelompokKerjaController::class, 'bulkToggleUnitStatus'])->name('bulk-toggle-unit-status');

                        // Bukti Portofolio
                        Route::prefix('{kelompokKerja}/bukti-portofolio')
                            ->name('bukti-portofolio.')
                            ->group(function () {
                                Route::get('/', [BuktiPortofolioController::class, 'index'])->name('index');
                                Route::post('/', [BuktiPortofolioController::class, 'store'])->name('store');
                                Route::get('/available', [BuktiPortofolioController::class, 'getAvailableBukti'])->name('available');
                                Route::post('/batch', [BuktiPortofolioController::class, 'storeBatch'])->name('store-batch');
                                Route::post('/bulk-action', [BuktiPortofolioController::class, 'bulkAction'])->name('bulk-action');
                                Route::post('/manage-groups', [BuktiPortofolioController::class, 'manageGroups'])->name('manage-groups');
                                Route::post('/reorder', [BuktiPortofolioController::class, 'reorder'])->name('reorder');
                                Route::post('/remove-from-group', [BuktiPortofolioController::class, 'removeGroup'])->name('remove-from-group');
                                Route::get('/dependency-options', [BuktiPortofolioController::class, 'getDependencyOptions'])->name('dependency-options');

                                Route::get('/{bukti}', [BuktiPortofolioController::class, 'show'])->name('show');
                                Route::put('/{bukti}', [BuktiPortofolioController::class, 'update'])->name('update');
                                Route::delete('/{bukti}', [BuktiPortofolioController::class, 'destroy'])->name('destroy');
                                Route::patch('/{bukti}/toggle-status', [BuktiPortofolioController::class, 'toggleStatus'])->name('toggle-status');
                            });
                    });

                // Portfolio Files Routes
                Route::prefix('unit-kompetensi/{unit}/portfolio-files')
                    ->name('unit-kompetensi.portfolio-files.')
                    ->group(function () {
                        Route::get('/', [PortfolioFileController::class, 'index'])->name('index');
                        Route::post('/', [PortfolioFileController::class, 'store'])->name('store');
                        Route::post('/duplicate', [PortfolioFileController::class, 'duplicate'])->name('duplicate');
                        Route::put('/{portfolioFile}', [PortfolioFileController::class, 'update'])->name('update');
                        Route::delete('/{portfolioFile}', [PortfolioFileController::class, 'destroy'])->name('destroy');
                        Route::post('/{portfolioFile}/toggle-status', [PortfolioFileController::class, 'toggleStatus'])->name('toggle-status');
                    });
            });
    });

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api/admin')
    ->name('api.admin.')
    ->middleware(['auth', 'verified', 'role:superadmin'])
    ->group(function () {
        // Scheme Statistics
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

        // Unit Search
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

        // Kelompok Search
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

/*
|--------------------------------------------------------------------------
| Asesi Routes
|--------------------------------------------------------------------------
*/

Route::prefix('asesi')
    ->middleware(['auth', 'verified', 'role:asesi'])
    ->name('asesi.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardAsesiController::class, 'index'])->name('dashboard');

        // inbox
        Route::get('/inbox', [AsesiInboxController::class, 'index'])->name('inbox.index');
        Route::post('/inbox/data', [AsesiInboxController::class, 'getData'])->name('inbox.data');
        Route::get('/inbox/statistics', [AsesiInboxController::class, 'getStatistics'])->name('inbox.statistics');
        Route::get('/inbox/export', [AsesiInboxController::class, 'exportData'])->name('inbox.export');

        // Data Pribadi
        Route::get('/data-pribadi', [DataPribadiController::class, 'index'])->name('data-pribadi.index');
        Route::post('/data-pribadi', [DataPribadiController::class, 'store'])->name('data-pribadi.store');
        Route::get('/data-pribadi/document/{id}/download', [DataPribadiController::class, 'downloadDocument'])->name('data-pribadi.download');
        Route::delete('/data-pribadi/document/{id}', [DataPribadiController::class, 'deleteDocument'])->name('data-pribadi.delete-document');

        // Skema Sertifikasi
        Route::get('skema-sertifikasi', [SkemaSertifikasiController::class, 'index'])->name('skema-sertifikasi.index');

        // APL 01
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

        // APL 02
        Route::prefix('apl02')
            ->name('apl02.')
            ->group(function () {
                Route::get('/', [Apl02Controller::class, 'index'])->name('index');
                Route::get('/create', [Apl02Controller::class, 'create'])->name('create');
                Route::post('/', [Apl02Controller::class, 'store'])->name('store');
                Route::get('/{apl02}', [Apl02Controller::class, 'show'])->name('show');
                Route::get('/{apl02}/edit', [Apl02Controller::class, 'edit'])->name('edit');
                Route::post('/{apl02}/update', [Apl02Controller::class, 'update'])->name('update');

                // Evidence Management
                Route::post('/{apl02}/upload-evidence', [Apl02Controller::class, 'uploadEvidence'])->name('upload-evidence');
                Route::delete('/{apl02}/evidence/{evidenceId}', [Apl02Controller::class, 'deleteEvidence'])->name('delete-evidence');
                Route::get('/{apl02}/evidence/{evidenceId}/download', [Apl02Controller::class, 'downloadEvidence'])->name('download-evidence');
                Route::get('/{apl02}/evidence/{evidenceId}/preview', [Apl02Controller::class, 'previewEvidence'])->name('preview-evidence');

                // Assessment Progress & Export
                Route::get('/{apl02}/progress', [Apl02Controller::class, 'getAssessmentProgress'])->name('progress');
                Route::get('/{apl02}/preview', [Apl02Controller::class, 'preview'])->name('preview');
                Route::get('/{apl02}/export-pdf', [Apl02Controller::class, 'exportPdf'])->name('export-pdf');
            });

        // Region API Routes (untuk form APL01)
        Route::prefix('regions')
            ->name('regions.')
            ->group(function () {
                Route::get('/provinces', [App\Http\Controllers\Asesi\Apl01Controller::class, 'getProvinces'])->name('provinces');
                Route::get('/cities/{provinceId}', [App\Http\Controllers\Asesi\Apl01Controller::class, 'getCities'])->name('cities');
            });
    });

Route::prefix('asesi')
    ->middleware(['auth', 'verified', 'role:asesi'])
    ->name('asesi.')
    ->group(function () {
        Route::prefix('ak07')
            ->name('ak07.')
            ->group(function () {
                Route::get('/', [AsesiAk07Controller::class, 'index'])->name('index');
                Route::get('{ak07}/sign', [AsesiAk07Controller::class, 'sign'])->name('sign');
                Route::post('{ak07}/submit-signature', [AsesiAk07Controller::class, 'submitSignature'])->name('submit-signature');
                Route::get('{ak07}/view', [AsesiAk07Controller::class, 'view'])->name('view');
            });

        // TUK Request Routes
        Route::prefix('tuk')
            ->name('tuk.')
            ->group(function () {
                // Show TUK form for specific APL01
                Route::get('/{apl01}/form', [App\Http\Controllers\Asesi\TukRequestController::class, 'show'])->name('form');

                // Store/Update TUK request
                Route::post('/{apl01}/store', [App\Http\Controllers\Asesi\TukRequestController::class, 'store'])->name('store');

                // Show TUK Mandiri PDF
                Route::get('/{apl01}/mandiri-pdf', [App\Http\Controllers\Asesi\TukRequestController::class, 'showMandiriPdf'])->name('mandiri-pdf');

                // Check TUK status (AJAX)
                Route::get('/{apl01}/status', [App\Http\Controllers\Asesi\TukRequestController::class, 'checkStatus'])->name('status');

                Route::get('/mandiri/{apl01}', [AsesiInboxController::class, 'viewTukMandiri'])->name('view-mandiri');
            });

        Route::prefix('form-kerahasiaan')
            ->name('form-kerahasiaan.')
            ->group(function () {
                Route::get('/', [AsesiFormKerahasiaanController::class, 'index'])->name('index');
                Route::get('sign/{id}', [AsesiFormKerahasiaanController::class, 'sign'])->name('sign');
                Route::post('store-signature/{id}', [AsesiFormKerahasiaanController::class, 'storeSignature'])->name('store-signature');
                Route::get('view/{id}', [AsesiFormKerahasiaanController::class, 'view'])->name('view');
            });
    });

/*
|--------------------------------------------------------------------------
| Shared Routes (Multiple Roles)
|--------------------------------------------------------------------------
*/

// APL 02 Routes for both Asesi and Admin
Route::prefix('apl02-shared')
    ->middleware(['auth', 'verified'])
    ->name('apl02.shared.')
    ->group(function () {
        // Routes that both asesi and superadmin can access
        Route::middleware('role:asesi,superadmin')->group(function () {
            Route::get('/{apl02}/view', [Apl02Controller::class, 'sharedView'])->name('view');
            Route::get('/{apl02}/download-pdf', [Apl02Controller::class, 'downloadPdf'])->name('download-pdf');
        });

        // Admin only routes for APL02 management
        Route::middleware('role:superadmin')->group(function () {
            Route::post('/{apl02}/admin-approve', [Apl02Controller::class, 'adminApprove'])->name('admin-approve');
            Route::post('/{apl02}/admin-reject', [Apl02Controller::class, 'adminReject'])->name('admin-reject');
            Route::post('/{apl02}/admin-comment', [Apl02Controller::class, 'adminComment'])->name('admin-comment');
        });
    });

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Lembaga Pelatihan Routes
|--------------------------------------------------------------------------
*/
// routes/web.php - Tambahkan route untuk Lembaga Pelatihan

Route::middleware(['auth', 'role:lembagaPelatihan'])
    ->prefix('lembaga-pelatihan')
    ->name('lembaga-pelatihan.')
    ->group(function () {
        Route::get('/dashboard', [LembagaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/statistics/data', [LembagaDashboardController::class, 'getStatisticsData'])->name('statistics.data');
        Route::get('/lembaga-pelatihan/chart-data', [LembagaDashboardController::class, 'getChartData'])->name('chart-data');

        // Monitoring APL Routes
        Route::get('/monitoring/apl', [LembagaAplMonitoringController::class, 'index'])->name('monitoring.apl.index');
        Route::get('/monitoring/apl/statistics', [LembagaAplMonitoringController::class, 'getStatistics'])->name('monitoring.apl.statistics');
        Route::get('/monitoring/apl/data', [LembagaAplMonitoringController::class, 'getData'])->name('monitoring.apl.data');

        // APL 01 Routes
        Route::get('/monitoring/apl01/{apl}/review', [LembagaAplMonitoringController::class, 'getApl01ReviewData'])->name('monitoring.apl01.review');
        Route::post('/monitoring/apl/apl01/{apl}/approve', [LembagaAplMonitoringController::class, 'approveApl01'])->name('monitoring.apl01.approve');
        Route::post('/monitoring/apl/apl01/{apl}/reject', [LembagaAplMonitoringController::class, 'rejectApl01'])->name('monitoring.apl01.reject');
        Route::post('/monitoring/apl/apl01/{apl}/reopen', [LembagaAplMonitoringController::class, 'reopenApl01'])->name('monitoring.apl01.reopen');

        // APL 02 Routes
        Route::get('/monitoring/apl02/{apl02}/review', [LembagaAplMonitoringController::class, 'getApl02ReviewData'])->name('monitoring.apl02.review');
        Route::post('/monitoring/apl/apl02/{apl02}/approve', [LembagaAplMonitoringController::class, 'approveApl02'])->name('monitoring.apl02.approve');
        Route::post('/monitoring/apl/apl02/{apl02}/reject', [LembagaAplMonitoringController::class, 'rejectApl02'])->name('monitoring.apl02.reject');
        Route::post('/monitoring/apl/apl02/{apl02}/reopen', [LembagaAplMonitoringController::class, 'reopenApl02'])->name('monitoring.apl02.reopen');

        // Evidence Routes
        Route::get('/monitoring/apl02/evidence/{evidence}/preview', [LembagaAplMonitoringController::class, 'previewEvidence'])->name('monitoring.apl02.preview-evidence');
        Route::get('/monitoring/apl02/evidence/{evidence}/download', [LembagaAplMonitoringController::class, 'downloadEvidence'])->name('monitoring.apl02.download-evidence');
    });

require __DIR__ . '/auth.php';
