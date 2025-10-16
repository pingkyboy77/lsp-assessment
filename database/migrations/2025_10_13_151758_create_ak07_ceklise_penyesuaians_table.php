<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ak07_ceklis_penyesuaian', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('mapa_id')->constrained('mapa')->onDelete('cascade');
            $table->foreignId('delegasi_personil_asesmen_id')->constrained('delegasi_personil_asesmen')->onDelete('cascade');
            $table->foreignId('asesor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asesi_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('certification_scheme_id')->constrained()->onDelete('cascade');

            $table->string('nomor_ak07')->unique();

            // Questions Answers (Q1-Q8)
            $table->json('answers');

            // Potensi Asesi
            $table->json('potensi_asesi')->nullable();

            // Hasil Penyesuaian Yang Wajar dan Beralasan
            $table->string('acuan_pembahasan')->nullable(); // Ya/Tidak
            $table->text('tulisan_acuan_pembahasan')->nullable();
            $table->string('metode_asesmen')->nullable(); // Ya/Tidak
            $table->text('tulisan_metode_asesmen')->nullable();
            $table->string('instrumen_asesmen')->nullable(); // Ya/Tidak
            $table->text('tulisan_instrumen_asesmen')->nullable();

            // Nama Asesor
            $table->string('nama_asesor');

            // Asesor Signature
            $table->text('asesor_signature')->nullable();
            $table->date('tanggal_tanda_tangan')->nullable();
            $table->timestamp('asesor_signed_at')->nullable();
            $table->string('asesor_ip')->nullable();

            // Asesi Signature
            $table->text('asesi_signature')->nullable();
            $table->date('asesi_tanggal_tanda_tangan')->nullable();
            $table->timestamp('asesi_signed_at')->nullable();
            $table->string('asesi_ip')->nullable();

            // Status: draft, waiting_asesi, completed
            $table->enum('status', ['draft', 'waiting_asesi', 'completed'])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['mapa_id', 'status']);
            $table->index('nomor_ak07');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ak07_ceklis_penyesuaian');
    }
};
