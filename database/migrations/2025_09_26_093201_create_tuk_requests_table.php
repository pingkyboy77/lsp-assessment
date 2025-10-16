<?php

// Migration 1: Create tuk_requests table (super simple)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tuk_requests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tuk')->unique(); // SPT-TUK-148672/LSP-PM/08/08/2025
            $table->foreignId('apl01_id')->constrained('apl_01_pendaftarans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->date('tanggal_assessment'); // Tanggal assessment (admin bisa edit)
            $table->text('lokasi_assessment'); // Lokasi assessment (text input)
            $table->string('tanda_tangan_peserta_path')->nullable(); // TTD peserta

            $table->time('jam_mulai')->nullable(); // Admin isi jam mulai saat rekomendasi
            $table->text('catatan_rekomendasi')->nullable(); // Catatan admin
            $table->timestamp('recommended_at')->nullable(); // Kapan admin buat rekomendasi
            $table->foreignId('recommended_by')->nullable()->constrained('users'); // Admin yang rekomendasi

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tuk_requests');
    }
};
