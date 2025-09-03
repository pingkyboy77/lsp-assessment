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
        // Hapus constraint lama
        DB::statement("ALTER TABLE apl_01_pendaftarans DROP CONSTRAINT apl_01_pendaftarans_status_check");

        // Tambah constraint baru tanpa 'review', ganti dengan 'open'
        DB::statement("ALTER TABLE apl_01_pendaftarans 
            ADD CONSTRAINT apl_01_pendaftarans_status_check 
            CHECK (status IN ('draft', 'submitted', 'open', 'approved', 'rejected'))");
    }

    public function down(): void
    {
        // Kembalikan ke constraint lama (dengan review)
        DB::statement("ALTER TABLE apl_01_pendaftarans DROP CONSTRAINT apl_01_pendaftarans_status_check");

        DB::statement("ALTER TABLE apl_01_pendaftarans 
            ADD CONSTRAINT apl_01_pendaftarans_status_check 
            CHECK (status IN ('draft', 'submitted', 'review', 'approved', 'rejected'))");
    }
};
