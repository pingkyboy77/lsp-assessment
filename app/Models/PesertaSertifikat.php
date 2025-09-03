<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PesertaSertifikat extends Model
{
    use HasFactory;

    protected $table = 'peserta_sertifikats';
    protected $fillable = ['nama', 'sertifikasi', 'no_ser', 'no_reg', 'no_sertifikat', 'tanggal_terbit', 'tanggal_exp', 'registrasi_nomor', 'tahun_registrasi', 'nomor_blanko'];
}
