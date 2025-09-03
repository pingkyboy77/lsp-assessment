<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kelompok_kerja_unit_kompetensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_kerja_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_kompetensi_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['kelompok_kerja_id', 'unit_kompetensi_id'], 'kelompok_unit_unique');
            
            // Index untuk performa query
            $table->index(['kelompok_kerja_id', 'is_active']);
            $table->index(['unit_kompetensi_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kelompok_kerja_unit_kompetensi');
    }
};