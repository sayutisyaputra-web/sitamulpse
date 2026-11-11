@extends('layouts.app')

@section('title', 'Ganti Password | Buku Tamu')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-slate-800">Ganti password</h1>

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('profil.password.update') }}"
      class="max-w-md space-y-4 rounded-xl bg-white p-6 shadow">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-xs font-medium text-slate-600">Password lama</label>
        <input type="password" name="password_lama" required autocomplete="current-password"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600">Password baru (min. 8 karakter)</label>
        <input type="password" name="password" minlength="8" required autocomplete="new-password"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600">Ulangi password baru</label>
        <input type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan password</button>
</form>
@endsection