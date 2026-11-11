@extends('layouts.app')

@section('title', 'PIN Buku Tamu')

@section('content')
<div class="mx-auto max-w-md">
    <h1 class="mb-1 text-xl font-semibold text-slate-800">PIN buku tamu</h1>
    <p class="mb-4 text-sm text-slate-500">
        PIN 6 digit dipakai untuk membuka form buku tamu bagi tamu yang datang.
        @if ($sudahAda)
            PIN Anda sudah diatur. Isi form ini untuk menggantinya.
        @else
            PIN Anda belum diatur.
        @endif
    </p>

    <form method="POST" action="{{ route('profil.pin.update') }}" class="space-y-4 rounded-xl bg-white p-6 shadow">
        @csrf
        @method('PUT')

        <div>
            <label for="password_sekarang" class="block text-sm font-medium text-slate-700">Password saat ini</label>
            <input id="password_sekarang" name="password_sekarang" type="password" required autocomplete="current-password"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password_sekarang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="pin" class="block text-sm font-medium text-slate-700">PIN baru (6 digit angka)</label>
            <input id="pin" name="pin" type="password" inputmode="numeric" maxlength="6" required autocomplete="off"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('pin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="pin_confirmation" class="block text-sm font-medium text-slate-700">Ulangi PIN baru</label>
            <input id="pin_confirmation" name="pin_confirmation" type="password" inputmode="numeric" maxlength="6" required autocomplete="off"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" class="w-full rounded-lg bg-blue-600 py-2.5 font-semibold text-white hover:bg-blue-700">
            Simpan PIN
        </button>
    </form>
</div>
@endsection