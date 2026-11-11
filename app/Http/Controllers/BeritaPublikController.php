<?php

namespace App\Http\Controllers;

use App\Models\Berita;

class BeritaPublikController extends Controller
{
    public function show(Berita $berita)
    {
        abort_unless(Berita::terbit()->whereKey($berita->id)->exists(), 404);

        return view('berita.show', compact('berita'));
    }
}