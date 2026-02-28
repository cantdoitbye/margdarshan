<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivitySkill;
use Illuminate\Http\Request;

class ActivitySkillController extends Controller
{
    /**
     * Get all activity skills grouped by category
     */
    public function index()
    {
        $skills = ActivitySkill::getGroupedSkills();
        
        return response()->json([
            'success' => true,
            'data' => $skills
        ]);
    }

    /**
     * Get all active skills as flat list
     */
    public function list()
    {
        $skills = ActivitySkill::getActiveSkills();
        
        return response()->json([
            'success' => true,
            'data' => $skills
        ]);
    }
}
