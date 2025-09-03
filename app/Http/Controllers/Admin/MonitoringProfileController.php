<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\UserProfile;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MonitoringProfileController extends Controller
{
    public function index()
    {
        return view('admin.monitoringProfile.index');
    }

    public function getData(Request $request)
    {
        $profiles = UserProfile::with(['user'])->select(['id', 'user_id', 'nama_lengkap', 'nik', 'nama_tempat_kerja', 'email', 'created_at']);

        // 🔹 Tambahan: Filter tanggal
        if ($request->filled('tanggal_filter')) {
            $dates = explode(' s/d ', $request->tanggal_filter);
            if (count($dates) === 2) {
                try {
                    $start = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $end = \Carbon\Carbon::parse($dates[1])->endOfDay();
                    $profiles->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) {
                    // kalau format salah, abaikan filter
                }
            }
        }

        return DataTables::of($profiles)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $viewBtn =
                    '<a href="' .
                    route('admin.monitoring-profile.show', $row->id) .
                    '" class="btn btn-sm btn-outline-info me-1" title="Lihat Detail">
                    <i class="bi bi-eye"></i>
                </a>';
                $editBtn =
                    '<a href="' .
                    route('admin.monitoring-profile.edit', $row->id) .
                    '" class="btn btn-sm btn-outline-warning" title="Edit Profil">
                    <i class="bi bi-pencil-square"></i>
                </a>';
                return $viewBtn . $editBtn;
            })
            ->addColumn('status_dokumen', function ($row) {
                $documentCount = UserDocument::where('user_id', $row->user_id)->count();
                if ($documentCount > 0) {
                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lengkap (' . $documentCount . ')</span>';
                }
                return '<span class="badge bg-warning"><i class="bi bi-exclamation-triangle me-1"></i>Belum Upload</span>';
            })
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
            ->rawColumns(['action', 'status_dokumen'])
            ->make(true);
    }

    public function show($id)
    {
        try {
            $profile = UserProfile::with(['user'])->findOrFail($id);

            // Ambil dokumen sekaligus dengan atribut tambahan dari accessor model
            $documents = UserDocument::where('user_id', $profile->user_id)->get();

            return view('admin.monitoringProfile.show', compact('profile', 'documents'));
        } catch (Exception $e) {
            return back()->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $profile = UserProfile::with(['user'])->findOrFail($id);
        $documents = UserDocument::where('user_id', $profile->user_id)->get();

        return view('admin.monitoringProfile.edit', compact('profile', 'documents'));
    }

    public function update(Request $request, $id)
    {
        $profile = UserProfile::findOrFail($id);

        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'kebangsaan' => 'nullable|string|max:255',
            'alamat_rumah' => 'nullable|string',
            'no_telp_rumah' => 'nullable|string|max:20',
            'kota_rumah' => 'nullable|string|max:255',
            'provinsi_rumah' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'nama_sekolah_terakhir' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nama_tempat_kerja' => 'nullable|string|max:255',
            'kategori_pekerjaan' => 'nullable|string|max:255',
            'nama_jalan_kantor' => 'nullable|string|max:255',
            'kota_kantor' => 'nullable|string|max:255',
            'provinsi_kantor' => 'nullable|string|max:255',
            'kode_pos_kantor' => 'nullable|string|max:10',
            'negara_kantor' => 'nullable|string|max:255',
            'no_telp_kantor' => 'nullable|string|max:20',
        ]);

        $validatedData['updated_by'] = Auth::id();

        $profile->update($validatedData);

        return redirect()->route('admin.monitoring-profile.show', $profile->id)->with('success', 'Data profil berhasil diperbarui.');
    }

    // ==== Tambahan untuk dokumen ====
    public function viewDocument($documentId)
    {
        $document = UserDocument::findOrFail($documentId);

        if (!Storage::exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->file(storage_path('app/' . $document->file_path));
    }

    public function downloadDocument($documentId)
    {
        $document = UserDocument::findOrFail($documentId);

        if (!Storage::exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::download($document->file_path, basename($document->file_path));
    }
}
