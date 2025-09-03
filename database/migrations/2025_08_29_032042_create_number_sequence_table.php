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
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('sequence_key')->unique(); // contoh: apl_01_registration, certificate_number
            $table->string('name'); // nama yang mudah dibaca
            $table->string('prefix')->nullable(); // APL, CERT, TKP, dll
            $table->string('suffix')->nullable(); // optional suffix
            $table->integer('digits')->default(6); // jumlah digit angka: 000001
            $table->boolean('use_year')->default(true); // apakah pakai tahun
            $table->string('year_format')->default('Y'); // Y=2024, y=24, Ym=202401
            $table->string('separator')->default('-'); // pemisah: - atau /
            $table->string('format_template'); // template lengkap: {prefix}{separator}{year}{separator}{number}
            $table->integer('start_number')->default(1); // mulai dari angka berapa
            $table->integer('current_number')->default(0); // counter saat ini
            $table->string('current_period')->nullable(); // periode saat ini (tahun/bulan)
            $table->boolean('reset_yearly')->default(true); // reset counter tiap tahun
            $table->boolean('reset_monthly')->default(false); // reset counter tiap bulan
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('sample_output')->nullable(); // contoh hasil generate
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
