<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserDocument extends Model
{
    protected $fillable = ['user_id', 'jenis_dokumen', 'file_path'];

    protected $appends = ['file_exists', 'file_url', 'file_size_kb'];

    public function getFileExistsAttribute()
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    public function getFileUrlAttribute()
    {
        return $this->file_exists ? asset('storage/' . $this->file_path) : null;
    }

    public function getFileSizeKbAttribute()
    {
        if ($this->file_exists) {
            return number_format(Storage::disk('public')->size($this->file_path) / 1024, 2);
        }
        return null;
    }
}
