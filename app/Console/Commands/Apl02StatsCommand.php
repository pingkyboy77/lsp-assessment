<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Apl02;
use App\Models\Apl02ElementAssessment;
use App\Models\Apl02EvidenceSubmission;

class Apl02StatsCommand extends Command
{
    protected $signature = 'apl02:stats {--format=table : Output format (table|json)}';
    protected $description = 'Display APL 02 statistics and analytics';

    public function handle()
    {
        $this->info('APL 02 Statistics Report');
        $this->line('=' . str_repeat('=', 50));

        $stats = $this->gatherStats();

        if ($this->option('format') === 'json') {
            $this->line(json_encode($stats, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }

        $this->displayTableStats($stats);
        return Command::SUCCESS;
    }

    private function gatherStats()
    {
        return [
            'overview' => [
                'total_apl02' => Apl02::count(),
                'by_status' => Apl02::groupBy('status')
                    ->selectRaw('status, count(*) as count')
                    ->pluck('count', 'status')
                    ->toArray(),
                'signed_by_asesi' => Apl02::whereNotNull('asesi_signed_at')->count(),
                'signed_by_asesor' => Apl02::whereNotNull('asesor_signed_at')->count(),
                'fully_signed' => Apl02::whereNotNull('asesi_signed_at')
                    ->whereNotNull('asesor_signed_at')
                    ->count(),
            ],
            'assessments' => [
                'total_assessments' => Apl02ElementAssessment::count(),
                'kompeten' => Apl02ElementAssessment::where('assessment_result', 'kompeten')->count(),
                'belum_kompeten' => Apl02ElementAssessment::where('assessment_result', 'belum_kompeten')->count(),
                'not_assessed' => Apl02ElementAssessment::whereNull('assessment_result')->count(),
                'avg_competency' => Apl02::avg('competency_percentage'),
            ],
            'evidence' => [
                'total_evidence' => Apl02EvidenceSubmission::count(),
                'total_files_size' => Apl02EvidenceSubmission::sum('file_size'),
                'by_file_type' => Apl02EvidenceSubmission::groupBy('file_type')
                    ->selectRaw('file_type, count(*) as count')
                    ->pluck('count', 'file_type')
                    ->toArray(),
                'avg_files_per_apl02' => Apl02EvidenceSubmission::count() / max(Apl02::count(), 1),
            ],
            'performance' => [
                'high_performers' => Apl02::where('competency_percentage', '>=', 80)->count(),
                'medium_performers' => Apl02::whereBetween('competency_percentage', [65, 79])->count(),
                'low_performers' => Apl02::where('competency_percentage', '<', 65)->count(),
                'completion_rate' => Apl02::where('status', 'approved')->count() / max(Apl02::count(), 1) * 100,
            ]
        ];
    }

    private function displayTableStats($stats)
    {
        // Overview Table
        $this->info("\nOverview:");
        $overviewData = [];
        foreach ($stats['overview'] as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $overviewData[] = [ucfirst($subKey), $subValue];
                }
            } else {
                $overviewData[] = [str_replace('_', ' ', ucfirst($key)), $value];
            }
        }
        $this->table(['Metric', 'Value'], $overviewData);

        // Assessment Stats
        $this->info("\nAssessment Statistics:");
        $assessmentData = [];
        foreach ($stats['assessments'] as $key => $value) {
            $assessmentData[] = [str_replace('_', ' ', ucfirst($key)), is_float($value) ? round($value, 2) : $value];
        }
        $this->table(['Metric', 'Value'], $assessmentData);

        // Evidence Stats
        $this->info("\nEvidence Statistics:");
        $evidenceData = [];
        foreach ($stats['evidence'] as $key => $value) {
            if ($key === 'total_files_size') {
                $value = $this->formatBytes($value);
            } elseif ($key === 'by_file_type' && is_array($value)) {
                foreach ($value as $type => $count) {
                    $evidenceData[] = [ucfirst($type) . ' files', $count];
                }
                continue;
            } elseif (is_float($value)) {
                $value = round($value, 2);
            }
            $evidenceData[] = [str_replace('_', ' ', ucfirst($key)), $value];
        }
        $this->table(['Metric', 'Value'], $evidenceData);

        // Performance Stats
        $this->info("\nPerformance Statistics:");
        $performanceData = [];
        foreach ($stats['performance'] as $key => $value) {
            if ($key === 'completion_rate') {
                $value = round($value, 2) . '%';
            }
            $performanceData[] = [str_replace('_', ' ', ucfirst($key)), $value];
        }
        $this->table(['Metric', 'Value'], $performanceData);
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
