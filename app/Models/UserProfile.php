<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kebangsaan',
        'alamat_rumah',
        'no_telp_rumah',
        'kota_rumah',
        'provinsi_rumah',
        'kode_pos',
        'no_hp',
        'email',
        'pendidikan_terakhir',
        'nama_sekolah_terakhir',
        'jabatan',
        'nama_tempat_kerja',
        'kategori_pekerjaan',
        'nama_jalan_kantor',
        'kota_kantor',
        'provinsi_kantor',
        'kode_pos_kantor',
        'negara_kantor',
        'no_telp_kantor',
        'created_by',
        'updated_by',
    ];

    // protected $dates = [
    //     'tanggal_lahir',
    //     'created_at',
    //     'updated_at',
    // ];
public function getTanggalLahirFormatted($format = 'Y-m-d')
    {
        if (!$this->tanggal_lahir) {
            return null;
        }

        // Jika sudah Carbon instance
        if ($this->tanggal_lahir instanceof \Carbon\Carbon) {
            return $this->tanggal_lahir->format($format);
        }

        // Jika masih string, konversi dulu
        try {
            return \Carbon\Carbon::parse($this->tanggal_lahir)->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship dengan User yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship dengan User yang mengupdate
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relationship dengan UserDocument
    public function documents()
    {
        return $this->hasMany(UserDocument::class, 'user_id', 'user_id');
    }

    // Accessor untuk format jenis kelamin
    public function getJenisKelaminTextAttribute()
    {
        return $this->jenis_kelamin == 'L' ? 'Laki-laki' : ($this->jenis_kelamin == 'P' ? 'Perempuan' : '-');
    }

    // Accessor untuk format tanggal lahir
    public function getTanggalLahirFormattedAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->format('d/m/Y') : '-';
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('nama_tempat_kerja', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }


public function cityRumah()
{
    return $this->belongsTo(RegionKab::class, 'kota_rumah');
}

public function cityKantor()
{
    return $this->belongsTo(RegionKab::class, 'kota_kantor');
}

public function provinceRumah()
{
    return $this->belongsTo(RegionProv::class, 'provinsi_rumah');
}

public function provinceKantor()
{
    return $this->belongsTo(RegionProv::class, 'provinsi_kantor');
}
}