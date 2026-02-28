<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'class_id',
        'subject_id',
        'chapter_id',
        'difficulty_level',
        'total_questions',
        'time_limit',
        'status',
        'created_by',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'time_limit' => 'integer',
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassLevel::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByChapter($query, $chapterId)
    {
        return $query->where('chapter_id', $chapterId);
    }

    // Accessors
    public function getDifficultyBadgeClassAttribute()
    {
        return match($this->difficulty_level) {
            'easy' => 'badge-success',
            'medium' => 'badge-warning',
            'hard' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function getDifficultyIconAttribute()
    {
        return match($this->difficulty_level) {
            'easy' => '🟢',
            'medium' => '🟠',
            'hard' => '🔴',
            default => '⚪',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'published' => 'badge-success',
            'draft' => 'badge-warning',
            default => 'badge-secondary',
        };
    }

    // Methods
    public function updateTotalQuestions()
    {
        $this->total_questions = $this->questions()->count();
        $this->save();
    }

    /**
     * Get quiz attempts for this quiz
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
