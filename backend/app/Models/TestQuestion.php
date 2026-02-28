<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $fillable = [
        'subject',
        'class',
        'question',
        'options',
        'correct_answer',
        'difficulty',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'correct_answer', // Hide correct answer from API responses
    ];
}
