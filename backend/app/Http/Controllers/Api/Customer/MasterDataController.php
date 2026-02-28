<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Board;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Get all subjects
     */
    public function subjects()
    {
        $subjects = Subject::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $subjects
        ]);
    }

    /**
     * Get all boards
     */
    public function boards()
    {
        $boards = Board::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $boards
        ]);
    }

    /**
     * Get all classes (1-12)
     */
    public function classes()
    {
        $classes = [];
        for ($i = 1; $i <= 12; $i++) {
            $classes[] = [
                'id' => $i,
                'name' => "Class {$i}",
                'value' => $i,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }
}
