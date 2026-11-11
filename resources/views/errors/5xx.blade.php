@extends('errors._dasar')

@section('kode', $exception->getStatusCode())
@section('judul', 'Sistem sedang bermasalah')
@section('pesan', 'Maaf, ada gangguan di sisi kami. Silakan coba lagi beberapa saat. Jika berulang, hubungi administrator.')