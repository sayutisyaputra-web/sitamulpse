<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use App\Services\PenugasanPetugas;
use App\Support\KunciBukuTamu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KunjunganController extends Controller
{
    public function create(): View
    {
        if (! KunciBukuTamu::terbuka()) {
            return view('kunjungan.terkunci');
        }

        return view('kunjungan.create');
    }

    // Daftar petugas sebuah bidang untuk form tamu (JSON)
    public function petugasTersedia(Request $request, PenugasanPetugas $penugasan): JsonResponse
    {
        $data = $request->validate([
            'bidang_id' => ['required', 'integer', 'exists:bidangs,id,aktif,1'],
        ]);

        $daftar = $penugasan->daftarBidang((int) $data['bidang_id'])->values();

        return response()->json([
            'petugas'      => $daftar,
            'ada_tersedia' => $daftar->contains('tersedia', true),
        ]);
    }

    // Daftar petugas sebuah unit layanan untuk form tamu (JSON)
    public function petugasLayananTersedia(Request $request, PenugasanPetugas $penugasan): JsonResponse
    {
        $data = $request->validate([
            'kategori_layanan' => ['required', Rule::in(array_keys(Kunjungan::LAYANAN))],
            'sub_layanan'      => ['nullable', 'string', 'max:50'],
        ]);

        $kategori = $data['kategori_layanan'];
        $sub = (string) ($data['sub_layanan'] ?? '');
        $daftarSub = Kunjungan::LAYANAN[$kategori];

        // Sub layanan harus sesuai kategori; kategori tanpa pilihan lanjutan memakai sub kosong
        if ($daftarSub ? ! in_array($sub, $daftarSub, true) : $sub !== '') {
            return response()->json(['message' => 'Pilihan layanan tidak sesuai.'], 422);
        }

        $daftar = $penugasan->daftarLayanan($kategori, $sub)->values();

        return response()->json([
            'petugas'      => $daftar,
            'ada_tersedia' => $daftar->contains('tersedia', true),
        ]);
    }

    public function store(Request $request, PenugasanPetugas $penugasan): RedirectResponse
    {
        if (! KunciBukuTamu::terbuka()) {
            return redirect()
                ->route('kunjungan.create')
                ->with('pesan_kunci', 'Waktu pengisian habis. Mohon hubungi petugas untuk membuka buku tamu kembali.');
        }

        // Aturan nomor identitas menyesuaikan jenisnya: KTP 16 digit, NIP 18 digit, NIB 13 digit
        $aturanNomor = match ($request->input('jenis_identitas')) {
            'KTP'   => ['digits:16'],
            'NIP'   => ['digits:18'],
            'NIB'   => ['digits:13'],
            default => ['regex:/^[A-Za-z0-9\-]+$/'],
        };

        // Pilihan lanjutan layanan mengikuti kategori yang dipilih
        // (contoh: Sirup -> Fasmon 1 sampai 4). Kategori tanpa pilihan lanjutan tidak menyimpan sub layanan.
        $daftarSub = Kunjungan::LAYANAN[$request->input('kategori_layanan')] ?? [];

        $data = $request->validate(
            [
                'nama'              => ['required', 'string', 'max:100'],
                'jenis_identitas'   => ['required', Rule::in(['KTP', 'NIP', 'NIB'])],
                'nomor_identitas'   => array_merge(['required', 'max:30'], $aturanNomor),
                'instansi'          => ['nullable', 'string', 'max:150'],
                'jabatan'           => ['nullable', 'string', 'max:100'],
                'no_hp'             => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
                'email'             => ['nullable', 'email', 'max:150'],
                'petugas_id'        => ['nullable', 'integer'],
                'kategori_layanan'  => ['required', Rule::in(array_keys(Kunjungan::LAYANAN))],
                'sub_layanan'       => $daftarSub ? ['required', Rule::in($daftarSub)] : ['nullable'],
                'media_pelaporan'   => ['required', Rule::in(Kunjungan::MEDIA)],
                'waktu_kedatangan'  => ['nullable', 'date'],
                'keperluan'         => ['required', 'string', 'max:1000'],
                'jumlah_pendamping' => ['nullable', 'integer', 'min:0', 'max:20'],
                'foto_data'         => ['nullable', 'string', 'max:3000000', 'starts_with:data:image/jpeg;base64,'],
                'tanda_tangan_data' => ['required', 'string', 'max:1500000', 'starts_with:data:image/png;base64,'],
                'setuju_data'       => ['accepted'],
            ],
            [
                'required'                      => ':attribute wajib diisi.',
                'foto_data.required'            => 'Foto wajib diambil atau dipilih.',
                'foto_data.starts_with'         => 'Format foto tidak valid. Silakan ambil ulang.',
                'foto_data.max'                 => 'Ukuran foto terlalu besar. Silakan ambil ulang.',
                'tanda_tangan_data.required'    => 'Tanda tangan wajib diisi.',
                'tanda_tangan_data.starts_with' => 'Format tanda tangan tidak valid. Silakan ulangi.',
                'tanda_tangan_data.max'         => 'Tanda tangan terlalu besar. Silakan ulangi.',
                'max'                           => ':attribute terlalu panjang (maksimal :max).',
                'email'                         => 'Format :attribute tidak valid.',
                'digits'                        => ':attribute harus berupa :digits digit angka.',
                'regex'                         => 'Format :attribute tidak valid.',
                'accepted'                      => 'Anda harus menyetujui penggunaan data pribadi.',
                'exists'                        => ':attribute tidak valid.',
                'in'                            => ':attribute tidak valid.',
                'integer'                       => ':attribute harus berupa angka.',
                'min'                           => ':attribute minimal :min.',
                'date'                          => ':attribute tidak valid.',
            ],
            [
                'nama'              => 'Nama lengkap',
                'jenis_identitas'   => 'Jenis identitas',
                'nomor_identitas'   => 'Nomor identitas',
                'no_hp'             => 'Nomor HP',
                'petugas_id'        => 'Petugas',
                'kategori_layanan'  => 'Kategori layanan',
                'sub_layanan'       => 'Pilihan layanan',
                'media_pelaporan'   => 'Media pelaporan',
                'waktu_kedatangan'  => 'Waktu kedatangan',
                'keperluan'         => 'Permasalahan (detail)',
                'jumlah_pendamping' => 'Jumlah pendamping',
                'foto_data'         => 'Foto',
                'tanda_tangan_data' => 'Tanda tangan',
                'setuju_data'       => 'Persetujuan',
            ]
        );

        // Simpan tanda tangan lebih dulu (paling mungkin gagal karena kosong),
        // lalu foto. Kalau foto gagal, tanda tangan yang sudah tersimpan dihapus lagi.
        $ttdPath = $this->simpanGambar($data['tanda_tangan_data'], 'kunjungan/tanda-tangan', 'png', 'tanda_tangan_data', true);

        // Foto tamu bersifat opsional: bila tidak diambil atau dipilih, kunjungan tetap disimpan tanpa foto
        $fotoPath = null;

        if (! empty($data['foto_data'])) {
            try {
                $fotoPath = $this->simpanGambar($data['foto_data'], 'kunjungan/foto', 'jpg', 'foto_data');
            } catch (ValidationException $e) {
                Storage::disk('local')->delete($ttdPath);
                throw $e;
            }
        }

        // Petugas ditentukan di server saat tombol Kirim ditekan, berdasarkan layanan yang dipilih:
        // - petugas pilihan tamu dipakai bila masih tersedia dan memang memegang layanan itu
        // - bila sibuk (atau tidak dipilih), dialihkan ke petugas tersedia lain di layanan itu
        // - bila semua sibuk atau belum ada petugas, null: tiket masuk antrean (bisa diambil petugas)
        $petugasId = $penugasan->pilihLayanan(
            $data['kategori_layanan'],
            $daftarSub ? (string) ($data['sub_layanan'] ?? '') : '',
            ! empty($data['petugas_id']) ? (int) $data['petugas_id'] : null
        );

        $kunjungan = new Kunjungan([
            'nomor_kunjungan'   => $this->buatNomor(),
            'nama'              => $data['nama'],
            'jenis_identitas'   => $data['jenis_identitas'],
            'nomor_identitas'   => $data['nomor_identitas'],
            'instansi'          => $data['instansi'] ?? null,
            'jabatan'           => $data['jabatan'] ?? null,
            'no_hp'             => $data['no_hp'],
            'email'             => $data['email'] ?? null,
            'kategori_layanan'  => $data['kategori_layanan'] ?? null,
            'sub_layanan'       => $daftarSub ? ($data['sub_layanan'] ?? null) : null,
            'media_pelaporan'   => $data['media_pelaporan'] ?? null,
            'waktu_kedatangan'  => $data['waktu_kedatangan'] ?? null,
            'keperluan'         => $data['keperluan'],
            'jumlah_pendamping' => $data['jumlah_pendamping'] ?? 0,
            'foto_path'         => $fotoPath,
            'tanda_tangan_path' => $ttdPath,
            'status'            => 'menunggu',
            'waktu_masuk'       => now(),
            'setuju_data'       => true,
        ]);
        $kunjungan->petugas_id = $petugasId;
        $kunjungan->save();

        // Dicatat tanpa data pribadi: cukup nomor kunjungan (pengguna kosong = tamu)
        LogAktivitas::catat('kunjungan_baru', $kunjungan->nomor_kunjungan);

        // Setelah satu tamu selesai, buku tamu terkunci lagi
        KunciBukuTamu::kunci();

        return redirect()
            ->route('kunjungan.sukses')
            ->with('nomor_kunjungan', $kunjungan->nomor_kunjungan)
            ->with('nama', $kunjungan->nama);
    }

    public function sukses(): View|RedirectResponse
    {
        if (! session('nomor_kunjungan')) {
            return redirect()->route('kunjungan.create');
        }

        return view('kunjungan.sukses');
    }

    // Menampilkan foto (hanya admin, petugas, dan superadmin yang login, diatur di route)
    public function foto(Kunjungan $kunjungan): StreamedResponse
    {
        LogAktivitas::catat('lihat_foto', $kunjungan->nomor_kunjungan);

        return $this->kirimBerkas($kunjungan->foto_path);
    }

    // Menampilkan tanda tangan (hanya admin, petugas, dan superadmin yang login, diatur di route)
    public function tandaTangan(Kunjungan $kunjungan): StreamedResponse
    {
        LogAktivitas::catat('lihat_tanda_tangan', $kunjungan->nomor_kunjungan);

        return $this->kirimBerkas($kunjungan->tanda_tangan_path);
    }

    private function kirimBerkas(?string $path): StreamedResponse
    {
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    private function buatNomor(): string
    {
        $urut = Kunjungan::withTrashed()->whereDate('waktu_masuk', today())->count() + 1;

        return sprintf('KJ-%s-%04d', now()->format('Ymd'), $urut);
    }

    /**
     * Mengubah data gambar dari browser (base64) menjadi file di folder privat.
     * Gambar dibaca lalu dibuat ulang agar tidak ada data berbahaya yang ikut tersimpan.
     */
    private function simpanGambar(
        string $dataUrl,
        string $folder,
        string $ekstensi,
        string $kolom,
        bool $tolakKosong = false
    ): string {
        $biner = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);

        if ($biner === false || strlen($biner) > 2 * 1024 * 1024) {
            throw ValidationException::withMessages([$kolom => 'Gambar tidak valid atau terlalu besar.']);
        }

        $info = @getimagesizefromstring($biner);

        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            throw ValidationException::withMessages([$kolom => 'File bukan gambar yang valid.']);
        }

        $gambar = @imagecreatefromstring($biner);

        if (! $gambar) {
            throw ValidationException::withMessages([$kolom => 'Gambar tidak dapat diproses.']);
        }

        if ($tolakKosong && $this->gambarKosong($gambar)) {
            imagedestroy($gambar);
            throw ValidationException::withMessages([$kolom => 'Tanda tangan wajib diisi.']);
        }

        ob_start();
        if ($ekstensi === 'png') {
            imagesavealpha($gambar, true);
            imagepng($gambar);
        } else {
            imagejpeg($gambar, null, 85);
        }
        $hasil = ob_get_clean();
        imagedestroy($gambar);

        $path = $folder.'/'.Str::uuid().'.'.$ekstensi;
        Storage::disk('local')->put($path, $hasil);

        return $path;
    }

    // Memeriksa apakah gambar PNG transparan benar-benar kosong (tidak ada goresan)
    private function gambarKosong(\GdImage $gambar): bool
    {
        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);

        for ($y = 0; $y < $tinggi; $y += 2) {
            for ($x = 0; $x < $lebar; $x += 2) {
                $alpha = (imagecolorat($gambar, $x, $y) >> 24) & 0x7F;

                if ($alpha < 100) {
                    return false;
                }
            }
        }

        return true;
    }
}
