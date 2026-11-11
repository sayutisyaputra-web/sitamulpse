@php
    $kodeStatus = $exception->getStatusCode();

    $pesanUmum = match ($kodeStatus) {
        401 => 'Anda perlu masuk terlebih dahulu.',
        405 => 'Cara mengakses halaman ini tidak diizinkan.',
        408 => 'Waktu permintaan habis. Silakan coba lagi.',
        413 => 'Data yang Anda kirim terlalu besar. Perkecil ukurannya lalu coba lagi.',
        419 => 'Sesi Anda sudah berakhir. Muat ulang halaman lalu coba lagi.',
        422 => 'Data yang dikirim tidak dapat diproses. Periksa kembali isian Anda.',
        default => 'Permintaan Anda tidak dapat diproses. Silakan coba lagi.',
    };
@endphp
@extends('errors._dasar')

@section('kode', $kodeStatus)
@section('judul', 'Permintaan tidak dapat diproses')
@section('pesan', $pesanUmum)