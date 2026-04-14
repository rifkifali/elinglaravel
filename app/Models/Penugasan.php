<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use HasFactory;

    protected $table = 'penugasan';

    protected $fillable = [
        'dokumen_id',
        'perancang_id',
        'catatan_disposisi',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function dokumen()
    {
        return $this->belongsTo(DokumenInduk::class, 'dokumen_id');
    }

    public function perancang()
    {
        return $this->belongsTo(User::class, 'perancang_id');
    }
}