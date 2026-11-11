<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kunjungan extends Model
{
    use SoftDeletes;

    // Pilihan lanjutan yang sama untuk beberapa layanan
    public const FASMON = ['Fasmon 1', 'Fasmon 2', 'Fasmon 3', 'Fasmon 4'];

    // Kategori layanan => daftar pilihan lanjutan (kosong = tidak ada pilihan lanjutan)
    public const LAYANAN = [
        'Akun SPSE'             => ['Penyedia', 'Non Penyedia'],
        'Sikap'                 => [],
        'Sirup'                 => self::FASMON,
        'Akun Inaproc'          => [],
        'Katalog Inaproc v6'    => [],
        'Daftar Hitam'          => self::FASMON,
        'Pencatatan Non Tender' => self::FASMON,
        'Konsultasi Lainnya'    => [],
    ];

    // Media pelaporan
    public const MEDIA = ['Datang Langsung', 'WhatsApp/Telp'];

    protected $fillable = [
        'nomor_kunjungan',
        'nama',
        'jenis_identitas',
        'nomor_identitas',
        'instansi',
        'jabatan',
        'no_hp',
        'email',
        'bidang_id',
        'kategori_layanan',
        'sub_layanan',
        'media_pelaporan',
        'keperluan',
        'jumlah_pendamping',
        'status',
        'waktu_masuk',
        'waktu_kedatangan',
        'waktu_keluar',
        'foto_path',
        'tanda_tangan_path',
        'setuju_data',
    ];

    protected function casts(): array
    {
        return [
            'nomor_identitas' => 'encrypted',
            'waktu_masuk'     => 'datetime',
            'waktu_kedatangan' => 'datetime',
            'waktu_keluar'    => 'datetime',
            'setuju_data'     => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Setiap kali nomor identitas diisi/diubah, buat "sidik jari" (hash)-nya
        // supaya data tetap bisa dicari walau tersimpan terenkripsi.
        static::saving(function (Kunjungan $kunjungan) {
            if ($kunjungan->isDirty('nomor_identitas')) {
                $kunjungan->nomor_identitas_hash = self::buatHash($kunjungan->nomor_identitas);
            }
        });
    }

    public static function buatHash(string $nomor): string
    {
        return hash_hmac('sha256', $nomor, config('app.key'));
    }

    // Menampilkan nomor identitas tersamarkan, contoh: 1403************34
    protected function nomorIdentitasSamar(): Attribute
    {
        return Attribute::get(function () {
            $nomor = $this->nomor_identitas;
            $panjang = strlen($nomor);

            if ($panjang <= 6) {
                return str_repeat('*', $panjang);
            }

            return substr($nomor, 0, 4)
                . str_repeat('*', $panjang - 6)
                . substr($nomor, -2);
        });
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    // Petugas yang menangani (mengambil tiket) kunjungan ini
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
        /**
     * Versi samar nomor identitas, contoh: 3201********1234
     */
    public function identitasSamar(): string
    {
        $nomor = (string) $this->nomor_identitas;
        $panjang = mb_strlen($nomor);

        if ($panjang <= 4) {
            return str_repeat('*', $panjang);
        }

        if ($panjang <= 8) {
            return str_repeat('*', $panjang - 2) . mb_substr($nomor, -2);
        }

        return mb_substr($nomor, 0, 4)
            . str_repeat('*', $panjang - 8)
            . mb_substr($nomor, -4);
    }
        /**
     * Nomor identitas sesuai peran: pimpinan melihat versi samar, peran lain melihat penuh.
     */
    public function identitasUntuk(?User $user): string
    {
        if ($user && $user->role === 'pimpinan') {
            return $this->identitasSamar();
        }

        return (string) $this->nomor_identitas;
    }

    /**
     * Waktu kedatangan yang diisi tamu. Bila kosong (data lama atau tidak diisi),
     * dipakai waktu masuk otomatis.
     */
    public function waktuKedatanganTampil(): ?\Illuminate\Support\Carbon
    {
        return $this->waktu_kedatangan ?? $this->waktu_masuk;
    }

    /**
     * Antrean hangus (seperti antrean paspor): tiket yang masih berstatus "menunggu"
     * dan tanggal kedatangan yang dipilihnya sudah lewat (sebelum hari ini) ditandai "hangus".
     * Data lama tanpa tanggal kedatangan tidak disentuh. Mengembalikan jumlah tiket yang dihanguskan.
     */
    public static function hanguskanKadaluarsa(): int
    {
        return static::query()
            ->where('status', 'menunggu')
            ->whereNotNull('waktu_kedatangan')
            ->where('waktu_kedatangan', '<', today())
            ->update(['status' => 'hangus']);
    }

    /**
     * Teks layanan untuk ditampilkan, contoh: "Sirup - Fasmon 2". Kosong untuk data lama.
     */
    public function layananLengkap(): string
    {
        if (! $this->kategori_layanan) {
            return '-';
        }

        return $this->sub_layanan
            ? $this->kategori_layanan . ' - ' . $this->sub_layanan
            : $this->kategori_layanan;
    }
}
