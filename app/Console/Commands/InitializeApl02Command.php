<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Apl01;
use App\Models\Apl02;
use App\Models\Apl02ElementAssessment;

class InitializeApl02Command extends Command
{
    protected $signature = 'apl02:initialize {--force : Force initialization even if APL 02 exists}';
    protected $description = 'Initialize APL 02 for all approved APL 01';

    public function handle()
    {
        $this->info('Starting APL 02 initialization...');

        $approvedApl01s = Apl01::where('status', 'approved')
            ->with(['user', 'certificationScheme'])
            ->get();

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($approvedApl01s as $apl01) {
            try {
                // Check if APL 02 already exists
                if (!$this->option('force') && $apl01->apl02()->exists()) {
                    $this->line("Skipped: APL 02 already exists for {$apl01->nama_lengkap}");
                    $skipped++;
                    continue;
                }

                // Delete existing APL 02 if force option is used
                if ($this->option('force')) {
                    $apl01->apl02()->delete();
                }

                // Create APL 02
                $apl02 = Apl02::create([
                    'user_id' => $apl01->user_id,
                    'apl_01_id' => $apl01->id,
                    'certification_scheme_id' => $apl01->certification_scheme_id,
                    'status' => 'draft'
                ]);

                // Initialize element assessments
                $this->initializeElementAssessments($apl02);

                $this->info("Created: APL 02 for {$apl01->nama_lengkap}");
                $created++;
            } catch (\Exception $e) {
                $this->error("Error creating APL 02 for {$apl01->nama_lengkap}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("Initialization completed:");
        $this->line("- Created: {$created}");
        $this->line("- Skipped: {$skipped}");
        $this->line("- Errors: {$errors}");

        return Command::SUCCESS;
    }

    private function initializeElementAssessments(Apl02 $apl02)
    {
        $elements = $apl02->certificationScheme
            ->elemenKompetensis()
            ->whereHas('unitKompetensi', function ($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->with('unitKompetensi:id')
            ->get();

        foreach ($elements as $element) {
            Apl02ElementAssessment::create([
                'apl_02_id' => $apl02->id,
                'unit_kompetensi_id' => $element->unit_kompetensi_id,
                'elemen_kompetensi_id' => $element->id,
                'assessment_result' => null
            ]);
        }

        $this->line("  - Initialized {$elements->count()} element assessments");
    }
}
