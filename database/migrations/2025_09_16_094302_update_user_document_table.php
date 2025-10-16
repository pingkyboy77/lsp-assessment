<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            // kalau mau rename kolom
            if (Schema::hasColumn('user_documents', 'jenis_dokumen')) {
                $table->renameColumn('jenis_dokumen', 'document_type');
            }

            // tambahkan kolom baru
            if (!Schema::hasColumn('user_documents', 'original_name')) {
                $table->string('original_name')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('user_documents', 'file_name')) {
                $table->string('file_name')->nullable()->after('original_name');
            }
            if (!Schema::hasColumn('user_documents', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('user_documents', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('user_documents', 'description')) {
                $table->text('description')->nullable()->after('mime_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            // kembalikan perubahan
            if (Schema::hasColumn('user_documents', 'document_type')) {
                $table->renameColumn('document_type', 'jenis_dokumen');
            }

            $table->dropColumn(['original_name', 'file_name', 'file_size', 'mime_type', 'description']);
        });
    }
};
