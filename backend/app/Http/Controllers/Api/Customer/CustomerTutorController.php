<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerTutorController extends Controller
{
    /**
     * Get tutors list with filters and geolocation
     */
    public function index(Request $request)
    {
        // Validate input
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_km' => 'nullable|integer|min:1|max:50',
            'experience_min' => 'nullable|integer|min:0',
            'experience_max' => 'nullable|integer|min:0',
            'hourly_rate_min' => 'nullable|numeric|min:0',
            'hourly_rate_max' => 'nullable|numeric|min:0',
            'mode' => 'nullable|in:online,offline,both',
            'subject' => 'nullable|string',
            'class' => 'nullable|integer|min:1|max:12',
            'tutor_type' => 'nullable|in:academic,activity',
            'gender' => 'nullable|in:male,female,other',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radiusKm = $request->input('radius_km', 10);
        $perPage = $request->input('per_page', 20);

        // Start building query
        $query = TutorProfile::query()
            ->join('users', 'tutor_profiles.user_id', '=', 'users.id')
            ->where('users.status', 'active')
            ->where('tutor_profiles.is_profile_complete', true)
            ->select('tutor_profiles.*');

        // Apply filters
        if ($request->filled('experience_min')) {
            $query->where('tutor_profiles.experience_years', '>=', $request->experience_min);
        }

        if ($request->filled('experience_max')) {
            $query->where('tutor_profiles.experience_years', '<=', $request->experience_max);
        }

        if ($request->filled('hourly_rate_min')) {
            $query->where('tutor_profiles.hourly_rate', '>=', $request->hourly_rate_min);
        }

        if ($request->filled('hourly_rate_max')) {
            $query->where('tutor_profiles.hourly_rate', '<=', $request->hourly_rate_max);
        }

        if ($request->filled('tutor_type')) {
            $type = $request->tutor_type;
            $query->where(function ($q) use ($type) {
                $q->where('tutor_profiles.tutor_type', $type)
                    ->orWhere('tutor_profiles.tutor_type', 'both');
            });
        }

        // Filter by subject (for academic tutors)
        if ($request->filled('subject')) {
            $query->whereJsonContains('tutor_profiles.subjects', $request->subject);
        }

        // Filter by class (for academic tutors)
        if ($request->filled('class')) {
            $query->whereJsonContains('tutor_profiles.classes', (int) $request->class);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('users.gender', $request->gender);
        }

        // Geolocation filtering using Haversine formula
        if ($latitude && $longitude) {
            $query->selectRaw("
                tutor_profiles.*,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(service_latitude)) * 
                    cos(radians(service_longitude) - radians(?)) + 
                    sin(radians(?)) * sin(radians(service_latitude))
                )) AS distance_km
            ", [$latitude, $longitude, $latitude])
                // ->whereRaw("
                // (6371 * acos(
                //     cos(radians(?)) * cos(radians(service_latitude)) * 
                //     cos(radians(service_longitude) - radians(?)) + 
                //     sin(radians(?)) * sin(radians(service_latitude))
                // )) <= tutor_profiles.service_radius_km
                // ", [$latitude, $longitude, $latitude])
                ->orderBy('distance_km', 'asc');
        } else {
            // If no location provided, just order by experience
            $query->orderBy('tutor_profiles.experience_years', 'desc');
        }

        // Execute query with pagination
        $tutors = $query->with('user:id,name,email,phone')->paginate($perPage);

        // Transform data for response
        $tutors->getCollection()->transform(function ($tutor) {
            return [
                'id' => $tutor->id,
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'phone' => $tutor->user->phone,
                'gender' => $tutor->user->gender,
                'profile_image' => $tutor->profile_image ? url('storage/' . $tutor->profile_image) : null,
                'tutor_type' => $tutor->tutor_type,
                'bio' => $tutor->bio,
                'subjects' => $tutor->subjects,
                'classes' => $tutor->classes,
                'activity_skills' => $tutor->activity_skills,
                'education' => $tutor->education,
                'experience_years' => $tutor->experience_years,
                'hourly_rate' => $tutor->hourly_rate,
                'language' => $tutor->language,
                'timezone' => $tutor->timezone,
                'availability' => $tutor->availability,
                'service_location' => $tutor->service_location,
                'service_radius_km' => $tutor->service_radius_km,
                'distance_km' => isset($tutor->distance_km) ? round($tutor->distance_km, 2) : null,
                // TODO: Add rating and reviews when implemented
                'rating' => 4.5, // Placeholder
                'total_reviews' => 0, // Placeholder
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tutors' => $tutors->items(),
                'pagination' => [
                    'current_page' => $tutors->currentPage(),
                    'per_page' => $tutors->perPage(),
                    'total' => $tutors->total(),
                    'last_page' => $tutors->lastPage(),
                    'from' => $tutors->firstItem(),
                    'to' => $tutors->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * Get single tutor details
     */
    public function show(Request $request, $id)
    {
        $tutor = TutorProfile::with('user:id,name,email,phone')
            ->where('id', $id)
            ->first();

        if (!$tutor) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor not found',
            ], 404);
        }

        // Calculate distance if location provided
        $distance = null;
        if ($request->filled(['latitude', 'longitude'])) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $tutor->service_latitude,
                $tutor->service_longitude
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tutor->id,
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'phone' => $tutor->user->phone,
                'gender' => $tutor->user->gender,
                'profile_image' => $tutor->profile_image ? url('storage/' . $tutor->profile_image) : null,
                'tutor_type' => $tutor->tutor_type,
                'bio' => $tutor->bio,
                'subjects' => $tutor->subjects,
                'classes' => $tutor->classes,
                'activity_skills' => $tutor->activity_skills,
                'demo_video_path' => $tutor->demo_video_path ? url('storage/' . $tutor->demo_video_path) : null,
                'education' => $tutor->education,
                'experience_years' => $tutor->experience_years,
                'hourly_rate' => $tutor->hourly_rate,
                'language' => $tutor->language,
                'timezone' => $tutor->timezone,
                'availability' => $tutor->availability,
                'service_location' => $tutor->service_location,
                'service_radius_km' => $tutor->service_radius_km,
                'distance_km' => $distance ? round($distance, 2) : null,
                // TODO: Add rating and reviews when implemented
                'rating' => 4.5, // Placeholder
                'total_reviews' => 0, // Placeholder
            ]
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
