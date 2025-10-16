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
        // First, backup existing data if needed
        $this->backupExistingData();

        // Drop existing columns that are no longer needed
        Schema::table('portfolio_files', function (Blueprint $table) {
            $table->dropColumn([
                'original_name',
                'file_name',
                'file_path',
                'mime_type',
                'file_size',
                'description'
            ]);
        });

        // Add new columns for document requirements
        Schema::table('portfolio_files', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('portfolio_files', 'document_name')) {
                $table->string('document_name')->after('unit_kompetensi_id');
            }
            if (!Schema::hasColumn('portfolio_files', 'document_description')) {
                $table->text('document_description')->nullable()->after('document_name');
            }
            if (!Schema::hasColumn('portfolio_files', 'is_required')) {
                $table->boolean('is_required')->default(true)->after('document_description');
            }

            // Update sort_order to be after is_required
            $table->unsignedInteger('sort_order')->default(1)->change();

            // Rename is_active and move it to the end
            $table->boolean('is_active')->default(true)->change();
        });

        // Add indexes separately with existence check
        $this->addIndexIfNotExists('portfolio_files', ['unit_kompetensi_id', 'is_active']);
        $this->addIndexIfNotExists('portfolio_files', ['unit_kompetensi_id', 'is_required']);
        $this->addIndexIfNotExists('portfolio_files', ['unit_kompetensi_id', 'sort_order']);

        // Populate with default template data
        $this->seedTemplateData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove new columns
        Schema::table('portfolio_files', function (Blueprint $table) {
            $table->dropIndex(['unit_kompetensi_id', 'is_active']);
            $table->dropIndex(['unit_kompetensi_id', 'is_required']);
            $table->dropIndex(['unit_kompetensi_id', 'sort_order']);

            $table->dropColumn([
                'document_name',
                'document_description',
                'is_required'
            ]);
        });

        // Restore original columns
        Schema::table('portfolio_files', function (Blueprint $table) {
            $table->string('original_name')->after('unit_kompetensi_id');
            $table->string('file_name')->after('original_name');
            $table->string('file_path')->after('file_name');
            $table->string('mime_type')->after('file_path');
            $table->bigInteger('file_size')->after('mime_type');
            $table->text('description')->nullable()->after('file_size');
        });

        // Restore backup data if exists
        $this->restoreBackupData();
    }

    /**
     * Backup existing data before migration
     */
    private function backupExistingData(): void
    {
        // Check if table has data
        $hasData = \DB::table('portfolio_files')->exists();

        if ($hasData) {
            // Create backup table
            \DB::statement('CREATE TABLE portfolio_files_backup AS SELECT * FROM portfolio_files');

            echo "✓ Existing data backed up to portfolio_files_backup table\n";
        }
    }

    /**
     * Seed template data for existing units
     */
    private function seedTemplateData(): void
    {
        // Get all unit kompetensi
        $units = \DB::table('unit_kompetensis')->where('is_active', true)->get();

        if ($units->isEmpty()) {
            return;
        }

        $templates = [
            [
                'document_name' => 'Sertifikat Kompetensi',
                'document_description' => 'Sertifikat yang menunjukkan pencapaian kompetensi',
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'document_name' => 'Portofolio Bukti Kerja',
                'document_description' => 'Kumpulan dokumen yang menunjukkan hasil kerja',
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'document_name' => 'Logbook/Jurnal Kerja',
                'document_description' => 'Catatan harian aktivitas kerja',
                'is_required' => true,
                'sort_order' => 3
            ],
            [
                'document_name' => 'Testimoni/Referensi Kerja',
                'document_description' => 'Surat keterangan dari atasan atau rekan kerja',
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'document_name' => 'Foto/Video Dokumentasi',
                'document_description' => 'Dokumentasi visual dari aktivitas kerja',
                'is_required' => false,
                'sort_order' => 5
            ]
        ];

        $now = now();
        $insertData = [];

        foreach ($units as $unit) {
            foreach ($templates as $template) {
                $insertData[] = [
                    'unit_kompetensi_id' => $unit->id,
                    'document_name' => $template['document_name'],
                    'document_description' => $template['document_description'],
                    'is_required' => $template['is_required'],
                    'sort_order' => $template['sort_order'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($insertData, 1000);
        foreach ($chunks as $chunk) {
            \DB::table('portfolio_files')->insert($chunk);
        }

        echo "✓ Template portfolio files seeded for " . count($units) . " units\n";
    }

    /**
     * Restore backup data (for rollback)
     */
    private function restoreBackupData(): void
    {
        // Check if backup table exists
        $backupExists = \DB::select("SHOW TABLES LIKE 'portfolio_files_backup'");

        if (!empty($backupExists)) {
            // Clear current data
            \DB::table('portfolio_files')->truncate();

            // Restore from backup
            \DB::statement('INSERT INTO portfolio_files SELECT * FROM portfolio_files_backup');

            // Drop backup table
            \DB::statement('DROP TABLE portfolio_files_backup');

            echo "✓ Data restored from backup\n";
        }
    }

    /**
     * Add index if it doesn't exist
     */
    private function addIndexIfNotExists(string $table, array $columns): void
    {
        $indexName = $table . '_' . implode('_', $columns) . '_index';

        // Check if index exists
        $exists = \DB::select("
            SELECT indexname 
            FROM pg_indexes 
            WHERE tablename = ? AND indexname = ?
        ", [$table, $indexName]);

        if (empty($exists)) {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                $table->index($columns);
            });
            echo "✓ Index created for " . implode(', ', $columns) . "\n";
        } else {
            echo "! Index already exists for " . implode(', ', $columns) . "\n";
        }
    }

    /**
     * Drop index if it exists
     */
    private function dropIndexIfExists(string $table, array $columns): void
    {
        $indexName = $table . '_' . implode('_', $columns) . '_index';

        // Check if index exists
        $exists = \DB::select("
            SELECT indexname 
            FROM pg_indexes 
            WHERE tablename = ? AND indexname = ?
        ", [$table, $indexName]);

        if (!empty($exists)) {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                $table->dropIndex($columns);
            });
            echo "✓ Index dropped for " . implode(', ', $columns) . "\n";
        }
    }
};
