<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorIdCardController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get or generate tutor ID card
     */
    public function getIdCard(Request $request)
    {
        $user = $request->user();

        // Check if user is a tutor
        if ($user->role !== 'tutor') {
            return response()->json([
                'success' => false,
                'message' => 'Only tutors can access ID cards',
            ], 403);
        }

        // Check if tutor profile is complete
        $tutorProfile = $user->tutorProfile;
        if (!$tutorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your profile first',
            ], 400);
        }

        // Generate tutor ID code if not exists
        if (!$user->tutor_id_code) {
            $user->tutor_id_code = $this->generateUniqueTutorIdCode();
            $user->qr_code_generated_at = now();
            $user->save();
        }

        // Generate QR code
        $profileUrl = $this->qrCodeService->getPublicProfileUrl($user->tutor_id_code);
        $qrCodeDataUrl = $this->qrCodeService->generateQrCode($profileUrl);

        // Get tutor subjects
        $subjects = [];
        if ($tutorProfile->tutor_type === 'academic') {
            $qualifications = $tutorProfile->qualifications()->with('subject')->get();
            $subjects = $qualifications->pluck('subject.name')->unique()->values()->toArray();
        } else {
            $subjects = $tutorProfile->activity_skills ?? [];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tutor_id_code' => $user->tutor_id_code,
                'profile_url' => $profileUrl,
                'qr_code_data_url' => $qrCodeDataUrl,
                'generated_at' => $user->qr_code_generated_at,
                'tutor_info' => [
                    'name' => $user->name,
                    'photo' => $user->profile_photo,
                    'tutor_type' => $tutorProfile->tutor_type,
                    'subjects' => $subjects,
                    'experience_years' => $tutorProfile->experience_years,
                    'education' => $tutorProfile->education,
                    'status' => $user->status,
                    'verified' => $user->status === 'active' || $user->status === 'approved',
                ],
            ]
        ]);
    }

    /**
     * Regenerate QR code (in case of security concerns)
     */
    public function regenerateQrCode(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'tutor') {
            return response()->json([
                'success' => false,
                'message' => 'Only tutors can regenerate QR codes',
            ], 403);
        }

        // Generate new tutor ID code
        $user->tutor_id_code = $this->generateUniqueTutorIdCode();
        $user->qr_code_generated_at = now();
        $user->qr_access_count = 0;
        $user->qr_last_accessed_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'QR code regenerated successfully',
            'data' => [
                'tutor_id_code' => $user->tutor_id_code,
            ]
        ]);
    }

    /**
     * Get QR code access statistics
     */
    public function getAccessStats(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'total_scans' => $user->qr_access_count ?? 0,
                'last_scanned_at' => $user->qr_last_accessed_at,
                'generated_at' => $user->qr_code_generated_at,
            ]
        ]);
    }

    /**
     * Generate unique tutor ID code
     */
    private function generateUniqueTutorIdCode(): string
    {
        do {
            $code = $this->qrCodeService->generateTutorIdCode();
            $exists = DB::table('users')->where('tutor_id_code', $code)->exists();
        } while ($exists);

        return $code;
    }
}
