<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Http\Controllers\Admin\PetugasBidangController;

class LogController extends Controller
{
    private const AKSI = [
        'login'           => 'Login berhasil',
        'login_gagal'     => 'Login gagal',
        'logout'          => 'Logout',
        'ganti_password'  => 'Ganti password sendiri',
        'lihat_detail'    => 'Lihat detail tamu',
        'ubah_status'     => 'Ubah status kunjungan',
        'checkout'        => 'Check-out',
        'hapus_kunjungan' => 'Hapus kunjungan',
        'ekspor_excel'    => 'Ekspor Excel',
        'ekspor_pdf'      => 'Ekspor PDF',
        'tambah_bidang'   => 'Tambah bidang',
        'ubah_bidang'     => 'Ubah nama bidang',
        'status_bidang'   => 'Aktif/nonaktif bidang',
        'hapus_bidang'    => 'Hapus bidang',
        'tambah_akun'     => 'Tambah akun',
        'ubah_akun'       => 'Ubah data akun',
        'status_akun'     => 'Aktif/nonaktif akun',
        'reset_password'  => 'Reset password akun',
        'reset_pin'       => 'Reset PIN akun',
        'cetak_tanda_pengenal' => 'Cetak tanda pengenal',
    ];

    public function index(Request $request): View
    {
        $request->validate([
            'aksi'   => ['nullable', Rule::in(array_keys(self::AKSI))],
            'dari'   => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        $query = LogAktivitas::with('user')->latest('id');

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->input('aksi'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->input('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->input('sampai'));
        }

        return view('admin.log.index', [
            'logs'  => $query->paginate(25)->withQueryString(),
            'aksis' => self::AKSI,
        ]);
    }
}