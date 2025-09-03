<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NumberSequence;
use App\Helpers\NumberSequence as NumberSequenceHelper;

class ManageNumberSequence extends Command
{
    protected $signature = 'sequence:manage 
                            {action : Action to perform (list|preview|reset|generate)}
                            {key? : Sequence key for specific actions}
                            {--count=5 : Number of previews to show}';

    protected $description = 'Manage number sequences';

    public function handle()
    {
        $action = $this->argument('action');
        $key = $this->argument('key');

        switch ($action) {
            case 'list':
                return $this->listSequences();
            
            case 'preview':
                return $this->previewSequence($key);
            
            case 'reset':
                return $this->resetSequence($key);
            
            case 'generate':
                return $this->generateNumber($key);
            
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    private function listSequences()
    {
        $sequences = NumberSequence::active()->orderBy('sequence_key')->get();
        
        if ($sequences->isEmpty()) {
            $this->info('No active sequences found.');
            return;
        }

        $this->table(
            ['Key', 'Name', 'Current Number', 'Current Period', 'Last Updated'],
            $sequences->map(function ($seq) {
                return [
                    $seq->sequence_key,
                    $seq->name,
                    $seq->current_number,
                    $seq->current_period ?? '-',
                    $seq->updated_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        );
    }

    private function previewSequence($key)
    {
        if (!$key) {
            $this->error('Please provide a sequence key.');
            return 1;
        }

        $count = $this->option('count');
        $previews = NumberSequenceHelper::preview($key, $count);

        $this->info("Preview for sequence '{$key}':");
        foreach ($previews as $i => $preview) {
            $this->line(($i + 1) . ". {$preview}");
        }
    }

    private function resetSequence($key)
    {
        if (!$key) {
            $this->error('Please provide a sequence key.');
            return 1;
        }

        if (NumberSequenceHelper::reset($key)) {
            $this->info("Sequence '{$key}' has been reset.");
        } else {
            $this->error("Failed to reset sequence '{$key}'.");
        }
    }

    private function generateNumber($key)
    {
        if (!$key) {
            $this->error('Please provide a sequence key.');
            return 1;
        }

        try {
            $number = NumberSequenceHelper::generate($key);
            $this->info("Generated number: {$number}");
        } catch (\Exception $e) {
            $this->error("Failed to generate number: " . $e->getMessage());
        }
    }
}