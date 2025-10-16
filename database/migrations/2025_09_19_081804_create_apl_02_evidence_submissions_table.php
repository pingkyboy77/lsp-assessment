<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apl_02_evidence_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apl_02_id')->constrained('apl_02')->onDelete('cascade');
            $table->foreignId('portfolio_file_id')->constrained('portfolio_files')->onDelete('cascade');

            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); 
            $table->bigInteger('file_size');
            $table->string('mime_type');

            // Evidence metadata
            $table->text('description')->nullable();
            $table->boolean('is_submitted')->default(true);

            $table->timestamps();

            // Unique constraint - one file per portfolio requirement per APL 02
            $table->unique(['apl_02_id', 'portfolio_file_id'], 'unique_apl02_evidence');

            // Indexes
            $table->index(['apl_02_id', 'is_submitted']);
            $table->index('file_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apl_02_evidence_submissions');
    }
};
