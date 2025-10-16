<?php
// database/migrations/xxxx_create_rekomendasi_lsp_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rekomendasi_lsp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apl01_id')->constrained('apl_01_pendaftarans')->cascadeOnDelete(); // UBAH INI
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('admin_nik')->nullable();
            $table->text('rekomendasi_text')->nullable();
            $table->string('ttd_admin_path')->nullable();
            $table->timestamp('tanggal_ttd_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('apl01_id');
            $table->index('admin_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekomendasi_lsp');
    }
};
