<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class ClassSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all classes and subjects
        $classes = ClassLevel::all();
        $subjects = Subject::where('type', 'academic')->get();

        // Define subject mappings for different class levels
        $mappings = [
            // LKG, UKG - Basic subjects
            'early_childhood' => [
                'classes' => ['LKG', 'UKG'],
                'subjects' => ['English', 'Hindi', 'Mathematics', 'General Science', 'Environmental Studies']
            ],
            
            // Class 1-5 - Primary education
            'primary' => [
                'classes' => ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5'],
                'subjects' => ['English', 'Hindi', 'Mathematics', 'General Science', 'Environmental Studies', 'Social Studies']
            ],
            
            // Class 6-8 - Middle school
            'middle' => [
                'classes' => ['Class 6', 'Class 7', 'Class 8'],
                'subjects' => ['English', 'Hindi', 'Sanskrit', 'Mathematics', 'General Science', 'Social Studies', 'Computer Science']
            ],
            
            // Class 9-10 - Secondary
            'secondary' => [
                'classes' => ['Class 9', 'Class 10'],
                'subjects' => ['English', 'Hindi', 'Sanskrit', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'General Science',
                    'History', 'Geography', 'Political Science', 'Economics', 'Computer Science', 'Informatics Practices']
            ],
            
            // Class 11-12 - Senior Secondary (All subjects available)
            'senior_secondary' => [
                'classes' => ['Class 11', 'Class 12'],
                'subjects' => ['English', 'Hindi', 'Sanskrit', 'Mathematics', 'Applied Mathematics', 'Physics', 'Chemistry', 'Biology',
                    'History', 'Geography', 'Political Science', 'Economics', 'Accountancy', 'Business Studies',
                    'Computer Science', 'Informatics Practices', 'Physical Education']
            ],
        ];

        // Insert mappings
        foreach ($mappings as $level => $data) {
            foreach ($data['classes'] as $className) {
                $class = $classes->firstWhere('name', $className);
                
                if ($class) {
                    foreach ($data['subjects'] as $subjectName) {
                        $subject = $subjects->firstWhere('name', $subjectName);
                        
                        if ($subject) {
                            DB::table('class_subject')->insert([
                                'class_id' => $class->id,
                                'subject_id' => $subject->id,
                                'is_active' => true,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
