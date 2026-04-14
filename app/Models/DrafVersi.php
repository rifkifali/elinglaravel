<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrafVersi extends Model
{
    use HasFactory;

    protected $table = 'draf_versi';

    protected $fillable = [
        'dokumen_id',
        'versi_ke',
        'file_draf',
        'catatan_revisi',
    ];

    protected $casts = [
        'waktu_upload' => 'datetime',
    ];

    public function dokumen()
    {
        return $this->belongsTo(DokumenInduk::class, 'dokumen_id');
    }
}