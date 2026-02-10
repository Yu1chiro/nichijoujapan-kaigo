<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deck extends Model
{
    // Pastikan 'access_key' ada di sini
    protected $fillable = ['title', 'category', 'access_key', 'thumbnail_url', 'description', 'timer_per_question'];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}