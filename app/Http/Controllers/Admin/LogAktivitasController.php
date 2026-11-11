<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    private const LABEL = [
        'login'                => 'Login',
        'login_gagal'          => 'Login gagal',
        'logout'               => 'Logout',
        'lihat_detail'         => 'Lihat detail kunjungan',
        'ubah_status'          => 'Ubah status kunjungan',
        'checkout'             => 'Checkout tamu',
        'hapus_kunjungan'      => 'Hapus kunjungan',
        'ekspor_excel'         => 'Ekspor Excel',
        'ekspor_pdf'           => 'Ekspor PDF',
        'cetak_tanda_pengenal' => 'Cetak tanda pengenal',
        'kunjungan_baru'       => 'Kunjungan baru (form tamu)',
        'lihat_foto'           => 'Lihat foto tamu',
        'lihat_tanda_tangan'   => 'Lihat tanda tangan tamu',
        'ganti_password'       => 'Ganti password',
        'tambah_bidang'        => 'Tambah bidang',
        'ubah_bidang'          => 'Ubah bidang',
        'status_bidang'        => 'Ubah status bidang',
        'hapus_bidang'         => 'Hapus bidang',
        'tambah_akun'          => 'Tambah akun',
        'ubah_akun'            => 'Ubah akun',
        'status_akun'          => 'Ubah status akun',
        'reset_password'       => 'Reset password akun',
    ];

    public function index(Request $request): View
    {
        $request->validate([
            'aksi'   => ['nullable', 'string', 'max:50'],
            'dari'   => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        // Daftar aktivitas diambil dari data yang benar-benar ada di tabel,
        // jadi jenis aktivitas baru otomatis ikut muncul di filter.
        $aksis = LogAktivitas::query()
            ->select('aksi')
            ->distinct()
            ->orderBy('aksi')
            ->pluck('aksi')
            ->mapWithKeys(fn ($aksi) => [
                $aksi => self::LABEL[$aksi] ?? ucfirst(str_replace('_', ' ', $aksi)),
            ])
            ->all();

        $logs = LogAktivitas::with('user')
            ->when($request->filled('aksi'), fn ($q) => $q->where('aksi', $request->query('aksi')))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('sampai')))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.log.index', compact('logs', 'aksis'));
    }
}