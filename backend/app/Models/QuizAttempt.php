<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'time_taken',
        'total_questions',
        'attempted_questions',
        'correct_answers',
        'wrong_answers',
        'skipped_questions',
        'total_marks',
        'obtained_marks',
        'percentage',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByQuiz($query, $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    // Methods
    public function calculateResults()
    {
        $this->attempted_questions = $this->answers()->whereNotNull('selected_answers')->count();
        $this->correct_answers = $this->answers()->where('is_correct', true)->count();
        $this->wrong_answers = $this->answers()->where('is_correct', false)->whereNotNull('selected_answers')->count();
        $this->skipped_questions = $this->total_questions - $this->attempted_questions;
        $this->obtained_marks = $this->answers()->sum('marks_obtained');
        $this->percentage = $this->total_marks > 0 ? ($this->obtained_marks / $this->total_marks) * 100 : 0;
        $this->save();
    }

    public function isExpired()
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $timeLimit = $this->quiz->time_limit * 60; // Convert minutes to seconds
        $elapsed = now()->diffInSeconds($this->started_at);
        
        return $elapsed > $timeLimit;
    }

    public function getRemainingTime()
    {
        if ($this->status !== 'in_progress') {
            return 0;
        }

        $timeLimit = $this->quiz->time_limit * 60; // Convert minutes to seconds
        $elapsed = now()->diffInSeconds($this->started_at);
        $remaining = $timeLimit - $elapsed;
        
        return max(0, $remaining);
    }
}
