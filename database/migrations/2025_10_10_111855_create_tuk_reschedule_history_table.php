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
        Schema::create('tuk_reschedule_history', function (Blueprint $table) {
            $table->id();

            // Reference Data
            $table->string('kode_tuk')->nullable()->comment('Kode TUK untuk TUK Sewaktu');
            $table->foreignId('apl01_id')->constrained('apl_01_pendaftarans')->onDelete('cascade');
            $table->enum('tuk_type', ['sewaktu', 'mandiri']);

            // Reschedule Info
            $table->text('reschedule_reason');
            $table->foreignId('rescheduled_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('rescheduled_at');

            // Deleted Data Snapshot (untuk referensi)
            $table->date('old_tanggal_assessment')->nullable();
            $table->text('old_lokasi_assessment')->nullable();
            $table->boolean('had_signature')->default(false);
            $table->boolean('had_recommendation')->default(false);
            $table->boolean('had_delegation')->default(false);
            $table->boolean('had_mapa')->default(false);
            $table->string('mapa_nomor')->nullable()->comment('Nomor MAPA yang dihapus');

            // APL Status Snapshot
            $table->string('apl01_status_before')->nullable()->comment('Status APL01 sebelum reschedule');
            $table->string('apl02_status_before')->nullable()->comment('Status APL02 sebelum reschedule');

            $table->timestamps();

            // Indexes
            $table->index('kode_tuk');
            $table->index('apl01_id');
            $table->index('tuk_type');
            $table->index('rescheduled_by');
            $table->index('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuk_reschedule_history');
    }
};
