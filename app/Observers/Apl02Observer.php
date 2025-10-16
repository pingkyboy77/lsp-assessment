<?php

namespace App\Observers;

use App\Models\Apl02;
use Illuminate\Support\Facades\Log;

class Apl02Observer
{
    /**
     * Handle the Apl02 "created" event.
     */
    public function created(Apl02 $apl02)
    {
        Log::info("APL 02 created: {$apl02->id} for user {$apl02->user_id}");

        // Initialize folder path
        $apl02->ensureFolderPath();

        // You could trigger notifications here
        // event(new Apl02Created($apl02));
    }

    /**
     * Handle the Apl02 "updated" event.
     */
    public function updated(Apl02 $apl02)
    {
        // Log status changes
        if ($apl02->wasChanged('status')) {
            $oldStatus = $apl02->getOriginal('status');
            $newStatus = $apl02->status;

            Log::info("APL 02 status changed: {$apl02->id} from {$oldStatus} to {$newStatus}");

            // Trigger status-specific events
            $this->handleStatusChange($apl02, $oldStatus, $newStatus);
        }

        // Log signature events
        if ($apl02->wasChanged('asesi_signed_at')) {
            Log::info("APL 02 signed by asesi: {$apl02->id}");
        }

        if ($apl02->wasChanged('asesor_signed_at')) {
            Log::info("APL 02 signed by asesor: {$apl02->id}");
        }
    }

    /**
     * Handle the Apl02 "deleted" event.
     */
    public function deleted(Apl02 $apl02)
    {
        Log::info("APL 02 deleted: {$apl02->id}");

        // Clean up associated files
        $this->cleanupFiles($apl02);
    }

    /**
     * Handle status change events
     */
    private function handleStatusChange(Apl02 $apl02, $oldStatus, $newStatus)
    {
        switch ($newStatus) {
            case 'submitted':
                // Notify reviewers
                // event(new Apl02Submitted($apl02));
                break;

            case 'approved':
                // Generate certificate or trigger next step
                // event(new Apl02Approved($apl02));
                break;

            case 'rejected':
                // Notify asesi
                // event(new Apl02Rejected($apl02));
                break;

            case 'returned':
                // Notify asesi for revision
                // event(new Apl02Returned($apl02));
                break;
        }
    }

    /**
     * Clean up files when APL 02 is deleted
     */
    private function cleanupFiles(Apl02 $apl02)
    {
        try {
            // Delete all evidence files
            $apl02->evidenceSubmissions()->each(function ($evidence) {
                $evidence->deleteFile();
            });

            // Delete folder if empty
            if ($apl02->file_folder_path) {
                \Storage::disk('public')->deleteDirectory($apl02->file_folder_path);
            }
        } catch (\Exception $e) {
            Log::error("Failed to cleanup files for APL 02 {$apl02->id}: " . $e->getMessage());
        }
    }
}
