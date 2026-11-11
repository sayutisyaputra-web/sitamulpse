<?php

namespace App\Exports;

use App\Models\Kunjungan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KunjunganExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Query dikirim dari controller (sudah memuat filter yang dipilih di Daftar tamu).
     * Bila tidak dikirim, semua kunjungan diekspor.
     */
    public function __construct(private ?Builder $query = null)
    {
    }

    public function query(): Builder
    {
        return $this->query
            ?? Kunjungan::with(['bidang', 'petugas'])->orderByDesc('waktu_masuk');
    }

    public function headings(): array
    {
        return [
            'No. Kunjungan',
            'Nama',
            'Instansi',
            'Jabatan',
            'Kategori Layanan',
            'Pilihan Layanan',
            'Media Pelaporan',
            'Petugas',
            'Pendamping',
            'Permasalahan (Detail)',
            'Kedatangan',
            'Masuk',
            'Keluar',
            'Status',
            'Bidang (data lama)',
        ];
    }

    public function map($k): array
    {
        return [
            $k->nomor_kunjungan,
            $k->nama,
            $k->instansi,
            $k->jabatan,
            $k->kategori_layanan ?: '-',
            $k->sub_layanan ?: '-',
            $k->media_pelaporan ?: '-',
            $k->petugas->name ?? 'Antrean',
            $k->jumlah_pendamping,
            $k->keperluan,
            $k->waktu_kedatangan ? $k->waktu_kedatangan->format('d/m/Y H:i') : '-',
            $k->waktu_masuk ? $k->waktu_masuk->format('d/m/Y H:i') : '-',
            $k->waktu_keluar ? $k->waktu_keluar->format('d/m/Y H:i') : '-',
            $k->status,
            $k->bidang->nama ?? '-',
        ];
    }
}
