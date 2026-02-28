<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestQuestion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_tutors' => User::where('role', 'tutor')->count(),
            'active_tutors' => User::where('role', 'tutor')->where('status', 'active')->count(),
            'pending_tutors' => User::where('role', 'tutor')->where('status', 'under_review')->count(),
            'inactive_tutors' => User::where('role', 'tutor')->where('status', 'inactive')->count(),
            'total_questions' => TestQuestion::count(),
        ];
        
        $recent_tutors = User::where('role', 'tutor')
            ->with('tutorProfile')
            ->latest()
            ->take(5)
            ->get();
        
        // Questions by subject
        $questions_by_subject = TestQuestion::selectRaw('subject, COUNT(*) as count')
            ->groupBy('subject')
            ->pluck('count', 'subject')
            ->toArray();
        
        // Questions by class
        $questions_by_class = TestQuestion::selectRaw('class, COUNT(*) as count')
            ->groupBy('class')
            ->orderBy('class')
            ->pluck('count', 'class')
            ->toArray();
        
        return view('admin.dashboard', compact('stats', 'recent_tutors', 'questions_by_subject', 'questions_by_class'));
    }
}
