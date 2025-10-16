<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First backup existing data
        $this->backupOldData();

        // Drop old columns that are no longer needed
        Schema::table('portfolio_files', function (Blueprint $table) {
            // Check and drop each column individually
            if (Schema::hasColumn('portfolio_files', 'original_name')) {
                $table->dropColumn('original_name');
            }
            if (Schema::hasColumn('portfolio_files', 'file_name')) {
                $table->dropColumn('file_name');
            }
            if (Schema::hasColumn('portfolio_files', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('portfolio_files', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
            if (Schema::hasColumn('portfolio_files', 'file_size')) {
                $table->dropColumn('file_size');
            }
            if (Schema::hasColumn('portfolio_files', 'description')) {
                $table->dropColumn('description');
            }
        });

        echo "✓ Old file-related columns have been removed\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the old columns
        Schema::table('portfolio_files', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('unit_kompetensi_id');
            $table->string('file_name')->nullable()->after('original_name');
            $table->string('file_path')->nullable()->after('file_name');
            $table->string('mime_type')->nullable()->after('file_path');
            $table->bigInteger('file_size')->nullable()->after('mime_type');
            $table->text('description')->nullable()->after('file_size');
        });

        // Restore backup data if exists
        $this->restoreOldData();
    }

    /**
     * Backup old data before dropping columns
     */
    private function backupOldData(): void
    {
        // Check if any of the old columns exist and have data
        $columnsToCheck = ['original_name', 'file_name', 'file_path', 'mime_type', 'file_size', 'description'];
        $hasOldData = false;

        foreach ($columnsToCheck as $column) {
            if (Schema::hasColumn('portfolio_files', $column)) {
                $hasData = \DB::table('portfolio_files')
                    ->whereNotNull($column)
                    ->where($column, '!=', '')
                    ->exists();

                if ($hasData) {
                    $hasOldData = true;
                    break;
                }
            }
        }

        if ($hasOldData) {
            // Create backup table with old file data
            \DB::statement('
                CREATE TABLE portfolio_files_old_backup AS 
                SELECT id, unit_kompetensi_id, 
                       ' . (Schema::hasColumn('portfolio_files', 'original_name') ? 'original_name' : 'NULL as original_name') . ',
                       ' . (Schema::hasColumn('portfolio_files', 'file_name') ? 'file_name' : 'NULL as file_name') . ',
                       ' . (Schema::hasColumn('portfolio_files', 'file_path') ? 'file_path' : 'NULL as file_path') . ',
                       ' . (Schema::hasColumn('portfolio_files', 'mime_type') ? 'mime_type' : 'NULL as mime_type') . ',
                       ' . (Schema::hasColumn('portfolio_files', 'file_size') ? 'file_size' : 'NULL as file_size') . ',
                       ' . (Schema::hasColumn('portfolio_files', 'description') ? 'description' : 'NULL as description') . ',
                       created_at, updated_at
                FROM portfolio_files 
                WHERE ' . implode(' IS NOT NULL OR ', array_map(function ($col) {
                return Schema::hasColumn('portfolio_files', $col) ? $col : '1=0';
            }, $columnsToCheck)) . ' IS NOT NULL
            ');

            echo "✓ Old file data backed up to portfolio_files_old_backup table\n";
        }
    }

    /**
     * Restore old data (for rollback)
     */
    private function restoreOldData(): void
    {
        // Check if backup table exists
        $backupExists = \DB::select("SELECT to_regclass('portfolio_files_old_backup')")[0]->to_regclass ?? null;

        if ($backupExists) {
            // Update existing records with backup data
            \DB::statement('
                UPDATE portfolio_files 
                SET original_name = backup.original_name,
                    file_name = backup.file_name,
                    file_path = backup.file_path,
                    mime_type = backup.mime_type,
                    file_size = backup.file_size,
                    description = backup.description
                FROM portfolio_files_old_backup backup
                WHERE portfolio_files.id = backup.id
            ');

            // Drop backup table
            \DB::statement('DROP TABLE portfolio_files_old_backup');

            echo "✓ Old file data restored from backup\n";
        }
    }
};
