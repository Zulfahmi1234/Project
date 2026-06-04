<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController; // Pastikan ini di-import!

// Menghubungkan URL ke Controller dan memberi nama (Named Route)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::get('/layanan', [PageController::class, 'services'])->name('services');
Route::get('/artikel/{slug}', [PageController::class, 'article'])->name('artikel.show');