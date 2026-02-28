<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuizAttemptController extends Controller
{
    /**
     * Start a new quiz attempt
     */
    public function start(Request $request, $quizId)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $quiz = Quiz::with('questions')->where('status', 'published')->findOrFail($quizId);

        // Check if student has an in-progress attempt
        $existingAttempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $request->student_id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            // Check if expired
            if ($existingAttempt->isExpired()) {
                $existingAttempt->status = 'abandoned';
                $existingAttempt->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an in-progress attempt for this quiz',
                    'data' => [
                        'attempt_id' => $existingAttempt->id,
                        'remaining_time' => $existingAttempt->getRemainingTime(),
                    ],
                ], 400);
            }
        }

        // Calculate total marks
        $totalMarks = $quiz->questions->sum('marks');

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => $request->student_id,
            'started_at' => now(),
            'total_questions' => $quiz->total_questions,
            'total_marks' => $totalMarks,
            'status' => 'in_progress',
        ]);

        // Create answer records for all questions
        foreach ($quiz->questions as $question) {
            QuizAttemptAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $question->id,
            ]);
        }

        // Get questions for the attempt
        $questions = $quiz->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_type' => $question->question_type,
                'question_text' => $question->question_text,
                'options' => $question->getOptions(),
                'marks' => $question->marks,
                'negative_marks' => $question->negative_marks,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz attempt started successfully',
            'data' => [
                'attempt_id' => $attempt->id,
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'total_questions' => $quiz->total_questions,
                    'time_limit' => $quiz->time_limit,
                    'total_marks' => $totalMarks,
                ],
                'questions' => $questions,
                'started_at' => $attempt->started_at->toDateTimeString(),
                'time_limit_seconds' => $quiz->time_limit * 60,
            ],
        ]);
    }

    /**
     * Save answer for a question
     */
    public function saveAnswer(Request $request, $attemptId)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:quiz_questions,id',
            'selected_answers' => 'required|array',
            'selected_answers.*' => 'in:A,B,C,D',
            'time_taken' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attempt = QuizAttempt::findOrFail($attemptId);

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'This attempt has already been submitted or abandoned',
            ], 400);
        }

        // Check if time expired
        if ($attempt->isExpired()) {
            $attempt->status = 'abandoned';
            $attempt->save();

            return response()->json([
                'success' => false,
                'message' => 'Time limit exceeded. Quiz has been abandoned.',
            ], 400);
        }

        // Find or create answer record
        $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attemptId)
            ->where('quiz_question_id', $request->question_id)
            ->firstOrFail();

        // Update answer
        $answer->selected_answers = $request->selected_answers;
        $answer->time_taken = $request->time_taken;
        $answer->save();

        // Check if answer is correct
        $answer->checkAnswer();

        return response()->json([
            'success' => true,
            'message' => 'Answer saved successfully',
            'data' => [
                'question_id' => $request->question_id,
                'saved' => true,
            ],
        ]);
    }

    /**
     * Submit quiz attempt
     */
    public function submit(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::with(['quiz', 'answers.question'])->findOrFail($attemptId);

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'This attempt has already been submitted or abandoned',
            ], 400);
        }

        DB::transaction(function () use ($attempt) {
            // Calculate time taken
            $attempt->time_taken = now()->diffInSeconds($attempt->started_at);
            $attempt->submitted_at = now();
            $attempt->status = 'submitted';

            // Check all answers
            foreach ($attempt->answers as $answer) {
                $answer->checkAnswer();
            }

            // Calculate results
            $attempt->calculateResults();
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz submitted successfully',
            'data' => [
                'attempt_id' => $attempt->id,
                'total_questions' => $attempt->total_questions,
                'attempted_questions' => $attempt->attempted_questions,
                'correct_answers' => $attempt->correct_answers,
                'wrong_answers' => $attempt->wrong_answers,
                'skipped_questions' => $attempt->skipped_questions,
                'total_marks' => $attempt->total_marks,
                'obtained_marks' => $attempt->obtained_marks,
                'percentage' => $attempt->percentage,
                'time_taken' => $attempt->time_taken,
            ],
        ]);
    }

    /**
     * Get quiz attempt result
     */
    public function result($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.class', 'quiz.subject', 'quiz.chapter', 'student', 'answers.question'])
            ->findOrFail($attemptId);

        if ($attempt->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Quiz has not been submitted yet',
            ], 400);
        }

        $answers = $attempt->answers->map(function ($answer) {
            return [
                'question_id' => $answer->quiz_question_id,
                'question_text' => $answer->question->question_text,
                'question_type' => $answer->question->question_type,
                'options' => $answer->question->getOptions(),
                'correct_answers' => $answer->question->correct_answers,
                'selected_answers' => $answer->selected_answers,
                'is_correct' => $answer->is_correct,
                'marks' => $answer->question->marks,
                'marks_obtained' => $answer->marks_obtained,
                'explanation' => $answer->question->explanation,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'attempt_id' => $attempt->id,
                'quiz' => [
                    'id' => $attempt->quiz->id,
                    'title' => $attempt->quiz->title,
                    'class' => $attempt->quiz->class->name,
                    'subject' => $attempt->quiz->subject->name,
                    'chapter' => $attempt->quiz->chapter->name,
                    'difficulty_level' => $attempt->quiz->difficulty_level,
                ],
                'student' => [
                    'id' => $attempt->student->id,
                    'name' => $attempt->student->name,
                ],
                'summary' => [
                    'total_questions' => $attempt->total_questions,
                    'attempted_questions' => $attempt->attempted_questions,
                    'correct_answers' => $attempt->correct_answers,
                    'wrong_answers' => $attempt->wrong_answers,
                    'skipped_questions' => $attempt->skipped_questions,
                    'total_marks' => $attempt->total_marks,
                    'obtained_marks' => $attempt->obtained_marks,
                    'percentage' => $attempt->percentage,
                    'time_taken' => $attempt->time_taken,
                    'time_taken_formatted' => gmdate('H:i:s', $attempt->time_taken),
                ],
                'answers' => $answers,
                'submitted_at' => $attempt->submitted_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Get student's quiz history
     */
    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attempts = QuizAttempt::with(['quiz.class', 'quiz.subject', 'quiz.chapter'])
            ->where('student_id', $request->student_id)
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $attempts->map(function ($attempt) {
                return [
                    'attempt_id' => $attempt->id,
                    'quiz' => [
                        'id' => $attempt->quiz->id,
                        'title' => $attempt->quiz->title,
                        'class' => $attempt->quiz->class->name,
                        'subject' => $attempt->quiz->subject->name,
                        'chapter' => $attempt->quiz->chapter->name,
                        'difficulty_level' => $attempt->quiz->difficulty_level,
                    ],
                    'score' => [
                        'obtained_marks' => $attempt->obtained_marks,
                        'total_marks' => $attempt->total_marks,
                        'percentage' => $attempt->percentage,
                        'correct_answers' => $attempt->correct_answers,
                        'total_questions' => $attempt->total_questions,
                    ],
                    'submitted_at' => $attempt->submitted_at->toDateTimeString(),
                ];
            }),
            'pagination' => [
                'total' => $attempts->total(),
                'per_page' => $attempts->perPage(),
                'current_page' => $attempts->currentPage(),
                'last_page' => $attempts->lastPage(),
            ],
        ]);
    }

    /**
     * Get current attempt status
     */
    public function status($attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);

        return response()->json([
            'success' => true,
            'data' => [
                'attempt_id' => $attempt->id,
                'status' => $attempt->status,
                'started_at' => $attempt->started_at->toDateTimeString(),
                'remaining_time' => $attempt->getRemainingTime(),
                'is_expired' => $attempt->isExpired(),
                'attempted_questions' => $attempt->answers()->whereNotNull('selected_answers')->count(),
                'total_questions' => $attempt->total_questions,
            ],
        ]);
    }
}
