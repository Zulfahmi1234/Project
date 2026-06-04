<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $title = 'Beranda';
        $tagline = 'Belajar Laravel dari Nol';
        return view('pages.home', compact('title', 'tagline'));
    }

    public function about()
    {
        return view('pages.about', [
            'title' => 'Tentang Kami',
            'content' => 'Ini adalah halaman tentang aplikasi kita.'
        ]);
    }

    public function contact()
    {
        return view('pages.contact')->with('title', 'Kontak');
    }

    public function services()
{
    $title = 'Layanan Kami';
    $services = ['Pendaftaran Tanah', 'Pengukuran Lapangan', 'Pemetaan Digital'];
    
    return view('pages.services', compact('title', 'services'));
}

public function article($slug)
{
    // Mengubah tanda '-' menjadi spasi, lalu membuat huruf pertama kapital (ucwords)
    $judul = ucwords(str_replace('-', ' ', $slug));
    
    return view('pages.artikel', compact('judul'));
}
}