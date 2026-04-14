<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenInduk extends Model
{
    use HasFactory;

    protected $table = 'dokumen_induk';

    protected $fillable = [
        'judul_draf',
        'instansi_pemohon',
        'file_surat_awal',
        'status_global',
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
    ];

    // Relasi
    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'dokumen_id');
    }

    public function drafVersi()
    {
        return $this->hasMany(DrafVersi::class, 'dokumen_id');
    }

    public function lembarKendali()
    {
        return $this->hasMany(LembarKendali::class, 'dokumen_id')->with('aktor');
    }

    public function drafTerbaru()
    {
        return $this->hasOne(DrafVersi::class, 'dokumen_id')->latestOfMany('versi_ke');
    }

    // Badge class helper untuk status
    public function getBadgeClassAttribute(): string
    {
        return match($this->status_global) {
            'Menunggu Disposisi' => 'badge-soft-warning',
            'Proses Drafting'    => 'badge-soft-primary',
            'Review Kasubbag',
            'Review Kabag'       => 'badge-soft-warning',
            'Final'              => 'badge-soft-success',
            default              => 'badge-soft-secondary',
        };
    }

    // Versi berikutnya
    public function nextVersi(): int
    {
        $last = $this->drafVersi()->max('versi_ke');
        return $last ? $last + 1 : 1;
    }
}