<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CertificationScheme; 

class SkemaSertifikasiController extends Controller
{
    public function index()
    {
        $schemes = CertificationScheme::with('field')
                ->active()
                ->orderBy('nama')
                ->get();
        
        return view('asesi.skema.index', compact('schemes'));
    }
    
    public function register(Request $request, $schemeId)
    {
        $user = Auth::user();
        $userProfile = $user->profile;
        
        // Check if profile is complete
        $isProfileIncomplete = !$userProfile || 
            !$userProfile->nama_lengkap || 
            !$userProfile->nik || 
            !$userProfile->tempat_lahir || 
            !$userProfile->tanggal_lahir;
        
        if ($isProfileIncomplete) {
            return response()->json([
                'success' => false,
                'message' => 'Profil Anda belum lengkap. Silakan lengkapi profil terlebih dahulu.',
                'redirect' => route('asesi.data-pribadi.index')
            ], 400);
        }
        
        // Check if scheme exists
        $scheme = Scheme::findOrFail($schemeId);
        
        // Check if user already registered for this scheme
        $existingRegistration = $this->checkExistingRegistration($user->id, $schemeId);
        
        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar untuk skema sertifikasi ini.'
            ], 400);
        }
        
        try {
            // Create registration record
            $registration = $this->createRegistration($user->id, $schemeId);
            
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Anda akan dihubungi untuk jadwal asesmen.',
                'data' => [
                    'registration_id' => $registration->id,
                    'scheme_name' => $scheme->nama
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam proses pendaftaran. Silakan coba lagi.'
            ], 500);
        }
    }
    
    private function checkExistingRegistration($userId, $schemeId)
    {
        // Replace with your actual registration model
        // Example: return Registration::where('user_id', $userId)->where('scheme_id', $schemeId)->exists();
        return false; // For demonstration
    }
    
    private function createRegistration($userId, $schemeId)
    {
        // Replace with your actual registration creation logic
        // Example:
        /*
        return Registration::create([
            'user_id' => $userId,
            'scheme_id' => $schemeId,
            'status' => 'pending',
            'registered_at' => now()
        ]);
        */
        
        // For demonstration, return a mock object
        return (object) [
            'id' => rand(1000, 9999),
            'user_id' => $userId,
            'scheme_id' => $schemeId,
            'status' => 'pending'
        ];
    }
    
    public function checkProfileStatus()
    {
        $user = Auth::user();
        $userProfile = $user->profile;
        
        $isProfileIncomplete = !$userProfile || 
            !$userProfile->nama_lengkap || 
            !$userProfile->nik || 
            !$userProfile->tempat_lahir || 
            !$userProfile->tanggal_lahir;
        
        $completedFields = 0;
        $totalFields = 4;
        
        if ($userProfile) {
            if ($userProfile->nama_lengkap) $completedFields++;
            if ($userProfile->nik) $completedFields++;
            if ($userProfile->tempat_lahir) $completedFields++;
            if ($userProfile->tanggal_lahir) $completedFields++;
        }
        
        return response()->json([
            'is_complete' => !$isProfileIncomplete,
            'completed_fields' => $completedFields,
            'total_fields' => $totalFields,
            'progress_percentage' => round(($completedFields / $totalFields) * 100),
            'missing_fields' => $this->getMissingFields($userProfile)
        ]);
    }
    
    private function getMissingFields($userProfile)
    {
        $missing = [];
        
        if (!$userProfile || !$userProfile->nama_lengkap) {
            $missing[] = 'Nama Lengkap';
        }
        if (!$userProfile || !$userProfile->nik) {
            $missing[] = 'NIK';
        }
        if (!$userProfile || !$userProfile->tempat_lahir) {
            $missing[] = 'Tempat Lahir';
        }
        if (!$userProfile || !$userProfile->tanggal_lahir) {
            $missing[] = 'Tanggal Lahir';
        }
        
        return $missing;
    }
}