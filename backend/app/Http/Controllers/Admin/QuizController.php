<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\ClassLevel;
use App\Models\Subject;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with(['class', 'subject', 'chapter']);

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $quizzes = $query->latest()->paginate(15);
        $classes = ClassLevel::active()->ordered()->get();
        $subjects = Subject::active()->ordered()->get();

        return view('admin.quizzes.index', compact('quizzes', 'classes', 'subjects'));
    }

    public function create()
    {
        $classes = ClassLevel::active()->ordered()->get();
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.quizzes.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quiz = Quiz::create([
            'title' => $request->title,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'difficulty_level' => $request->difficulty_level,
            'time_limit' => $request->time_limit,
            'status' => $request->status,
            'total_questions' => 0,
            'created_by' => Auth::guard('admin')->id(),
        ]);

        if ($request->has('continue_to_questions')) {
            return redirect()->route('admin.quizzes.questions.index', $quiz->id)
                ->with('success', 'Quiz created successfully. Now add questions.');
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function edit($id)
    {
        $quiz = Quiz::with(['class', 'subject', 'chapter'])->findOrFail($id);
        $classes = ClassLevel::active()->ordered()->get();
        $subjects = Subject::active()->ordered()->get();
        $chapters = Chapter::active()
            ->where('class_id', $quiz->class_id)
            ->where('subject_id', $quiz->subject_id)
            ->ordered()
            ->get();

        return view('admin.quizzes.edit', compact('quiz', 'classes', 'subjects', 'chapters'));
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quiz->update($request->all());

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function publish($id)
    {
        $quiz = Quiz::findOrFail($id);

        if ($quiz->questions()->count() == 0) {
            return back()->with('error', 'Cannot publish quiz without questions.');
        }

        $quiz->status = 'published';
        $quiz->save();

        return back()->with('success', 'Quiz published successfully.');
    }

    public function unpublish($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->status = 'draft';
        $quiz->save();

        return back()->with('success', 'Quiz unpublished successfully.');
    }

    public function duplicate($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

        DB::transaction(function () use ($quiz) {
            $newQuiz = $quiz->replicate();
            $newQuiz->title = $quiz->title . ' (Copy)';
            $newQuiz->status = 'draft';
            $newQuiz->created_by = Auth::guard('admin')->id();
            $newQuiz->save();

            foreach ($quiz->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->quiz_id = $newQuiz->id;
                $newQuestion->save();
            }

            $newQuiz->updateTotalQuestions();
        });

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz duplicated successfully.');
    }
}
