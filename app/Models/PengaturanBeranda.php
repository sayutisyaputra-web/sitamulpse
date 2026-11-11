<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanBeranda extends Model
{
    protected $table = 'pengaturan_beranda';

    protected $fillable = [
        'judul_utama', 'subjudul', 'judul_sambutan', 'teks_sambutan',
        'daftar_layanan', 'daftar_langkah', 'teks_privasi', 'teks_footer',
        'logo_path', 'banner_path',
    ];

    public const BAWAAN = [
        'judul_utama'    => 'SITAMU LPSE ( Sistem Informasi Tamu LPSE )',
        'subjudul'       => 'Selamat datang. Silakan isi buku tamu sebelum memasuki kantor.',
        'judul_sambutan' => 'Selamat Datang di LPSE Kabupaten Siak',
        'teks_sambutan'  => 'Terima kasih telah berkunjung. Layanan Pengadaan Secara Elektronik (LPSE) Kabupaten Siak siap membantu kebutuhan Anda terkait pengadaan barang dan jasa pemerintah.',
        'daftar_layanan' => "Akun SPSE\nSikap\nSirup\nAkun Inaproc\nKatalog Inaproc v6\nDaftar Hitam\nPencatatan Non Tender\nKonsultasi Lainnya",
        'daftar_langkah' => "Isi data diri dan pilih layanan yang Anda butuhkan.\nCatat nomor kunjungan yang muncul setelah formulir terkirim.\nTunggu petugas melayani Anda.",
        'teks_privasi'   => 'Data pribadi Anda hanya digunakan untuk pencatatan kunjungan dan keamanan kantor ini.',
        'teks_footer'    => '© 2026 SITAMU LPSE ( Sistem Informasi Tamu LPSE )',
    ];

    /** Ambil satu-satunya baris pengaturan; dibuat otomatis bila belum ada. */
    public static function ambil(): self
    {
        return static::first() ?? static::create(self::BAWAAN);
    }

    public function daftarLayananArray(): array
    {
        return self::pecahBaris($this->daftar_layanan);
    }

    public function daftarLangkahArray(): array
    {
        return self::pecahBaris($this->daftar_langkah);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function bannerUrl(): ?string
    {
        return $this->banner_path ? asset('storage/' . $this->banner_path) : null;
    }

    private static function pecahBaris(?string $teks): array
    {
        $baris = preg_split('/\r\n|\r|\n/', (string) $teks);

        return array_values(array_filter(array_map('trim', $baris), fn ($b) => $b !== ''));
    }
}