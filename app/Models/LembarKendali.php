<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LembarKendali extends Model
{
    use HasFactory;

    protected $table = 'lembar_kendali';

    protected $fillable = [
        'dokumen_id',
        'aktor_id',
        'tindakan',
    ];

    protected $casts = [
        'waktu_tindakan' => 'datetime',
    ];

    public function dokumen()
    {
        return $this->belongsTo(DokumenInduk::class, 'dokumen_id');
    }

    public function aktor()
    {
        return $this->belongsTo(User::class, 'aktor_id');
    }
}