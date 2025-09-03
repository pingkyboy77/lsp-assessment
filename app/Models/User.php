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

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
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
}
