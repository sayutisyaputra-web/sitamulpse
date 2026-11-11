@extends('layouts.tamu')

@section('title', 'Form Buku Tamu')
@section('subjudul', 'Selamat datang. Silakan isi buku tamu sebelum memasuki kantor.')

@section('content')
<div class="rounded-xl bg-white p-6 shadow sm:p-8">
    <div class="bt-judul">
        <h2>Formulir Kunjungan</h2>
        <p>
            Isi data berikut dengan benar sesuai identitas Anda.
            Kolom bertanda <span class="text-red-600">*</span> wajib diisi.
        </p>
    </div>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            Mohon periksa kembali isian yang ditandai merah.
        </div>
    @endif

    <form method="POST" action="{{ route('kunjungan.store') }}">
        @csrf

        {{-- ============ BAGIAN 1: DATA DIRI ============ --}}
        <section class="bt-bagian">
            <div class="bt-kepala">
                <span class="bt-nomor">1</span>
                <div>
                    <h3>Data diri</h3>
                    <p>Sesuai dengan kartu identitas Anda.</p>
                </div>
                <svg class="bt-ikon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                </svg>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-slate-700">Nama lengkap <span class="text-red-600">*</span></label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required
                           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="jenis_identitas" class="block text-sm font-medium text-slate-700">Jenis identitas <span class="text-red-600">*</span></label>
                        <select id="jenis_identitas" name="jenis_identitas" required
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach (['NIK', 'NIP', 'NIB'] as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis_identitas', 'KTP') === $jenis)>{{ $jenis }}</option>
                            @endforeach
                        </select>
                        @error('jenis_identitas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nomor_identitas" class="block text-sm font-medium text-slate-700">Nomor identitas <span class="text-red-600">*</span></label>
                        <input id="nomor_identitas" name="nomor_identitas" type="text" inputmode="numeric" autocomplete="off"
                               value="{{ old('nomor_identitas') }}" required
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p id="hint-identitas" class="mt-1 text-xs text-slate-500"></p>
                        @error('nomor_identitas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-slate-700">Nomor HP <span class="text-red-600">*</span></label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" required
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('no_hp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="instansi" class="block text-sm font-medium text-slate-700">Instansi / Nama Usaha</label>
                        <input id="instansi" name="instansi" type="text" value="{{ old('instansi') }}"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('instansi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-slate-700">Jabatan</label>
                        <input id="jabatan" name="jabatan" type="text" value="{{ old('jabatan') }}"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('jabatan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ BAGIAN 2: KEPERLUAN ============ --}}
        <section class="bt-bagian">
            <div class="bt-kepala">
                <span class="bt-nomor">2</span>
                <div>
                    <h3>Keperluan Kunjungan</h3>
                    <p>Tujuan dan jumlah orang yang datang bersama Anda.</p>
                </div>
                <svg class="bt-ikon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9z"/><path d="M9 12h6M9 16h4"/>
                </svg>
            </div>

            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="jumlah_pendamping" class="block text-sm font-medium text-slate-700">Jumlah tamu</label>
                        <input id="jumlah_pendamping" name="jumlah_pendamping" type="number" min="0" max="20"
                               value="{{ old('jumlah_pendamping', 0) }}"
                               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('jumlah_pendamping') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- TANGGAL DAN WAKTU KEDATANGAN (diisi manual, boleh diubah) --}}
                <div>
                    <label for="waktu_kedatangan" class="block text-sm font-medium text-slate-700">Tanggal dan waktu kedatangan</label>
                    <input id="waktu_kedatangan" name="waktu_kedatangan" type="datetime-local"
                           value="{{ old('waktu_kedatangan', now()->format('Y-m-d\TH:i')) }}"
                           class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-1/2">
                    <p class="mt-1 text-xs text-slate-500">Terisi otomatis dengan waktu sekarang. Bisa diubah ke tanggal dan jam lain, termasuk hari-hari ke depan.</p>
                    @error('waktu_kedatangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- KATEGORI LAYANAN DAN MEDIA PELAPORAN --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="kategori_layanan" class="block text-sm font-medium text-slate-700">Kategori layanan <span class="text-red-600">*</span></label>
                        <select id="kategori_layanan" name="kategori_layanan" required
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih kategori layanan --</option>
                            @foreach (array_keys(\App\Models\Kunjungan::LAYANAN) as $kategori)
                                <option value="{{ $kategori }}" @selected(old('kategori_layanan') === $kategori)>{{ $kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_layanan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="media_pelaporan" class="block text-sm font-medium text-slate-700">Media pelaporan <span class="text-red-600">*</span></label>
                        <select id="media_pelaporan" name="media_pelaporan" required
                                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih media pelaporan --</option>
                            @foreach (\App\Models\Kunjungan::MEDIA as $media)
                                <option value="{{ $media }}" @selected(old('media_pelaporan') === $media)>{{ $media }}</option>
                            @endforeach
                        </select>
                        @error('media_pelaporan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- PILIHAN LANJUTAN LAYANAN (muncul bila kategori punya pilihan lanjutan) --}}
                <div id="blok-sub-layanan" class="hidden">
                    <label for="sub_layanan" class="block text-sm font-medium text-slate-700">Pilihan layanan <span class="text-red-600">*</span></label>
                    <select id="sub_layanan" name="sub_layanan" data-old="{{ old('sub_layanan') }}" disabled
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih --</option>
                    </select>
                    @error('sub_layanan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- PILIHAN PETUGAS (muncul setelah layanan dipilih) --}}
                <div id="blok-petugas" class="hidden">
                    <label for="petugas_id" class="block text-sm font-medium text-slate-700">Petugas yang dituju</label>
                    <select id="petugas_id" name="petugas_id" data-old="{{ old('petugas_id') }}"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Otomatis (petugas yang tersedia)</option>
                    </select>
                    <p id="info-petugas" class="mt-1 text-xs text-slate-500"></p>
                    @error('petugas_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="keperluan" class="block text-sm font-medium text-slate-700">Permasalahan (Detail) <span class="text-red-600">*</span></label>
                    <textarea id="keperluan" name="keperluan" rows="3" required
                              placeholder="Jelaskan permasalahan Anda secara singkat dan jelas"
                              class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keperluan') }}</textarea>
                    @error('keperluan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- ============ BAGIAN 3: FOTO DAN TANDA TANGAN ============ --}}
        <section class="bt-bagian">
            <div class="bt-kepala">
                <span class="bt-nomor">3</span>
                <div>
                    <h3>Foto dan Tanda Tangan</h3>
                    <p>Pastikan wajah terlihat jelas pada foto.</p>
                </div>
                <svg class="bt-ikon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>
                </svg>
            </div>

            <div class="space-y-4">
                {{-- FOTO TAMU --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">Foto tamu <span class="text-xs font-normal text-slate-400">(opsional)</span></label>
                    <div class="mt-1 space-y-3 rounded-lg border border-slate-300 bg-slate-50 p-3">
                        <video id="video" autoplay playsinline muted class="hidden w-full rounded-lg bg-black"></video>
                        <img id="preview" alt="Pratinjau foto" class="hidden w-full rounded-lg">
                        <canvas id="kanvas" class="hidden"></canvas>
                        <p id="pesan-kamera" class="text-sm text-slate-500">Nyalakan kamera atau pilih foto dari perangkat.</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="btn-kamera"
                                    class="rounded-lg bg-slate-700 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                Nyalakan kamera
                            </button>
                            <button type="button" id="btn-berkas"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                                Pilih foto dari perangkat
                            </button>
                            <button type="button" id="btn-ambil"
                                    class="hidden rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Ambil foto
                            </button>
                            <button type="button" id="btn-ulang"
                                    class="hidden rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                Foto ulang
                            </button>
                        </div>
                        <input type="file" id="berkas-foto" accept="image/*" capture="user" class="hidden">
                    </div>
                    <input type="hidden" name="foto_data" id="foto_data" value="{{ old('foto_data') }}">
                    @error('foto_data') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- TANDA TANGAN DIGITAL --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tanda tangan <span class="text-red-600">*</span></label>
                    <div class="mt-1 rounded-lg border border-slate-300 bg-slate-50 p-3">
                        <canvas id="kanvas-ttd" width="600" height="200"
                                class="w-full rounded-lg border border-dashed border-slate-400 bg-white"
                                style="touch-action: none; cursor: crosshair;"></canvas>
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-slate-500">Tanda tangani di dalam kotak di atas.</p>
                            <button type="button" id="btn-hapus-ttd"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-100">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="tanda_tangan_data" id="tanda_tangan_data" value="{{ old('tanda_tangan_data') }}">
                    @error('tanda_tangan_data') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- ============ PERSETUJUAN DAN KIRIM ============ --}}
        <div class="bt-bagian">
            <div class="rounded-lg bg-slate-50 p-3">
                <label class="flex items-start gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="setuju_data" value="1" class="mt-1 rounded border-slate-300" @checked(old('setuju_data'))>
                    <span>
                        Saya menyetujui data pribadi saya (termasuk nomor identitas) dicatat dan digunakan hanya
                        untuk keperluan pencatatan kunjungan dan keamanan kantor ini.
                    </span>
                </label>
                @error('setuju_data') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="mt-4 w-full rounded-lg bg-blue-600 py-2.5 font-semibold text-white hover:bg-blue-700">
                Kirim
            </button>
        </div>
    </form>
</div>

<style>
    .bt-judul h2 { margin: 0; font-size: 1.5rem; font-weight: 800; color: #1e2a78; }
    .bt-judul p { margin: .35rem 0 0; font-size: .9rem; color: #64748b; }

    .bt-bagian { margin-top: 1.75rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; }
    .bt-bagian:first-of-type { margin-top: 1.5rem; }

    .bt-kepala { display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem; }
    .bt-nomor {
        display: inline-flex; flex: none; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 999px;
        background: #1e2a78; color: #fff; font-size: .9rem; font-weight: 700;
    }
    .bt-kepala h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; }
    .bt-kepala p { margin: .1rem 0 0; font-size: .8rem; color: #64748b; }
    .bt-ikon { margin-left: auto; flex: none; color: #94a3b8; }
</style>

<script>
(function () {
    // ======================= FOTO =======================
    const video = document.getElementById('video');
    const preview = document.getElementById('preview');
    const kanvas = document.getElementById('kanvas');
    const pesan = document.getElementById('pesan-kamera');
    const input = document.getElementById('foto_data');
    const btnKamera = document.getElementById('btn-kamera');
    const btnBerkas = document.getElementById('btn-berkas');
    const btnAmbil = document.getElementById('btn-ambil');
    const btnUlang = document.getElementById('btn-ulang');
    const berkas = document.getElementById('berkas-foto');
    let stream = null;

    function matikanKamera() {
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
        video.classList.add('hidden');
    }

    function tampilkanPratinjau() {
        preview.src = input.value;
        preview.classList.remove('hidden');
        video.classList.add('hidden');
        btnKamera.classList.add('hidden');
        btnBerkas.classList.add('hidden');
        btnAmbil.classList.add('hidden');
        btnUlang.classList.remove('hidden');
        pesan.textContent = 'Foto sudah siap.';
    }

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
            video.classList.remove('hidden');
            preview.classList.add('hidden');
            btnKamera.classList.add('hidden');
            btnBerkas.classList.add('hidden');
            btnAmbil.classList.remove('hidden');
            pesan.textContent = 'Hadapkan wajah ke kamera, lalu klik Ambil foto.';
        } catch (e) {
            pesan.textContent = 'Kamera tidak dapat diakses (' + e.name + '). Anda dapat memilih foto dari perangkat.';
        }
    });

    btnAmbil.addEventListener('click', function () {
        if (!video.videoWidth) {
            pesan.textContent = 'Kamera belum siap, coba lagi sebentar.';
            return;
        }
        const lebar = 640;
        const tinggi = Math.round(lebar * (video.videoHeight / video.videoWidth));
        kanvas.width = lebar;
        kanvas.height = tinggi;
        kanvas.getContext('2d').drawImage(video, 0, 0, lebar, tinggi);
        input.value = kanvas.toDataURL('image/jpeg', 0.8);
        matikanKamera();
        tampilkanPratinjau();
    });

    btnBerkas.addEventListener('click', function () {
        berkas.click();
    });

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
            const lebar = 640;
            const tinggi = Math.round(lebar * (img.height / img.width));
            kanvas.width = lebar;
            kanvas.height = tinggi;
            kanvas.getContext('2d').drawImage(img, 0, 0, lebar, tinggi);
            URL.revokeObjectURL(url);
            input.value = kanvas.toDataURL('image/jpeg', 0.8);
            matikanKamera();
            tampilkanPratinjau();
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            pesan.textContent = 'Gambar tidak dapat dibaca. Coba file lain.';
        };
        img.src = url;
        berkas.value = '';
    });

    btnUlang.addEventListener('click', function () {
        input.value = '';
        preview.classList.add('hidden');
        btnUlang.classList.add('hidden');
        btnKamera.classList.remove('hidden');
        btnBerkas.classList.remove('hidden');
        pesan.textContent = 'Nyalakan kamera atau pilih foto dari perangkat.';
    });

    // Kalau form dikembalikan karena ada isian salah, tampilkan lagi foto yang sudah ada
    if (input.value) {
        tampilkanPratinjau();
    }

    // ================= TANDA TANGAN =================
    const ttd = document.getElementById('kanvas-ttd');
    const ttdInput = document.getElementById('tanda_tangan_data');
    const btnHapusTtd = document.getElementById('btn-hapus-ttd');
    const ctx = ttd.getContext('2d');
    let menggambar = false;

    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#111827';

    function posisi(e) {
        const r = ttd.getBoundingClientRect();
        return {
            x: (e.clientX - r.left) * (ttd.width / r.width),
            y: (e.clientY - r.top) * (ttd.height / r.height)
        };
    }

    ttd.addEventListener('pointerdown', function (e) {
        e.preventDefault();
        ttd.setPointerCapture(e.pointerId);
        menggambar = true;
        const p = posisi(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p.x + 0.01, p.y + 0.01);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
    });

    ttd.addEventListener('pointermove', function (e) {
        if (!menggambar) { return; }
        const p = posisi(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
    });

    function selesaiMenggambar() {
        if (!menggambar) { return; }
        menggambar = false;
        ttdInput.value = ttd.toDataURL('image/png');
    }

    ttd.addEventListener('pointerup', selesaiMenggambar);
    ttd.addEventListener('pointercancel', selesaiMenggambar);

    btnHapusTtd.addEventListener('click', function () {
        ctx.clearRect(0, 0, ttd.width, ttd.height);
        ttdInput.value = '';
    });

    // Kalau form dikembalikan karena ada isian salah, gambar ulang tanda tangan yang sudah ada
    if (ttdInput.value) {
        const gambarLama = new Image();
        gambarLama.onload = function () { ctx.drawImage(gambarLama, 0, 0); };
        gambarLama.src = ttdInput.value;
    }
})();
</script>


<script>
(function () {
    // ================= KATEGORI LAYANAN DAN PILIHAN LANJUTAN =================
    const daftarLayanan = @json(\App\Models\Kunjungan::LAYANAN);
    const kategori = document.getElementById('kategori_layanan');
    const blokSub = document.getElementById('blok-sub-layanan');
    const sub = document.getElementById('sub_layanan');
    const subLama = sub.dataset.old || '';

    function perbaruiSub(pakaiLama) {
        const pilihan = daftarLayanan[kategori.value] || [];

        sub.innerHTML = '';
        sub.appendChild(new Option('-- Pilih --', ''));

        if (pilihan.length === 0) {
            // Kategori tanpa pilihan lanjutan: sembunyikan dan jangan ikut dikirim
            blokSub.classList.add('hidden');
            sub.disabled = true;
            sub.required = false;
            return;
        }

        pilihan.forEach(function (nama) {
            const opsi = new Option(nama, nama);
            if (pakaiLama && nama === subLama) { opsi.selected = true; }
            sub.appendChild(opsi);
        });

        blokSub.classList.remove('hidden');
        sub.disabled = false;
        sub.required = true;
    }

    kategori.addEventListener('change', function () { perbaruiSub(false); });
    perbaruiSub(true);
})();
</script>

<script>
(function () {
    // ================= PILIHAN PETUGAS PER LAYANAN =================
    const daftarLayanan = @json(\App\Models\Kunjungan::LAYANAN);
    const kategori = document.getElementById('kategori_layanan');
    const sub = document.getElementById('sub_layanan');
    const blok = document.getElementById('blok-petugas');
    const pilih = document.getElementById('petugas_id');
    const info = document.getElementById('info-petugas');
    const urlDasar = @json(url('/petugas-layanan-tersedia'));
    const lama = pilih.dataset.old || '';
    let permintaan = 0;

    function kosongkan() {
        pilih.innerHTML = '';
        pilih.appendChild(new Option('Otomatis (petugas yang tersedia)', ''));
    }

    async function muat() {
        const nomor = ++permintaan;
        kosongkan();
        info.textContent = '';

        const butuhSub = (daftarLayanan[kategori.value] || []).length > 0;

        // Tampil hanya bila layanan sudah lengkap dipilih (kategori, dan sub bila ada)
        if (!kategori.value || (butuhSub && !sub.value)) {
            blok.classList.add('hidden');
            return;
        }

        blok.classList.remove('hidden');
        info.textContent = 'Memuat petugas...';

        try {
            const alamat = urlDasar
                + '?kategori_layanan=' + encodeURIComponent(kategori.value)
                + '&sub_layanan=' + encodeURIComponent(butuhSub ? sub.value : '');
            const res = await fetch(alamat, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { throw new Error('HTTP ' + res.status); }
            const data = await res.json();
            if (nomor !== permintaan) { return; }

            (data.petugas || []).forEach(function (p) {
                const opsi = new Option(p.nama + (p.tersedia ? '' : ' (sedang melayani)'), p.id);
                opsi.disabled = !p.tersedia;
                if (p.tersedia && String(p.id) === lama) { opsi.selected = true; }
                pilih.appendChild(opsi);
            });

            if (!data.petugas || data.petugas.length === 0) {
                info.textContent = 'Belum ada petugas untuk layanan ini. Kunjungan Anda akan masuk antrean dan dilayani petugas yang tersedia.';
            } else if (!data.ada_tersedia) {
                info.textContent = 'Semua petugas layanan ini sedang melayani. Anda tetap dapat mengirim; kunjungan masuk antrean.';
            } else {
                info.textContent = 'Petugas yang sedang melayani tidak dapat dipilih.';
            }
        } catch (e) {
            if (nomor !== permintaan) { return; }
            info.textContent = 'Daftar petugas tidak dapat dimuat. Anda tetap dapat mengirim; petugas ditentukan otomatis.';
        }
    }

    kategori.addEventListener('change', muat);
    sub.addEventListener('change', muat);
    if (kategori.value) { muat(); }
})();
</script>

<script>
(function () {
    // ================= PETUNJUK NOMOR IDENTITAS =================
    const jenis = document.getElementById('jenis_identitas');
    const nomor = document.getElementById('nomor_identitas');
    const hint = document.getElementById('hint-identitas');

    function perbarui() {
    if (jenis.value === NIK') {
        nomor.setAttribute('maxlength', '16');
        hint.textContent = 'NIK KTP terdiri dari 16 digit angka.';
    } else if (jenis.value === 'NIP') {
        nomor.setAttribute('maxlength', '18');
        hint.textContent = 'NIP terdiri dari 18 digit angka (tanpa spasi).';
    } else if (jenis.value === 'NIB') {
        nomor.setAttribute('maxlength', '13');
        hint.textContent = 'NIB (Nomor Induk Berusaha) terdiri dari 13 digit angka.';
    } else {
        nomor.setAttribute('maxlength', '30');
        hint.textContent = '';
    }
}

    jenis.addEventListener('change', perbarui);
    perbarui();
})();
</script>
@endsection
