<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Get all active boards.
     */
    public function getBoards()
    {
        $boards = Board::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $boards
        ]);
    }

    /**
     * Get all active classes.
     */
    public function getClasses()
    {
        $classes = ClassLevel::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Get subjects by type (academic/activity).
     */
    public function getSubjects(Request $request)
    {
        $query = Subject::active()->ordered();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $subjects = $query->get();

        return response()->json([
            'success' => true,
            'data' => $subjects
        ]);
    }

    /**
     * Get available subjects for a specific class.
     */
    public function getClassSubjects($classId)
    {
        $class = ClassLevel::findOrFail($classId);
        $subjects = $class->subjects()->get();

        return response()->json([
            'success' => true,
            'data' => $subjects
        ]);
    }
}
