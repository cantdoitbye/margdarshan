<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VideoUploadController extends Controller
{
    /**
     * Upload demo video for activity tutors
     */
    public function uploadDemoVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:15360', // 15MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $video = $request->file('video');
            $userId = $request->user()->id;
            
            // Generate unique filename
            $filename = 'demo_video_' . $userId . '_' . time() . '.' . $video->getClientOriginalExtension();
            
            // Store in public/videos/demo directory
            $path = $video->move(public_path('videos/demo'), $filename);
            
            // Return relative path for database storage
            $relativePath = 'videos/demo/' . $filename;
            
            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'data' => [
                    'path' => $relativePath,
                    'url' => url($relativePath),
                    'filename' => $filename,
                    'size' => $video->getSize()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete demo video
     */
    public function deleteDemoVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $videoPath = public_path($request->video_path);
            
            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete video: ' . $e->getMessage()
            ], 500);
        }
    }
}
