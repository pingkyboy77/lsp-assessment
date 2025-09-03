<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unit_kompetensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_scheme_id')->constrained()->onDelete('cascade');
            $table->string('kode_unit')->index();
            $table->string('judul_unit');
            $table->text('standar_kompetensi_kerja')->nullable(); // SKKNI info
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['certification_scheme_id', 'kode_unit']);
            $table->index(['certification_scheme_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit_kompetensis');
    }
};