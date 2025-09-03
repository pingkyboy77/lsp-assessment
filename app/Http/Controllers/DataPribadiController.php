<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\UserDocument;
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
            $documents = UserDocument::where('user_id', $userId)->get();

            return view('asesi.data-pribadi', compact('profile', 'documents'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        // Validasi sesuai dengan struktur migration
        try {
            $request->validate([
                // Data Personal
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'nullable|string|max:16',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'kebangsaan' => 'nullable|string|max:255',
                
                // Alamat Rumah
                'alamat_rumah' => 'nullable|string',
                'no_telp_rumah' => 'nullable|string|max:20',
                'kota_rumah' => 'nullable|string|max:255',
                'provinsi_rumah' => 'nullable|string|max:255',
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
                'kota_kantor' => 'nullable|string|max:255',
                'provinsi_kantor' => 'nullable|string|max:255',
                'kode_pos_kantor' => 'nullable|string|max:10',
                'negara_kantor' => 'nullable|string|max:255',
                'no_telp_kantor' => 'nullable|string|max:20',
                
                // Documents
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);
        } catch (Exception $e) {
            return redirect()->back()
                           ->with('error', 'Validasi gagal: ' . $e->getMessage())
                           ->withInput();
        }

        DB::beginTransaction();

        try {
            $userId = Auth::id();
            
            // Simpan atau update profile dengan semua field sesuai migration
            $profileData = $request->only([
                // Data Personal
                'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'kebangsaan',
                
                // Alamat Rumah
                'alamat_rumah', 'no_telp_rumah', 'kota_rumah', 'provinsi_rumah', 'kode_pos', 'no_hp', 'email',
                
                // Pendidikan
                'pendidikan_terakhir', 'nama_sekolah_terakhir',
                
                // Pekerjaan
                'pekerjaan', 'jabatan', 'nama_tempat_kerja', 'kategori_pekerjaan',
                
                // Alamat Kantor
                'alamat_kantor', 'nama_jalan_kantor', 'kota_kantor', 'provinsi_kantor', 
                'kode_pos_kantor', 'negara_kantor', 'no_telp_kantor'
            ]);

            $profileData['user_id'] = $userId;
            $profileData['updated_by'] = $userId;

            // Set default value untuk negara_kantor jika tidak ada
            if (empty($profileData['negara_kantor'])) {
                $profileData['negara_kantor'] = 'Indonesia';
            }

            $profile = UserProfile::updateOrCreate(
                ['user_id' => $userId],
                $profileData + ['created_by' => $userId]
            );

            // Handle file uploads
            if ($request->hasFile('documents')) {
                try {
                    foreach ($request->file('documents') as $jenisDoc => $file) {
                        if ($file && $file->isValid()) {
                            // Delete existing file if exists
                            $existingDoc = UserDocument::where('user_id', $userId)
                                                     ->where('jenis_dokumen', $jenisDoc)
                                                     ->first();
                            
                            if ($existingDoc && Storage::disk('public')->exists($existingDoc->file_path)) {
                                try {
                                    Storage::disk('public')->delete($existingDoc->file_path);
                                } catch (Exception $e) {
                                    // Log error tapi lanjutkan proses
                                    \Log::warning('Gagal menghapus file lama: ' . $e->getMessage());
                                }
                            }

                            // Upload new file
                            $fileName = time() . '_' . $jenisDoc . '.' . $file->getClientOriginalExtension();
                            
                            try {
                                $filePath = $file->storeAs('documents', $fileName, 'public');
                                
                                if (!$filePath) {
                                    throw new Exception('Gagal menyimpan file ' . $jenisDoc);
                                }

                                UserDocument::updateOrCreate(
                                    [
                                        'user_id' => $userId,
                                        'jenis_dokumen' => $jenisDoc
                                    ],
                                    [
                                        'file_path' => $filePath,
                                        'created_by' => $userId,
                                        'updated_by' => $userId
                                    ]
                                );
                            } catch (Exception $e) {
                                throw new Exception('Gagal mengupload dokumen ' . $jenisDoc . ': ' . $e->getMessage());
                            }
                        }
                    }
                } catch (Exception $e) {
                    throw new Exception('Error saat mengupload dokumen: ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data profil berhasil disimpan!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                           ->withInput();
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

            if (!Storage::disk('public')->exists($document->file_path)) {
                return redirect()->back()->with('error', 'File tidak ditemukan.');
            }

            $filePath = Storage::disk('public')->path($document->file_path);
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak dapat diakses.');
            }

            return Storage::disk('public')->download($document->file_path);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
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

            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                try {
                    Storage::disk('public')->delete($document->file_path);
                } catch (Exception $e) {
                    \Log::warning('Gagal menghapus file dari storage: ' . $e->getMessage());
                    // Lanjutkan untuk menghapus record dari database
                }
            }

            // Delete record from database
            $document->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Dokumen berhasil dihapus!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage());
        }
    }
}