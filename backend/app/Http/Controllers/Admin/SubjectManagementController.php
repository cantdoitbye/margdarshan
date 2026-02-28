<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectManagementController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $query = Subject::ordered();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $subjects = $query->get();

        // Return view for web requests, JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $subjects
            ]);
        }

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:subjects,name',
            'category' => 'nullable|string|max:100',
            'type' => 'required|in:academic,activity',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subject = Subject::create([
            'name' => $request->name,
            'category' => $request->category,
            'type' => $request->type,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully',
            'data' => $subject
        ], 201);
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:subjects,name,' . $id,
            'category' => 'nullable|string|max:100',
            'type' => 'required|in:academic,activity',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subject->update([
            'name' => $request->name,
            'category' => $request->category,
            'type' => $request->type,
            'sort_order' => $request->sort_order ?? $subject->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully',
            'data' => $subject
        ]);
    }

    /**
     * Remove the specified subject.
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully'
        ]);
    }

    /**
     * Toggle subject active status.
     */
    public function toggleActive($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->is_active = !$subject->is_active;
        $subject->save();

        return response()->json([
            'success' => true,
            'message' => 'Subject status updated successfully',
            'data' => $subject
        ]);
    }

    /**
     * Reorder subjects.
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjects' => 'required|array',
            'subjects.*.id' => 'required|exists:subjects,id',
            'subjects.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->subjects as $subjectData) {
            Subject::where('id', $subjectData['id'])->update(['sort_order' => $subjectData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subjects reordered successfully'
        ]);
    }
}
