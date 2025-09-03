<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAsesiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userProfile = $user->profile;

        // Check if profile is complete
        $isProfileIncomplete = !$userProfile || !$userProfile->nama_lengkap || !$userProfile->nik || !$userProfile->tempat_lahir || !$userProfile->tanggal_lahir;

        // Dashboard data - only show real data if profile is complete
        $dashboardData = [];

        if (!$isProfileIncomplete) {
            $dashboardData = [
                'total_jadwal_asesmen' => $this->getTotalJadwalAsesmen($user->id),
                'asesmen_selesai' => $this->getAsesmenSelesai($user->id),
                'sertifikat_diterbitkan' => $this->getSertifikatDiterbitkan($user->id),
                'tingkat_keberhasilan' => $this->getTingkatKeberhasilan($user->id),
            ];
        } else {
            // Show zeros if profile is incomplete
            $dashboardData = [
                'total_jadwal_asesmen' => 0,
                'asesmen_selesai' => 0,
                'sertifikat_diterbitkan' => 0,
                'tingkat_keberhasilan' => 0,
            ];
        }

        return view('dashboard.asesi', compact('dashboardData'));
    }

    private function getTotalJadwalAsesmen($userId)
    {
        return 0; // default dulu
    }

    private function getAsesmenSelesai($userId)
    {
        return 0; // default dulu
    }

    private function getSertifikatDiterbitkan($userId)
    {
        return 0; // default dulu
    }

    private function getTingkatKeberhasilan($userId)
    {
        return 0; // default dulu
    }

}
