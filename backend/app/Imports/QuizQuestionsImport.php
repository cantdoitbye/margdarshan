<?php

namespace App\Imports;

use App\Models\QuizQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class QuizQuestionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $quizId;
    protected $startingSortOrder;
    protected $currentSortOrder;

    public function __construct($quizId)
    {
        $this->quizId = $quizId;
        // Get the max sort_order for this quiz
        $this->startingSortOrder = QuizQuestion::where('quiz_id', $quizId)->max('sort_order') ?? 0;
        $this->currentSortOrder = $this->startingSortOrder;
    }

    public function model(array $row)
    {
        $this->currentSortOrder++;

        // Parse correct answers
        $correctAnswers = $this->parseCorrectAnswers($row['correct_answers']);

        return new QuizQuestion([
            'quiz_id' => $this->quizId,
            'question_type' => strtolower($row['question_type']),
            'question_text' => $row['question_text'],
            'option_a' => $row['option_a'],
            'option_b' => $row['option_b'],
            'option_c' => $row['option_c'] ?? null,
            'option_d' => $row['option_d'] ?? null,
            'correct_answers' => $correctAnswers,
            'marks' => $row['marks'] ?? 1,
            'negative_marks' => $row['negative_marks'] ?? 0,
            'explanation' => $row['explanation'] ?? null,
            'sort_order' => $this->currentSortOrder,
        ]);
    }

    public function rules(): array
    {
        return [
            'question_type' => 'required|in:single_correct,multiple_correct,true_false',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answers' => 'required|string',
            'marks' => 'nullable|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
        ];
    }

    /**
     * Parse correct answers from string to array
     * Accepts: "A", "A,B", "A, B", "A|B", etc.
     */
    private function parseCorrectAnswers($answersString)
    {
        // Remove spaces and convert to uppercase
        $answersString = strtoupper(trim($answersString));
        
        // Split by comma, pipe, or semicolon
        $answers = preg_split('/[,|;]/', $answersString);
        
        // Clean up each answer
        $answers = array_map('trim', $answers);
        
        // Filter out empty values
        $answers = array_filter($answers);
        
        // Ensure valid options (A, B, C, D)
        $answers = array_filter($answers, function($answer) {
            return in_array($answer, ['A', 'B', 'C', 'D']);
        });
        
        return array_values($answers);
    }
}
