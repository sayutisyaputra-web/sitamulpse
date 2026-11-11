<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function adalahSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    // admin atau superadmin
    public function adalahAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin'], true);
    }

    // boleh memproses kunjungan: lihat foto, ubah status, check-out, cetak
    public function bisaProses(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'petugas'], true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'nik' => 'encrypted',
            'aktif' => 'boolean',
        ];
    }
        public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : 'https://ui-avatars.com/api/?background=1e2a78&color=fff&size=128&name=' . urlencode($this->name);
    }
}