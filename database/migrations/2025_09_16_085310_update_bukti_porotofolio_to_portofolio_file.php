<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Create new table with correct structure
        Schema::create('portfolio_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_kompetensi_id');
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['unit_kompetensi_id', 'is_active']);
            $table->index('mime_type');

            // Foreign key
            $table->foreign('unit_kompetensi_id')->references('id')->on('unit_kompetensis')->onDelete('cascade');
        });

        // Step 2: Backup old table (rename it)
        Schema::rename('bukti_portofolios', 'bukti_portofolios_backup');

        // Optional: If you want to preserve some data, you can migrate relevant records
        // But since we're changing the structure completely, we'll leave it empty
        // The backup table can be dropped later once everything works fine
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new table
        Schema::dropIfExists('portfolio_files');

        // Restore old table
        Schema::rename('bukti_portofolios_backup', 'bukti_portofolios');
    }
};
