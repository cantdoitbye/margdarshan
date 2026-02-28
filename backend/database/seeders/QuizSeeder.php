<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Chapter;
use App\Models\Admin;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        $quizzes = [
            [
                'class' => 'Class 10',
                'subject' => 'Mathematics',
                'chapter' => 'Quadratic Equations',
                'title' => 'Quadratic Equations - Basic Concepts',
                'difficulty_level' => 'easy',
                'time_limit' => 30,
                'status' => 'published',
            ],
            [
                'class' => 'Class 10',
                'subject' => 'Mathematics',
                'chapter' => 'Quadratic Equations',
                'title' => 'Quadratic Equations - Advanced Problems',
                'difficulty_level' => 'hard',
                'time_limit' => 45,
                'status' => 'published',
            ],
            [
                'class' => 'Class 10',
                'subject' => 'Mathematics',
                'chapter' => 'Trigonometry',
                'title' => 'Introduction to Trigonometry',
                'difficulty_level' => 'medium',
                'time_limit' => 40,
                'status' => 'published',
            ],
            [
                'class' => 'Class 10',
                'subject' => 'Physics',
                'chapter' => 'Electricity',
                'title' => 'Ohm\'s Law and Resistance',
                'difficulty_level' => 'easy',
                'time_limit' => 25,
                'status' => 'published',
            ],
            [
                'class' => 'Class 10',
                'subject' => 'Chemistry',
                'chapter' => 'Acids, Bases and Salts',
                'title' => 'pH Scale and Indicators',
                'difficulty_level' => 'medium',
                'time_limit' => 35,
                'status' => 'draft',
            ],
            [
                'class' => 'Class 9',
                'subject' => 'Mathematics',
                'chapter' => 'Polynomials',
                'title' => 'Polynomials - Basics',
                'difficulty_level' => 'easy',
                'time_limit' => 30,
                'status' => 'published',
            ],
            [
                'class' => 'Class 11',
                'subject' => 'Mathematics',
                'chapter' => 'Trigonometric Functions',
                'title' => 'Trigonometric Identities',
                'difficulty_level' => 'hard',
                'time_limit' => 50,
                'status' => 'published',
            ],
            [
                'class' => 'Class 12',
                'subject' => 'Mathematics',
                'chapter' => 'Integrals',
                'title' => 'Integration Techniques',
                'difficulty_level' => 'hard',
                'time_limit' => 60,
                'status' => 'draft',
            ],
        ];

        foreach ($quizzes as $quizData) {
            $chapter = Chapter::whereHas('class', function($q) use ($quizData) {
                $q->where('name', $quizData['class']);
            })
            ->whereHas('subject', function($q) use ($quizData) {
                $q->where('name', $quizData['subject']);
            })
            ->where('name', $quizData['chapter'])
            ->first();

            if ($chapter) {
                Quiz::create([
                    'title' => $quizData['title'],
                    'class_id' => $chapter->class_id,
                    'subject_id' => $chapter->subject_id,
                    'chapter_id' => $chapter->id,
                    'difficulty_level' => $quizData['difficulty_level'],
                    'total_questions' => 0, // Will be updated when questions are added
                    'time_limit' => $quizData['time_limit'],
                    'status' => $quizData['status'],
                    'created_by' => $admin ? $admin->id : null,
                ]);
            }
        }
    }
}
