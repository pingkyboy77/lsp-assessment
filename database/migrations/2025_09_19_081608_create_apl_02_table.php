<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apl_02', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_apl_02')->unique()->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('apl_01_id')->constrained('apl_01_pendaftarans')->onDelete('cascade');
            $table->foreignId('certification_scheme_id')->constrained('certification_schemes')->onDelete('cascade');

            // Status: draft, submitted, review, approved, rejected, returned
            $table->enum('status', ['draft', 'submitted', 'review', 'approved', 'rejected', 'returned'])
                ->default('draft');

            // Reviewer info
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reviewer_notes')->nullable();

            // Self assessment summary
            $table->integer('total_elements')->default(0);
            $table->integer('kompeten_count')->default(0);
            $table->integer('belum_kompeten_count')->default(0);
            $table->decimal('competency_percentage', 5, 2)->default(0);

            // File management
            $table->string('file_folder_path')->nullable(); // apl02-files/tahun/bulan/code_1/nama_asesi
            $table->json('uploaded_files')->nullable(); // Array of uploaded file info

            // Digital Signatures
            $table->text('asesi_signature')->nullable(); // Base64 encoded signature
            $table->timestamp('asesi_signed_at')->nullable();
            $table->string('asesi_signature_ip')->nullable();

            $table->text('asesor_signature')->nullable(); // Base64 encoded signature  
            $table->timestamp('asesor_signed_at')->nullable();
            $table->string('asesor_signature_ip')->nullable();
            $table->foreignId('asesor_id')->nullable()->constrained('users')->onDelete('set null');

            // Submission tracking
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['certification_scheme_id', 'status']);
            $table->index('nomor_apl_02');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apl_02');
    }
};