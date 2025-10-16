<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Apl01Pendaftaran;
use App\Models\CertificationScheme;
use App\Models\LembagaPelatihan;
use App\Models\RequirementTemplate;
use App\Models\RegionProv;
use App\Models\RegionKab;
use App\Models\TrainingProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class Apl01Controller extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Get APL 01 data dengan relasi menggunakan model Apl01Pendaftaran
            $apls = Apl01Pendaftaran::with(['certificationScheme', 'reviewer', 'provinsiRumah', 'kotaRumah'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Hitung statistik
            $stats = [
                'total' => $apls->count(),
                'draft' => $apls->where('status', 'draft')->count(),
                'submitted' => $apls->whereIn('status', ['submitted'])->count(),
                'approved' => $apls->where('status', 'approved')->count(),
            ];

            // Jika request AJAX (untuk auto-refresh)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'stats' => $stats,
                    'apls_count' => $apls->count(),
                ]);
            }

            return view('asesi.apl01.index', compact('apls', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in APL 01 index: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Gagal memuat data APL 01',
                    ],
                    500,
                );
            }

            return redirect()->back()->with('error', 'Gagal memuat data APL 01');
        }
    }

    public function edit($id)
    {
        $apl = Apl01Pendaftaran::where('user_id', Auth::id())
            ->with(['selectedRequirementTemplate.activeItems'])
            ->findOrFail($id);

        if (!$apl->isEditable) {
            return redirect()->route('asesi.apl01.show', $id)->with('error', 'APL 01 tidak dapat diedit.');
        }

        $scheme = $apl
            ->certificationScheme()
            ->with([
                'activeUnitKompetensis',
                'requirementTemplates' => function ($query) {
                    $query->wherePivot('is_active', true)->orderBy('certification_scheme_requirements.sort_order');
                },
                'requirementTemplates.activeItems' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->first();

        $provinces = RegionProv::orderBy('name')->get();
        $cities = RegionKab::with('province')->orderBy('name')->get();
        $trainingProviders = LembagaPelatihan::orderBy('name')->get();

        // Add user profile data for auto-fill
        $userProfile = $this->getUserProfileData();

        return view('asesi.apl01.form', [
            'scheme' => $scheme,
            'existingApl' => $apl,
            'provinces' => $provinces,
            'cities' => $cities,
            'trainingProviders' => $trainingProviders,
            'userProfile' => $userProfile,
        ]);
    }

    public function create($schemeId)
    {
        $scheme = CertificationScheme::with([
            'activeUnitKompetensis',
            'requirementTemplates' => function ($query) {
                $query->wherePivot('is_active', true)->orderBy('certification_scheme_requirements.sort_order');
            },
            'requirementTemplates.activeItems' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            },
        ])->findOrFail($schemeId);
        // Check if user already has APL for this scheme
        $existingApl = Apl01Pendaftaran::where('user_id', Auth::id())->where('certification_scheme_id', $schemeId)->first();

        if ($existingApl) {
            return redirect()->route('asesi.apl01.edit', $existingApl->id)->with('info', 'Anda sudah memiliki APL 01 untuk skema ini.');
        }

        $provinces = RegionProv::orderBy('name')->get();
        $cities = RegionKab::with('province')->orderBy('name')->get();
        $trainingProviders = LembagaPelatihan::orderBy('name')->get();

        $existingApl = null;

        // Add user profile data for auto-fill
        $userProfile = $this->getUserProfileData();
        // dd(vars: $userProfile);
        return view('asesi.apl01.form', [
            'scheme' => $scheme,
            'existingApl' => $existingApl,
            'provinces' => $provinces,
            'cities' => $cities,
            'trainingProviders' => $trainingProviders,
            'userProfile' => $userProfile,
        ]);
    }

    /**
     * Get user profile data for auto-fill
     */
    private function getUserProfileData()
    {
        $user = Auth::user();

        // Load profile relation
        $user->load('profile');

        // Jika profile tidak ada, return empty array
        if (!$user->profile) {
            return [
                'nama_lengkap' => '',
                'nik' => '',
                'tempat_lahir' => '',
                'tanggal_lahir' => null,
                'jenis_kelamin' => '',
                'kebangsaan' => 'Indonesia',
                'alamat_rumah' => '',
                'kota_rumah' => '',
                'provinsi_rumah' => '',
                'kode_pos' => '',
                'no_telp_rumah' => '',
                'no_hp' => '',
                'email' => $user->email ?? '',
                'pendidikan_terakhir' => '',
                'nama_sekolah_terakhir' => '',
                'nama_tempat_kerja' => '',
                'kategori_pekerjaan' => '',
                'jabatan' => '',
                'nama_jalan_kantor' => '',
                'kota_kantor' => '',
                'provinsi_kantor' => '',
                'kode_pos_kantor' => '',
                'no_telp_kantor' => '',
            ];
        }

        $profile = $user->profile;

        return [
            'nama_lengkap' => $profile->nama_lengkap ?? '',
            'nik' => $profile->nik ?? '',
            'tempat_lahir' => $profile->tempat_lahir ?? '',
            'tanggal_lahir' => $profile->tanggal_lahir ? $this->formatDateForInput($profile->tanggal_lahir) : null,
            'jenis_kelamin' => $profile->jenis_kelamin ?? '',
            'kebangsaan' => $profile->kebangsaan ?? 'Indonesia',
            'alamat_rumah' => $profile->alamat_rumah ?? '',
            'kota_rumah' => $profile->kota_rumah ?? '',
            'provinsi_rumah' => $profile->provinsi_rumah ?? '',
            'kode_pos' => $profile->kode_pos ?? '',
            'no_telp_rumah' => $profile->no_telp_rumah ?? '',
            'no_hp' => $profile->no_hp ?? '',
            'email' => $profile->email ?? ($user->email ?? ''),
            'pendidikan_terakhir' => $profile->pendidikan_terakhir ?? '',
            'nama_sekolah_terakhir' => $profile->nama_sekolah_terakhir ?? '',
            'nama_tempat_kerja' => $profile->nama_tempat_kerja ?? '',
            'kategori_pekerjaan' => $profile->kategori_pekerjaan ?? '',
            'jabatan' => $profile->jabatan ?? '',
            'nama_jalan_kantor' => $profile->nama_jalan_kantor ?? '',
            'kota_kantor' => $profile->kota_kantor ?? '',
            'provinsi_kantor' => $profile->provinsi_kantor ?? '',
            'kode_pos_kantor' => $profile->kode_pos_kantor ?? '',
            'no_telp_kantor' => $profile->no_telp_kantor ?? '',
        ];
    }

    /**
     * Helper method to format date for input field
     */
    private function formatDateForInput($date)
    {
        if (!$date) {
            return null;
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        if ($date instanceof \Carbon\Carbon) {
            return $date->format('Y-m-d');
        }

        return null;
    }

    public function store(Request $request, $schemeId)
    {
        $scheme = CertificationScheme::with('requirementTemplates.activeItems')->findOrFail($schemeId);
        // Build validation rules dynamically
        $rules = $this->getValidationRules($scheme, $request);
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Create APL record first
            $apl = Apl01Pendaftaran::create([
                'user_id' => Auth::id(),
                'certification_scheme_id' => $schemeId,
                'selected_requirement_template_id' => $validated['selected_requirement_template'] ?? null,
                'nomor_apl_01' => $request->input('action') === 'submit' ? $this->generateAplNumber() : null,
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'kebangsaan' => $validated['kebangsaan'],
                'alamat_rumah' => $validated['alamat_rumah'],
                'provinsi_rumah' => $validated['provinsi_rumah'],
                'kota_rumah' => $validated['kota_rumah'],
                'kode_pos' => $validated['kode_pos'] ?? null,
                'no_telp_rumah' => $validated['no_telp_rumah'] ?? null,
                'no_hp' => $validated['no_hp'],
                'email' => $validated['email'],
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
                'nama_sekolah_terakhir' => $validated['nama_sekolah_terakhir'],
                'nama_tempat_kerja' => $validated['nama_tempat_kerja'],
                'jabatan' => $validated['jabatan'],
                'kategori_pekerjaan' => $validated['kategori_pekerjaan'],
                'nama_jalan_kantor' => $validated['nama_jalan_kantor'] ?? null,
                'provinsi_kantor' => $validated['provinsi_kantor'] ?? null,
                'kota_kantor' => $validated['kota_kantor'] ?? null,
                'kode_pos_kantor' => $validated['kode_pos_kantor'] ?? null,
                'no_telp_kantor' => $validated['no_telp_kantor'] ?? null,
                'tuk' => $validated['tuk'] ?? null,
                'kategori_peserta' => $validated['kategori_peserta'],
                'training_provider' => $validated['training_provider'] ?? null,
                'tujuan_asesmen' => empty($validated['tujuan_asesmen']) ? $validated['tujuan_asesmen_radio'] : $validated['tujuan_asesmen'],
                'pernah_asesmen_lsp' => $validated['pernah_asesmen_lsp'] ?? null,
                'bisa_share_screen' => $validated['bisa_share_screen'] ?? null,
                'bisa_gunakan_browser' => $validated['bisa_gunakan_browser'] ?? null,
                'aplikasi_yang_digunakan' => $validated['aplikasi_yang_digunakan'] ?? [],
                'pernyataan_benar' => $validated['pernyataan_benar'] ?? false,
                'nama_lengkap_ktp' => $validated['nama_lengkap_ktp'] ?? $validated['nama_lengkap'],
                'status' => $request->input('action') === 'submit' ? 'submitted' : 'draft',
                'submitted_at' => $request->input('action') === 'submit' ? now() : null,
            ]);

            // Handle dynamic requirement responses
            $this->handleRequirementResponses($request, $apl, $scheme);

            // Process signature if provided dan submit
            if ($request->filled('tanda_tangan_asesi') && $request->input('action') === 'submit') {
                $signatureData = $this->processSignature($request->input('tanda_tangan_asesi'));
                if ($signatureData) {
                    // Gunakan method baru dari model untuk simpan signature
                    $apl->signByAsesi($signatureData);
                }
            }

            DB::commit();

            $message = $request->input('action') === 'submit' ? 'APL 01 berhasil disubmit!' : 'Draft APL 01 berhasil disimpan!';
            return redirect()->route('asesi.inbox.index', $apl->id)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving APL 01: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $apl = Apl01Pendaftaran::where('user_id', Auth::id())->findOrFail($id);

        if (!$apl->isEditable) {
            return redirect()->route('asesi.apl01.show', $id)->with('error', 'APL 01 tidak dapat diedit.');
        }

        $scheme = $apl->certificationScheme()->with('requirementTemplates.activeItems')->first();

        // Build validation rules dynamically
        $rules = $this->getValidationRules($scheme, $request);
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Cek apakah template berubah → hapus requirement lama yang tidak relevan
            if (!empty($validated['selected_requirement_template'])) {
                $selectedTemplateId = $validated['selected_requirement_template'];
                $selectedTemplate = $scheme->requirementTemplates->find($selectedTemplateId);

                if ($selectedTemplate) {
                    $validItemIds = $selectedTemplate->activeItems->pluck('id')->toArray();

                    // Hapus requirement lama yang tidak ada di template baru
                    $responses = $apl->requirement_answers ?? [];
                    $filtered = collect($responses)->only($validItemIds)->toArray();
                    $apl->requirement_answers = $filtered;
                    $apl->save();
                }
            }

            // Update APL record
            $apl->update([
                'selected_requirement_template_id' => $validated['selected_requirement_template'] ?? null,
                'nomor_apl_01' => $request->input('action') === 'submit' && !$apl->nomor_apl_01 ? $this->generateAplNumber() : $apl->nomor_apl_01,
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'kebangsaan' => $validated['kebangsaan'],
                'alamat_rumah' => $validated['alamat_rumah'],
                'provinsi_rumah' => $validated['provinsi_rumah'],
                'kota_rumah' => $validated['kota_rumah'],
                'kode_pos' => $validated['kode_pos'] ?? null,
                'no_telp_rumah' => $validated['no_telp_rumah'] ?? null,
                'no_hp' => $validated['no_hp'],
                'email' => $validated['email'],
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
                'nama_sekolah_terakhir' => $validated['nama_sekolah_terakhir'],
                'nama_tempat_kerja' => $validated['nama_tempat_kerja'],
                'jabatan' => $validated['jabatan'],
                'kategori_pekerjaan' => $validated['kategori_pekerjaan'],
                'nama_jalan_kantor' => $validated['nama_jalan_kantor'] ?? null,
                'provinsi_kantor' => $validated['provinsi_kantor'] ?? null,
                'kota_kantor' => $validated['kota_kantor'] ?? null,
                'kode_pos_kantor' => $validated['kode_pos_kantor'] ?? null,
                'no_telp_kantor' => $validated['no_telp_kantor'] ?? null,
                'tuk' => $validated['tuk'] ?? null,
                'kategori_peserta' => $validated['kategori_peserta'],
                'training_provider' => $validated['training_provider'] ?? null,
                'tujuan_asesmen' => empty($validated['tujuan_asesmen']) ? $validated['tujuan_asesmen_radio'] : $validated['tujuan_asesmen'],
                'pernah_asesmen_lsp' => $validated['pernah_asesmen_lsp'] ?? null,
                'bisa_share_screen' => $validated['bisa_share_screen'] ?? null,
                'bisa_gunakan_browser' => $validated['bisa_gunakan_browser'] ?? null,
                'aplikasi_yang_digunakan' => $validated['aplikasi_yang_digunakan'] ?? [],
                'pernyataan_benar' => $validated['pernyataan_benar'] ?? false,
                'nama_lengkap_ktp' => $validated['nama_lengkap_ktp'] ?? $validated['nama_lengkap'],
                'status' => $request->input('action') === 'submit' ? 'submitted' : 'draft',
                'submitted_at' => $request->input('action') === 'submit' && !$apl->submitted_at ? now() : $apl->submitted_at,
            ]);

            // Handle dynamic requirement responses
            $this->handleRequirementResponses($request, $apl, $scheme);

            // Process signature if provided
            if ($request->filled('tanda_tangan_asesi')) {
                $signatureData = $this->processSignature($request->input('tanda_tangan_asesi'));
                if ($signatureData) {
                    // Gunakan method baru dari model untuk simpan signature
                    $apl->signByAsesi($signatureData);
                }
            }

            DB::commit();

            $message = $request->input('action') === 'submit' ? 'APL 01 berhasil disubmit!' : 'Draft APL 01 berhasil diupdate!';
            return redirect()->route('asesi.inbox.index', $apl->id)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating APL 01: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    protected function getValidationRules($scheme, $request = null)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'kebangsaan' => 'required|string|max:255',
            'alamat_rumah' => 'required|string|max:1000',
            'provinsi_rumah' => 'required|string|max:255',
            'kota_rumah' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'no_telp_rumah' => 'nullable|string|max:15',
            'no_hp' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'nama_sekolah_terakhir' => 'required|string|max:255',
            'nama_tempat_kerja' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori_pekerjaan' => 'required|string|max:255',
            'nama_jalan_kantor' => 'nullable|string|max:1000',
            'provinsi_kantor' => 'nullable|string|max:255',
            'kota_kantor' => 'nullable|string|max:255',
            'kode_pos_kantor' => 'nullable|string|max:10',
            'no_telp_kantor' => 'nullable|string|max:15',
            'tuk' => 'nullable|string|max:255',
            'kategori_peserta' => 'required|in:individu,training_provider',
            'training_provider' => 'required_if:kategori_peserta,training_provider|nullable|string|max:255',
            'tujuan_asesmen_radio' => 'required|string|max:255',
            'tujuan_asesmen' => 'required_if:tujuan_asesmen_radio,Lainnya|nullable|string|max:1000',
            'pernah_asesmen_lsp' => 'nullable|in:sudah,belum',
            'bisa_share_screen' => 'nullable|in:ya,tidak',
            'bisa_gunakan_browser' => 'nullable|in:ya,tidak',
            'aplikasi_yang_digunakan' => 'nullable|array',
            'aplikasi_yang_digunakan.*' => 'string|max:255',
            'pernyataan_benar' => 'required|boolean',
            'nama_lengkap_ktp' => 'nullable|string|max:255',
        ];

        // Add signature validation for submit action
        if ($request && $request->input('action') === 'submit') {
            if (!$request->filled('existing_signature')) {
                $rules['tanda_tangan_asesi'] = 'required|string';
            }
        }

        // Dynamic requirement validation
        if ($scheme && $scheme->requirementTemplates && $scheme->requirementTemplates->count() > 0) {
            $rules['selected_requirement_template'] = 'required|exists:requirement_templates,id';

            $selectedTemplateId = $request ? $request->input('selected_requirement_template') : null;
            if ($selectedTemplateId) {
                $selectedTemplate = $scheme->requirementTemplates->find($selectedTemplateId);
                if ($selectedTemplate && $selectedTemplate->activeItems) {
                    $existingApl = null;
                    if ($request && method_exists($request, 'route') && $request->route()->getName() === 'asesi.apl01.update') {
                        $aplId = $request->route('apl01');
                        $existingApl = Apl01Pendaftaran::find($aplId);
                    }

                    foreach ($selectedTemplate->activeItems as $item) {
                        $fieldName = "requirement_item_{$item->id}";

                        switch ($item->type) {
                            case 'file_upload':
                                $maxSizeKB = ($item->max_file_size ?? 5) * 1024;
                                $allowedExtensions = str_replace('.', '', $item->allowed_extensions ?? 'pdf,doc,docx,jpg,jpeg,png');

                                $hasExistingFile = $existingApl ? $existingApl->hasRequirementFile($item->id) : false;
                                $hasExistingMarker = $request && $request->has($fieldName . '_existing');

                                if ($item->is_required && !$hasExistingFile && !$hasExistingMarker) {
                                    $rules[$fieldName] = "required|file|max:{$maxSizeKB}|mimes:{$allowedExtensions}";
                                } else {
                                    $rules[$fieldName] = "nullable|file|max:{$maxSizeKB}|mimes:{$allowedExtensions}";
                                }
                                break;

                            case 'email':
                                $rules[$fieldName] = $item->is_required ? 'required|email|max:255' : 'nullable|email|max:255';
                                break;

                            case 'url':
                                $rules[$fieldName] = $item->is_required ? 'required|url|max:255' : 'nullable|url|max:255';
                                break;

                            case 'date':
                                $rules[$fieldName] = $item->is_required ? 'required|date' : 'nullable|date';
                                break;

                            case 'select':
                            case 'radio':
                                $options = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                                if (is_array($options) && !empty($options)) {
                                    $optionValues = implode(',', $options);
                                    $rules[$fieldName] = $item->is_required ? "required|in:{$optionValues}" : "nullable|in:{$optionValues}";
                                }
                                break;

                            case 'checkbox':
                                $options = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                                if (is_array($options) && !empty($options)) {
                                    $rules[$fieldName] = 'nullable|array';
                                    $rules[$fieldName . '.*'] = 'in:' . implode(',', $options);
                                } else {
                                    $rules[$fieldName] = 'nullable|boolean';
                                }
                                break;

                            case 'textarea':
                                $rules[$fieldName] = $item->is_required ? 'required|string|max:2000' : 'nullable|string|max:2000';
                                break;

                            default:
                                // text_input
                                $rules[$fieldName] = $item->is_required ? 'required|string|max:1000' : 'nullable|string|max:1000';
                        }
                    }
                }
            }
        }

        return $rules;
    }

    protected function processSignature($signatureData)
    {
        if (empty($signatureData) || !str_starts_with($signatureData, 'data:image/')) {
            return null;
        }

        try {
            // Validate base64 image format
            if (!preg_match('/^data:image\/(\w+);base64,/', $signatureData, $matches)) {
                throw new \Exception('Invalid signature format');
            }

            $imageType = $matches[1];
            $allowedTypes = ['png', 'jpeg', 'jpg'];

            if (!in_array(strtolower($imageType), $allowedTypes)) {
                throw new \Exception('Invalid image type for signature');
            }

            // Return validated signature data - actual storage will be handled by model
            return $signatureData;
        } catch (\Exception $e) {
            Log::error('Error processing signature: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleRequirementResponses($request, $apl, $scheme)
    {
        // Pastikan requirement_answers selalu berupa array
        $responses = $apl->requirement_answers ?? [];
        if (!is_array($responses)) {
            $responses = json_decode($responses, true) ?: [];
        }

        $selectedTemplateId = $request->input('selected_requirement_template');

        if ($selectedTemplateId && $scheme->requirementTemplates) {
            $selectedTemplate = $scheme->requirementTemplates->where('id', $selectedTemplateId)->first();

            if ($selectedTemplate && $selectedTemplate->activeItems) {
                foreach ($selectedTemplate->activeItems as $item) {
                    $fieldName = "requirement_item_{$item->id}";
                    $existingMarker = $fieldName . '_existing';

                    if ($request->hasFile($fieldName)) {
                        try {
                            $file = $request->file($fieldName);
                            $maxSizeBytes = ($item->max_file_size ?? 5) * 1024 * 1024;

                            if ($file->getSize() > $maxSizeBytes) {
                                throw new \Exception('File terlalu besar. Maksimal ' . ($item->max_file_size ?? 5) . 'MB');
                            }

                            // Store file dan update requirement_answers
                            $filePath = $apl->storeRequirementFile($file, $item->id);

                            // Tidak perlu reload atau save lagi karena sudah dilakukan di storeRequirementFile
                        } catch (\Exception $e) {
                            Log::error("Error uploading file for item {$item->id}: " . $e->getMessage());
                            throw new \Exception("Gagal mengupload file untuk {$item->document_name}: " . $e->getMessage());
                        }
                    } elseif ($request->has($existingMarker)) {
                        // Pertahankan file lama (tidak diubah)
                    } elseif ($request->has($fieldName)) {
                        $value = $request->input($fieldName);

                        if ($item->type === 'checkbox' && is_array($value)) {
                            $apl->setRequirementItemAnswer($item->id, implode(',', $value));
                        } elseif ($value !== null && $value !== '') {
                            $apl->setRequirementItemAnswer($item->id, $value);
                        }
                        $apl->save();
                    }
                }
            }
        }
    }

    protected function generateAplNumber()
    {
        $year = date('Y');
        $month = date('m');

        $lastApl = Apl01Pendaftaran::whereNotNull('nomor_apl_01')->whereYear('created_at', $year)->whereMonth('created_at', $month)->orderBy('nomor_apl_01', 'desc')->first();

        if ($lastApl && preg_match('/(\d{4})$/', $lastApl->nomor_apl_01, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('APL01/%s%s/%04d', $year, $month, $nextNumber);
    }

    public function show($id)
    {
        $apl = Apl01Pendaftaran::where('user_id', Auth::id())
            ->with(['certificationScheme', 'user'])
            ->findOrFail($id);

        return view('asesi.apl01.show', compact('apl'));
    }
}
