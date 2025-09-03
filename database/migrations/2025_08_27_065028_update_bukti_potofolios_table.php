<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migration untuk tabel bukti_portofolios
        Schema::table('bukti_portofolios', function (Blueprint $table) {
            $table->string('dependency_type')->default('standalone'); 
            $table->json('dependency_rules')->nullable(); 
            $table->string('group_identifier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bukti_portofolios', function (Blueprint $table) {
            //
        });
    }
};
