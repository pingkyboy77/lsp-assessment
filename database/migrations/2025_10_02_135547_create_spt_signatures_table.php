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
        Schema::create('spt_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegasi_personil_id')
                ->constrained('delegasi_personil_asesmen')
                ->cascadeOnDelete();

            // SPT Files yang sudah di-generate
            $table->string('spt_verifikator_file')->nullable();
            $table->string('spt_observer_file')->nullable();
            $table->string('spt_asesor_file')->nullable();

            // Informasi tanda tangan
            $table->foreignId('signed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_image')->nullable(); // Path ke gambar TTD direktur

            // Status
            $table->enum('status', ['pending', 'signed'])->default('pending');

            // Catatan tambahan
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('delegasi_personil_id');
            $table->index('status');
            $table->index('signed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spt_signatures');
    }
};
