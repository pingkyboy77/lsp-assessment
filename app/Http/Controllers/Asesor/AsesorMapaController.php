<?php

namespace App\Http\Controllers\Asesor;

use App\Models\Mapa;
use App\Models\DelegasiPersonilAsesmen;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AsesorMapaController extends Controller
{
    /**
     * Display list of asesi yang perlu perencanaan asesmen
     */
    public function index(Request $request)
    {
        $asesor = Auth::user();

        // Base query
        $query = DelegasiPersonilAsesmen::with(['asesi:id,name,email', 'certificationScheme:id,code_1,nama', 'apl01:id,nomor_apl_01,nama_lengkap,status', 'apl01.apl02:id,apl_01_id,nomor_apl_02,status', 'mapa'])
            ->where('asesor_id', $asesor->id)
            ->whereHas('apl01', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereHas('apl01.apl02', function ($q) {
                $q->where('status', 'approved');
            });

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('asesi', function ($sq) use ($search) {
                    $sq->where('name', 'ILIKE', "%{$search}%")->orWhere('email', 'ILIKE', "%{$search}%");
                })->orWhereHas('certificationScheme', function ($sq) use ($search) {
                    $sq->where('code_1', 'ILIKE', "%{$search}%")->orWhere('nama', 'ILIKE', "%{$search}%");
                });
            });
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('tanggal_pelaksanaan_asesmen', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    Log::warning('Invalid date range format: ' . $request->date_range);
                }
            }
        }

        // Filter by MAPA status
        if ($request->filled('status_mapa')) {
            $statusMapa = $request->status_mapa;

            if ($statusMapa === 'belum') {
                $query->doesntHave('mapa');
            } else {
                $query->whereHas('mapa', function ($q) use ($statusMapa) {
                    $q->where('status', $statusMapa);
                });
            }
        }

        $query->orderBy('tanggal_pelaksanaan_asesmen', 'desc');

        // Calculate stats
        $stats = $this->calculateStats($asesor->id);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $delegasiList = $query->paginate($perPage)->withQueryString();

        // AJAX request handling
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $delegasiList->items(),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $delegasiList->currentPage(),
                    'last_page' => $delegasiList->lastPage(),
                    'per_page' => $delegasiList->perPage(),
                    'total' => $delegasiList->total(),
                ],
                'html' => view('asesor.mapa.partials.table-rows', compact('delegasiList'))->render(),
                'pagination_html' => $delegasiList
                    ->appends(request()->query())
                    ->links()
                    ->render(),
            ]);
        }

        return view('asesor.mapa.index', compact('delegasiList', 'stats'));
    }

    /**
     * Calculate statistics for dashboard
     */
    private function calculateStats($asesorId)
    {
        $baseQuery = DelegasiPersonilAsesmen::where('asesor_id', $asesorId)
            ->whereHas('apl01', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereHas('apl01.apl02', function ($q) {
                $q->where('status', 'approved');
            });

        $total = (clone $baseQuery)->count();

        $validated = (clone $baseQuery)
            ->whereHas('mapa', function ($q) {
                $q->where('status', 'validated');
            })
            ->count();

        $perluDibuat = (clone $baseQuery)->doesntHave('mapa')->count();

        $draft = (clone $baseQuery)
            ->whereHas('mapa', function ($q) {
                $q->where('status', 'draft');
            })
            ->count();

        $perluValidasi = (clone $baseQuery)
            ->whereHas('mapa', function ($q) {
                $q->where('status', 'approved');
            })
            ->count();

        return [
            'total' => $total,
            'validated' => $validated,
            'perlu_dibuat' => $perluDibuat,
            'draft' => $draft,
            'perlu_validasi' => $perluValidasi,
        ];
    }

    /**
     * Show REVIEW page: APL 01, APL 02, dan form perencanaan MAPA
     */
    public function create($delegasiId)
    {
        $asesor = Auth::user();

        $delegasi = DelegasiPersonilAsesmen::with(['asesi:id,name,email', 'certificationScheme.kelompokKerjas.unitKompetensis.elemenKompetensis.kriteriaKerjas', 'certificationScheme.kelompokKerjas.unitKompetensis.portfolioFiles', 'apl01.selectedRequirementTemplate.activeItems', 'apl01.apl02', 'apl02.elementAssessments.elemenKompetensi.unitKompetensi', 'apl02.evidenceSubmissions.portfolioFile', 'mapa'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($delegasiId);

        // Jika MAPA sudah ada, redirect ke edit
        if ($delegasi->mapa) {
            return redirect()->route('asesor.mapa.edit', $delegasi->mapa->id);
        }

        $kelompokKerjas = $delegasi->certificationScheme
            ->kelompokKerjas()
            ->with([
                'unitKompetensis.elemenKompetensis.kriteriaKerjas',
                'unitKompetensis.portfolioFiles' => function ($q) {
                    $q->where('is_active', true);
                },
            ])
            ->orderBy('sort_order')
            ->get();

        return view('asesor.mapa.create', compact('delegasi', 'kelompokKerjas'));
    }

    /**
     * Store MAPA (no signature required at this stage)
     */
    public function store(Request $request, $delegasiId)
    {
        $asesor = Auth::user();
        $delegasi = DelegasiPersonilAsesmen::where('asesor_id', $asesor->id)->findOrFail($delegasiId);

        $validated = $request->validate([
            'p_level' => 'required|integer|min:0',
            'catatan_asesor' => 'nullable|string',
            'submit_action' => 'required|in:draft,submit',
        ]);

        try {
            DB::beginTransaction();

            $totalKelompok = $delegasi->certificationScheme->kelompokKerjas()->count();

            if ($validated['p_level'] > $totalKelompok) {
                throw new \Exception('P-Level tidak boleh melebihi jumlah kelompok kerja');
            }

            // Create MAPA
            $mapa = Mapa::create([
                'delegasi_personil_asesmen_id' => $delegasi->id,
                'asesor_id' => $asesor->id,
                'apl01_id' => $delegasi->apl01_id,
                'apl02_id' => $delegasi->apl01->apl02->id ?? null,
                'certification_scheme_id' => $delegasi->certification_scheme_id,
                'nomor_mapa' => Mapa::generateNomorMapa($delegasi->certification_scheme_id, $asesor->id),
                'p_level' => $validated['p_level'],
                'status' => 'draft',
                'catatan_asesor' => $validated['catatan_asesor'] ?? null,
            ]);

            // If submit action
            if ($validated['submit_action'] === 'submit') {
                $mapa->submit();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['submit_action'] === 'submit' ? 'MAPA berhasil disimpan dan disubmit' : 'MAPA berhasil disimpan sebagai draft',
                'data' => [
                    'mapa_id' => $mapa->id,
                    'nomor_mapa' => $mapa->nomor_mapa,
                    'p_level' => $mapa->p_level,
                    'mapa_code' => $mapa->mapa_code,
                    'status' => $mapa->status,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal menyimpan MAPA: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show form edit MAPA
     */
    public function edit($mapaId)
    {
        $asesor = Auth::user();

        $mapa = Mapa::with(['delegasi.asesi', 'delegasi.certificationScheme.kelompokKerjas.unitKompetensis.elemenKompetensis.kriteriaKerjas', 'delegasi.certificationScheme.kelompokKerjas.unitKompetensis.portfolioFiles', 'delegasi.apl01.selectedRequirementTemplate.activeItems', 'delegasi.apl01.apl02', 'delegasi.apl02.elementAssessments.elemenKompetensi.unitKompetensi', 'delegasi.apl02.evidenceSubmissions.portfolioFile'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($mapaId);

        // Only allow editing if status is draft or rejected
        if (!$mapa->canBeEdited()) {
            return redirect()->route('asesor.mapa.view', $mapa->id)->with('error', 'MAPA hanya dapat diedit jika berstatus draft atau rejected');
        }

        $kelompokKerjas = $mapa->certificationScheme
            ->kelompokKerjas()
            ->with([
                'unitKompetensis.elemenKompetensis.kriteriaKerjas',
                'unitKompetensis.portfolioFiles' => function ($q) {
                    $q->where('is_active', true);
                },
            ])
            ->orderBy('sort_order')
            ->get();

        return view('asesor.mapa.edit', compact('mapa', 'kelompokKerjas'));
    }

    /**
     * Update MAPA (no signature required at this stage)
     */
    public function update(Request $request, $mapaId)
    {
        $asesor = Auth::user();
        $mapa = Mapa::where('asesor_id', $asesor->id)->findOrFail($mapaId);

        if (!$mapa->canBeEdited()) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'MAPA hanya dapat diupdate jika berstatus draft atau rejected',
                ],
                403,
            );
        }

        $validated = $request->validate([
            'p_level' => 'required|integer|min:0',
            'catatan_asesor' => 'nullable|string',
            'submit_action' => 'required|in:draft,submit',
        ]);

        try {
            DB::beginTransaction();

            $totalKelompok = $mapa->certificationScheme->kelompokKerjas()->count();

            if ($validated['p_level'] > $totalKelompok) {
                throw new \Exception('P-Level tidak boleh melebihi jumlah kelompok kerja');
            }

            // Update MAPA
            $mapa->update([
                'p_level' => $validated['p_level'],
                'catatan_asesor' => $validated['catatan_asesor'] ?? null,
                'status' => 'draft', // Reset to draft
            ]);

            // If submit action
            if ($validated['submit_action'] === 'submit') {
                $mapa->submit();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['submit_action'] === 'submit' ? 'MAPA berhasil diupdate dan disubmit' : 'MAPA berhasil diupdate',
                'data' => [
                    'mapa_id' => $mapa->id,
                    'p_level' => $mapa->p_level,
                    'mapa_code' => $mapa->mapa_code,
                    'status' => $mapa->status,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal mengupdate MAPA: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
    public function updateValidated(Request $request, $mapaId)
    {
        $asesor = Auth::user();
        $mapa = Mapa::where('asesor_id', $asesor->id)->findOrFail($mapaId);

        // Hanya bisa update jika status approved atau validated
        if (!in_array($mapa->status, ['approved', 'validated'])) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'MAPA hanya dapat diupdate jika berstatus approved atau validated',
                ],
                403,
            );
        }

        $validated = $request->validate([
            'p_level' => 'required|integer|min:0',
            'catatan_asesor' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalKelompok = $mapa->certificationScheme->kelompokKerjas()->count();

            if ($validated['p_level'] > $totalKelompok) {
                throw new \Exception('P-Level tidak boleh melebihi jumlah kelompok kerja');
            }

            // Simpan p_level dan catatan sebelumnya untuk log
            $oldPLevel = $mapa->p_level;
            $oldCatatan = $mapa->catatan_asesor;

            // Update MAPA - TANPA mengubah status atau signature
            $mapa->update([
                'p_level' => $validated['p_level'],
                'status' => 'validated',
                'catatan_asesor' => $validated['catatan_asesor'] ?? null,
            ]);

            DB::commit();

            // Log activity
            Log::info('MAPA validated/approved updated (no signature)', [
                'mapa_id' => $mapa->id,
                'nomor_mapa' => $mapa->nomor_mapa,
                'old_p_level' => $oldPLevel,
                'new_p_level' => $validated['p_level'],
                'status' => $mapa->status,
                'asesor_id' => $asesor->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'MAPA berhasil diupdate. Perubahan akan diterapkan pada proses asesmen.',
                'data' => [
                    'mapa_id' => $mapa->id,
                    'p_level' => $mapa->p_level,
                    'mapa_code' => $mapa->mapa_code,
                    'status' => $mapa->status,
                    'changed' => $oldPLevel !== $validated['p_level'],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating validated MAPA: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal mengupdate MAPA: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Update method validate untuk memastikan bisa edit
     */
    public function validate($mapaId)
    {
        $asesor = Auth::user();

        $mapa = Mapa::with(['delegasi.asesi', 'delegasi.certificationScheme.kelompokKerjas.unitKompetensis.elemenKompetensis.kriteriaKerjas', 'delegasi.certificationScheme.kelompokKerjas.unitKompetensis.portfolioFiles', 'delegasi.apl01.selectedRequirementTemplate.activeItems', 'delegasi.apl01.apl02', 'delegasi.apl02.elementAssessments.elemenKompetensi.unitKompetensi', 'delegasi.apl02.evidenceSubmissions.portfolioFile', 'reviewedBy'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($mapaId);

        // Check if MAPA is approved or validated (both can be edited)
        if (!in_array($mapa->status, ['approved', 'validated'])) {
            return redirect()->route('asesor.mapa.view', $mapa->id)->with('error', 'MAPA hanya dapat direview/edit jika sudah diapprove oleh admin');
        }

        $kelompokKerjas = $mapa->certificationScheme
            ->kelompokKerjas()
            ->with([
                'unitKompetensis.elemenKompetensis.kriteriaKerjas',
                'unitKompetensis.portfolioFiles' => function ($q) {
                    $q->where('is_active', true);
                },
            ])
            ->orderBy('sort_order')
            ->get();

        return view('asesor.mapa.validate', compact('mapa', 'kelompokKerjas'));
    }

    /**
     * View MAPA (detail view)
     */
    public function view($mapaId)
    {
        $asesor = Auth::user();

        $mapa = Mapa::with(['delegasi.asesi', 'delegasi.certificationScheme.kelompokKerjas.unitKompetensis', 'asesor', 'reviewedBy', 'validatedBy', 'apl01', 'apl02', 'delegasi.sptSignature'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($mapaId);

        $kelompokDetails = $mapa->getKelompokMetodeDetails();
        $spt = $mapa->delegasi->sptSignature;

        return view('asesor.mapa.view', compact('mapa', 'kelompokDetails', 'spt'));
    }

    /**
     * Save signature image
     */
    private function saveSignature($base64Signature, $delegasiId, $type = 'validation')
    {
        // Remove data:image/png;base64, prefix
        $image = str_replace('data:image/png;base64,', '', $base64Signature);
        $image = str_replace(' ', '+', $image);
        $imageName = 'mapa_signature_' . $delegasiId . '_' . $type . '_' . time() . '.png';
        $path = 'signatures/mapa/' . $imageName;

        Storage::disk('public')->put($path, base64_decode($image));

        return $path;
    }
}
