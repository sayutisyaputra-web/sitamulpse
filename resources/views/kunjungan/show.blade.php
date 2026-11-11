@extends('layouts.app')

@section('title', 'Detail Kunjungan | Buku Tamu')

@section('content')
@php
    $bolehKelola = auth()->user()->bisaProses();

    $keluar = $kunjungan->waktu_keluar
        ? $kunjungan->waktu_keluar->format('d/m/Y H:i')
            . ' (' . (int) abs($kunjungan->waktu_masuk->diffInMinutes($kunjungan->waktu_keluar)) . ' menit)'
        : 'Belum check-out';

    $dataTamu = [
        'Identitas' => $kunjungan->jenis_identitas . ' · ' . $kunjungan->nomor_identitas_samar,
        'Nomor HP'  => $kunjungan->no_hp,
        'Email'     => $kunjungan->email ?: '-',
        'Instansi'  => $kunjungan->instansi ?: '-',
        'Jabatan'   => $kunjungan->jabatan ?: '-',
    ];

    $dataKunjungan = [
        'Petugas'            => $kunjungan->petugas
            ? ((int) $kunjungan->petugas_id === (int) auth()->id() ? 'Anda (' . $kunjungan->petugas->name . ')' : $kunjungan->petugas->name)
            : 'Antrean (belum ada petugas)',
        'Jumlah pendamping'  => $kunjungan->jumlah_pendamping,
        'Kategori layanan'   => $kunjungan->kategori_layanan ?: '-',
        'Pilihan layanan'    => $kunjungan->sub_layanan ?: '-',
        'Media pelaporan'    => $kunjungan->media_pelaporan ?: '-',
        'Waktu kedatangan'   => $kunjungan->waktu_kedatangan ? $kunjungan->waktu_kedatangan->format('d/m/Y H:i') : '-',
        'Waktu masuk'        => $kunjungan->waktu_masuk->format('d/m/Y H:i'),
        'Waktu keluar'       => $keluar,
    ];

    // Bidang hanya tampil untuk data lama yang memang punya bidang
    if ($kunjungan->bidang) {
        $dataKunjungan = ['Bidang (data lama)' => $kunjungan->bidang->nama] + $dataKunjungan;
    }
@endphp

<div class="mb-5 flex items-center justify-between gap-3">
    <a href="{{ route('petugas.kunjungan.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-700 hover:underline">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali ke daftar
    </a>
    @include('kunjungan._status', ['status' => $kunjungan->status])
</div>

