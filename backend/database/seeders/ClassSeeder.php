<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassLevel;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['name' => 'LKG', 'sort_order' => 1],
            ['name' => 'UKG', 'sort_order' => 2],
            ['name' => 'Class 1', 'sort_order' => 3],
            ['name' => 'Class 2', 'sort_order' => 4],
            ['name' => 'Class 3', 'sort_order' => 5],
            ['name' => 'Class 4', 'sort_order' => 6],
            ['name' => 'Class 5', 'sort_order' => 7],
            ['name' => 'Class 6', 'sort_order' => 8],
            ['name' => 'Class 7', 'sort_order' => 9],
            ['name' => 'Class 8', 'sort_order' => 10],
            ['name' => 'Class 9', 'sort_order' => 11],
            ['name' => 'Class 10', 'sort_order' => 12],
            ['name' => 'Class 11', 'sort_order' => 13],
            ['name' => 'Class 12', 'sort_order' => 14],
        ];

        foreach ($classes as $class) {
            ClassLevel::create($class);
        }
    }
}
