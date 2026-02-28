<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\DemoBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorDemoBookingController extends Controller
{
    /**
     * Get all demo bookings for authenticated tutor
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $tutorProfile = $request->user()->tutorProfile;
        
        if (!$tutorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor profile not found',
            ], 404);
        }

        $query = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->with(['customer:id,name,phone', 'student']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by date
        $query->orderBy('demo_date', 'desc')
              ->orderBy('demo_time', 'desc')
              ->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 15);
        $bookings = $query->paginate($perPage);

        // Transform data
        $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'customer' => [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name,
                    'phone' => $booking->customer->phone,
                ],
                'student' => [
                    'id' => $booking->student->id,
                    'name' => $booking->student->name,
                    'class' => $booking->student->class,
                    'board' => $booking->student->board,
                ],
                'demo_date' => $booking->demo_date->format('Y-m-d'),
                'demo_time' => $booking->demo_time,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'tutor_response' => $booking->tutor_response,
                'responded_at' => $booking->responded_at?->format('Y-m-d H:i:s'),
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings->items(),
                'pagination' => [
                    'current_page' => $bookings->currentPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                    'last_page' => $bookings->lastPage(),
                    'from' => $bookings->firstItem(),
                    'to' => $bookings->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * Get single demo booking details
     */
    public function show(Request $request, $id)
    {
        $tutorProfile = $request->user()->tutorProfile;
        
        if (!$tutorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor profile not found',
            ], 404);
        }

        $booking = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->where('id', $id)
            ->with(['customer:id,name,phone', 'student'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $booking->id,
                'customer' => [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name,
                    'phone' => $booking->customer->phone,
                ],
                'student' => [
                    'id' => $booking->student->id,
                    'name' => $booking->student->name,
                    'class' => $booking->student->class,
                    'board' => $booking->student->board,
                    'subjects' => $booking->student->subjects,
                ],
                'demo_date' => $booking->demo_date->format('Y-m-d'),
                'demo_time' => $booking->demo_time,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'tutor_response' => $booking->tutor_response,
                'responded_at' => $booking->responded_at?->format('Y-m-d H:i:s'),
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $booking->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:confirmed,cancelled,completed',
            'tutor_response' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tutorProfile = $request->user()->tutorProfile;
        
        if (!$tutorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor profile not found',
            ], 404);
        }

        $booking = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }

        // Validate status transitions
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update a completed or cancelled booking',
            ], 400);
        }

        $booking->update([
            'status' => $request->status,
            'tutor_response' => $request->tutor_response,
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'tutor_response' => $booking->tutor_response,
                'responded_at' => $booking->responded_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Get booking statistics
     */
    public function getStatistics(Request $request)
    {
        $tutorProfile = $request->user()->tutorProfile;
        
        if (!$tutorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor profile not found',
            ], 404);
        }

        $totalPending = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->where('status', 'pending')
            ->count();

        $totalConfirmed = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->where('status', 'confirmed')
            ->count();

        $totalCompleted = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->where('status', 'completed')
            ->count();

        $upcomingThisWeek = DemoBooking::where('tutor_id', $tutorProfile->id)
            ->where('status', 'confirmed')
            ->whereBetween('demo_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_pending' => $totalPending,
                'total_confirmed' => $totalConfirmed,
                'total_completed' => $totalCompleted,
                'upcoming_this_week' => $upcomingThisWeek,
            ]
        ]);
    }
}
