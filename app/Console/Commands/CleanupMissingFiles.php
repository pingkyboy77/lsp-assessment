<?php
// File: app/Console/Commands/CleanupMissingFiles.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Apl01Pendaftaran;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Storage;

class CleanupMissingFiles extends Command
{
    protected $signature = 'apl01:cleanup-missing-files {--dry-run : Show what would be cleaned without actually doing it}';
    protected $description = 'Clean up missing file references from APL 01 applications';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting cleanup of missing files...');
        $this->info($isDryRun ? 'DRY RUN MODE - No changes will be made' : 'LIVE MODE - Changes will be applied');
        $this->newLine();

        $missingUserDocs = 0;
        $missingRequirementDocs = 0;
        $fixedUserDocs = 0;
        $fixedRequirementDocs = 0;

        // Check User Documents
        $this->info('Checking User Documents...');
        $userDocs = UserDocument::whereNotNull('file_path')->get();

        foreach ($userDocs as $doc) {
            if (!Storage::disk('public')->exists($doc->file_path)) {
                $missingUserDocs++;
                $this->warn("Missing: {$doc->document_type} - {$doc->file_path}");

                if (!$isDryRun) {
                    // Clear the file reference but keep the record
                    $doc->update([
                        'file_path' => null,
                        'file_size' => null,
                        'original_name' => $doc->original_name . ' (FILE MISSING)'
                    ]);
                    $fixedUserDocs++;
                }
            }
        }

        // Check Requirement Documents
        $this->info('Checking Requirement Documents...');
        $apls = Apl01Pendaftaran::whereNotNull('requirement_answers')->get();

        foreach ($apls as $apl) {
            if (!is_array($apl->requirement_answers)) continue;

            $requirementAnswers = $apl->requirement_answers;
            $hasChanges = false;

            foreach ($requirementAnswers as $itemId => $filePath) {
                if (!$filePath) continue;

                if (!Storage::disk('public')->exists($filePath)) {
                    $missingRequirementDocs++;
                    $this->warn("Missing: APL {$apl->nomor_apl_01} - Item {$itemId} - {$filePath}");

                    if (!$isDryRun) {
                        // Remove the missing file reference
                        unset($requirementAnswers[$itemId]);
                        $hasChanges = true;
                    }
                }
            }

            if ($hasChanges && !$isDryRun) {
                $apl->update(['requirement_answers' => $requirementAnswers]);
                $fixedRequirementDocs++;
            }
        }

        // Summary
        $this->newLine();
        $this->info('=== SUMMARY ===');
        $this->line("Missing User Documents: {$missingUserDocs}");
        $this->line("Missing Requirement Documents: {$missingRequirementDocs}");

        if (!$isDryRun) {
            $this->line("Fixed User Documents: {$fixedUserDocs}");
            $this->line("Fixed APL Records: {$fixedRequirementDocs}");
            $this->info('Cleanup completed!');
        } else {
            $this->info('Run without --dry-run to apply fixes');
        }

        return 0;
    }
}
