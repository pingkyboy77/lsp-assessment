<?php

namespace App\Traits;

trait HasApl02Assessment
{
    /**
     * Check if user has APL 02 for specific scheme
     */
    public function hasApl02ForScheme($schemeId)
    {
        return $this->apl02s()->where('certification_scheme_id', $schemeId)->exists();
    }

    /**
     * Get APL 02 for specific scheme
     */
    public function getApl02ForScheme($schemeId)
    {
        return $this->apl02s()->where('certification_scheme_id', $schemeId)->first();
    }

    /**
     * Get latest APL 02
     */
    public function getLatestApl02()
    {
        return $this->apl02s()->latest()->first();
    }

    /**
     * Get APL 02 statistics for user
     */
    public function getApl02Stats()
    {
        $apl02s = $this->apl02s;

        return [
            'total' => $apl02s->count(),
            'draft' => $apl02s->where('status', 'draft')->count(),
            'submitted' => $apl02s->where('status', 'submitted')->count(),
            'approved' => $apl02s->where('status', 'approved')->count(),
            'rejected' => $apl02s->where('status', 'rejected')->count(),
            'avg_competency' => $apl02s->avg('competency_percentage'),
            'total_evidence' => $apl02s->sum(function ($apl02) {
                return $apl02->evidenceSubmissions->count();
            }),
        ];
    }

    /**
     * Get APL 02 relationship
     */
    public function apl02s()
    {
        return $this->hasMany(\App\Models\Apl02::class);
    }
}
