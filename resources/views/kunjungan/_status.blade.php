@php
    $gaya = [
        'menunggu' => ['bg-amber-50 text-amber-700 ring-amber-200', 'bg-amber-500'],
        'diterima' => ['bg-sky-50 text-sky-700 ring-sky-200', 'bg-sky-500'],
        'selesai'  => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'bg-emerald-500'],
        'ditolak'  => ['bg-red-50 text-red-700 ring-red-200', 'bg-red-500'],
    ];

    [$kelasLencana, $kelasTitik] = $gaya[$status] ?? ['bg-slate-100 text-slate-700 ring-slate-200', 'bg-slate-400'];
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $kelasLencana }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $kelasTitik }}"></span>
    {{ ucfirst($status) }}
</span>