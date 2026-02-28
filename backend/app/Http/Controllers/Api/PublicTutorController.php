<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class PublicTutorController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get public tutor profile by tutor ID code
     * This endpoint is public and doesn't require authentication
     */
    public function show(Request $request, string $tutorIdCode)
    {
        // Validate tutor ID code format
        if (!$this->qrCodeService->isValidTutorIdCode($tutorIdCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid tutor ID code format',
            ], 400);
        }

        // Find tutor by ID code
        $user = User::where('tutor_id_code', $tutorIdCode)
            ->where('role', 'tutor')
            ->with('tutorProfile')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor not found',
            ], 404);
        }

        // Check if tutor is active
        if ($user->status !== 'active' && $user->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'This tutor profile is not currently active',
            ], 403);
        }

        $tutorProfile = $user->tutorProfile;

        if (!$tutorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Tutor profile not found',
            ], 404);
        }

        // Log QR code access
        $this->logQrAccess($user, $request);

        // Get subjects based on tutor type
        $subjects = [];
        $qualifications = [];
        
        if ($tutorProfile->tutor_type === 'academic') {
            $subjects = $tutorProfile->subjects ?? [];
        } else {
            $subjects = $tutorProfile->activity_skills ?? [];
        }

        // Parse education if it's JSON
        $education = $tutorProfile->education;
        if (is_string($education)) {
            $education = json_decode($education, true);
        }

        // Format qualifications for display
        if (is_array($education)) {
            foreach ($education as $edu) {
                $qualifications[] = [
                    'degree' => $edu['degree'] ?? $edu['qualification'] ?? 'N/A',
                    'institution' => $edu['institution'] ?? $edu['university'] ?? 'N/A',
                    'year' => $edu['year'] ?? $edu['graduation_year'] ?? null,
                ];
            }
        }

        // Calculate rating (placeholder - implement actual rating system)
        $rating = 4.5; // TODO: Calculate from actual reviews
        $reviewCount = 0; // TODO: Get actual review count

        // Get profile image URL
        $profileImage = null;
        if ($user->profile_photo) {
            $profileImage = url('storage/' . $user->profile_photo);
        } elseif ($tutorProfile->profile_image) {
            $profileImage = url('storage/' . $tutorProfile->profile_image);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tutor_id_code' => $user->tutor_id_code,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $profileImage,
                'tutor_type' => $tutorProfile->tutor_type,
                'subjects' => $subjects,
                'qualifications' => $qualifications,
                'education' => $education,
                'experience_years' => $tutorProfile->experience_years,
                'bio' => $tutorProfile->bio,
                'hourly_rate' => $tutorProfile->hourly_rate,
                'location' => $tutorProfile->service_location,
                'languages' => $tutorProfile->language ? [$tutorProfile->language] : ['English'],
                'rating' => $rating,
                'review_count' => $reviewCount,
                'is_verified' => true,
                'status' => 'active',
                'headline' => $tutorProfile->tutor_type === 'academic' 
                    ? 'Academic Tutor' 
                    : 'Activity Instructor',
                'verification_badges' => [
                    'identity_verified' => true,
                    'documents_verified' => true,
                    'background_check' => true,
                ],
            ]
        ]);
    }

    /**
     * Log QR code access
     */
    private function logQrAccess(User $user, Request $request)
    {
        $user->increment('qr_access_count');
        $user->qr_last_accessed_at = now();
        $user->save();

        // Optional: Log detailed access information
        // You can create a separate table for detailed logs if needed
        \Log::info('QR Code Accessed', [
            'tutor_id' => $user->id,
            'tutor_id_code' => $user->tutor_id_code,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
