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
        Schema::table('tuk_requests', function (Blueprint $table) {
            $table->enum('jenis_tuk', ['Sewaktu', 'Mandiri'])->default('Sewaktu')->after('kode_tuk');
            $table->index('jenis_tuk');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuk_requests', function (Blueprint $table) {
            $table->dropIndex(['jenis_tuk']);
            $table->dropColumn('jenis_tuk');
        });
    }
};
