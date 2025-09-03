<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bukti_portofolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_kerja_id')->constrained()->onDelete('cascade');
            $table->text('bukti_portofolio');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['kelompok_kerja_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bukti_portofolios');
    }
};