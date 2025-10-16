<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('delegasi_personil_asesmen', function (Blueprint $table) {
            if (!Schema::hasColumn('delegasi_personil_asesmen', 'status_assessment')) {
                $table->string('status_assessment')
                    ->default('scheduled')
                    ->after('notes')
                    ->comment('Status asesmen: scheduled, ongoing, completed, canceled');
            }
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('delegasi_personil_asesmen', function (Blueprint $table) {
            if (Schema::hasColumn('delegasi_personil_asesmen', 'status_assessment')) {
                $table->dropColumn('status_assessment');
            }
        });
    }
};
