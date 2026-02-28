<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index()
    {
        $classes = ClassLevel::ordered()->get();

        // Return view for web requests, JSON for API requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
        }

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:classes,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $class = ClassLevel::create([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class created successfully',
            'data' => $class
        ], 201);
    }

    /**
     * Update the specified class.
     */
    public function update(Request $request, $id)
    {
        $class = ClassLevel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:classes,name,' . $id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $class->update([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? $class->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class updated successfully',
            'data' => $class
        ]);
    }

    /**
     * Remove the specified class.
     */
    public function destroy($id)
    {
        $class = ClassLevel::findOrFail($id);
        $class->delete();

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully'
        ]);
    }

    /**
     * Toggle class active status.
     */
    public function toggleActive($id)
    {
        $class = ClassLevel::findOrFail($id);
        $class->is_active = !$class->is_active;
        $class->save();

        return response()->json([
            'success' => true,
            'message' => 'Class status updated successfully',
            'data' => $class
        ]);
    }

    /**
     * Reorder classes.
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classes' => 'required|array',
            'classes.*.id' => 'required|exists:classes,id',
            'classes.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->classes as $classData) {
            ClassLevel::where('id', $classData['id'])->update(['sort_order' => $classData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Classes reordered successfully'
        ]);
    }
}
