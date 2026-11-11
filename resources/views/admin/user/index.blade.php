@extends('layouts.app')

@section('title', 'Kelola Akun | Buku Tamu')

@section('content')
@php
    $aktor = auth()->user();
    $adalahSuperadmin = $aktor->adalahSuperadmin();
    $namaPeran = $semuaRole ?? $roles;
    $jumlahAktif = $users->where('aktif', true)->count();

    // Peran yang memakai PIN buku tamu (pimpinan tidak)
    $punyaPin = ['superadmin', 'admin', 'petugas'];

    $warnaPeran = [
        'superadmin' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'admin'      => 'bg-brand-50 text-brand-800 ring-brand-200',
        'petugas'    => 'bg-sky-50 text-sky-700 ring-sky-200',
        'pimpinan'   => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="ui-title">Kelola akun</h1>
        <p class="mt-1 text-sm text-slate-500">
            @if ($adalahSuperadmin)
                Kelola semua akun pengguna aplikasi.
            @else
                Anda dapat mengelola akun petugas dan pimpinan.
            @endif
        </p>
    </div>
    <button type="button" data-buka="tambah-akun" class="ui-btn ui-btn-primary">+ Tambah akun</button>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
        {{ $errors->first() }}
    </div>
@endif

{{-- Pencarian dan filter --}}
<div class="ui-card mb-4 flex flex-wrap items-center gap-3">
    <input type="search" id="cari-akun" placeholder="Cari nama, email, jabatan, atau NIP"
           class="ui-field !mt-0 min-w-0 flex-1 sm:max-w-sm">
    <select id="filter-peran" class="ui-field !mt-0 !w-44">
        <option value="">Semua peran</option>
        @foreach ($roles as $kunci => $label)
            <option value="{{ $kunci }}">{{ $label }}</option>
        @endforeach
    </select>
    <p class="ml-auto text-sm text-slate-500">{{ $users->count() }} akun &middot; {{ $jumlahAktif }} aktif</p>
</div>

{{-- Daftar akun --}}
<div class="ui-card overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-medium">Akun</th>
                    <th class="px-5 py-3 font-medium">Peran</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="isi-akun" class="divide-y divide-slate-100">
                @foreach ($users as $u)
                    <tr class="align-middle hover:bg-brand-50/40"
                        data-peran="{{ $u->role }}"
                        data-teks="{{ mb_strtolower($u->name . ' ' . $u->email . ' ' . $u->jabatan . ' ' . $u->nip) }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $u->foto_url }}" alt="Foto {{ $u->name }}"
                                     class="h-11 w-11 shrink-0 rounded-full object-cover ring-1 ring-slate-200">
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-slate-900">
                                        {{ $u->name }}
                                        @if ($u->id === $aktor->id)
                                            <span class="ml-1 text-xs font-normal text-slate-400">(akun Anda)</span>
                                        @endif
                                    </p>
                                    <p class="truncate text-slate-500">{{ $u->email }}</p>
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        {{ $u->jabatan ?: 'Jabatan belum diisi' }} &middot; NIP {{ $u->nip ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $warnaPeran[$u->role] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                {{ $namaPeran[$u->role] ?? $u->role }}
                            </span>
                        </td>

                        <td class="px-5 py-3">
                            @if ($u->aktif)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <button type="button" data-buka="ubah-{{ $u->id }}"
                                        class="ui-btn ui-btn-secondary !px-3 !py-1.5">Ubah</button>

                                <button type="button"
                                        onclick="document.getElementById('profil-{{ $u->id }}').showModal()"
                                        class="ui-btn ui-btn-secondary !px-3 !py-1.5">Profil</button>

                                @if ($u->id !== $aktor->id)
                                    <form method="POST" action="{{ route('admin.user.toggle', $u) }}"
                                          data-pesan="{{ $u->aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $u->name }}?"
                                          onsubmit="return confirm(this.dataset.pesan)">
                                        @csrf
                                        <button class="ui-btn !px-3 !py-1.5 {{ $u->aktif ? 'border border-red-200 bg-white text-red-600 hover:bg-red-50' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                            {{ $u->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Jendela: ubah data, reset password, reset PIN --}}
                            <dialog id="ubah-{{ $u->id }}" class="ak-dialog">
                                <div class="ak-kepala">
                                    <div>
                                        <h3>Ubah akun</h3>
                                        <p>{{ $u->name }}</p>
                                    </div>
                                    <button type="button" data-tutup class="ak-x" aria-label="Tutup">&times;</button>
                                </div>

                                <div class="ak-isi">
                                    <form method="POST" action="{{ route('admin.user.update', $u) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="ui-label">Nama</label>
                                            <input type="text" name="name" value="{{ $u->name }}" maxlength="100" required class="ui-field">
                                        </div>
                                        <div>
                                            <label class="ui-label">Email</label>
                                            <input type="email" name="email" value="{{ $u->email }}" maxlength="255" required class="ui-field">
                                        </div>
                                        <div>
                                            <label class="ui-label">Peran</label>
                                            @if ($u->id === $aktor->id)
                                                <input type="hidden" name="role" value="{{ $u->role }}">
                                                <input type="text" value="{{ $namaPeran[$u->role] ?? $u->role }}" disabled class="ui-field">
                                                <p class="mt-1 text-xs text-slate-500">Peran akun sendiri tidak dapat diubah.</p>
                                            @else
                                                <select name="role" class="ui-field">
                                                    @foreach ($roles as $kunci => $label)
                                                        <option value="{{ $kunci }}" @selected($u->role === $kunci)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>

                                        <div class="flex justify-end gap-2">
                                            <button type="button" data-tutup class="ui-btn ui-btn-secondary">Batal</button>
                                            <button class="ui-btn ui-btn-primary">Simpan perubahan</button>
                                        </div>
                                    </form>

                                    <hr class="ak-garis">

                                    <form method="POST" action="{{ route('admin.user.password', $u) }}" class="space-y-3">
                                        @csrf
                                        <h4 class="ak-judul-bagian">Reset password</h4>
                                        <input type="password" name="password" placeholder="Password baru (min. 8 karakter)"
                                               minlength="8" required autocomplete="new-password" class="ui-field">
                                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                                               minlength="8" required autocomplete="new-password" class="ui-field">
                                        <button class="ui-btn ui-btn-secondary">Simpan password</button>
                                    </form>

                                    @if (in_array($u->role, $punyaPin, true))
                                        <hr class="ak-garis">

                                        <form method="POST" action="{{ route('admin.user.reset-pin', $u) }}"
                                              data-pesan="Reset PIN akun {{ $u->name }}?"
                                              onsubmit="return confirm(this.dataset.pesan)"
                                              class="flex flex-wrap items-center justify-between gap-3">
                                            @csrf
                                            <div>
                                                <h4 class="ak-judul-bagian">PIN buku tamu</h4>
                                                <p class="text-xs text-slate-500">Menghapus PIN. Pemilik akun membuat PIN baru sendiri.</p>
                                            </div>
                                            <button class="ui-btn ui-btn-secondary">Reset PIN</button>
                                        </form>
                                    @endif
                                </div>
                            </dialog>

                            @include('admin.user._profil-modal', ['user' => $u])
                        </td>
                    </tr>
                @endforeach

                <tr id="akun-kosong" style="{{ $users->isEmpty() ? '' : 'display:none' }}">
                    <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-500">
                        Tidak ada akun yang cocok.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Jendela: tambah akun --}}
<dialog id="tambah-akun" class="ak-dialog">
    <div class="ak-kepala">
        <div>
            <h3>Tambah akun baru</h3>
            <p>Isi data akun, lalu pengguna dapat langsung masuk.</p>
        </div>
        <button type="button" data-tutup class="ak-x" aria-label="Tutup">&times;</button>
    </div>

    <div class="ak-isi">
        <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_form" value="tambah">

            <div>
                <label class="ui-label">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required class="ui-field">
            </div>
            <div>
                <label class="ui-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" maxlength="255" required class="ui-field">
            </div>
            <div>
                <label class="ui-label">Peran</label>
                <select name="role" required class="ui-field">
                    <option value="" disabled @selected(old('role') === null)>Pilih peran</option>
                    @foreach ($roles as $kunci => $label)
                        <option value="{{ $kunci }}" @selected(old('role') === $kunci)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="ui-label">Password (min. 8 karakter)</label>
                    <input type="password" name="password" minlength="8" required autocomplete="new-password" class="ui-field">
                </div>
                <div>
                    <label class="ui-label">Ulangi password</label>
                    <input type="password" name="password_confirmation" minlength="8" required autocomplete="new-password" class="ui-field">
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" data-tutup class="ui-btn ui-btn-secondary">Batal</button>
                <button class="ui-btn ui-btn-primary">Tambah akun</button>
            </div>
        </form>
    </div>
</dialog>

<style>
    .ak-dialog { width: min(32rem, calc(100vw - 2rem)); max-height: 90vh; padding: 0; border: 0; border-radius: 1rem; background: #fff; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, .45); }
    .ak-dialog::backdrop { background: rgba(15, 23, 42, .55); }
    .ak-kepala { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; }
    .ak-kepala h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e2a78; }
    .ak-kepala p { margin: .15rem 0 0; font-size: .8rem; color: #64748b; }
    .ak-x { border: 0; background: transparent; font-size: 1.6rem; line-height: 1; color: #94a3b8; cursor: pointer; }
    .ak-x:hover { color: #475569; }
    .ak-isi { padding: 1.25rem; max-height: calc(90vh - 5rem); overflow: auto; }
    .ak-garis { margin: 1.25rem 0; border: 0; border-top: 1px solid #e2e8f0; }
    .ak-judul-bagian { margin: 0; font-size: .9rem; font-weight: 600; color: #1e293b; }
</style>

<script>
(function () {
    // Buka dan tutup jendela
    document.querySelectorAll('[data-buka]').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            var jendela = document.getElementById(tombol.getAttribute('data-buka'));
            if (jendela) { jendela.showModal(); }
        });
    });

    document.querySelectorAll('dialog.ak-dialog').forEach(function (jendela) {
        jendela.addEventListener('click', function (e) {
            if (e.target === jendela) { jendela.close(); }
        });
        jendela.querySelectorAll('[data-tutup]').forEach(function (tombol) {
            tombol.addEventListener('click', function () { jendela.close(); });
        });
    });

    // Pencarian dan filter peran
    var cari = document.getElementById('cari-akun');
    var peran = document.getElementById('filter-peran');
    var baris = document.querySelectorAll('#isi-akun tr[data-peran]');
    var kosong = document.getElementById('akun-kosong');

    function saring() {
        var kata = cari.value.trim().toLowerCase();
        var pilihan = peran.value;
        var tampil = 0;

        baris.forEach(function (tr) {
            var cocok = (!pilihan || tr.dataset.peran === pilihan)
                && (!kata || tr.dataset.teks.indexOf(kata) !== -1);
            tr.style.display = cocok ? '' : 'none';
            if (cocok) { tampil++; }
        });

        kosong.style.display = tampil ? 'none' : '';
    }

    cari.addEventListener('input', saring);
    peran.addEventListener('change', saring);

    // Buka lagi jendela tambah akun bila ada isian yang salah
    @if ($errors->any() && old('_form') === 'tambah')
        document.getElementById('tambah-akun').showModal();
    @endif
})();
</script>
@endsection
