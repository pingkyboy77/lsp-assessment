<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delegasi_personil_asesmen', function (Blueprint $table) {
            $table->id();

            // Reference ke Asesi (User) dan Skema
            $table->unsignedBigInteger('asesi_id'); // user_id peserta/asesi
            $table->unsignedBigInteger('certification_scheme_id'); // skema sertifikasi

            // Reference ke APL01 atau TUK Request (optional, bisa salah satu)
            $table->unsignedBigInteger('apl01_id')->nullable();
            $table->unsignedBigInteger('tuk_request_id')->nullable();

            // Jenis Ujian
            $table->enum('jenis_ujian', ['online', 'offline'])->default('offline');

            // Verifikator TUK
            $table->unsignedBigInteger('verifikator_tuk_id')->nullable(); // user_id
            $table->string('verifikator_nik')->nullable(); // dari id_number
            $table->date('verifikator_spt_date')->nullable();

            // Observer
            $table->unsignedBigInteger('observer_id')->nullable(); // user_id dengan role observer
            $table->string('observer_nik')->nullable(); // dari id_number
            $table->date('observer_spt_date')->nullable();

            // Asesor
            $table->unsignedBigInteger('asesor_id')->nullable(); // user_id dengan role asesor
            $table->string('asesor_met')->nullable(); // dari id_number (MET number)
            $table->date('asesor_spt_date')->nullable();

            // Tanggal dan Waktu Pelaksanaan Asesmen
            $table->date('tanggal_pelaksanaan_asesmen')->nullable();
            $table->time('waktu_mulai')->nullable(); // hanya jam mulai

            // Delegasi Info - Siapa yang melakukan delegasi
            $table->unsignedBigInteger('delegated_by')->nullable(); // Admin yang melakukan delegasi
            $table->timestamp('delegated_at')->nullable(); // Kapan delegasi dilakukan
            $table->text('notes')->nullable(); // Catatan delegasi

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('asesi_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('certification_scheme_id')
                ->references('id')
                ->on('certification_schemes')
                ->onDelete('cascade');

            // APL01 foreign key - nama tabel yang benar
            $table->foreign('apl01_id')
                ->references('id')
                ->on('apl_01_pendaftarans') // Nama tabel yang benar
                ->onDelete('cascade');

            $table->foreign('tuk_request_id')
                ->references('id')
                ->on('tuk_requests')
                ->onDelete('cascade');

            $table->foreign('verifikator_tuk_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('observer_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('asesor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('delegated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('asesi_id');
            $table->index('certification_scheme_id');
            $table->index('apl01_id');
            $table->index('tuk_request_id');
            $table->index('verifikator_tuk_id');
            $table->index('observer_id');
            $table->index('asesor_id');
            $table->index('delegated_by');
            $table->index('tanggal_pelaksanaan_asesmen');
            $table->index('delegated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegasi_personil_asesmen');
    }
};
