<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'asal_sekolah',
        'jurusan',
        'no_hp',
        'tanggal_mulai',
        'tanggal_selesai',
        'foto_profil',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the user that owns the siswa.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the absensi for the siswa.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}