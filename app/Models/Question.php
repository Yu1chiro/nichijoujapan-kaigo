<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'deck_id', 
        'question_text', 
        'thumbnail_url', 
        'audio_url', // <-- Kolom baru
        'options', 
        'correct_answer', 
        'feedback_text'
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }
}