<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'foto_masuk',
        'lokasi_masuk',
        'jam_keluar',
        'foto_keluar',
        'lokasi_keluar',
        'keterangan',
        'qr_code_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
    ];

    /**
     * Get the siswa that owns the absensi.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    
    /**
     * Get the QR Code used for this attendance.
     */
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }
}