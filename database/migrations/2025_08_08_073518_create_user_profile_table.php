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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Data Personal
            $table->string('nama_lengkap');
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable(); // L / P
            $table->string('kebangsaan')->nullable();

            // Alamat Rumah
            $table->text('alamat_rumah')->nullable();
            $table->string('no_telp_rumah')->nullable();
            $table->string('kota_rumah')->nullable(); // Tambahan dari form
            $table->string('provinsi_rumah')->nullable(); // Tambahan dari form
            $table->string('kode_pos')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Pendidikan
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('nama_sekolah_terakhir')->nullable();

            // Pekerjaan
            $table->string('jabatan')->nullable();
            $table->string('nama_tempat_kerja')->nullable();
            $table->string('kategori_pekerjaan')->nullable(); // Tambahan dari form

            // Alamat Kantor
            $table->string('nama_jalan_kantor')->nullable(); // Tambahan dari form
            $table->string('kota_kantor')->nullable(); // Tambahan dari form
            $table->string('provinsi_kantor')->nullable(); // Tambahan dari form
            $table->string('kode_pos_kantor')->nullable(); // Tambahan dari form
            $table->string('negara_kantor')->nullable()->default('Indonesia'); // Tambahan dari form
            $table->string('no_telp_kantor')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
