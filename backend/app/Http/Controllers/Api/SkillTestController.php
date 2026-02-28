<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SkillTest;
use App\Models\TestQuestion;
use App\Models\TestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillTestController extends Controller
{
    public function getQuestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjects' => 'required|array',
            'subjects.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subjects = $request->subjects;
        // Always add Personality test
        if (!in_array('Personality', $subjects)) {
            $subjects[] = 'Personality';
        }

        $allTests = [];

        foreach ($subjects as $subject) {
            // Get 10 random questions for each subject
            $questions = TestQuestion::where('subject', $subject)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->makeHidden(['correct_answer']);

            if ($questions->isEmpty()) {
                continue; // Skip if no questions available
            }

            // Create a skill test record for each subject
            $skillTest = SkillTest::create([
                'user_id' => $request->user()->id,
                'subject' => $subject,
                'total_questions' => $questions->count(),
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            $allTests[] = [
                'test_id' => $skillTest->id,
                'subject' => $subject,
                'questions' => $questions,
            ];
        }

        if (empty($allTests)) {
            return response()->json([
                'success' => false,
                'message' => 'No questions available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tests' => $allTests,
            ]
        ]);
    }

    public function submitTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:skill_tests,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:test_questions,id',
            'answers.*.selected_answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $skillTest = SkillTest::where('id', $request->test_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$skillTest) {
            return response()->json([
                'success' => false,
                'message' => 'Test not found'
            ], 404);
        }

        if ($skillTest->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Test already submitted'
            ], 400);
        }

        $correctAnswers = 0;

        foreach ($request->answers as $answer) {
            $question = TestQuestion::find($answer['question_id']);
            $isCorrect = $question->correct_answer === $answer['selected_answer'];

            if ($isCorrect) {
                $correctAnswers++;
            }

            TestAnswer::create([
                'skill_test_id' => $skillTest->id,
                'test_question_id' => $answer['question_id'],
                'selected_answer' => $answer['selected_answer'],
                'is_correct' => $isCorrect,
            ]);
        }

        $score = ($correctAnswers / $skillTest->total_questions) * 100;
        $passed = $score >= 60; // 60% passing score

        $skillTest->update([
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'status' => $passed ? 'passed' : 'failed',
            'completed_at' => now(),
        ]);

        // Update user status to under_review if passed
        if ($passed) {
            $request->user()->update(['status' => 'under_review']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test submitted successfully',
            'data' => [
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'total_questions' => $skillTest->total_questions,
                'status' => $skillTest->status,
                'passed' => $passed,
            ]
        ]);
    }

    public function getResult(Request $request, $testId)
    {
        $skillTest = SkillTest::where('id', $testId)
            ->where('user_id', $request->user()->id)
            ->with('answers.question')
            ->first();

        if (!$skillTest) {
            return response()->json([
                'success' => false,
                'message' => 'Test not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $skillTest
        ]);
    }
}
