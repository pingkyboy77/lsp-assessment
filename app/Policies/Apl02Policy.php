<?php

namespace App\Policies;

use App\Models\Apl02;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class Apl02Policy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function getApl02Assessment($apl02Id)
    {
        return $this->hasOne(Apl02ElementAssessment::class, 'elemen_kompetensi_id')->where('apl_02_id', $apl02Id);
    }

    // Add to UnitKompetensi model methods
    // app/Models/UnitKompetensi.php - Add these methods

    public function apl02ElementAssessments()
    {
        return $this->hasMany(Apl02ElementAssessment::class, 'unit_kompetensi_id');
    }

    public function getApl02AssessmentStats($apl02Id = null)
    {
        $query = $this->apl02ElementAssessments();

        if ($apl02Id) {
            $query->where('apl_02_id', $apl02Id);
        }

        $assessments = $query->get();

        return [
            'total' => $assessments->count(),
            'kompeten' => $assessments->where('assessment_result', 'kompeten')->count(),
            'belum_kompeten' => $assessments->where('assessment_result', 'belum_kompeten')->count(),
            'not_assessed' => $assessments->whereNull('assessment_result')->count(),
        ];
    }
}
