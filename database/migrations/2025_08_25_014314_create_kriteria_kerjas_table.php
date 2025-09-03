<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kriteria_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elemen_kompetensi_id')->constrained()->onDelete('cascade');
            $table->string('kode_kriteria')->index();
            $table->text('uraian_kriteria');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['elemen_kompetensi_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kriteria_kerjas');
    }
};