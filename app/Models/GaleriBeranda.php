<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GaleriBeranda extends Model
{
    protected $table = 'galeri_beranda';

    protected $fillable = ['keterangan', 'foto_path', 'urutan', 'tampil'];

    protected $casts = [
        'tampil' => 'boolean',
        'urutan' => 'integer',
    ];

    public function scopeTampil(Builder $query): Builder
    {
        return $query->where('tampil', true);
    }

    public function fotoUrl(): string
    {
        return asset('storage/' . $this->foto_path);
    }
}