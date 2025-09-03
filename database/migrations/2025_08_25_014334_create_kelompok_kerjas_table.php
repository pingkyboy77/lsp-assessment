<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kelompok_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_scheme_id')->constrained()->onDelete('cascade');
            $table->string('nama_kelompok');
            $table->text('deskripsi')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['certification_scheme_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kelompok_kerjas');
    }
};