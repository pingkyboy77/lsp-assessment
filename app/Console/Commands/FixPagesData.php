<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Page;
use Illuminate\Support\Facades\DB;

class FixPagesData extends Command
{
    protected $signature = 'pages:fix-data';
    protected $description = 'Fix pages data untuk kompatibilitas PostgreSQL';

    public function handle()
    {
        $this->info('Memperbaiki data pages...');

        // Update pages yang belum memiliki is_active dan is_sidebar_menu
        DB::table('pages')->whereNull('is_active')->update(['is_active' => true]);
        DB::table('pages')->whereNull('is_sidebar_menu')->update(['is_sidebar_menu' => true]);

        $this->info('Data pages berhasil diperbaiki!');

        return Command::SUCCESS;
    }
}
