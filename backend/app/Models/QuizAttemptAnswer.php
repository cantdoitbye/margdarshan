<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        'selected_answers',
        'is_correct',
        'marks_obtained',
        'time_taken',
    ];

    protected $casts = [
        'selected_answers' => 'array',
        'is_correct' => 'boolean',
        'marks_obtained' => 'decimal:2',
    ];

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    // Methods
    public function checkAnswer()
    {
        $question = $this->question;
        
        if (!$this->selected_answers || empty($this->selected_answers)) {
            $this->is_correct = false;
            $this->marks_obtained = 0;
            return;
        }

        $correctAnswers = $question->correct_answers;
        $selectedAnswers = $this->selected_answers;

        // Sort both arrays for comparison
        sort($correctAnswers);
        sort($selectedAnswers);

        // Check if answers match
        $this->is_correct = $correctAnswers === $selectedAnswers;

        // Calculate marks
        if ($this->is_correct) {
            $this->marks_obtained = $question->marks;
        } else {
            $this->marks_obtained = -$question->negative_marks;
        }

        $this->save();
    }
}
