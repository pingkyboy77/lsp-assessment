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
        Schema::table('ak07_ceklis_penyesuaian', function (Blueprint $table) {
            $table->json('potensi_asesi')->nullable()->after('nomor_ak07')
                ->comment('Array of selected potensi asesi (p1, p2, p3, p4, p5)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ak07_ceklis_penyesuaian', function (Blueprint $table) {
            $table->dropColumn('potensi_asesi');
        });
    }
};
