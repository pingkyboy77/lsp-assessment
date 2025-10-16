<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Apl02;
use App\Models\Apl02EvidenceSubmission;
use Illuminate\Support\Facades\Storage;

class CleanupApl02FilesCommand extends Command
{
    protected $signature = 'apl02:cleanup-files {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Cleanup orphaned APL 02 files and optimize storage';

    public function handle()
    {
        $this->info('Starting APL 02 files cleanup...');

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No files will be deleted');
        }

        // Find orphaned evidence files
        $orphanedFiles = $this->findOrphanedEvidenceFiles();

        // Find empty directories
        $emptyDirs = $this->findEmptyDirectories();

        // Find oversized APL 02s
        $oversizedApl02s = $this->findOversizedApl02s();

        $this->info("Found {$orphanedFiles->count()} orphaned files");
        $this->info("Found {$emptyDirs->count()} empty directories");
        $this->info("Found {$oversizedApl02s->count()} oversized APL 02s");

        if (!$isDryRun && $this->confirm('Proceed with cleanup?')) {
            $this->performCleanup($orphanedFiles, $emptyDirs);
        }

        return Command::SUCCESS;
    }

    private function findOrphanedEvidenceFiles()
    {
        $evidenceFiles = Apl02EvidenceSubmission::all();
        $orphaned = collect();

        foreach ($evidenceFiles as $evidence) {
            if (!Storage::disk('public')->exists($evidence->file_path)) {
                $orphaned->push($evidence);
            }
        }

        return $orphaned;
    }

    private function findEmptyDirectories()
    {
        $basePath = 'apl02-files';
        $directories = collect();

        if (Storage::disk('public')->exists($basePath)) {
            $allDirs = Storage::disk('public')->allDirectories($basePath);

            foreach ($allDirs as $dir) {
                $files = Storage::disk('public')->allFiles($dir);
                if (empty($files)) {
                    $directories->push($dir);
                }
            }
        }

        return $directories;
    }

    private function findOversizedApl02s($maxSizeMB = 100)
    {
        $maxSize = $maxSizeMB * 1024 * 1024; // Convert to bytes

        return Apl02::whereHas('evidenceSubmissions')
            ->get()
            ->filter(function ($apl02) use ($maxSize) {
                $totalSize = $apl02->evidenceSubmissions->sum('file_size');
                return $totalSize > $maxSize;
            });
    }

    private function performCleanup($orphanedFiles, $emptyDirs)
    {
        // Remove orphaned database records
        foreach ($orphanedFiles as $evidence) {
            $evidence->delete();
            $this->line("Deleted orphaned record: {$evidence->file_name}");
        }

        // Remove empty directories
        foreach ($emptyDirs as $dir) {
            Storage::disk('public')->deleteDirectory($dir);
            $this->line("Deleted empty directory: {$dir}");
        }

        $this->info('Cleanup completed successfully');
    }
}
