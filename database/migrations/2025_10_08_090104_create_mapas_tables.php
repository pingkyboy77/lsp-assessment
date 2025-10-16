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
        // Drop old tables if exists
        Schema::dropIfExists('mapa_metode_asesmen');
        Schema::dropIfExists('mapa_kelompok_kerja');
        Schema::dropIfExists('mapa');

        // Create MAPA table - SIMPLE!
        Schema::create('mapa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegasi_personil_asesmen_id')->constrained('delegasi_personil_asesmen')->cascadeOnDelete();
            $table->foreignId('asesor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('apl01_id')->constrained('apl_01_pendaftarans')->cascadeOnDelete();
            $table->foreignId('apl02_id')->nullable()->constrained('apl_02')->cascadeOnDelete();
            $table->foreignId('certification_scheme_id')->constrained('certification_schemes')->cascadeOnDelete();

            $table->string('nomor_mapa')->unique();
            $table->unsignedTinyInteger('p_level')->default(0)->comment('MAPA Level: P0, P1, P2, dst. Menentukan berapa kelompok yang Tidak Langsung');

            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');

            // Global notes dari asesor
            $table->text('catatan_asesor')->nullable();

            // Signature
            $table->text('tanda_tangan_asesor')->nullable();
            $table->timestamp('tanggal_tanda_tangan_asesor')->nullable();
            $table->string('ip_tanda_tangan_asesor')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('asesor_id');
            $table->index('p_level');
            $table->index('nomor_mapa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapa');
    }
};
