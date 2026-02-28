<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillTest extends Model
{
    protected $fillable = [
        'user_id',
        'answers',
        'time_taken',
        'score',
        'total_questions',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
