<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Support\Collection;

class PenugasanPetugas
{
    /**
     * Petugas dianggap "sedang melayani" bila punya kunjungan HARI INI
     * berstatus menunggu atau diterima yang belum check-out.
     */
    public const STATUS_MELAYANI = ['menunggu', 'diterima'];

    /** @return int[] id petugas yang sedang melayani */
    public function idSibuk(): array
    {
        return Kunjungan::query()
            ->whereNotNull('petugas_id')
            ->whereIn('status', self::STATUS_MELAYANI)
            ->whereNull('waktu_keluar')
            ->whereDate('waktu_masuk', today())
            ->pluck('petugas_id')
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Petugas aktif yang menangani sebuah bidang, lengkap dengan status tersedia.
     * (Dipertahankan untuk data dan menu lama.)
     *
     * @return Collection<int, array{id:int, nama:string, tersedia:bool}>
     */
    public function daftarBidang(int $bidangId): Collection
    {
        $sibuk = $this->idSibuk();

        return User::query()
            ->join('bidang_petugas', 'bidang_petugas.user_id', '=', 'users.id')
            ->where('bidang_petugas.bidang_id', $bidangId)
            ->where('users.role', 'petugas')
            ->where('users.aktif', true)
            ->orderBy('users.name')
            ->get(['users.id', 'users.name'])
            ->map(fn ($u) => [
                'id'       => (int) $u->id,
                'nama'     => $u->name,
                'tersedia' => ! in_array((int) $u->id, $sibuk, true),
            ]);
    }

    /**
     * Petugas aktif yang memegang sebuah unit layanan (kategori + sub layanan),
     * lengkap dengan status tersedia. Sub kosong untuk kategori tanpa pilihan lanjutan.
     *
     * @return Collection<int, array{id:int, nama:string, tersedia:bool}>
     */
    public function daftarLayanan(string $kategori, string $sub = ''): Collection
    {
        $sibuk = $this->idSibuk();

        return User::query()
            ->join('layanan_petugas', 'layanan_petugas.user_id', '=', 'users.id')
            ->where('layanan_petugas.kategori_layanan', $kategori)
            ->where('layanan_petugas.sub_layanan', $sub)
            ->where('users.role', 'petugas')
            ->where('users.aktif', true)
            ->orderBy('users.name')
            ->get(['users.id', 'users.name'])
            ->map(fn ($u) => [
                'id'       => (int) $u->id,
                'nama'     => $u->name,
                'tersedia' => ! in_array((int) $u->id, $sibuk, true),
            ]);
    }

    /** @return array<int,int> jumlah kunjungan hari ini per petugas */
    public function bebanHariIni(): array
    {
        return Kunjungan::query()
            ->whereNotNull('petugas_id')
            ->whereDate('waktu_masuk', today())
            ->selectRaw('petugas_id, COUNT(*) as total')
            ->groupBy('petugas_id')
            ->pluck('total', 'petugas_id')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    /**
     * Pilih petugas untuk bidang tertentu (cara lama).
     */
    public function pilih(int $bidangId, ?int $diminta = null): ?int
    {
        return $this->pilihDari($this->daftarBidang($bidangId), $diminta);
    }

    /**
     * Pilih petugas untuk unit layanan tertentu.
     * - Jika petugas yang diminta masih tersedia, dia yang dipilih.
     * - Jika tidak, dipilih petugas tersedia dengan beban hari ini paling sedikit.
     * - Jika semua sedang melayani (atau belum ada petugas), hasilnya null: tiket masuk antrean.
     */
    public function pilihLayanan(string $kategori, string $sub = '', ?int $diminta = null): ?int
    {
        return $this->pilihDari($this->daftarLayanan($kategori, $sub), $diminta);
    }

    private function pilihDari(Collection $daftar, ?int $diminta): ?int
    {
        $tersedia = $daftar->where('tersedia', true);

        if ($tersedia->isEmpty()) {
            return null;
        }

        if ($diminta !== null && $tersedia->contains('id', $diminta)) {
            return $diminta;
        }

        $beban = $this->bebanHariIni();

        $terpilih = $tersedia
            ->sort(fn ($a, $b) => [$beban[$a['id']] ?? 0, $a['nama']] <=> [$beban[$b['id']] ?? 0, $b['nama']])
            ->first();

        return $terpilih['id'];
    }
}
