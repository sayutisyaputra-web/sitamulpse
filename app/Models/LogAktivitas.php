<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\KunciBukuTamu;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    public $timestamps = false;

    protected $fillable = ['user_id', 'aksi', 'keterangan', 'ip'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mencatat satu aktivitas. Kalau pencatatan gagal, aplikasi tetap berjalan.
     * $userId boleh diisi bila pelakunya bukan pengguna yang sedang login.
     */
    public static function catat(string $aksi, ?string $keterangan = null, ?int $userId = null): void
    {
        try {
            static::create([
                'user_id'    => $userId ?? auth()->id(),
                'aksi'       => $aksi,
                'keterangan' => $keterangan,
                'ip'         => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}