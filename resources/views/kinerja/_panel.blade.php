@php
    $peranPanel = auth()->user()->role;
@endphp

@if (in_array($peranPanel, ['pimpinan', 'admin', 'superadmin'], true))
    @php
        $medaliPanel = ['#f59e0b', '#94a3b8', '#b45309'];

        try {
            $topPanel = collect(app(\App\Services\KinerjaPetugas::class)->bulanan(now()->format('Y-m'), auth()->id()))
                ->filter(fn ($b) => $b['total'] > 0)
                ->take(3)
                ->values();
        } catch (\Throwable $e) {
            $topPanel = collect();
        }
    @endphp

    @include('kinerja._gaya')

    <div class="ui-card mb-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-semibold text-brand-900">Petugas terbaik bulan ini</h2>
                <p class="text-xs text-slate-500">Berdasarkan jumlah tamu yang dilayani</p>
            </div>
            <a href="{{ route('kinerja.index') }}" class="ui-btn ui-btn-secondary !px-3 !py-1.5">
                {{ $peranPanel === 'pimpinan' ? 'Lihat kinerja dan beri bintang' : 'Lihat kinerja petugas' }}
            </a>
        </div>

        @if ($topPanel->isEmpty())
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-center text-sm text-slate-500">
                Belum ada tamu yang dilayani petugas bulan ini.
            </p>
        @else
            <div class="grid gap-3 sm:grid-cols-3">
                @foreach ($topPanel as $i => $b)
                    <div class="flex items-center gap-3 rounded-xl border p-3 {{ $i === 0 ? 'border-amber-300 bg-amber-50/60' : 'border-slate-200' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                              style="background: {{ $medaliPanel[$i] }}">{{ $i + 1 }}</span>
                        <img src="{{ $b['foto'] }}" alt="Foto {{ $b['nama'] }}" class="h-10 w-10 shrink-0 rounded-full object-cover ring-1 ring-slate-200">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $b['nama'] }}</p>
                            <p class="text-xs text-slate-500"><span class="font-semibold text-brand-800">{{ $b['total'] }}</span> tamu dilayani</p>
                            <div class="mt-0.5">
                                @include('kinerja._bintang', ['nilai' => $b['rataBintang'], 'jumlah' => $b['jumlahPenilai']])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif
