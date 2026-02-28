<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_type',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answers',
        'marks',
        'negative_marks',
        'explanation',
        'sort_order',
    ];

    protected $casts = [
        'correct_answers' => 'array',
        'marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    // Accessors
    public function getQuestionTypeDisplayAttribute()
    {
        return match($this->question_type) {
            'single_correct' => 'Single Correct',
            'multiple_correct' => 'Multiple Correct',
            'true_false' => 'True/False',
            default => 'Unknown',
        };
    }

    public function getCorrectAnswersDisplayAttribute()
    {
        if (is_array($this->correct_answers)) {
            return implode(', ', $this->correct_answers);
        }
        return '';
    }

    // Methods
    public function isCorrectAnswer($answer)
    {
        if ($this->question_type === 'single_correct') {
            return in_array($answer, $this->correct_answers);
        } elseif ($this->question_type === 'multiple_correct') {
            // For multiple correct, check if all selected answers are correct
            if (!is_array($answer)) {
                $answer = [$answer];
            }
            return empty(array_diff($answer, $this->correct_answers)) && 
                   empty(array_diff($this->correct_answers, $answer));
        }
        return false;
    }

    public function getOptions()
    {
        $options = [
            'A' => $this->option_a,
            'B' => $this->option_b,
        ];

        if ($this->option_c) {
            $options['C'] = $this->option_c;
        }

        if ($this->option_d) {
            $options['D'] = $this->option_d;
        }

        return $options;
    }
}
