@if ($nilai === null)
    <span class="text-xs text-slate-400">Belum dinilai</span>
@else
    <span class="inline-flex items-center gap-1.5">
        <span class="st-tampil" style="--nilai: {{ $nilai }}" aria-label="{{ $nilai }} dari 5 bintang">★★★★★</span>
        <span class="text-xs font-semibold text-slate-700">{{ number_format($nilai, 1, ',', '.') }}</span>
        @if (! empty($jumlah))
            <span class="text-xs text-slate-400">({{ $jumlah }} penilai)</span>
        @endif
    </span>
@endif
