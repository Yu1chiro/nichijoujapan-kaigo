<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckExamAccess
{
    public function handle(Request $request, Closure $next)
    {
        $deck = $request->route('deck');
        
        if (!$deck) {
            return $next($request);
        }

        $deckId = is_object($deck) ? $deck->id : $deck;

        // Cek Session
        if (!session()->has("exam_access_{$deckId}")) {
            // Jika memaksa masuk URL result tanpa login, tendang ke halaman ujian
            return redirect()->route('shiken.show', $deckId)
                ->with('error', 'Sesi Anda telah berakhir atau belum login.');
        }

        return $next($request);
    }
}