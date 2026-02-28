<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    /**
     * Display a listing of tutors
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'tutor')
            ->with(['tutorProfile.qualifications']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $tutors = $query->orderBy('id', 'desc')->get();
        
        return view('admin.tutors.index', compact('tutors'));
    }

    /**
     * Display the specified tutor
     */
    public function show($id)
    {
        $tutor = User::where('role', 'tutor')
            ->with([
                'tutorProfile.qualifications.board',
                'tutorProfile.qualifications.classLevel',
                'tutorProfile.qualifications.subject',
                'documents',
                'skillTests',
                'adminReview'
            ])
            ->findOrFail($id);

        // Group qualifications by board for better display
        if ($tutor->tutorProfile && $tutor->tutorProfile->qualifications) {
            $groupedQualifications = $tutor->tutorProfile->qualifications->groupBy('board.name');
            $tutor->tutorProfile->grouped_qualifications = $groupedQualifications;
        }

        return response()->json([
            'success' => true,
            'data' => $tutor
        ]);
    }

    /**
     * Approve a tutor
     */
    public function approve($id)
    {
        $tutor = User::where('role', 'tutor')->findOrFail($id);
        
        $tutor->update(['status' => 'active']);

        return response()->json([
            'message' => 'Tutor approved successfully',
            'tutor' => $tutor
        ]);
    }

    /**
     * Reject a tutor
     */
    public function reject($id)
    {
        $tutor = User::where('role', 'tutor')->findOrFail($id);
        
        $tutor->update(['status' => 'inactive']);

        return response()->json([
            'message' => 'Tutor rejected',
            'tutor' => $tutor
        ]);
    }

    /**
     * Update tutor status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,under_review'
        ]);

        $tutor = User::where('role', 'tutor')->findOrFail($id);
        
        $tutor->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status updated successfully',
            'tutor' => $tutor
        ]);
    }
}
