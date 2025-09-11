<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserProfile;
use App\Models\UserDocument;
use App\Models\LembagaPelatihan;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'company', 'id_number'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function lembaga()
    {
        return $this->belongsTo(LembagaPelatihan::class, 'company');
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function apl01Registrations()
    {
        return $this->hasMany(Apl01Pendaftaran::class);
    }


    /**
     * Check if user has complete profile
     */
    public function hasCompleteProfile()
    {
        $profile = $this->profile;

        return $profile && $profile->nama_lengkap && $profile->nik && $profile->tempat_lahir && $profile->tanggal_lahir && $profile->jenis_kelamin && $profile->alamat_rumah && $profile->kota_rumah && $profile->provinsi_rumah && $profile->no_hp && $profile->email;
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // Accessor untuk mengambil data dari profile dengan fallback ke user table
    public function getNamaLengkapAttribute($value)
    {
        return $this->profile ? $this->profile->nama_lengkap : $value;
    }

    public function getNikAttribute($value)
    {
        return $this->profile ? $this->profile->nik : $value;
    }

    public function getTempatLahirAttribute($value)
    {
        return $this->profile ? $this->profile->tempat_lahir : $value;
    }

    public function getTanggalLahirAttribute($value)
    {
        return $this->profile ? $this->profile->tanggal_lahir : $value;
    }

    public function getJenisKelaminAttribute($value)
    {
        return $this->profile ? $this->profile->jenis_kelamin : $value;
    }

    public function getKebangsaanAttribute($value)
    {
        return $this->profile ? $this->profile->kebangsaan : ($value ?: 'Indonesia');
    }

    public function getAlamatRumahAttribute($value)
    {
        return $this->profile ? $this->profile->alamat_rumah : $value;
    }

    public function getKotaRumahAttribute($value)
    {
        return $this->profile ? $this->profile->kota_rumah : $value;
    }

    public function getProvinsiRumahAttribute($value)
    {
        return $this->profile ? $this->profile->provinsi_rumah : $value;
    }

    public function getKodePosAttribute($value)
    {
        return $this->profile ? $this->profile->kode_pos : $value;
    }

    public function getNoTelpRumahAttribute($value)
    {
        return $this->profile ? $this->profile->no_telp_rumah : $value;
    }

    public function getNoHpAttribute($value)
    {
        return $this->profile ? $this->profile->no_hp : $value;
    }

    public function getPendidikanTerakhirAttribute($value)
    {
        return $this->profile ? $this->profile->pendidikan_terakhir : $value;
    }

    public function getNamaSekolahTerakhirAttribute($value)
    {
        return $this->profile ? $this->profile->nama_sekolah_terakhir : $value;
    }

    public function getNamaTempatKerjaAttribute($value)
    {
        return $this->profile ? $this->profile->nama_tempat_kerja : $value;
    }

    public function getKategoriPekerjaanAttribute($value)
    {
        return $this->profile ? $this->profile->kategori_pekerjaan : $value;
    }

    public function getJabatanAttribute($value)
    {
        return $this->profile ? $this->profile->jabatan : $value;
    }

    public function getNamaJalanKantorAttribute($value)
    {
        return $this->profile ? $this->profile->nama_jalan_kantor : $value;
    }

    public function getKotaKantorAttribute($value)
    {
        return $this->profile ? $this->profile->kota_kantor : $value;
    }

    public function getProvinsiKantorAttribute($value)
    {
        return $this->profile ? $this->profile->provinsi_kantor : $value;
    }

    public function getKodePosPosKantorAttribute($value)
    {
        return $this->profile ? $this->profile->kode_pos_kantor : $value;
    }

    public function getNoTelpKantorAttribute($value)
    {
        return $this->profile ? $this->profile->no_telp_kantor : $value;
    }
}
