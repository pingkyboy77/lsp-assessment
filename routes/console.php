<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\Apl02StatsCommand;
use App\Console\Commands\InitializeApl02Command;
use App\Console\Commands\CleanupApl02FilesCommand;

// Register commands
Artisan::command('apl02:initialize {--force : Force initialization}', function () {
    $command = new InitializeApl02Command();
    return $command->handle();
})->purpose('Initialize APL 02 for all approved APL 01');

Artisan::command('apl02:cleanup-files {--dry-run : Show what would be deleted}', function () {
    $command = new CleanupApl02FilesCommand();
    return $command->handle();
})->purpose('Cleanup orphaned APL 02 files');

Artisan::command('apl02:stats {--format=table : Output format}', function () {
    $command = new Apl02StatsCommand();
    return $command->handle();
})->purpose('Display APL 02 statistics');

// Schedule tasks (Laravel 12 style)
Schedule::command('apl02:cleanup-files')->weekly();
Schedule::command('apl02:stats --format=json')
    ->daily()
    ->sendOutputTo(storage_path('logs/apl02-stats.log'));
