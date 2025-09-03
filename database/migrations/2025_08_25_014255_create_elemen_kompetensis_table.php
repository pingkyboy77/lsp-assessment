<?php
// Migration 2: create_elemen_kompetensis_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('elemen_kompetensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kompetensi_id')->constrained()->onDelete('cascade');
            $table->string('kode_elemen')->index();
            $table->text('judul_elemen');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['unit_kompetensi_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('elemen_kompetensis');
    }
};