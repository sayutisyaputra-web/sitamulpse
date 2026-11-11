<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    protected $fillable = ['nama'];

    public function kunjungans(): HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }
}