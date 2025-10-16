<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table: form_kerahasiaan
        Schema::create('form_kerahasiaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegasi_personil_asesmen_id')->constrained('delegasi_personil_asesmen')->cascadeOnDelete();
            $table->foreignId('ak07_id')->constrained('ak07_ceklis_penyesuaian')->cascadeOnDelete();

            // Data from delegasi (can be edited by asesor)
            $table->string('nama_asesi');
            $table->string('nama_asesor');
            $table->string('skema_sertifikasi');
            $table->date('tanggal_asesmen');
            $table->time('jam_mulai'); // Editable by asesor, akan sync ke delegasi & tuk

            // Asesor signature
            $table->text('ttd_asesor')->nullable();
            $table->date('tanggal_ttd_asesor')->nullable();

            // Asesi signature
            $table->text('ttd_asesi')->nullable();
            $table->date('tanggal_ttd_asesi')->nullable();

            // Status
            $table->enum('status', ['draft', 'waiting_asesi', 'completed'])->default('draft');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_kerahasiaan');
    }
};
