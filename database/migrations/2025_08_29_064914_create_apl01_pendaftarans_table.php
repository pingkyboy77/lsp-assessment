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
        Schema::create('apl_01_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_apl_01')->unique()->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('certification_scheme_id')->constrained()->onDelete('cascade');
            
            // Data Pribadi (auto-filled from profile)
            $table->string('nama_lengkap');
            $table->string('nik', 16);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('kebangsaan')->default('Indonesia');
            
            // Alamat
            $table->text('alamat_rumah');
            $table->string('no_telp_rumah')->nullable();
            $table->string('kota_rumah');
            $table->string('provinsi_rumah');
            $table->string('kode_pos', 10)->nullable();
            
            // Kontak
            $table->string('no_hp', 15);
            $table->string('email');
            
            // Pendidikan
            $table->string('pendidikan_terakhir');
            $table->string('nama_sekolah_terakhir');
            
            // Pekerjaan
            $table->string('jabatan');
            $table->string('nama_tempat_kerja');
            $table->string('kategori_pekerjaan');
            $table->text('nama_jalan_kantor')->nullable();
            $table->string('kota_kantor')->nullable();
            $table->string('provinsi_kantor')->nullable();
            $table->string('kode_pos_kantor', 10)->nullable();
            $table->string('negara_kantor')->default('Indonesia');
            $table->string('no_telp_kantor', 15)->nullable();
            
            // Unit Kompetensi
            $table->json('selected_units')->nullable(); // Store selected unit IDs
            $table->text('standar_kompetensi_kerja')->nullable();
            
            // APL 01 Specific Fields
            $table->text('tujuan_asesmen')->nullable();
            $table->string('tujuan_asesmen_radio')->nullable(); // Radio button selection
            
            // TUK (Tempat Uji Kompetensi)
            $table->string('tuk')->nullable();
            
            // Kategori Peserta
            $table->enum('kategori_peserta', ['individu', 'training_provider'])->nullable();
            $table->string('training_provider')->nullable(); // Jika kategori = training_provider
            
            // Pertanyaan Asesmen Sebelumnya
            $table->enum('pernah_asesmen_lsp', ['sudah', 'belum'])->nullable();
            
            // Pertanyaan Aplikasi - Updated field name to match usage
            $table->json('aplikasi_yang_digunakan')->nullable(); // Store as JSON array
            
            // Pertanyaan Share Screen
            $table->enum('bisa_share_screen', ['ya', 'tidak'])->nullable();
            
            // Pertanyaan Browser
            $table->enum('bisa_gunakan_browser', ['ya', 'tidak'])->nullable();
            
            // Nama Lengkap Sesuai KTP (untuk sertifikat)
            $table->string('nama_lengkap_ktp')->nullable();
            
            // Pernyataan
            $table->boolean('pernyataan_benar')->default(false);
            
            // Enhanced Digital Signature Support
            $table->longText('tanda_tangan_asesi')->nullable(); // Store base64 signature data
            $table->timestamp('tanggal_tanda_tangan_asesi')->nullable();
            
            // Tanda Tangan Asesor (diisi saat review)
            $table->longText('tanda_tangan_asesor')->nullable(); // Store base64 signature data
            $table->timestamp('tanggal_tanda_tangan_asesor')->nullable();
            $table->string('nama_asesor')->nullable();
            
            // Dynamic Requirements Storage
            $table->json('requirement_answers')->nullable(); // Store answers for dynamic requirements
            
            // Status Management
            $table->enum('status', ['draft', 'submitted', 'review', 'approved', 'rejected'])->default('draft');
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'certification_scheme_id']);
            $table->index('status');
            $table->index('submitted_at');
            $table->index('nomor_apl_01');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apl01_pendaftarans');
    }
};
