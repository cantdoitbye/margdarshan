<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Imports\QuizQuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class QuizQuestionController extends Controller
{
    public function index($quizId)
    {
        $quiz = Quiz::with(['class', 'subject', 'chapter', 'questions'])->findOrFail($quizId);
        
        return view('admin.quizzes.questions.index', compact('quiz'));
    }

    public function create($quizId)
    {
        $quiz = Quiz::with(['class', 'subject', 'chapter'])->findOrFail($quizId);
        
        return view('admin.quizzes.questions.create', compact('quiz'));
    }

    public function store(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $validator = Validator::make($request->all(), [
            'question_type' => 'required|in:single_correct,multiple_correct,true_false',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answers' => 'required|array',
            'correct_answers.*' => 'in:A,B,C,D',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get next sort order
        $maxSortOrder = $quiz->questions()->max('sort_order') ?? 0;

        QuizQuestion::create([
            'quiz_id' => $quizId,
            'question_type' => $request->question_type,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answers' => $request->correct_answers,
            'marks' => $request->marks,
            'negative_marks' => $request->negative_marks ?? 0,
            'explanation' => $request->explanation,
            'sort_order' => $maxSortOrder + 1,
        ]);

        $quiz->updateTotalQuestions();

        if ($request->has('save_and_add_another')) {
            return redirect()->route('admin.quizzes.questions.create', $quizId)
                ->with('success', 'Question added successfully. Add another question.');
        }

        return redirect()->route('admin.quizzes.questions.index', $quizId)
            ->with('success', 'Question added successfully.');
    }

    public function edit($quizId, $id)
    {
        $quiz = Quiz::with(['class', 'subject', 'chapter'])->findOrFail($quizId);
        $question = QuizQuestion::where('quiz_id', $quizId)->findOrFail($id);
        
        return view('admin.quizzes.questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, $quizId, $id)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::where('quiz_id', $quizId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'question_type' => 'required|in:single_correct,multiple_correct,true_false',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answers' => 'required|array',
            'correct_answers.*' => 'in:A,B,C,D',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $question->update([
            'question_type' => $request->question_type,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answers' => $request->correct_answers,
            'marks' => $request->marks,
            'negative_marks' => $request->negative_marks ?? 0,
            'explanation' => $request->explanation,
        ]);

        return redirect()->route('admin.quizzes.questions.index', $quizId)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy($quizId, $id)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::where('quiz_id', $quizId)->findOrFail($id);
        
        $question->delete();
        $quiz->updateTotalQuestions();

        return redirect()->route('admin.quizzes.questions.index', $quizId)
            ->with('success', 'Question deleted successfully.');
    }

    /**
     * Reorder questions via drag and drop (AJAX)
     */
    public function reorder(Request $request, $quizId)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*.id' => 'required|exists:quiz_questions,id',
            'order.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $quizId) {
                foreach ($request->order as $item) {
                    QuizQuestion::where('id', $item['id'])
                        ->where('quiz_id', $quizId)
                        ->update(['sort_order' => $item['sort_order']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Question order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show import form
     */
    public function showImportForm($quizId)
    {
        $quiz = Quiz::with(['class', 'subject', 'chapter'])->findOrFail($quizId);
        
        return view('admin.quizzes.questions.import', compact('quiz'));
    }

    /**
     * Import questions from Excel/CSV
     */
    public function import(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $import = new QuizQuestionsImport($quizId);
            Excel::import($import, $request->file('file'));

            // Update quiz total questions
            $quiz->updateTotalQuestions();

            $errors = $import->failures();
            
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $failure) {
                    $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                }
                
                return redirect()->route('admin.quizzes.questions.index', $quizId)
                    ->with('warning', 'Questions imported with some errors: ' . implode(' | ', $errorMessages));
            }

            return redirect()->route('admin.quizzes.questions.index', $quizId)
                ->with('success', 'Questions imported successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'question_type',
            'question_text',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answers',
            'marks',
            'negative_marks',
            'explanation'
        ];

        $sampleData = [
            [
                'single_correct',
                'What is 2 + 2?',
                '3',
                '4',
                '5',
                '6',
                'B',
                '1',
                '0.25',
                'Simple addition: 2 + 2 = 4'
            ],
            [
                'multiple_correct',
                'Which are even numbers?',
                '2',
                '3',
                '4',
                '5',
                'A,C',
                '2',
                '0.5',
                'Even numbers are divisible by 2'
            ],
            [
                'true_false',
                'The Earth is flat.',
                'True',
                'False',
                '',
                '',
                'B',
                '1',
                '0',
                'The Earth is spherical'
            ],
        ];

        $filename = 'quiz_questions_template.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($handle, $headers);
        
        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
