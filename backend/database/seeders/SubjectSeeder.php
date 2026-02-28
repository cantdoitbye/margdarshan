<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // Mathematics
            ['name' => 'Mathematics', 'category' => 'Core', 'type' => 'academic', 'sort_order' => 1],
            ['name' => 'Applied Mathematics', 'category' => 'Core', 'type' => 'academic', 'sort_order' => 2],
            
            // Sciences
            ['name' => 'Physics', 'category' => 'Science', 'type' => 'academic', 'sort_order' => 3],
            ['name' => 'Chemistry', 'category' => 'Science', 'type' => 'academic', 'sort_order' => 4],
            ['name' => 'Biology', 'category' => 'Science', 'type' => 'academic', 'sort_order' => 5],
            ['name' => 'General Science', 'category' => 'Science', 'type' => 'academic', 'sort_order' => 6],
            
            // Languages
            ['name' => 'English', 'category' => 'Language', 'type' => 'academic', 'sort_order' => 7],
            ['name' => 'Hindi', 'category' => 'Language', 'type' => 'academic', 'sort_order' => 8],
            ['name' => 'Sanskrit', 'category' => 'Language', 'type' => 'academic', 'sort_order' => 9],
            
            // Social Sciences
            ['name' => 'History', 'category' => 'Social Science', 'type' => 'academic', 'sort_order' => 10],
            ['name' => 'Geography', 'category' => 'Social Science', 'type' => 'academic', 'sort_order' => 11],
            ['name' => 'Political Science', 'category' => 'Social Science', 'type' => 'academic', 'sort_order' => 12],
            ['name' => 'Economics', 'category' => 'Social Science', 'type' => 'academic', 'sort_order' => 13],
            ['name' => 'Social Studies', 'category' => 'Social Science', 'type' => 'academic', 'sort_order' => 14],
            
            // Commerce
            ['name' => 'Accountancy', 'category' => 'Commerce', 'type' => 'academic', 'sort_order' => 15],
            ['name' => 'Business Studies', 'category' => 'Commerce', 'type' => 'academic', 'sort_order' => 16],
            
            // Computer
            ['name' => 'Computer Science', 'category' => 'Technology', 'type' => 'academic', 'sort_order' => 17],
            ['name' => 'Informatics Practices', 'category' => 'Technology', 'type' => 'academic', 'sort_order' => 18],
            
            // Others
            ['name' => 'Environmental Studies', 'category' => 'Other', 'type' => 'academic', 'sort_order' => 19],
            ['name' => 'Physical Education', 'category' => 'Other', 'type' => 'academic', 'sort_order' => 20],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
