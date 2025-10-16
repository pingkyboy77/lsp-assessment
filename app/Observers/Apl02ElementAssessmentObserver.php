<?php

namespace App\Observers;

use App\Models\Apl02ElementAssessment;

class Apl02ElementAssessmentObserver
{
    /**
     * Handle the assessment "saved" event.
     */
    public function saved(Apl02ElementAssessment $assessment)
    {
        // Recalculate APL 02 competency stats whenever an assessment is saved
        if ($assessment->apl02) {
            $assessment->apl02->calculateCompetencyStats();
        }
    }

    /**
     * Handle the assessment "deleted" event.
     */
    public function deleted(Apl02ElementAssessment $assessment)
    {
        // Recalculate APL 02 competency stats when an assessment is deleted
        if ($assessment->apl02) {
            $assessment->apl02->calculateCompetencyStats();
        }
    }
}
