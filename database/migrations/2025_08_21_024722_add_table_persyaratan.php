<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel utama untuk menyimpan template persyaratan
        Schema::create('requirement_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama template persyaratan
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel untuk menyimpan item persyaratan dalam template
        Schema::create('requirement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('requirement_templates')->onDelete('cascade');
            $table->string('document_name'); // Nama dokumen yang diperlukan
            $table->text('description')->nullable(); // Deskripsi detail
            $table->enum('type', ['file_upload', 'text_input', 'checkbox', 'select', 'number'])->default('file_upload');
            $table->json('validation_rules')->nullable(); // Aturan validasi (format, ukuran, dll)
            $table->json('options')->nullable(); // Untuk select dropdown
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['template_id', 'sort_order']);
        });

        // Menambahkan kolom template_id ke tabel certification_schemes
        Schema::table('certification_schemes', function (Blueprint $table) {
            $table->foreignId('requirement_template_id')
                  ->nullable()
                  ->constrained('requirement_templates')
                  ->onDelete('set null');
        });

        // Tabel untuk menyimpan jawaban/upload user
        Schema::create('application_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id'); // ID aplikasi sertifikasi
            $table->foreignId('requirement_item_id')->constrained('requirement_items');
            $table->text('value')->nullable(); // Nilai text input
            $table->string('file_path')->nullable(); // Path file yang diupload
            $table->string('original_filename')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['application_id']);
            $table->index(['requirement_item_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_requirements');
        Schema::table('certification_schemes', function (Blueprint $table) {
            $table->dropForeign(['requirement_template_id']);
            $table->dropColumn('requirement_template_id');
        });
        Schema::dropIfExists('requirement_items');
        Schema::dropIfExists('requirement_templates');
    }
};