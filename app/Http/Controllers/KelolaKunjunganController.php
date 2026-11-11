<?php

namespace App\Http\Controllers;

use App\Exports\KunjunganExport;
use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class KelolaKunjunganController extends Controller
{
    private const STATUS = [
        'menunggu' => 'Menunggu',
        'diterima' => 'Diterima',
        'selesai'  => 'Selesai',
        'ditolak'  => 'Ditolak',
        'hangus'   => 'Hangus',
    ];

    public function dashboard(): View
    {
        return view('dashboard', [
            'hariIni'   => Kunjungan::whereDate('waktu_masuk', today())->count(),
            'mingguIni' => Kunjungan::whereBetween('waktu_masuk', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'bulanIni'  => Kunjungan::whereYear('waktu_masuk', now()->year)
                                    ->whereMonth('waktu_masuk', now()->month)->count(),
            'didalam'   => Kunjungan::whereNull('waktu_keluar')
                                    ->where('status', '!=', 'ditolak')
                                    ->whereDate('waktu_masuk', today())->count(),
            'terbaru'   => Kunjungan::with('bidang')->latest('waktu_masuk')->limit(5)->get(),
        ]);
    }

    // Menandai tiket yang melewati tanggal kedatangan sebagai hangus,
    // lalu menyegarkan data tiket yang sedang dibuka (supaya tidak memakai status lama)
    private function segarkan(?Kunjungan $kunjungan = null): void
    {
        Kunjungan::hanguskanKadaluarsa();

        $kunjungan?->refresh();
    }

    private function validasiFilter(Request $request): void
    {
        $request->validate([
            'q'         => ['nullable', 'string', 'max:100'],
            'dari'      => ['nullable', 'date'],
            'sampai'    => ['nullable', 'date'],
            'layanan'   => [
                'nullable',
                'string',
                'max:100',
                function ($atribut, $nilai, $gagal) {
                    // Format: "Kategori" atau "Kategori|Sub layanan"
                    [$kategori, $sub] = array_pad(explode('|', (string) $nilai, 2), 2, '');
                    $daftarSub = Kunjungan::LAYANAN[$kategori] ?? null;

                    if ($daftarSub === null || ($sub !== '' && ! in_array($sub, $daftarSub, true))) {
                        $gagal('Pilihan layanan tidak valid.');
                    }
                },
            ],
            'status'    => ['nullable', Rule::in(array_keys(self::STATUS))],
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $query = Kunjungan::with(['bidang', 'petugas'])->latest('waktu_masuk');

        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nomor_kunjungan', 'like', "%{$q}%")
                  ->orWhere('instansi', 'like', "%{$q}%")
                  // Nomor identitas tersimpan terenkripsi, jadi dicari lewat hash-nya (harus sama persis)
                  ->orWhere('nomor_identitas_hash', Kunjungan::buatHash($q));
            });
        }

        if ($request->filled('dari')) {
            $query->whereDate('waktu_masuk', '>=', $request->input('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('waktu_masuk', '<=', $request->input('sampai'));
        }

        if ($request->filled('layanan')) {
            [$kategori, $sub] = array_pad(explode('|', (string) $request->input('layanan'), 2), 2, '');

            $query->where('kategori_layanan', $kategori);

            if ($sub !== '') {
                $query->where('sub_layanan', $sub);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query;
    }

    // Nama filter yang dipakai (tanpa isinya, karena pencarian bisa memuat data pribadi)
    private function ringkasFilter(Request $request): string
    {
        $dipakai = collect($request->only(['q', 'dari', 'sampai', 'layanan', 'status']))
            ->filter()
            ->keys()
            ->implode(', ');

        $teks = $dipakai !== '' ? 'Filter: ' . $dipakai : 'Tanpa filter';

        return $this->hanyaMilikSendiri($request) ? $teks . ' (hanya tiket sendiri)' : $teks;
    }

    // Petugas hanya memuat tiket miliknya sendiri; admin, superadmin, dan pimpinan memuat semuanya
    private function hanyaMilikSendiri(Request $request): bool
    {
        return $request->user()->role === 'petugas';
    }

    // Data untuk ekspor: sesuai filter, dan khusus akun petugas hanya tiket yang menjadi miliknya
    private function queryEkspor(Request $request)
    {
        $query = $this->filteredQuery($request);

        if ($this->hanyaMilikSendiri($request)) {
            $query->where('petugas_id', $request->user()->id);
        }

        return $query;
    }

    private function namaBerkas(Request $request, string $ekstensi): string
    {
        $awalan = $this->hanyaMilikSendiri($request)
            ? 'buku-tamu-' . Str::slug($request->user()->name) . '-'
            : 'buku-tamu-';

        return $awalan . now()->format('Ymd') . '.' . $ekstensi;
    }

    public function index(Request $request): View
    {
        $this->validasiFilter($request);

        $this->segarkan();

        return view('kunjungan.index', [
            'kunjungans' => $this->filteredQuery($request)->paginate(15)->withQueryString(),
            'layanans'   => Kunjungan::LAYANAN,
            'statuses'   => self::STATUS,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $this->validasiFilter($request);

        LogAktivitas::catat('ekspor_excel', $this->ringkasFilter($request));

        return Excel::download(
            new KunjunganExport($this->queryEkspor($request)),
            $this->namaBerkas($request, 'xlsx')
        );
    }

    public function exportPdf(Request $request)
    {
        $this->validasiFilter($request);

        LogAktivitas::catat('ekspor_pdf', $this->ringkasFilter($request));

        $kunjungans = $this->queryEkspor($request)->get();
        $namaPetugas = $this->hanyaMilikSendiri($request) ? $request->user()->name : 'Semua petugas';
        $pencetak = $request->user()->name;

        return Pdf::loadView('laporan.kunjungan-pdf', compact('kunjungans', 'namaPetugas', 'pencetak'))
            ->setPaper('a4', 'landscape')
            ->download($this->namaBerkas($request, 'pdf'));
    }

    public function show(Kunjungan $kunjungan): View
    {
        $this->segarkan($kunjungan);

        $kunjungan->load(['bidang', 'petugas']);

        LogAktivitas::catat('lihat_detail', $kunjungan->nomor_kunjungan);

        return view('kunjungan.show', ['kunjungan' => $kunjungan]);
    }

    public function status(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['diterima', 'ditolak'])],
        ]);

        $this->segarkan($kunjungan);

        abort_unless($kunjungan->status === 'menunggu', 422, 'Kunjungan ini sudah diproses.');

        $kunjungan->update(['status' => $data['status']]);

        LogAktivitas::catat('ubah_status', $kunjungan->nomor_kunjungan . ' menjadi ' . $data['status']);

        return back()->with('pesan', 'Status kunjungan diperbarui.');
    }

    /**
     * Petugas mengambil tiket tamu yang masih menunggu (bidang apa pun).
     * Baris tiket dikunci selama diproses, jadi dua petugas yang menekan tombol
     * bersamaan tidak akan mengambil tiket yang sama: yang kedua mendapat pemberitahuan.
     */
    public function ambil(Kunjungan $kunjungan): RedirectResponse
    {
        $petugas = auth()->user();

        $this->segarkan();

        $hasil = DB::transaction(function () use ($kunjungan, $petugas) {
            $tiket = Kunjungan::lockForUpdate()->find($kunjungan->id);

            if ($tiket && $tiket->status === 'hangus') {
                return 'hangus';
            }

            if (! $tiket || $tiket->status !== 'menunggu' || $tiket->waktu_keluar !== null) {
                return 'tidak_tersedia';
            }

            if ((int) $tiket->petugas_id === (int) $petugas->id) {
                return 'sudah_milik';
            }

            $sebelumnya = $tiket->petugas_id;

            $tiket->petugas_id = $petugas->id;
            $tiket->save();

            return $sebelumnya ? 'alih' : 'berhasil';
        });

        if ($hasil === 'hangus') {
            return back()->with('pesan', 'Tiket ini hangus karena melewati tanggal kedatangan yang dipilih, sehingga tidak dapat diambil.');
        }

        if ($hasil === 'tidak_tersedia') {
            return back()->with('pesan', 'Tiket ini sudah diproses atau sudah selesai, sehingga tidak dapat diambil.');
        }

        if ($hasil === 'sudah_milik') {
            return back()->with('pesan', 'Tiket ini sudah menjadi tiket Anda.');
        }

        LogAktivitas::catat(
            'ambil_tiket',
            $kunjungan->nomor_kunjungan . ($hasil === 'alih' ? ' (diambil alih dari petugas lain)' : '')
        );

        return back()->with('pesan', 'Tiket berhasil diambil. Silakan proses tamu ini.');
    }

    public function checkout(Kunjungan $kunjungan): RedirectResponse
    {
        $this->segarkan($kunjungan);

        abort_if($kunjungan->waktu_keluar !== null, 422, 'Tamu ini sudah check-out.');
        abort_if($kunjungan->status === 'ditolak', 422, 'Kunjungan yang ditolak tidak dapat check-out.');
        abort_if($kunjungan->status === 'hangus', 422, 'Tiket yang hangus tidak dapat check-out.');

        $kunjungan->update([
            'waktu_keluar' => now(),
            'status'       => 'selesai',
        ]);

        LogAktivitas::catat('checkout', $kunjungan->nomor_kunjungan);

        return back()->with('pesan', 'Check-out tamu dicatat.');
    }

    public function destroy(Kunjungan $kunjungan): RedirectResponse
    {
        // Soft delete: data ditandai terhapus, tidak hilang permanen
        $kunjungan->delete();

        LogAktivitas::catat('hapus_kunjungan', $kunjungan->nomor_kunjungan);

        return redirect()
            ->route('petugas.kunjungan.index')
            ->with('pesan', 'Data kunjungan dihapus.');
    }
}
