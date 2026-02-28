<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\ClassLevel;
use App\Models\Subject;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = [
            // Class 10 - Mathematics
            [
                'class' => 'Class 10',
                'subject' => 'Mathematics',
                'chapters' => [
                    ['name' => 'Real Numbers', 'sort_order' => 1],
                    ['name' => 'Polynomials', 'sort_order' => 2],
                    ['name' => 'Linear Equations', 'sort_order' => 3],
                    ['name' => 'Quadratic Equations', 'sort_order' => 4],
                    ['name' => 'Arithmetic Progressions', 'sort_order' => 5],
                    ['name' => 'Triangles', 'sort_order' => 6],
                    ['name' => 'Coordinate Geometry', 'sort_order' => 7],
                    ['name' => 'Trigonometry', 'sort_order' => 8],
                    ['name' => 'Circles', 'sort_order' => 9],
                    ['name' => 'Statistics', 'sort_order' => 10],
                ]
            ],
            // Class 10 - Physics
            [
                'class' => 'Class 10',
                'subject' => 'Physics',
                'chapters' => [
                    ['name' => 'Light - Reflection and Refraction', 'sort_order' => 1],
                    ['name' => 'Human Eye and Colourful World', 'sort_order' => 2],
                    ['name' => 'Electricity', 'sort_order' => 3],
                    ['name' => 'Magnetic Effects of Electric Current', 'sort_order' => 4],
                ]
            ],
            // Class 10 - Chemistry
            [
                'class' => 'Class 10',
                'subject' => 'Chemistry',
                'chapters' => [
                    ['name' => 'Chemical Reactions and Equations', 'sort_order' => 1],
                    ['name' => 'Acids, Bases and Salts', 'sort_order' => 2],
                    ['name' => 'Metals and Non-metals', 'sort_order' => 3],
                    ['name' => 'Carbon and its Compounds', 'sort_order' => 4],
                    ['name' => 'Periodic Classification of Elements', 'sort_order' => 5],
                ]
            ],
            // Class 9 - Mathematics
            [
                'class' => 'Class 9',
                'subject' => 'Mathematics',
                'chapters' => [
                    ['name' => 'Number Systems', 'sort_order' => 1],
                    ['name' => 'Polynomials', 'sort_order' => 2],
                    ['name' => 'Coordinate Geometry', 'sort_order' => 3],
                    ['name' => 'Linear Equations in Two Variables', 'sort_order' => 4],
                    ['name' => 'Introduction to Euclid Geometry', 'sort_order' => 5],
                ]
            ],
            // Class 11 - Mathematics
            [
                'class' => 'Class 11',
                'subject' => 'Mathematics',
                'chapters' => [
                    ['name' => 'Sets', 'sort_order' => 1],
                    ['name' => 'Relations and Functions', 'sort_order' => 2],
                    ['name' => 'Trigonometric Functions', 'sort_order' => 3],
                    ['name' => 'Complex Numbers', 'sort_order' => 4],
                    ['name' => 'Linear Inequalities', 'sort_order' => 5],
                    ['name' => 'Permutations and Combinations', 'sort_order' => 6],
                    ['name' => 'Binomial Theorem', 'sort_order' => 7],
                ]
            ],
            // Class 12 - Mathematics
            [
                'class' => 'Class 12',
                'subject' => 'Mathematics',
                'chapters' => [
                    ['name' => 'Relations and Functions', 'sort_order' => 1],
                    ['name' => 'Inverse Trigonometric Functions', 'sort_order' => 2],
                    ['name' => 'Matrices', 'sort_order' => 3],
                    ['name' => 'Determinants', 'sort_order' => 4],
                    ['name' => 'Continuity and Differentiability', 'sort_order' => 5],
                    ['name' => 'Application of Derivatives', 'sort_order' => 6],
                    ['name' => 'Integrals', 'sort_order' => 7],
                    ['name' => 'Application of Integrals', 'sort_order' => 8],
                ]
            ],
        ];

        foreach ($chapters as $data) {
            $class = ClassLevel::where('name', $data['class'])->first();
            $subject = Subject::where('name', $data['subject'])->first();

            if ($class && $subject) {
                foreach ($data['chapters'] as $chapter) {
                    Chapter::create([
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'name' => $chapter['name'],
                        'sort_order' => $chapter['sort_order'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
