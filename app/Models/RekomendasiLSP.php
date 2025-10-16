<?php
// app/Models/RekomendasiLSP.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekomendasiLSP extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rekomendasi_lsp';

    protected $fillable = [
        'apl01_id',
        'admin_id',
        'admin_nik',
        'rekomendasi_text',
        'ttd_admin_path',
        'tanggal_ttd_admin',
    ];

    protected $casts = [
        'tanggal_ttd_admin' => 'datetime',
    ];

    // Relationships
    public function apl01()
    {
        return $this->belongsTo(Apl01Pendaftaran::class, 'apl01_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Accessors
    public function getAdminNamaAttribute()
    {
        return $this->admin->name ?? '-';
    }

    public function getFormattedTanggalTtdAttribute()
    {
        return $this->tanggal_ttd_admin ?
            $this->tanggal_ttd_admin->format('d F Y H:i') : '-';
    }
}
