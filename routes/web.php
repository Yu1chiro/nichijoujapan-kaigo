<?php

use App\Http\Controllers\ShikenController;
use App\Models\Deck;
use Illuminate\Support\Facades\Route;

// Halaman Depan
Route::get('/', function () {
    return view('welcome', ['decks' => Deck::all()]);
})->name('home');

// Dashboard User (Client-side based)
Route::get('/dashboard', function () {
    return view('user.dashboard'); 
})->name('dashboard');

// --- ROUTE UJIAN (CBT) ---

// 1. Halaman Utama Ujian (Logika "Lock Screen" ada di Controller)
// Jangan pasang middleware di sini agar user bisa masuk untuk input Access Key
Route::get('/shiken/{deck}', [ShikenController::class, 'show'])->name('shiken.show');

// 2. Proses Verifikasi Access Key (Dipanggil via AJAX)
Route::post('/shiken/{deck}/verify', [ShikenController::class, 'verify'])->name('shiken.verify');

// 3. Halaman Hasil (DIPROTEKSI)
// Wajib pakai middleware agar user tidak bisa tembak URL hasil tanpa ikut ujian/login
Route::middleware(['exam.access'])->group(function () {
    Route::get('/shiken/{deck}/result', [ShikenController::class, 'result'])->name('shiken.result');
});