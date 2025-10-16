<?php

namespace App\Http\Controllers\Asesor;

use App\Models\Mapa;
use App\Models\TukRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KelompokKerja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Ak07CeklisePenyesuaian;
use App\Models\TukRescheduleHistory;
use Illuminate\Support\Facades\Storage;

class AsesorAk07Controller extends Controller
{
    /**
     * Show form AK07 (after MAPA approved/validated)
     */
    public function create($mapaId)
    {
        $asesor = Auth::user();

        $mapa = Mapa::with(['delegasi.asesi', 'delegasi.certificationScheme', 'ak07'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($mapaId);

        // Check if MAPA is approved or validated
        if (!in_array($mapa->status, ['approved', 'validated'])) {
            return redirect()->route('asesor.mapa.view', $mapa->id)->with('error', 'FR.AK.07 hanya dapat dibuat setelah MAPA diapprove');
        }

        // If AK07 already exists, redirect to edit
        if ($mapa->ak07) {
            return redirect()->route('asesor.ak07.edit', $mapa->ak07->id);
        }

        // Auto-select potensi asesi dari KelompokKerja berdasarkan p_level MAPA
        $autoSelectedPotensiAsesi = $this->getAutoSelectedPotensiAsesi($mapa);

        // Get all options untuk ditampilkan di form
        $potensiAsesiOptions = KelompokKerja::POTENSI_ASESI_OPTIONS;

        return view('asesor.ak07.create', compact('mapa', 'autoSelectedPotensiAsesi', 'potensiAsesiOptions'));
    }

    /**
     * Get auto-selected potensi asesi from KelompokKerja based on MAPA p_level
     */
    private function getAutoSelectedPotensiAsesi($mapa)
    {
        if (!$mapa->p_level || !$mapa->certification_scheme_id) {
            Log::warning("MAPA {$mapa->id} doesn't have p_level or scheme_id");
            return [];
        }

        $kelompokKerja = KelompokKerja::where('certification_scheme_id', $mapa->certification_scheme_id)->where('p_level', $mapa->p_level)->where('is_active', true)->first();

        if (!$kelompokKerja) {
            Log::warning("No active KelompokKerja found with p_level {$mapa->p_level}");
            return [];
        }

        if (!$kelompokKerja->potensi_asesi || empty($kelompokKerja->potensi_asesi)) {
            Log::warning("KelompokKerja {$kelompokKerja->id} doesn't have potensi_asesi configured");
            return [];
        }

        Log::info('Auto-selected potensi asesi for AK07', [
            'mapa_id' => $mapa->id,
            'mapa_p_level' => $mapa->p_level,
            'kelompok_kerja_id' => $kelompokKerja->id,
            'potensi_asesi' => $kelompokKerja->potensi_asesi,
        ]);

        return $kelompokKerja->potensi_asesi;
    }

    /**
     * Store AK07
     */
    public function store(Request $request, $mapaId)
    {
        $asesor = Auth::user();
        $mapa = Mapa::where('asesor_id', $asesor->id)->findOrFail($mapaId);

        if (!in_array($mapa->status, ['approved', 'validated'])) {
            return redirect()->route('asesor.mapa.view', $mapa->id)->with('error', 'MAPA harus diapprove terlebih dahulu');
        }

        $validated = $request->validate([
            'potensi_asesi' => 'required|array|min:1',
            'potensi_asesi.*' => 'in:p1,p2,p3,p4,p5',

            'q1_answer' => 'required|in:Ya,Tidak',
            'q1_keterangan' => 'nullable|array',
            'q1_keterangan.*' => 'string',

            'q2_answer' => 'required|in:Ya,Tidak',
            'q2_keterangan' => 'nullable|array',
            'q2_keterangan.*' => 'string',

            'q3_answer' => 'required|in:Ya,Tidak',
            'q3_keterangan' => 'nullable|array',
            'q3_keterangan.*' => 'string',

            'q4_answer' => 'required|in:Ya,Tidak',
            'q4_keterangan' => 'nullable|array',
            'q4_keterangan.*' => 'string',

            'q5_answer' => 'required|in:Ya,Tidak',
            'q5_keterangan' => 'nullable|array',
            'q5_keterangan.*' => 'string',

            'q6_answer' => 'required|in:Ya,Tidak',
            'q6_keterangan' => 'nullable|array',
            'q6_keterangan.*' => 'string',

            'q7_answer' => 'required|in:Ya,Tidak',
            'q7_keterangan' => 'nullable|array',
            'q7_keterangan.*' => 'string',

            'q8_answer' => 'required|in:Ya,Tidak',
            'q8_keterangan' => 'nullable|array',
            'q8_keterangan.*' => 'string',

            'acuan_pembahasan' => 'required|in:Ya,Tidak',
            'tulisan_acuan_pembahasan' => 'nullable|string',

            'metode_asesmen' => 'required|in:Ya,Tidak',
            'tulisan_metode_asesmen' => 'nullable|string',

            'instrumen_asesmen' => 'required|in:Ya,Tidak',
            'tulisan_instrumen_asesmen' => 'nullable|string',

            'nama_asesor' => 'required|string',
            'asesor_signature' => 'required|string',
            'tanggal_ttd_asesor' => 'required|date',

            'asesi_signature' => 'nullable|string',
            'asesi_tanggal_tanda_tangan' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Prepare answers
            $answers = [];
            for ($i = 1; $i <= 8; $i++) {
                $answers["q{$i}"] = [
                    'answer' => $validated["q{$i}_answer"],
                    'keterangan' => $validated["q{$i}_keterangan"] ?? [],
                ];
            }

            // Prepare hasil penyesuaian
            $hasilPenyesuaian = [
                'acuan_pembahasan' => [
                    'answer' => $validated['acuan_pembahasan'],
                    'keterangan' => $validated['tulisan_acuan_pembahasan'] ?? null,
                ],
                'metode_asesmen' => [
                    'answer' => $validated['metode_asesmen'],
                    'keterangan' => $validated['tulisan_metode_asesmen'] ?? null,
                ],
                'instrumen_asesmen' => [
                    'answer' => $validated['instrumen_asesmen'],
                    'keterangan' => $validated['tulisan_instrumen_asesmen'] ?? null,
                ],
            ];

            // Save asesor signature
            $asesorSignaturePath = $this->saveSignature($validated['asesor_signature'], $mapa->delegasi_personil_asesmen_id, 'ak07_asesor');

            // Save asesi signature if provided
            $asesiSignaturePath = null;
            $asesiSignedAt = null;
            if (!empty($validated['asesi_signature'])) {
                $asesiSignaturePath = $this->saveSignature($validated['asesi_signature'], $mapa->delegasi_personil_asesmen_id, 'ak07_asesi');
                $asesiSignedAt = $validated['asesi_tanggal_tanda_tangan'] ?? now();
            }

            // Determine status
            $status = $asesiSignaturePath ? 'completed' : 'waiting_asesi';

            // Create AK07
            $ak07 = Ak07CeklisePenyesuaian::create([
                'mapa_id' => $mapa->id,
                'delegasi_personil_asesmen_id' => $mapa->delegasi_personil_asesmen_id,
                'asesor_id' => $asesor->id,
                'asesi_id' => $mapa->delegasi->asesi_id,
                'certification_scheme_id' => $mapa->certification_scheme_id,
                'nomor_ak07' => Ak07CeklisePenyesuaian::generateNomorAk07($mapa->certification_scheme_id, $asesor->id),
                'potensi_asesi' => $validated['potensi_asesi'],
                'answers' => $answers,
                'hasil_penyesuaian' => $hasilPenyesuaian,
                'catatan_asesor' => $validated['nama_asesor'],
                'asesor_signature' => $asesorSignaturePath,
                'asesor_signed_at' => $validated['tanggal_ttd_asesor'],
                'asesor_ip' => $request->ip(),
                'asesi_signature' => $asesiSignaturePath,
                'asesi_signed_at' => $asesiSignedAt,
                'asesi_ip' => $asesiSignaturePath ? $request->ip() : null,
                'status' => $status,
            ]);

            DB::commit();

            Log::info('AK07 created successfully', [
                'ak07_id' => $ak07->id,
                'mapa_id' => $mapa->id,
                'status' => $ak07->status,
            ]);

            $message = $status === 'completed' ? 'FR.AK.07 berhasil disimpan dan ditandatangani!' : 'FR.AK.07 berhasil disimpan. Menunggu tanda tangan Asesi.';

            return redirect()->route('asesor.mapa.index')->with('success', $message)->with('ak07_status', $status);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing AK07: ' . $e->getMessage(), [
                'mapa_id' => $mapaId,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan FR.AK.07: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($ak07Id)
    {
        $asesor = Auth::user();

        $ak07 = Ak07CeklisePenyesuaian::with(['mapa.delegasi.asesi', 'mapa.delegasi.certificationScheme'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($ak07Id);

        if (!$ak07->canBeEdited()) {
            return redirect()->route('asesor.ak07.view', $ak07->id)->with('error', 'FR.AK.07 tidak dapat diedit');
        }

        $autoSelectedPotensiAsesi = $ak07->potensi_asesi ?? [];
        $potensiAsesiOptions = KelompokKerja::POTENSI_ASESI_OPTIONS;

        return view('asesor.ak07.edit', compact('ak07', 'autoSelectedPotensiAsesi', 'potensiAsesiOptions'));
    }

    /**
     * Update AK07
     */
    public function update(Request $request, $ak07Id)
    {
        $asesor = Auth::user();
        $ak07 = Ak07CeklisePenyesuaian::where('asesor_id', $asesor->id)->findOrFail($ak07Id);

        if (!$ak07->canBeEdited()) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'FR.AK.07 tidak dapat diupdate',
                ],
                403,
            );
        }

        $validated = $request->validate([
            'potensi_asesi' => 'required|array|min:1',
            'potensi_asesi.*' => 'in:p1,p2,p3,p4,p5',
            'q1_answer' => 'required|in:Ya,Tidak',
            'q1_keterangan' => 'nullable|array',
            'q1_keterangan.*' => 'string',
            // ... (same validations as store)
        ]);

        try {
            DB::beginTransaction();

            $answers = [];
            for ($i = 1; $i <= 8; $i++) {
                $answers["q{$i}"] = [
                    'answer' => $validated["q{$i}_answer"],
                    'keterangan' => $validated["q{$i}_keterangan"] ?? [],
                ];
            }

            $hasilPenyesuaian = [
                'acuan_pembahasan' => [
                    'answer' => $validated['acuan_pembahasan'],
                    'keterangan' => $validated['tulisan_acuan_pembahasan'] ?? null,
                ],
                'metode_asesmen' => [
                    'answer' => $validated['metode_asesmen'],
                    'keterangan' => $validated['tulisan_metode_asesmen'] ?? null,
                ],
                'instrumen_asesmen' => [
                    'answer' => $validated['instrumen_asesmen'],
                    'keterangan' => $validated['tulisan_instrumen_asesmen'] ?? null,
                ],
            ];

            if ($ak07->asesor_signature) {
                Storage::disk('public')->delete($ak07->asesor_signature);
            }

            $asesorSignaturePath = $this->saveSignature($validated['asesor_signature'], $ak07->delegasi_personil_asesmen_id, 'ak07_asesor');

            $ak07->update([
                'potensi_asesi' => $validated['potensi_asesi'],
                'answers' => $answers,
                'hasil_penyesuaian' => $hasilPenyesuaian,
                'catatan_asesor' => $validated['nama_asesor'],
                'asesor_signature' => $asesorSignaturePath,
                'asesor_signed_at' => $validated['tanggal_ttd_asesor'],
                'asesor_ip' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'FR.AK.07 berhasil diupdate',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating AK07: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal mengupdate FR.AK.07: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * View AK07
     */
    public function view($ak07Id)
    {
        $user = Auth::user();

        $ak07 = Ak07CeklisePenyesuaian::with(['mapa', 'asesor', 'asesi', 'certificationScheme'])->findOrFail($ak07Id);

        // Check authorization
        if ($user->id !== $ak07->asesor_id && $user->id !== $ak07->asesi_id && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $potensiAsesiOptions = KelompokKerja::POTENSI_ASESI_OPTIONS;

        return view('asesor.ak07.view', compact('ak07', 'potensiAsesiOptions'));
    }

    /**
     * Show final recommendation form
     */
    public function showFinalRecommendation($ak07Id)
    {
        $asesor = Auth::user();

        $ak07 = Ak07CeklisePenyesuaian::with(['mapa.delegasi.asesi', 'mapa.delegasi.certificationScheme.kelompokKerjas.unitKompetensis.elemenKompetensis.kriteriaKerjas', 'mapa.delegasi.certificationScheme.kelompokKerjas.unitKompetensis.portfolioFiles', 'mapa.delegasi.apl01.selectedRequirementTemplate.activeItems', 'mapa.delegasi.apl02.elementAssessments.elemenKompetensi.unitKompetensi', 'mapa.delegasi.apl02.evidenceSubmissions.portfolioFile', 'asesor'])
            ->where('asesor_id', $asesor->id)
            ->findOrFail($ak07Id);

        if ($ak07->status !== 'completed') {
            return redirect()->route('asesor.ak07.view', $ak07Id)->with('error', 'AK.07 harus selesai terlebih dahulu');
        }

        $mapa = $ak07->mapa;
        $delegasi = $mapa->delegasi;
        $assessmentSummary = $this->getAssessmentSummary($mapa);

        // Get kelompokKerjas for APL02 review
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

        return view('asesor.ak07.final-recommendation', compact('ak07', 'mapa', 'delegasi', 'assessmentSummary', 'kelompokKerjas'));
    }

    // Update method storeFinalRecommendation di AsesorAk07Controller.php

    public function storeFinalRecommendation(Request $request, $ak07Id)
    {
        $asesor = Auth::user();
        $ak07 = Ak07CeklisePenyesuaian::where('asesor_id', $asesor->id)->findOrFail($ak07Id);

        if ($ak07->status !== 'completed') {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'AK.07 harus selesai terlebih dahulu',
                ],
                403,
            );
        }

        $validated = $request->validate([
            'recommendation' => 'required|in:continue,not_continue',
            'recommendation_notes' => 'nullable|string|max:1000',
            'signature' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $mapa = $ak07->mapa;

            if ($validated['recommendation'] === 'not_continue') {
                $result = $this->handleNotContinueRecommendation($ak07, $validated['recommendation_notes'] ?? 'Asesor merekomendasikan asesi tidak dilanjutkan', $validated['signature'], $asesor->id);

                DB::commit();
                return response()->json($result);
            }

            // Save "continue" recommendation
            $signaturePath = $this->saveSignature($validated['signature'], $ak07->delegasi_personil_asesmen_id, 'final_recommendation');

            // Update AK07
            $ak07->update([
                'final_recommendation' => 'continue',
                'recommendation_notes' => $validated['recommendation_notes'] ?? null,
                'final_signature_path' => $signaturePath,
                'final_signed_at' => now(),
                'final_signed_by' => $asesor->id,
            ]);

            // Validate MAPA (if not already validated)
            if ($mapa->status === 'approved') {
                $mapa->update([
                    'status' => 'validated',
                    'validation_signature' => $signaturePath,
                    'validated_by' => $asesor->id,
                    'validated_at' => now(),
                    'validation_ip' => $request->ip(),
                    'final_recommendation_status' => 'approved',
                ]);
            } else {
                // Just update recommendation status
                $mapa->update([
                    'final_recommendation_status' => 'approved',
                ]);
            }

            DB::commit();

            Log::info('Final recommendation and MAPA validation completed', [
                'ak07_id' => $ak07->id,
                'mapa_id' => $mapa->id,
                'mapa_status' => $mapa->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rekomendasi Final dan Validasi MAPA berhasil disimpan. Asesi dapat dilanjutkan ke tahap berikutnya.',
                'data' => [
                    'ak07_id' => $ak07->id,
                    'mapa_id' => $mapa->id,
                    'recommendation' => 'continue',
                    'status' => 'approved',
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing final recommendation: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal menyimpan rekomendasi: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Handle "Tidak Dilanjutkan" recommendation (reschedule flow)
     */
    private function handleNotContinueRecommendation($ak07, $notes, $signatureData, $asesorId)
    {
        try {
            $mapa = $ak07->mapa;
            $delegasi = $mapa->delegasi;
            $apl01 = $delegasi->apl01;
            $tukRequest = TukRequest::where('apl01_id', $apl01->id)->first();

            // Save final recommendation with "not_continue" status
            $signaturePath = $this->saveSignature($signatureData, $delegasi->id, 'final_recommendation');

            $ak07->update([
                'final_recommendation' => 'not_continue',
                'recommendation_notes' => $notes,
                'final_signature_path' => $signaturePath,
                'final_signed_at' => now(),
                'final_signed_by' => $asesorId,
            ]);

            // Create reschedule history
            $rescheduleHistory = $this->createRescheduleHistoryFromAk07($ak07, $apl01, $tukRequest, $notes, $asesorId);

            // Delete SPT if exists
            if ($delegasi->sptSignature) {
                $this->deleteSPT($delegasi->sptSignature);
            }

            // Delete MAPA
            if ($mapa) {
                if ($mapa->signature_image && Storage::disk('public')->exists($mapa->signature_image)) {
                    Storage::disk('public')->delete($mapa->signature_image);
                }
                if ($mapa->validation_signature && Storage::disk('public')->exists($mapa->validation_signature)) {
                    Storage::disk('public')->delete($mapa->validation_signature);
                }
                $mapa->delete();
            }

            // Delete AK07 files if exists
            $ak07->delete();

            // Delete Delegasi
            $delegasi->delete();

            // Delete Rekomendasi LSP if exists
            if ($apl01->rekomendasiLsp) {
                if ($apl01->rekomendasiLsp->ttd_admin_path && Storage::disk('public')->exists($apl01->rekomendasiLsp->ttd_admin_path)) {
                    Storage::disk('public')->delete($apl01->rekomendasiLsp->ttd_admin_path);
                }
                $apl01->rekomendasiLsp->delete();
            }

            // Reset APL01 & APL02 status
            $apl01->update([
                'reviewed_at' => null,
                'reviewed_by' => null,
                'rejection_reason' => null,
                'completed_at' => null,
            ]);

            if ($apl01->apl02) {
                $apl01->apl02->update([
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                    'completed_at' => null,
                ]);
            }

            if ($tukRequest) {
                $tukRequest->delete();
            }

            Log::info('Reschedule from AK07 recommendation completed', [
                'ak07_id' => $ak07->id,
                'apl01_id' => $apl01->id,
                'reschedule_history_id' => $rescheduleHistory->id,
            ]);

            return [
                'success' => true,
                'message' => 'Rekomendasi "Tidak Dilanjutkan" berhasil disimpan. Data telah dimasukkan ke history reschedule dan status APL direset untuk review ulang.',
                'data' => [
                    'ak07_id' => $ak07->id,
                    'recommendation' => 'not_continue',
                    'reschedule_history_id' => $rescheduleHistory->id,
                    'status' => 'rescheduled',
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error in handleNotContinueRecommendation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Create reschedule history from AK07
     */
    private function createRescheduleHistoryFromAk07($ak07, $apl01, $tukRequest, $notes, $asesorId)
    {
        $history = TukRescheduleHistory::create([
            'tuk_type' => $tukRequest ? 'sewaktu' : 'mandiri',
            'kode_tuk' => $tukRequest?->kode_tuk,
            'apl01_id' => $apl01->id,
            'tuk_request_id' => $tukRequest?->id,
            'reschedule_reason' => $notes,
            'reschedule_source' => 'ak07_final_recommendation',
            'rescheduled_by' => $asesorId,
            'rescheduled_at' => now(),
            'old_tanggal_assessment' => $tukRequest?->tanggal_assessment,
            'old_lokasi_assessment' => $tukRequest?->lokasi_assessment,
            'apl01_status_before' => $apl01->status,
            'apl02_status_before' => $apl01->apl02?->status,
            'had_delegation' => true,
            'had_mapa' => true,
            'had_recommendation' => $apl01->rekomendasiLsp !== null,
            'mapa_nomor' => $ak07->mapa->nomor_mapa,
            'ak07_nomor' => $ak07->nomor_ak07,
        ]);

        return $history;
    }

    /**
     * Delete SPT with all files
     */
    private function deleteSPT($spt)
    {
        if ($spt->spt_verifikator_file && Storage::exists($spt->spt_verifikator_file)) {
            Storage::delete($spt->spt_verifikator_file);
        }
        if ($spt->spt_observer_file && Storage::exists($spt->spt_observer_file)) {
            Storage::delete($spt->spt_observer_file);
        }
        if ($spt->spt_asesor_file && Storage::exists($spt->spt_asesor_file)) {
            Storage::delete($spt->spt_asesor_file);
        }
        if ($spt->signature_image && $spt->signature_image !== 'assets/signatures/direktur_signature.png' && Storage::disk('public')->exists($spt->signature_image)) {
            Storage::disk('public')->delete($spt->signature_image);
        }
        $spt->delete();
    }

    /**
     * Get assessment summary
     */
    private function getAssessmentSummary($mapa)
    {
        $ak07 = $mapa->ak07;
        $delegasi = $mapa->delegasi;

        $summary = [
            'mapa_nomor' => $mapa->nomor_mapa,
            'mapa_p_level' => $mapa->p_level,
            'ak07_nomor' => $ak07->nomor_ak07,
            'ak07_status' => $ak07->status,
            'asesi_nama' => $delegasi->asesi->name,
            'scheme_nama' => $delegasi->certificationScheme->nama,
            'tanggal_assessment' => $delegasi->tanggal_pelaksanaan_asesmen,
            'assessment_evidences' => $this->countAssessmentEvidences($ak07),
        ];

        return $summary;
    }

    /**
     * Count assessment evidences
     */
    private function countAssessmentEvidences($ak07)
    {
        return [
            'total_elements' => $ak07->mapa->delegasi->apl02->elementAssessments->count() ?? 0,
            'total_evidences' => $ak07->mapa->delegasi->apl02->evidenceSubmissions->count() ?? 0,
        ];
    }

    /**
     * Save signature image
     */
    private function saveSignature($base64Signature, $delegasiId, $type = 'ak07')
    {
        $image = str_replace('data:image/png;base64,', '', $base64Signature);
        $image = str_replace(' ', '+', $image);
        $imageName = 'ak07_signature_' . $delegasiId . '_' . $type . '_' . time() . '.png';
        $path = 'signatures/ak07/' . $imageName;

        Storage::disk('public')->put($path, base64_decode($image));

        return $path;
    }
}
