<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apl_02_element_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apl_02_id')->constrained('apl_02')->onDelete('cascade');
            $table->foreignId('unit_kompetensi_id')->constrained('unit_kompetensis')->onDelete('cascade');
            $table->foreignId('elemen_kompetensi_id')->constrained('elemen_kompetensis')->onDelete('cascade');

            // Self assessment result per element
            $table->enum('assessment_result', ['kompeten', 'belum_kompeten'])->nullable();
            $table->text('notes')->nullable(); // Optional notes from asesi

            $table->timestamps();

            // Unique constraint - one assessment per element per APL 02
            $table->unique(['apl_02_id', 'elemen_kompetensi_id'], 'unique_apl02_element_assessment');

            // Indexes
            $table->index(['apl_02_id', 'unit_kompetensi_id']);
            $table->index('assessment_result');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apl_02_element_assessments');
    }
};
