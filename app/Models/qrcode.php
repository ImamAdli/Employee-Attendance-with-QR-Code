<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'tipe',
        'berlaku_mulai',
        'berlaku_sampai',
        'is_active',
    ];

    protected $casts = [
        'berlaku_mulai' => 'datetime',
        'berlaku_sampai' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active QR codes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('berlaku_mulai', '<=', now())
            ->where('berlaku_sampai', '>=', now());
    }
}