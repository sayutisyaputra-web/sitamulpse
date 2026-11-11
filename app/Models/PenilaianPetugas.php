<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianPetugas extends Model
{
    protected $table = 'penilaian_petugas';

    protected $fillable = ['petugas_id', 'penilai_id', 'periode', 'bintang', 'catatan'];
}
