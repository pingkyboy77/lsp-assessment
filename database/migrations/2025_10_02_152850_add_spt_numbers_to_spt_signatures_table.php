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
        Schema::table('spt_signatures', function (Blueprint $table) {
            // Add SPT number columns after delegasi_personil_id
            $table->string('spt_verifikator_number')->nullable()->after('delegasi_personil_id');
            $table->string('spt_observer_number')->nullable()->after('spt_verifikator_number');
            $table->string('spt_asesor_number')->nullable()->after('spt_observer_number');

            // Add index for better query performance
            $table->index('spt_verifikator_number');
            $table->index('spt_observer_number');
            $table->index('spt_asesor_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spt_signatures', function (Blueprint $table) {
            $table->dropIndex(['spt_verifikator_number']);
            $table->dropIndex(['spt_observer_number']);
            $table->dropIndex(['spt_asesor_number']);

            $table->dropColumn([
                'spt_verifikator_number',
                'spt_observer_number',
                'spt_asesor_number'
            ]);
        });
    }
};
