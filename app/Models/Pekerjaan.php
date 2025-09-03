<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    use HasFactory;

    // Nama tabel (opsional jika nama tabel mengikuti konvensi Laravel)
    protected $table = 'pekerjaans';

    // Primary key
    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
        'kode',
        'nama_pekerjaan',
    ];

    // Jika tidak pakai timestamps (created_at, updated_at)
    public $timestamps = false;
}
