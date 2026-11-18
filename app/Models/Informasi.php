<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Informasi extends Model
{
    use SoftDeletes;

    protected $table = 'informasis';

    protected $fillable = [
        'judul',
        'isi',
        'foto_path',
        'aktif',
        'dibuat_oleh',
        'diubah_oleh',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    // Hanya informasi yang aktif (tampil kepada tamu)
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
