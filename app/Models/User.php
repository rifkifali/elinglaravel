<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nama_lengkap',
        'nip',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'perancang_id');
    }

    public function lembarKendali()
    {
        return $this->hasMany(LembarKendali::class, 'aktor_id');
    }

    // Helper: cek role
    public function isAdmin(): bool        { return $this->role === 'Admin'; }
    public function isPerancang(): bool    { return $this->role === 'Perancang'; }
    public function isKasubbag(): bool     { return $this->role === 'Kasubbag'; }
    public function isKabag(): bool        { return $this->role === 'Kabag'; }
    public function isSuperAdmin(): bool   { return $this->role === 'Super Admin'; }

    // Redirect setelah login berdasarkan role
    public function dashboardRoute(): string
    {
        return match($this->role) {
            'Admin'       => 'admin.index',
            'Perancang'   => 'perancang.index',
            'Kasubbag'    => 'kasubbag.index',
            'Kabag'       => 'kabag.index',
            'Super Admin' => 'superadmin.index',
            default       => 'login',
        };
    }
}