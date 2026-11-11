<?php

namespace App\Support;

/**
 * Buku tamu selalu terbuka: tidak ada lagi kunci, dan tidak ada lagi PIN untuk membukanya.
 *
 * Kelas ini dipertahankan (dengan perilaku sederhana) supaya semua kode yang masih
 * memanggilnya, misalnya saat tamu mengirim formulir, tetap berjalan tanpa error.
 */
class KunciBukuTamu
{
    /** Tidak dipakai lagi, dipertahankan agar kode lama yang menyebutnya tidak error. */
    public const MENIT = 20;

    /** Buku tamu selalu terbuka. */
    public static function terbuka(): bool
    {
        return true;
    }

    /** Tidak melakukan apa pun: tidak ada kunci yang perlu dibuka. */
    public static function buka(): void
    {
        //
    }

    /** Tidak melakukan apa pun: buku tamu tidak dikunci lagi setelah tamu mengirim. */
    public static function kunci(): void
    {
        //
    }
}
