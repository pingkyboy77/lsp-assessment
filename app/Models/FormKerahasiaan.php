<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormKerahasiaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_kerahasiaan';

    protected $fillable = [
        'delegasi_personil_asesmen_id',
        'ak07_id',
        'nama_asesi',
        'nama_asesor',
        'skema_sertifikasi',
        'tanggal_asesmen',
        'jam_mulai',
        'ttd_asesor',
        'tanggal_ttd_asesor',
        'ttd_asesi',
        'tanggal_ttd_asesi',
        'status',
    ];

    protected $casts = [
        'tanggal_asesmen' => 'date',
        'tanggal_ttd_asesor' => 'date',
        'tanggal_ttd_asesi' => 'date',
    ];

    // Relationships
    public function delegasi()
    {
        return $this->belongsTo(DelegasiPersonilAsesmen::class, 'delegasi_personil_asesmen_id');
    }

    public function ak07()
    {
        return $this->belongsTo(Ak07CeklisePenyesuaian::class, 'ak07_id');
    }

    // Sync jam_mulai to delegasi and tuk_request
    public function syncJamMulai()
    {
        if ($this->delegasi) {
            // Update delegasi
            $this->delegasi->update([
                'waktu_mulai' => $this->jam_mulai
            ]);

            // Update tuk_request if exists
            if ($this->delegasi->tukRequest) {
                $this->delegasi->tukRequest->update([
                    'jam_mulai' => $this->jam_mulai
                ]);
            }
        }
    }

    // Sign by asesor
    public function signByAsesor($signatureData)
    {
        $this->update([
            'ttd_asesor' => $signatureData,
            'tanggal_ttd_asesor' => now(),
            'status' => 'waiting_asesi'
        ]);

        // Sync jam_mulai after asesor signs
        $this->syncJamMulai();

        return $this;
    }

    // Sign by asesi
    public function signByAsesi($signatureData)
    {
        $this->update([
            'ttd_asesi' => $signatureData,
            'tanggal_ttd_asesi' => now(),
            'status' => 'completed'
        ]);

        return $this;
    }

    // Check if can be signed by asesi
    public function canBeSignedByAsesi()
    {
        return $this->status === 'waiting_asesi' && !empty($this->ttd_asesor);
    }

    // Accessors
    public function getPernyataanAsesorAttribute()
    {
        return 'Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.';
    }

    public function getPernyataanAsesiAttribute()
    {
        return 'Saya menyatakan bersedia menjaga kerahasiaan informasi yang saya peroleh selama proses asesmen dan tidak akan membuka atau menyebarkan informasi tersebut kepada pihak lain tanpa seizin LSP.';
    }
}