@if ($kunjungan->status === 'hangus')
    <div class="mb-5 rounded-xl border border-slate-300 bg-slate-100 p-3 text-sm text-slate-700">
        Tiket ini <strong>hangus</strong> karena melewati tanggal kedatangan yang dipilih
        @if ($kunjungan->waktu_kedatangan)
            ({{ $kunjungan->waktu_kedatangan->format('d/m/Y') }})
        @endif
        dan belum sempat dilayani.
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-3">
    <div class="ui-card lg:col-span-2">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-5">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xl font-semibold text-white">
                {{ mb_strtoupper(mb_substr($kunjungan->nama, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h1 class="truncate text-xl font-semibold text-brand-900">{{ $kunjungan->nama }}</h1>
                <p class="font-mono text-sm text-slate-500">{{ $kunjungan->nomor_kunjungan }}</p>
            </div>
        </div>

        <h2 class="mt-5 text-xs font-semibold uppercase tracking-wider text-brand-700">Data tamu</h2>
        <dl class="mt-3 grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
            @foreach ($dataTamu as $label => $nilai)
                <div>
                    <dt class="text-slate-500">{{ $label }}</dt>
                    <dd class="mt-0.5 font-medium text-slate-900">{{ $nilai }}</dd>
                </div>
            @endforeach
        </dl>

        <h2 class="mt-6 text-xs font-semibold uppercase tracking-wider text-brand-700">Kunjungan</h2>
        <dl class="mt-3 grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
            @foreach ($dataKunjungan as $label => $nilai)
                <div>
                    <dt class="text-slate-500">{{ $label }}</dt>
                    <dd class="mt-0.5 font-medium text-slate-900">{{ $nilai }}</dd>
                </div>
            @endforeach
            <div class="sm:col-span-2">
                <dt class="text-slate-500">Permasalahan (Detail)</dt>
                <dd class="mt-0.5 whitespace-pre-line rounded-lg bg-slate-50 p-3 font-medium text-slate-900">{{ $kunjungan->keperluan }}</dd>
            </div>
        </dl>

        @if ($bolehKelola)
            <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                @if ($kunjungan->status === 'menunggu')
                    @if ((int) $kunjungan->petugas_id !== (int) auth()->id())
                        <form method="POST" action="{{ route('petugas.kunjungan.ambil', $kunjungan) }}">
                            @csrf
                            <button class="ui-btn ui-btn-primary whitespace-nowrap">Ambil tiket</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('petugas.kunjungan.status', $kunjungan) }}">
                        @csrf
                        <input type="hidden" name="status" value="diterima">
                        <button class="ui-btn ui-btn-primary">Terima</button>
                    </form>
                    <form method="POST" action="{{ route('petugas.kunjungan.status', $kunjungan) }}">
                        @csrf
                        <input type="hidden" name="status" value="ditolak">
                        <button class="ui-btn border border-red-300 bg-white text-red-700 hover:bg-red-50">Tolak</button>
                    </form>
                @endif

                @if (! $kunjungan->waktu_keluar && ! in_array($kunjungan->status, ['ditolak', 'hangus'], true))
                    {{-- CHECK-OUT DENGAN FOTO BUKTI --}}
                    <form id="form-checkout" method="POST" action="{{ route('petugas.kunjungan.checkout', $kunjungan) }}">
                        @csrf
                        <input type="hidden" name="foto_keluar_data" id="foto_keluar_data">
                        <button type="button" id="buka-checkout" class="ui-btn bg-emerald-600 text-white hover:bg-emerald-700">Check-out</button>
                        @error('foto_keluar_data')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div id="modal-checkout" class="fk-latar">
                            <div class="fk-kotak" role="dialog" aria-modal="true" aria-labelledby="judul-checkout">
                                <h3 id="judul-checkout">Check-out tamu</h3>
                                <p class="fk-sub">Ambil foto tamu saat selesai sebagai bukti kunjungan.</p>

                                <video id="video-checkout" class="fk-media" style="display:none" autoplay playsinline muted></video>
                                <img id="preview-checkout" class="fk-media" style="display:none" alt="Pratinjau foto check-out">
                                <canvas id="kanvas-checkout" style="display:none"></canvas>
                                <p id="pesan-checkout" class="fk-sub">Nyalakan kamera atau pilih foto dari perangkat.</p>

                                <div class="fk-aksi">
                                    <button type="button" id="kamera-checkout" class="ui-btn bg-slate-700 text-white hover:bg-slate-800">Nyalakan kamera</button>
                                    <button type="button" id="berkas-checkout" class="ui-btn ui-btn-secondary">Pilih foto dari perangkat</button>
                                    <button type="button" id="ambil-checkout" class="ui-btn ui-btn-primary" style="display:none">Ambil foto</button>
                                    <button type="button" id="ulang-checkout" class="ui-btn ui-btn-secondary" style="display:none">Foto ulang</button>
                                </div>
                                <input type="file" id="input-berkas-checkout" accept="image/*" capture="user" style="display:none">

                                <div class="fk-aksi fk-bawah">
                                    <button type="submit" id="kirim-checkout" class="ui-btn bg-emerald-600 text-white hover:bg-emerald-700" disabled>Simpan &amp; check-out</button>
                                    <button type="button" id="batal-checkout" class="ui-btn ui-btn-secondary">Batal</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if (! in_array($kunjungan->status, ['ditolak', 'hangus'], true))
                    <a href="{{ route('petugas.kunjungan.cetak', $kunjungan) }}" target="_blank" class="ui-btn ui-btn-secondary">Cetak tanda pengenal</a>
                @endif

                @if (auth()->user()->adalahSuperadmin())
                    <form method="POST" action="{{ route('petugas.kunjungan.destroy', $kunjungan) }}" class="ml-auto"
                          onsubmit="return confirm('Hapus data kunjungan ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button class="ui-btn border border-red-200 bg-white text-red-600 hover:bg-red-50">Hapus</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-6">
        @if ($bolehKelola)
            <div class="ui-card">
                <h2 class="mb-3 text-sm font-semibold text-brand-900">Tanda tangan</h2>
                @if ($kunjungan->tanda_tangan_path)
                    <img src="{{ route('kunjungan.tanda-tangan', $kunjungan) }}" alt="Tanda tangan"
                         class="w-full rounded-xl border border-slate-200 bg-white">
                @else
                    <p class="text-sm text-slate-500">Tidak ada tanda tangan.</p>
                @endif
            </div>
        @else
            <div class="ui-card text-sm text-slate-500">
                Foto dan tanda tangan tidak ditampilkan untuk peran Anda.
            </div>
        @endif
    </div>
</div>

@if ($bolehKelola)
    {{-- BUKTI FOTO: MASUK DAN CHECK-OUT BERDAMPINGAN --}}
    <div class="ui-card mt-6">
        <h2 class="mb-4 text-sm font-semibold text-brand-900">Bukti foto kunjungan</h2>
        <div class="grid gap-6 sm:grid-cols-2">
            <figure>
                <figcaption class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Foto masuk · {{ $kunjungan->waktu_masuk->format('d/m/Y H:i') }}
                </figcaption>
                @if ($kunjungan->foto_path)
                    <a href="{{ route('kunjungan.foto', $kunjungan) }}" target="_blank">
                        <img src="{{ route('kunjungan.foto', $kunjungan) }}" alt="Foto masuk" class="w-full rounded-xl ring-1 ring-slate-200">
                    </a>
                @else
                    <p class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Tidak ada foto masuk.</p>
                @endif
            </figure>

            <figure>
                <figcaption class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Foto check-out{{ $kunjungan->waktu_keluar ? ' · ' . $kunjungan->waktu_keluar->format('d/m/Y H:i') : '' }}
                </figcaption>
                @if ($kunjungan->foto_keluar_path)
                    <a href="{{ route('kunjungan.foto-keluar', $kunjungan) }}" target="_blank">
                        <img src="{{ route('kunjungan.foto-keluar', $kunjungan) }}" alt="Foto check-out" class="w-full rounded-xl ring-1 ring-slate-200">
                    </a>
                @elseif ($kunjungan->waktu_keluar)
                    <p class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Check-out dilakukan tanpa foto.</p>
                @else
                    <p class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Belum check-out. Foto akan muncul setelah tamu check-out.</p>
                @endif
            </figure>
        </div>
    </div>
@endif

<style>
    .fk-latar { display: none; position: fixed; inset: 0; z-index: 60; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, .55); }
    .fk-latar.fk-buka { display: flex; }
    .fk-kotak { width: 100%; max-width: 30rem; max-height: 92vh; overflow: auto; border-radius: 1rem; background: #fff; padding: 1.25rem; box-shadow: 0 20px 50px rgba(0, 0, 0, .3); }
    .fk-kotak h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e2a78; }
    .fk-sub { margin: .25rem 0 .75rem; font-size: .85rem; color: #64748b; }
    .fk-media { display: block; width: 100%; margin-bottom: .5rem; border-radius: .75rem; background: #000; }
    .fk-aksi { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .5rem; }
    .fk-bawah { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; }
    .fk-aksi button:disabled { opacity: .5; cursor: not-allowed; }
</style>

<script>
(function () {
    const form = document.getElementById('form-checkout');
    if (!form) { return; }

    const modal = document.getElementById('modal-checkout');
    const btnBuka = document.getElementById('buka-checkout');
    const btnBatal = document.getElementById('batal-checkout');
    const btnKirim = document.getElementById('kirim-checkout');
    const video = document.getElementById('video-checkout');
    const preview = document.getElementById('preview-checkout');
    const kanvas = document.getElementById('kanvas-checkout');
    const pesan = document.getElementById('pesan-checkout');
    const input = document.getElementById('foto_keluar_data');
    const btnKamera = document.getElementById('kamera-checkout');
    const btnBerkas = document.getElementById('berkas-checkout');
    const btnAmbil = document.getElementById('ambil-checkout');
    const btnUlang = document.getElementById('ulang-checkout');
    const berkas = document.getElementById('input-berkas-checkout');
    let stream = null;

    function atur(el, tampil) { el.style.display = tampil ? '' : 'none'; }

    function matikanKamera() {
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
        atur(video, false);
    }

    function mulai() {
        input.value = '';
        btnKirim.disabled = true;
        preview.removeAttribute('src');
        atur(preview, false);
        atur(video, false);
        atur(btnKamera, true);
        atur(btnBerkas, true);
        atur(btnAmbil, false);
        atur(btnUlang, false);
        pesan.textContent = 'Nyalakan kamera atau pilih foto dari perangkat.';
    }

    function siap() {
        preview.src = input.value;
        atur(preview, true);
        atur(video, false);
        atur(btnKamera, false);
        atur(btnBerkas, false);
        atur(btnAmbil, false);
        atur(btnUlang, true);
        btnKirim.disabled = false;
        pesan.textContent = 'Foto sudah siap. Periksa, lalu klik Simpan & check-out.';
    }

    function buka() { mulai(); modal.classList.add('fk-buka'); }
    function tutup() { matikanKamera(); modal.classList.remove('fk-buka'); }

    function dariGambar(sumber, lebarAsli, tinggiAsli) {
        const lebar = 640;
        const tinggi = Math.round(lebar * (tinggiAsli / lebarAsli));
        kanvas.width = lebar;
        kanvas.height = tinggi;
        kanvas.getContext('2d').drawImage(sumber, 0, 0, lebar, tinggi);
        input.value = kanvas.toDataURL('image/jpeg', 0.8);
        matikanKamera();
        siap();
    }

    btnBuka.addEventListener('click', buka);
    btnBatal.addEventListener('click', tutup);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('fk-buka')) { tutup(); }
    });

    btnKamera.addEventListener('click', async function () {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            pesan.textContent = 'Kamera tidak didukung di alamat ini. Gunakan HTTPS atau pilih foto dari perangkat.';
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = stream;
            atur(video, true);
            atur(preview, false);
            atur(btnKamera, false);
            atur(btnBerkas, false);
            atur(btnAmbil, true);
            pesan.textContent = 'Hadapkan tamu ke kamera, lalu klik Ambil foto.';
        } catch (e) {
            pesan.textContent = 'Kamera tidak dapat diakses (' + e.name + '). Anda dapat memilih foto dari perangkat.';
        }
    });

    btnAmbil.addEventListener('click', function () {
        if (!video.videoWidth) {
            pesan.textContent = 'Kamera belum siap, coba lagi sebentar.';
            return;
        }
        dariGambar(video, video.videoWidth, video.videoHeight);
    });

    btnBerkas.addEventListener('click', function () { berkas.click(); });

    berkas.addEventListener('change', function () {
        const file = berkas.files[0];
        if (!file) { return; }
        if (!file.type || file.type.indexOf('image/') !== 0) {
            pesan.textContent = 'File harus berupa gambar.';
            return;
        }
        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = function () {
            URL.revokeObjectURL(url);
            dariGambar(img, img.width, img.height);
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            pesan.textContent = 'Gambar tidak dapat dibaca. Coba file lain.';
        };
        img.src = url;
        berkas.value = '';
    });

    btnUlang.addEventListener('click', mulai);

    form.addEventListener('submit', function (e) {
        if (!input.value) {
            e.preventDefault();
            pesan.textContent = 'Foto check-out wajib diambil atau dipilih.';
            return;
        }
        btnKirim.disabled = true;
    });
})();
</script>
@endsection
