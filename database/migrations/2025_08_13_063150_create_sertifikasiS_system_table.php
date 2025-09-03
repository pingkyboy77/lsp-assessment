<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bidang', 10);
            $table->string('code_2', 10)->unique(); // INI yang unique identifier!
            $table->string('bidang')->nullable();
            $table->string('bidang_ing')->nullable();
            $table->string('kbbli_bidang', 10)->nullable();
            $table->string('kode_web', 10)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kode_bidang']);
            $table->index(['code_2']); // Primary identifier
            $table->index(['kbbli_bidang']);
            $table->index(['kode_web']);
            $table->index(['is_active']);
        });

        Schema::create('certification_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('code_1', 20);
            $table->string('code_2', 10); // Foreign key ke fields.code_2
            $table->decimal('fee_tanda_tangan', 15, 2)->nullable();
            $table->string('skema_ing')->nullable();
            $table->enum('jenjang', ['Utama', 'Madya', 'Menengah'])->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('code_2')->references('code_2')->on('fields')->onDelete('cascade');
            
            $table->index(['code_1']);
            $table->index(['code_2']);
            $table->index(['jenjang']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('certification_schemes');
        Schema::dropIfExists('fields');
    }
};
