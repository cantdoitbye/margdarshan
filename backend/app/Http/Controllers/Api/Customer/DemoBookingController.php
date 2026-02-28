<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\DemoBooking;
use App\Models\TutorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DemoBookingController extends Controller
{
    /**
     * Get all demo bookings for authenticated customer
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $customer = $request->user(); // auth:sanctum returns Customer directly
        
        $query = DemoBooking::where('customer_id', $customer->id)
            ->with(['tutor.user:id,name,phone', 'student:id,name,class,board']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by newest first, then by demo date
        $query->orderBy('created_at', 'desc')
              ->orderBy('demo_date', 'desc');

        $perPage = $request->input('per_page', 15);
        $bookings = $query->paginate($perPage);

        // Transform data
        $bookings->getCollection()->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'tutor' => [
                    'id' => $booking->tutor->id,
                    'name' => $booking->tutor->user->name,
                    'phone' => $booking->tutor->user->phone,
                    'profile_image' => $booking->tutor->profile_image ? url('storage/' . $booking->tutor->profile_image) : null,
                    'experience_years' => $booking->tutor->experience_years,
                    'hourly_rate' => $booking->tutor->hourly_rate,
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
        $customer = $request->user(); // auth:sanctum returns Customer directly
        
        $booking = DemoBooking::where('customer_id', $customer->id)
            ->where('id', $id)
            ->with(['tutor.user:id,name,phone', 'student:id,name,class,board,subjects'])
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
                'tutor' => [
                    'id' => $booking->tutor->id,
                    'name' => $booking->tutor->user->name,
                    'phone' => $booking->tutor->user->phone,
                    'profile_image' => $booking->tutor->profile_image ? url('storage/' . $booking->tutor->profile_image) : null,
                    'bio' => $booking->tutor->bio,
                    'experience_years' => $booking->tutor->experience_years,
                    'hourly_rate' => $booking->tutor->hourly_rate,
                    'subjects' => $booking->tutor->subjects,
                    'education' => $booking->tutor->education,
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
     * Get booking statistics for customer
     */
    public function getStatistics(Request $request)
    {
        $customer = $request->user(); // auth:sanctum returns Customer directly
        
        $totalPending = DemoBooking::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->count();

        $totalConfirmed = DemoBooking::where('customer_id', $customer->id)
            ->where('status', 'confirmed')
            ->count();

        $totalCompleted = DemoBooking::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->count();

        $upcomingBookings = DemoBooking::where('customer_id', $customer->id)
            ->where('status', 'confirmed')
            ->where('demo_date', '>=', now()->toDateString())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_pending' => $totalPending,
                'total_confirmed' => $totalConfirmed,
                'total_completed' => $totalCompleted,
                'upcoming_bookings' => $upcomingBookings,
            ]
        ]);
    }

    /**
     * Create a new demo booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tutor_id' => 'required|exists:tutor_profiles,id',
            'student_id' => 'required|exists:students,id',
            'demo_date' => 'required|date|after:today',
            'demo_time' => 'required', // Format validation can be added
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify student belongs to customer
        $customer = $request->user(); // auth:sanctum returns Customer directly
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        $student = $customer->students()->find($request->student_id);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid student selected',
            ], 403);
        }

        // Check if tutor is available (basic check)
        // In a real app, we would check specific availability slots

        $booking = DemoBooking::create([
            'customer_id' => $customer->id,
            'tutor_id' => $request->tutor_id,
            'student_id' => $request->student_id,
            'demo_date' => $request->demo_date,
            'demo_time' => $request->demo_time,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demo booking request sent successfully',
            'data' => $booking
        ], 201);
    }
}
