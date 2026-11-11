@extends('layouts.app')

@section('content')
<div style="max-width:820px">
    <h1>Pengaturan Beranda</h1>

    @if (session('status'))
        <p style="color:#15803d">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div style="color:#b91c1c">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('beranda.pengaturan.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <p><label>Judul utama (bagian atas)<br>
            <input type="text" name="judul_utama" value="{{ old('judul_utama', $p->judul_utama) }}" style="width:100%"></label></p>

        <p><label>Subjudul<br>
            <input type="text" name="subjudul" value="{{ old('subjudul', $p->subjudul) }}" style="width:100%"></label></p>

        <p><label>Judul sambutan<br>
            <input type="text" name="judul_sambutan" value="{{ old('judul_sambutan', $p->judul_sambutan) }}" style="width:100%"></label></p>

        <p><label>Teks sambutan<br>
            <textarea name="teks_sambutan" rows="4" style="width:100%">{{ old('teks_sambutan', $p->teks_sambutan) }}</textarea></label></p>

        <p><label>Layanan Kami (satu layanan per baris)<br>
            <textarea name="daftar_layanan" rows="8" style="width:100%">{{ old('daftar_layanan', $p->daftar_layanan) }}</textarea></label></p>

        <p><label>Langkah Singkat (satu langkah per baris)<br>
            <textarea name="daftar_langkah" rows="4" style="width:100%">{{ old('daftar_langkah', $p->daftar_langkah) }}</textarea></label></p>

        <p><label>Teks privasi<br>
            <input type="text" name="teks_privasi" value="{{ old('teks_privasi', $p->teks_privasi) }}" style="width:100%"></label></p>

        <p><label>Teks footer<br>
            <input type="text" name="teks_footer" value="{{ old('teks_footer', $p->teks_footer) }}" style="width:100%"></label></p>

        <hr>

        <p><strong>Logo</strong> (JPG/PNG/WEBP, maks. 2 MB; PNG transparan disarankan)<br>
            @if ($p->logoUrl())
                <img src="{{ $p->logoUrl() }}" alt="Logo" style="max-height:70px"><br>
                <label><input type="checkbox" name="hapus_logo" value="1"> Hapus logo (kembali ke bawaan)</label><br>
            @endif
            <input type="file" name="logo" accept="image/*">
        </p>

        <p><strong>Banner</strong> (JPG/PNG/WEBP, maks. 4 MB)<br>
            @if ($p->bannerUrl())
                <img src="{{ $p->bannerUrl() }}" alt="Banner" style="max-width:100%;max-height:140px"><br>
                <label><input type="checkbox" name="hapus_banner" value="1"> Hapus banner (kembali ke bawaan)</label><br>
            @endif
            <input type="file" name="banner" accept="image/*">
        </p>

        <button type="submit">Simpan</button>
    </form>
</div>
@endsection