<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\UserDocument;
use App\Models\RegionKab;
use App\Models\RegionProv;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class DataPribadiController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::id();
            $profile = UserProfile::where('user_id', $userId)->first();
            // Menggunakan method baru dari UserDocument model
            $documents = UserDocument::where('user_id', auth()->id())
                ->get()
                ->keyBy('document_type');

            // Load cities with their provinces for dynamic dropdown
            $cities = RegionKab::with('province')->orderBy('name')->get();

            return view('asesi.data-pribadi', compact('profile', 'documents', 'cities'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        // Convert inputs to uppercase before validation
        $this->convertInputsToUppercase($request);

        // Validasi sesuai dengan struktur migration
        try {
            $request->validate([
                // Data Personal
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|digits:16',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'kebangsaan' => 'nullable|string|max:255',

                // Alamat Rumah
                'alamat_rumah' => 'nullable|string',
                'no_telp_rumah' => 'nullable|string|max:20',
                'kota_rumah' => 'nullable|exists:region_kab,id',
                'provinsi_rumah' => 'nullable|exists:region_prov,id',
                'kode_pos' => 'nullable|string|max:10',
                'no_hp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',

                // Pendidikan
                'pendidikan_terakhir' => 'nullable|string|max:255',
                'nama_sekolah_terakhir' => 'nullable|string|max:255',

                // Pekerjaan
                'pekerjaan' => 'nullable|string|max:255',
                'jabatan' => 'nullable|string|max:255',
                'nama_tempat_kerja' => 'nullable|string|max:255',
                'kategori_pekerjaan' => 'nullable|string|max:255',

                // Alamat Kantor
                'alamat_kantor' => 'nullable|string',
                'nama_jalan_kantor' => 'nullable|string|max:255',
                'kota_kantor' => 'nullable|exists:region_kab,id',
                'provinsi_kantor' => 'nullable|exists:region_prov,id',
                'kode_pos_kantor' => 'nullable|string|max:10',
                'negara_kantor' => 'nullable|string|max:255',
                'no_telp_kantor' => 'nullable|string|max:20',

                // Documents - increased max size to 5MB
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            ]);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Validasi gagal: ' . $e->getMessage())
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $userId = Auth::id();

            // Prepare profile data for storage
            $profileData = $request->only([
                // Data Personal
                'nama_lengkap',
                'nik',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'kebangsaan',

                // Alamat Rumah
                'alamat_rumah',
                'no_telp_rumah',
                'kota_rumah',
                'provinsi_rumah',
                'kode_pos',
                'no_hp',
                'email',

                // Pendidikan
                'pendidikan_terakhir',
                'nama_sekolah_terakhir',

                // Pekerjaan
                'pekerjaan',
                'jabatan',
                'nama_tempat_kerja',
                'kategori_pekerjaan',

                // Alamat Kantor
                'alamat_kantor',
                'nama_jalan_kantor',
                'kota_kantor',
                'provinsi_kantor',
                'kode_pos_kantor',
                'negara_kantor',
                'no_telp_kantor',
            ]);
            
            $profileData['user_id'] = $userId;
            $profileData['updated_by'] = $userId;

            // Set default value untuk negara_kantor jika tidak ada
            if (empty($profileData['negara_kantor'])) {
                $profileData['negara_kantor'] = 'Indonesia';
            }

            // Convert empty strings to null for foreign key fields
            $foreignKeyFields = ['kota_rumah', 'provinsi_rumah', 'kota_kantor', 'provinsi_kantor'];
            foreach ($foreignKeyFields as $field) {
                if (empty($profileData[$field])) {
                    $profileData[$field] = null;
                }
            }

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $userId],
                array_merge($profileData, [
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'nik' => $request->nik,
                ])
            );


            // ✅ PERBAIKAN UTAMA: Gunakan method storeDocument dari model UserDocument
            if ($request->hasFile('documents')) {
                $uploadedCount = 0;
                $failedUploads = [];

                foreach ($request->file('documents') as $docType => $file) {
                    if ($file && $file->isValid()) {
                        try {
                            // ✅ Gunakan method storeDocument yang sudah ada di model
                            // Method ini akan otomatis membuat struktur folder: user-documents/YYYY/MM/user-name/document-type/
                            $document = UserDocument::storeDocument(
                                $file,
                                $userId,
                                $docType,
                                $this->getDocumentDescription($docType)
                            );

                            if ($document) {
                                $uploadedCount++;
                            } else {
                                $failedUploads[$docType] = 'Gagal menyimpan dokumen';
                            }
                        } catch (Exception $e) {
                            $failedUploads[$docType] = $e->getMessage();
                        }
                    }
                }

                // Buat pesan sukses/gagal
                $messages = [];
                if ($uploadedCount > 0) {
                    $messages[] = "{$uploadedCount} dokumen berhasil diupload dengan struktur folder terorganisir.";
                }

                if (!empty($failedUploads)) {
                    $failedMessages = [];
                    foreach ($failedUploads as $docType => $error) {
                        $failedMessages[] = "{$docType}: {$error}";
                    }
                    $messages[] = "Gagal upload: " . implode('; ', $failedMessages);
                }
            }

            DB::commit();

            $successMessage = 'Data profil berhasil disimpan!';
            if (isset($messages) && !empty($messages)) {
                $successMessage .= ' ' . implode(' ', $messages);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get document description based on type
     */
    private function getDocumentDescription($jenisDoc)
    {
        $descriptions = [
            'ktp' => 'Kartu Tanda Penduduk',
            'ijazah' => 'Ijazah Pendidikan Terakhir',
            'sertifikat' => 'Sertifikat Kompetensi/Pelatihan',
            'cv' => 'Curriculum Vitae',
            'foto' => 'Foto Profil',
            'kk' => 'Kartu Keluarga',
            'npwp' => 'Nomor Pokok Wajib Pajak',
            'bpjs' => 'Kartu BPJS',
            'surat_kerja' => 'Surat Keterangan Kerja',
            'portofolio' => 'Dokumen Portofolio',
        ];

        return $descriptions[$jenisDoc] ?? ucfirst(str_replace('_', ' ', $jenisDoc));
    }

    /**
     * Convert specific inputs to uppercase
     */
    private function convertInputsToUppercase(Request $request)
    {
        $fieldsToUppercase = ['nama_lengkap', 'nik', 'tempat_lahir', 'kebangsaan', 'alamat_rumah', 'no_telp_rumah', 'no_hp', 'nama_sekolah_terakhir', 'jabatan', 'nama_tempat_kerja', 'nama_jalan_kantor', 'no_telp_kantor', 'kode_pos', 'kode_pos_kantor'];

        foreach ($fieldsToUppercase as $field) {
            if ($request->has($field) && !empty($request->input($field))) {
                $request->merge([
                    $field => strtoupper($request->input($field)),
                ]);
            }
        }

        // Keep email lowercase
        if ($request->has('email') && !empty($request->input('email'))) {
            $request->merge([
                'email' => strtolower($request->input('email')),
            ]);
        }
    }

    public function downloadDocument($id)
    {
        try {
            $document = UserDocument::findOrFail($id);

            // Check if user can access this document
            if ($document->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            // Menggunakan method download dari model
            return $document->download();
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }

    public function deleteDocument($id)
    {
        DB::beginTransaction();

        try {
            $document = UserDocument::findOrFail($id);

            // Check if user can delete this document
            if ($document->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            $documentType = $document->document_type; // Store for success message

            // Menggunakan method deleteDocument dari model
            $document->deleteDocument();

            DB::commit();

            return redirect()->back()->with('success', "Dokumen {$documentType} berhasil dihapus!");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Replace existing document with new one
     */
    public function replaceDocument(Request $request, $id)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
                'description' => 'nullable|string|max:255',
            ]);

            $document = UserDocument::findOrFail($id);

            // Check if user can replace this document
            if ($document->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            DB::beginTransaction();

            // ✅ Replace document menggunakan method dari model yang sudah mendukung struktur folder terorganisir
            $document->replaceDocument($request->file('document'), $request->input('description'));

            DB::commit();
            return redirect()->back()->with('success', 'Dokumen berhasil diganti dengan struktur folder yang terorganisir!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengganti dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Get user storage statistics
     */
    public function getStorageStats()
    {
        try {
            $userId = Auth::id();
            $stats = UserDocument::getUserStorageStats($userId);

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Cleanup user's orphaned files
     */
    public function cleanupFiles()
    {
        try {
            $userId = Auth::id();
            $cleanedCount = UserDocument::cleanupOrphanedFiles($userId);

            return redirect()
                ->back()
                ->with('success', "Berhasil membersihkan {$cleanedCount} file orphaned.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat membersihkan file: ' . $e->getMessage());
        }
    }

    /**
     * Migrate user documents to new folder structure
     */
    public function migrateDocuments()
    {
        try {
            $userId = Auth::id();
            $result = UserDocument::batchMigrateToNewStructure($userId);

            $message = "Migrasi selesai. {$result['migrated']} dokumen berhasil dimigrate";
            if ($result['failed'] > 0) {
                $message .= ", {$result['failed']} dokumen gagal dimigrate";
            }

            return redirect()->back()->with('success', $message);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat migrasi dokumen: ' . $e->getMessage());
        }
    }
}
