<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;

class ShikenController extends Controller
{
    public function show(Deck $deck)
    {
        // Cek apakah user sudah punya akses sesi untuk deck ini
        $hasAccess = session()->has("exam_access_{$deck->id}");

        if ($hasAccess) {
            // Jika sudah ada akses, load soal
            $deck->load('questions');
            $questions = $deck->questions;
        } else {
            // Jika belum ada akses, kirim list kosong (KEMANAN)
            $questions = collect([]);
        }

        return view('shiken.play', compact('deck', 'questions', 'hasAccess'));
    }

    public function verify(Request $request, Deck $deck)
    {
        // Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string'
        ]);

        // Cek Access Key (Case Sensitive/Insensitive tergantung kebutuhan, ini exact match)
        if ($request->key === $deck->access_key) {
            // 1. Simpan Sesi Akses
            session(["exam_access_{$deck->id}" => true]);
            
            // 2. Simpan Nama Peserta di Session (opsional, untuk sertifikat/hasil)
            session(['exam_user_name' => $request->name]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('shiken.show', $deck->id) // Reload halaman untuk load soal
            ]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'Access Key Salah! Silakan hubungi admin.'
        ], 403);
    }

    public function result(Deck $deck)
    {
        // Pastikan middleware 'exam.access' terpasang di route ini di web.php
        return view('shiken.result', compact('deck'));
    }
}