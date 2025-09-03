<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LembagaPelatihan extends Model
{
    protected $table = 'lembaga_pelatihan';
    protected $fillable = ['id', 'name', 'created_by', 'updated_by'];

    public $incrementing = false; // ID bukan auto increment
    protected $keyType = 'string'; // ID bertipe string

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
