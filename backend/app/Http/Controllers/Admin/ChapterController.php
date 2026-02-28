<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $query = Chapter::with(['class', 'subject']);

        // Filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $chapters = $query->ordered()->paginate(15);
        $classes = ClassLevel::active()->ordered()->get();
        $subjects = Subject::active()->ordered()->get();

        return view('admin.chapters.index', compact('chapters', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Chapter::create($request->all());

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter created successfully.');
    }

    public function update(Request $request, $id)
    {
        $chapter = Chapter::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $chapter->update($request->all());

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter updated successfully.');
    }

    public function destroy($id)
    {
        $chapter = Chapter::findOrFail($id);

        // Check if chapter has quizzes
        if ($chapter->quizzes()->count() > 0) {
            return back()->with('error', 'Cannot delete chapter with existing quizzes.');
        }

        $chapter->delete();

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter deleted successfully.');
    }

    public function toggleActive($id)
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->is_active = !$chapter->is_active;
        $chapter->save();

        return back()->with('success', 'Chapter status updated successfully.');
    }

    public function getByClassSubject(Request $request)
    {
        $chapters = Chapter::active()
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->ordered()
            ->get(['id', 'name']);

        return response()->json($chapters);
    }
}
