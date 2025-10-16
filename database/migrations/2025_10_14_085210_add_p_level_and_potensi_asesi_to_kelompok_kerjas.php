<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kelompok_kerjas', function (Blueprint $table) {
            $table->integer('p_level')->nullable()->after('sort_order');
            $table->json('potensi_asesi')->nullable()->after('p_level');
        });
    }

    public function down()
    {
        Schema::table('kelompok_kerjas', function (Blueprint $table) {
            $table->dropColumn(['p_level', 'potensi_asesi']);
        });
    }
};
