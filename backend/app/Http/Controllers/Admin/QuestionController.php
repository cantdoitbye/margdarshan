<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions
     */
    public function index()
    {
        $questions = TestQuestion::orderBy('id', 'desc')->get();
        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string',
            'class' => 'required|integer|min:1|max:12',
            'question_type' => 'required|in:multiple_choice,true_false',
            'difficulty' => 'required|in:easy,medium,hard',
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'correct_answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = TestQuestion::create([
            'subject' => $request->subject,
            'class' => $request->class,
            'question_type' => $request->question_type,
            'difficulty' => $request->difficulty,
            'question_text' => $request->question_text,
            'options' => json_encode($request->options),
            'correct_answer' => $request->correct_answer,
        ]);

        return response()->json([
            'message' => 'Question created successfully',
            'question' => $question
        ], 201);
    }

    /**
     * Display the specified question
     */
    public function show($id)
    {
        $question = TestQuestion::findOrFail($id);
        
        return response()->json([
            'id' => $question->id,
            'subject' => $question->subject,
            'class' => $question->class,
            'question_type' => $question->question_type,
            'difficulty' => $question->difficulty,
            'question_text' => $question->question_text,
            'options' => json_decode($question->options),
            'correct_answer' => $question->correct_answer,
        ]);
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, $id)
    {
        $question = TestQuestion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'subject' => 'sometimes|required|string',
            'class' => 'sometimes|required|integer|min:1|max:12',
            'question_type' => 'sometimes|required|in:multiple_choice,true_false',
            'difficulty' => 'sometimes|required|in:easy,medium,hard',
            'question_text' => 'sometimes|required|string',
            'options' => 'sometimes|required|array|min:2',
            'correct_answer' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['subject', 'class', 'question_type', 'difficulty', 'question_text', 'correct_answer']);
        
        if ($request->has('options')) {
            $data['options'] = json_encode($request->options);
        }

        $question->update($data);

        return response()->json([
            'message' => 'Question updated successfully',
            'question' => $question
        ]);
    }

    /**
     * Remove the specified question
     */
    public function destroy($id)
    {
        $question = TestQuestion::findOrFail($id);
        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully'
        ]);
    }
}
