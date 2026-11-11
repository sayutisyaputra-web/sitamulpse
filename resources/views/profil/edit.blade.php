@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

<div class="mx-auto w-full max-w-6xl space-y-6">

    {{-- =====================================================
         HEADER HALAMAN
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-indigo-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.1a7.5 7.5 0 0 1 15 0A17.9 17.9 0 0 1 12 21.75 17.9 17.9 0 0 1 4.5 20.1Z"/>
                </svg>
                Pengaturan akun
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Profil Saya
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Kelola informasi pribadi, identitas, dan foto profil Anda
                untuk memastikan data akun selalu benar.
            </p>
        </div>

        <div class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
            <span class="relative flex h-2.5 w-2.5">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-50"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
            </span>
            Akun aktif
        </div>

    </div>


    {{-- =====================================================
         NOTIFIKASI BERHASIL
    ====================================================== --}}
    @if (session('pesan'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"
             role="status">

            <svg class="mt-0.5 h-5 w-5 shrink-0"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m5 12 4 4L19 6"/>
            </svg>

            <div>
                <p class="font-semibold">Perubahan berhasil disimpan</p>
                <p class="mt-1">{{ session('pesan') }}</p>
            </div>

        </div>
    @endif


    {{-- =====================================================
         NOTIFIKASI VALIDASI
    ====================================================== --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
             role="alert">

            <div class="flex items-start gap-3">

                <svg class="mt-0.5 h-5 w-5 shrink-0"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v4m0 4h.01M10.3 3.86 1.82 18.14A2 2 0 0 0 3.54 21h16.92a2 2 0 0 0 1.72-2.86L13.7 3.86a2 2 0 0 0-3.4 0Z"/>
                </svg>

                <div>
                    <p class="font-semibold">
                        Data belum dapat disimpan.
                    </p>

                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>
    @endif


    {{-- =====================================================
         FORM PROFIL
    ====================================================== --}}
    <form
        id="form-profil"
        action="{{ route('profil.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @method('PUT')


        {{-- =================================================
             KARTU IDENTITAS DAN INFORMASI PRIBADI
        ================================================== --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Bagian identitas --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-800 px-5 py-8 sm:px-8 sm:py-10">

                {{-- Hiasan latar --}}
                <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-white/10"></div>
                <div class="pointer-events-none absolute -right-5 -top-10 h-44 w-44 rounded-full bg-white/5"></div>
                <div class="pointer-events-none absolute -bottom-20 left-1/3 h-48 w-48 rounded-full bg-indigo-400/10 blur-2xl"></div>

                <div class="relative flex flex-col items-center gap-5 sm:flex-row sm:items-center sm:gap-6">

                    {{-- Foto profil --}}
                    <div class="shrink-0">
                        <div class="rounded-full border-4 border-white/30 bg-white/10 p-1.5 shadow-xl">

                            <img
                                id="preview-foto"
                                src="{{ $user->foto_url }}"
                                alt="Foto profil {{ $user->name }}"
                                class="h-24 w-24 rounded-full bg-white object-cover sm:h-28 sm:w-28"
                            >

                        </div>
                    </div>


                    {{-- Informasi pengguna --}}
                    <div class="min-w-0 flex-1 text-center sm:text-left">

                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">
                            Profil pengguna
                        </p>

                        <h2 class="mt-2 break-words text-xl font-bold text-white sm:text-2xl">
                            {{ $user->name }}
                        </h2>

                        <p class="mt-2 break-all text-sm text-indigo-100">
                            {{ $user->email }}
                        </p>

                        <div class="mt-4 flex flex-wrap justify-center gap-2 sm:justify-start">

                            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white">
                                <svg class="h-3.5 w-3.5"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M16 18.5a6 6 0 0 0-8 0M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>

                                {{ ucfirst($user->role ?? 'Pengguna') }}
                            </span>

                            @if ($user->jabatan)
                                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-medium text-indigo-100">
                                    {{ $user->jabatan }}
                                </span>
                            @endif

                        </div>

                    </div>

                </div>

            </div>


            {{-- Isi informasi --}}
            <div class="p-5 sm:p-8">

                {{-- Judul bagian --}}
                <div class="mb-7 flex items-start gap-3 border-b border-slate-100 pb-5">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">

                        <svg class="h-5 w-5"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.1a7.5 7.5 0 0 1 15 0A17.9 17.9 0 0 1 12 21.75 17.9 17.9 0 0 1 4.5 20.1Z"/>
                        </svg>

                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Informasi Pribadi
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Pastikan nama dan informasi identitas Anda sudah benar.
                        </p>
                    </div>

                </div>


                {{-- Grid formulir --}}
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 md:grid-cols-2">


                    {{-- Nama lengkap --}}
                    <div>
                        <label for="name"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama lengkap
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            maxlength="255"
                            autocomplete="name"
                            required
                            placeholder="Masukkan nama lengkap"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Email --}}
                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Alamat email
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            maxlength="255"
                            autocomplete="email"
                            required
                            placeholder="nama@email.com"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- NIK --}}
                    <div>
                        <label for="nik"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            NIK
                        </label>

                        <input
                            type="text"
                            id="nik"
                            name="nik"
                            value="{{ old('nik', $user->nik) }}"
                            inputmode="numeric"
                            maxlength="16"
                            pattern="[0-9]{16}"
                            placeholder="Masukkan 16 digit NIK"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Masukkan 16 digit angka jika tersedia.
                        </p>

                        @error('nik')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- NIP --}}
                    <div>
                        <label for="nip"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            NIP
                        </label>

                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            value="{{ old('nip', $user->nip) }}"
                            inputmode="numeric"
                            maxlength="18"
                            pattern="[0-9]{18}"
                            placeholder="Masukkan 18 digit NIP"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Masukkan 18 digit angka jika tersedia.
                        </p>

                        @error('nip')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Jabatan --}}
                    <div class="md:col-span-2">
                        <label for="jabatan"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Jabatan
                        </label>

                        <input
                            type="text"
                            id="jabatan"
                            name="jabatan"
                            value="{{ old('jabatan', $user->jabatan) }}"
                            maxlength="150"
                            placeholder="Masukkan jabatan Anda"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('jabatan')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>


                {{-- =================================================
                     FOTO PROFIL
                ================================================== --}}
                <div class="mt-9">

                    <div class="mb-5 flex items-start gap-3 border-b border-slate-100 pb-5">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">

                            <svg class="h-5 w-5"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="m21 15-5-5L5 21"/>
                            </svg>

                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Foto Profil
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Gunakan foto yang jelas agar akun Anda mudah dikenali.
                            </p>
                        </div>

                    </div>


                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 sm:p-6">

                        <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-center">

                            {{-- Pratinjau foto --}}
                            <div class="shrink-0 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">

                                <img
                                    id="preview-foto-besar"
                                    src="{{ $user->foto_url }}"
                                    alt="Pratinjau foto profil"
                                    class="h-24 w-24 rounded-xl object-cover sm:h-28 sm:w-28"
                                >

                            </div>


                            {{-- Input foto --}}
                            <div class="w-full min-w-0 flex-1">

                                <label for="foto"
                                       class="mb-2 block text-sm font-semibold text-slate-700">
                                    Pilih foto baru
                                </label>

                                <input
                                    type="file"
                                    id="foto"
                                    name="foto"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    class="block w-full cursor-pointer rounded-xl border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:cursor-pointer file:border-0 file:bg-indigo-50 file:px-4 file:py-3 file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                >

                                <p class="mt-3 text-xs leading-5 text-slate-500">
                                    Format JPG, JPEG, PNG, atau WEBP. Ukuran maksimal 2 MB.
                                </p>

                                <p id="nama-file-foto"
                                   class="mt-2 break-all text-xs font-medium text-indigo-700">
                                </p>

                                @error('foto')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     TOMBOL SIMPAN
                ================================================== --}}
                <div class="mt-8 border-t border-slate-200 pt-6">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-xs leading-5 text-slate-500">
                            Pastikan semua informasi sudah benar sebelum menyimpan.
                        </p>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">

                            <a
                                href="{{ route('profil.edit') }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                id="tombol-simpan"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                            >

                                <svg class="h-5 w-5"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="m5 12 4 4L19 6"/>
                                </svg>

                                <span>Simpan Perubahan</span>

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </form>


    {{-- =====================================================
         KARTU KEAMANAN AKUN
    ====================================================== --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-7">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">

                    <svg class="h-6 w-6"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <rect x="5" y="10" width="14" height="11" rx="2"/>
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8 10V7a4 4 0 1 1 8 0v3"/>
                        <path stroke-linecap="round"
                              d="M12 14v3"/>
                    </svg>

                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        Keamanan Akun
                    </h3>

                    <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                        Lindungi akun Anda dengan mengganti password secara berkala.
                        Gunakan password yang kuat dan jangan membagikannya kepada orang lain.
                    </p>
                </div>

            </div>

            <a
                href="{{ route('profil.password') }}"
                class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100"
            >

                <svg class="h-4 w-4"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.03 5.91c-.56-.1-1.16.03-1.56.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.82c0-.6.24-1.17.66-1.59l6.5-6.5c.4-.4.53-1 .43-1.56a6 6 0 1 1 11.91-1.03Z"/>
                </svg>

                Ganti Password

            </a>

        </div>

    </section>

</div>

@endsection


{{-- =========================================================
     PREVIEW FOTO
========================================================= --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputFoto = document.getElementById('foto');
    const previewFoto = document.getElementById('preview-foto');
    const previewFotoBesar = document.getElementById('preview-foto-besar');
    const namaFileFoto = document.getElementById('nama-file-foto');

    if (!inputFoto) {
        return;
    }

    let urlSebelumnya = null;

    inputFoto.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            if (namaFileFoto) {
                namaFileFoto.textContent = '';
            }

            return;
        }

        const tipeDiizinkan = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!tipeDiizinkan.includes(file.type)) {
            alert('Format foto harus JPG, JPEG, PNG, atau WEBP.');
            this.value = '';

            if (namaFileFoto) {
                namaFileFoto.textContent = '';
            }

            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            this.value = '';

            if (namaFileFoto) {
                namaFileFoto.textContent = '';
            }

            return;
        }

        if (urlSebelumnya) {
            URL.revokeObjectURL(urlSebelumnya);
        }

        urlSebelumnya = URL.createObjectURL(file);

        if (previewFoto) {
            previewFoto.src = urlSebelumnya;
        }

        if (previewFotoBesar) {
            previewFotoBesar.src = urlSebelumnya;
        }

        if (namaFileFoto) {
            namaFileFoto.textContent = 'Foto dipilih: ' + file.name;
        }
    });
});
</script>
@endpush