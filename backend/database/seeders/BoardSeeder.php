<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boards = [
            ['name' => 'CBSE', 'sort_order' => 1],
            ['name' => 'ICSE', 'sort_order' => 2],
            ['name' => 'State Board', 'sort_order' => 3],
            ['name' => 'IB (International Baccalaureate)', 'sort_order' => 4],
            ['name' => 'IGCSE', 'sort_order' => 5],
            ['name' => 'NIOS', 'sort_order' => 6],
            ['name' => 'Cambridge', 'sort_order' => 7],
            ['name' => 'Other', 'sort_order' => 8],
        ];

        foreach ($boards as $board) {
            Board::create($board);
        }
    }
}
