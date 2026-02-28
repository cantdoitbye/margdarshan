<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Get all students for authenticated customer
     */
    public function index(Request $request)
    {
        $students = $request->user()->students()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Create a new student
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'class' => 'required|integer|min:1|max:12',
            'subjects' => 'required|array|min:1',
            'board' => 'nullable|string|max:100',
            'tuition_type' => 'nullable|in:academic,activity,both',
            'learning_goal' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = $request->user()->students()->create([
            'name' => $request->name,
            'class' => $request->class,
            'subjects' => $request->subjects,
            'board' => $request->board,
            'tuition_type' => $request->tuition_type ?? 'academic',
            'learning_goal' => $request->learning_goal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ], 201);
    }

    /**
     * Get a single student
     */
    public function show(Request $request, $id)
    {
        $student = $request->user()->students()->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Update a student
     */
    public function update(Request $request, $id)
    {
        $student = $request->user()->students()->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'class' => 'required|integer|min:1|max:12',
            'subjects' => 'required|array|min:1',
            'board' => 'nullable|string|max:100',
            'tuition_type' => 'nullable|in:academic,activity,both',
            'learning_goal' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update([
            'name' => $request->name,
            'class' => $request->class,
            'subjects' => $request->subjects,
            'board' => $request->board,
            'tuition_type' => $request->tuition_type ?? 'academic',
            'learning_goal' => $request->learning_goal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    /**
     * Delete a student
     */
    public function destroy(Request $request, $id)
    {
        $student = $request->user()->students()->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }
}
