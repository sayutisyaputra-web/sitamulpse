@extends('layouts.app')

@section('title', 'Catatan Aktivitas | Buku Tamu')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-slate-800">Catatan aktivitas</h1>

<form method="GET" action="{{ route('admin.log.index') }}"
      class="mb-4 grid gap-3 rounded-xl bg-white p-4 shadow sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label class="block text-xs font-medium text-slate-600">Aktivitas</label>
        <select name="aksi" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua</option>
            @foreach ($aksis as $kunci => $label)
                <option value="{{ $kunci }}" @selected(request('aksi') === $kunci)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600">Dari tanggal</label>
        <input type="date" name="dari" value="{{ request('dari') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600">Sampai tanggal</label>
        <input type="date" name="sampai" value="{{ request('sampai') }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>
    <div class="flex items-end gap-2">
        <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Terapkan</button>
        <a href="{{ route('admin.log.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
    </div>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Waktu</th>
                <th class="px-4 py-3">Pengguna</th>
                <th class="px-4 py-3">Aktivitas</th>
                <th class="px-4 py-3">Keterangan</th>
                <th class="px-4 py-3">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($logs as $log)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-4 py-3">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-4 py-3">{{ $log->user->name ?? '(akun dihapus)' }}</td>
                    <td class="px-4 py-3">{{ $aksis[$log->aksi] ?? $log->aksi }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $log->keterangan ?: '-' }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $log->ip }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada aktivitas tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection