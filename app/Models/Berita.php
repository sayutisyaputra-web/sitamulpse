<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'beritas';

    protected $fillable = [
        'user_id', 'judul', 'slug', 'ringkasan', 'isi',
        'gambar_path', 'terbit', 'tanggal_terbit',
    ];

    protected $casts = [
        'terbit'         => 'boolean',
        'tanggal_terbit' => 'datetime',
    ];

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('terbit', true)
            ->where(function ($q) {
                $q->whereNull('tanggal_terbit')->orWhere('tanggal_terbit', '<=', now());
            });
    }

    public function gambarUrl(): ?string
    {
        return $this->gambar_path ? asset('storage/' . $this->gambar_path) : null;
    }
}