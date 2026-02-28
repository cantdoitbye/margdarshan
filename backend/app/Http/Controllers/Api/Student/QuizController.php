<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Chapter;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Get list of quizzes with filters
     */
    public function index(Request $request)
    {
        $query = Quiz::with(['class', 'subject', 'chapter'])
            ->where('status', 'published');

        // Filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->chapter_id);
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $quizzes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $quizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'class' => [
                        'id' => $quiz->class->id,
                        'name' => $quiz->class->name,
                    ],
                    'subject' => [
                        'id' => $quiz->subject->id,
                        'name' => $quiz->subject->name,
                    ],
                    'chapter' => [
                        'id' => $quiz->chapter->id,
                        'name' => $quiz->chapter->name,
                    ],
                    'difficulty_level' => $quiz->difficulty_level,
                    'difficulty_icon' => $quiz->difficulty_icon,
                    'total_questions' => $quiz->total_questions,
                    'time_limit' => $quiz->time_limit,
                    'created_at' => $quiz->created_at->toDateTimeString(),
                ];
            }),
            'pagination' => [
                'total' => $quizzes->total(),
                'per_page' => $quizzes->perPage(),
                'current_page' => $quizzes->currentPage(),
                'last_page' => $quizzes->lastPage(),
                'from' => $quizzes->firstItem(),
                'to' => $quizzes->lastItem(),
            ],
        ]);
    }

    /**
     * Get quiz details (without questions)
     */
    public function show($id)
    {
        $quiz = Quiz::with(['class', 'subject', 'chapter'])
            ->where('status', 'published')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'class' => [
                    'id' => $quiz->class->id,
                    'name' => $quiz->class->name,
                ],
                'subject' => [
                    'id' => $quiz->subject->id,
                    'name' => $quiz->subject->name,
                ],
                'chapter' => [
                    'id' => $quiz->chapter->id,
                    'name' => $quiz->chapter->name,
                    'description' => $quiz->chapter->description,
                ],
                'difficulty_level' => $quiz->difficulty_level,
                'difficulty_icon' => $quiz->difficulty_icon,
                'total_questions' => $quiz->total_questions,
                'time_limit' => $quiz->time_limit,
                'created_at' => $quiz->created_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Get chapters by class and subject
     */
    public function getChapters(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $chapters = Chapter::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('is_active', true)
            ->ordered()
            ->get(['id', 'name', 'description']);

        return response()->json([
            'success' => true,
            'data' => $chapters,
        ]);
    }

    /**
     * Get quiz statistics
     */
    public function statistics()
    {
        $stats = [
            'total_quizzes' => Quiz::where('status', 'published')->count(),
            'by_difficulty' => [
                'easy' => Quiz::where('status', 'published')->where('difficulty_level', 'easy')->count(),
                'medium' => Quiz::where('status', 'published')->where('difficulty_level', 'medium')->count(),
                'hard' => Quiz::where('status', 'published')->where('difficulty_level', 'hard')->count(),
            ],
            'by_class' => ClassLevel::withCount(['quizzes' => function ($query) {
                $query->where('status', 'published');
            }])->get()->map(function ($class) {
                return [
                    'class_id' => $class->id,
                    'class_name' => $class->name,
                    'quiz_count' => $class->quizzes_count,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
