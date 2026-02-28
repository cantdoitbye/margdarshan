<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\SkillTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillTestController extends Controller
{
    /**
     * Submit skill test
     */
    public function submit(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'time_taken' => 'required|integer',
        ]);

        $user = Auth::user();

        // Check if user already has a passed test
        $existingTest = SkillTest::where('user_id', $user->id)
            ->where('status', 'passed')
            ->first();

        if ($existingTest) {
            return response()->json([
                'success' => false,
                'message' => 'You have already completed the skill test',
            ], 400);
        }

        // Create skill test record
        $skillTest = SkillTest::create([
            'user_id' => $user->id,
            'answers' => json_encode($request->answers),
            'time_taken' => $request->time_taken,
            'score' => count($request->answers), // Simple scoring for now
            'total_questions' => 5,
            'status' => 'passed', // Auto-pass for now
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Skill test submitted successfully',
            'data' => [
                'test_id' => $skillTest->id,
                'status' => $skillTest->status,
                'score' => $skillTest->score,
                'total_questions' => $skillTest->total_questions,
            ],
        ]);
    }

    /**
     * Get skill test results
     */
    public function results()
    {
        $user = Auth::user();

        $tests = SkillTest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tests,
        ]);
    }
}
