<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TutorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->tutorProfile;
        
        if ($profile) {
            // Load qualifications with relationships
            $profile->load([
                'qualifications.board',
                'qualifications.classLevel',
                'qualifications.subject'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'tutor_type' => 'required|in:academic,activity',
            'bio' => 'required|string',
            'education' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'language' => 'required|string',
            'timezone' => 'required|string',
            'availability' => 'nullable|array',
            'service_location' => 'required|string',
            'service_latitude' => 'required|numeric|between:-90,90',
            'service_longitude' => 'required|numeric|between:-180,180',
            'service_radius_km' => 'required|integer|min:1|max:50',
        ];

        // Conditional validation based on tutor type
        if ($request->tutor_type === 'academic') {
            $rules['qualifications'] = 'required|array|min:1';
            $rules['qualifications.*.board_id'] = 'required|exists:boards,id';
            $rules['qualifications.*.class_id'] = 'required|exists:classes,id';
            $rules['qualifications.*.subject_id'] = 'required|exists:subjects,id';
        } else if ($request->tutor_type === 'activity') {
            $rules['activity_skills'] = 'required|array|min:1';
            $rules['demo_video_path'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if tutor type is being changed (not allowed)
        $existingProfile = TutorProfile::where('user_id', $request->user()->id)->first();
        if ($existingProfile && $existingProfile->tutor_type !== $request->tutor_type) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change tutor type once set. You can only update within your current type.'
            ], 422);
        }

        // Use database transaction
        \DB::beginTransaction();
        try {
            // Prepare data for saving
            $data = [
                'tutor_type' => $request->tutor_type,
                'bio' => $request->bio,
                'education' => $request->education,
                'experience_years' => $request->experience_years,
                'hourly_rate' => $request->hourly_rate,
                'language' => $request->language,
                'timezone' => $request->timezone,
                'availability' => $request->availability,
                'service_location' => $request->service_location,
                'service_latitude' => $request->service_latitude,
                'service_longitude' => $request->service_longitude,
                'service_radius_km' => $request->service_radius_km,
                'is_profile_complete' => true,
            ];

            // Add type-specific fields
            if ($request->tutor_type === 'academic') {
                // Keep old fields for backward compatibility
                $data['subjects'] = null;
                $data['classes'] = null;
                $data['activity_skills'] = null;
                $data['demo_video_path'] = null;
            } else {
                $data['activity_skills'] = $request->activity_skills;
                $data['demo_video_path'] = $request->demo_video_path;
                $data['subjects'] = null;
                $data['classes'] = null;
            }

            $profile = TutorProfile::updateOrCreate(
                ['user_id' => $request->user()->id],
                $data
            );

            // Handle qualifications for academic tutors
            if ($request->tutor_type === 'academic' && $request->has('qualifications')) {
                // Delete existing qualifications
                $profile->qualifications()->delete();
                
                $validCount = 0;
                $skippedCount = 0;
                
                // Insert new qualifications (only valid ones)
                foreach ($request->qualifications as $qualification) {
                    // Validate that subject is available for the class
                    $isValid = \DB::table('class_subject')
                        ->where('class_id', $qualification['class_id'])
                        ->where('subject_id', $qualification['subject_id'])
                        ->where('is_active', true)
                        ->exists();
                    
                    if ($isValid) {
                        $profile->qualifications()->create($qualification);
                        $validCount++;
                    } else {
                        $skippedCount++;
                        // Log for debugging
                        \Log::info("Skipped invalid qualification: Board {$qualification['board_id']}, Class {$qualification['class_id']}, Subject {$qualification['subject_id']}");
                    }
                }
                
                // If no valid qualifications were created, return error
                if ($validCount === 0) {
                    \DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid qualifications could be created. Please ensure subjects are available for selected classes.'
                    ], 422);
                }
            }

            \DB::commit();

            // Load relationships for response
            $profile->load([
                'qualifications.board',
                'qualifications.classLevel',
                'qualifications.subject'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile saved successfully',
                'data' => $profile
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatus(Request $request)
    {
        $user = $request->user()->load(['tutorProfile', 'documents', 'skillTests', 'adminReview']);
        
        $hasProfile = $user->tutorProfile && $user->tutorProfile->is_profile_complete;
        
        // For academic tutors, also check if they have qualifications
        if ($hasProfile && $user->tutorProfile->tutor_type === 'academic') {
            $hasQualifications = $user->tutorProfile->qualifications()->count() > 0;
            $hasProfile = $hasProfile && $hasQualifications;
        }
        
        $hasDocuments = $user->documents()->count() > 0;
        $hasCompletedTest = $user->skillTests()->where('status', 'completed')->exists();
        
        $status = [
            'user_status' => $user->status,
            'has_profile' => $hasProfile,
            'has_documents' => $hasDocuments,
            'has_completed_test' => $hasCompletedTest,
            'next_step' => $this->getNextStep($user, $hasProfile, $hasDocuments, $hasCompletedTest),
        ];

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }

    private function getNextStep($user, $hasProfile, $hasDocuments, $hasCompletedTest)
    {
        if (!$hasProfile) {
            return 'complete_profile';
        }
        if (!$hasDocuments) {
            return 'upload_documents';
        }
        if (!$hasCompletedTest) {
            return 'take_skill_test';
        }
        if ($user->status === 'pending' || $user->status === 'under_review') {
            return 'under_review';
        }
        if ($user->status === 'approved' || $user->status === 'active') {
            return 'approved';
        }
        if ($user->status === 'rejected' || $user->status === 'inactive') {
            return 'rejected';
        }
        return 'pending';
    }
}
