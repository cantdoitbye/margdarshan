<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TutorProfileController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\SkillTestController;
use App\Http\Controllers\Api\ActivitySkillController;
use App\Http\Controllers\Api\VideoUploadController;
use App\Http\Controllers\Api\PublicTutorController;
use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Customer\StudentController;
use App\Http\Controllers\Api\Customer\CustomerTutorController;
use App\Http\Controllers\Api\Customer\MasterDataController;
// Test route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()
    ]);
});

// Public routes
Route::post('/tutor/register', [AuthController::class, 'register']);
Route::post('/tutor/login', [AuthController::class, 'login']);

// Activity Skills (public - needed for registration)
Route::get('/activity-skills', [ActivitySkillController::class, 'index']);
Route::get('/activity-skills/list', [ActivitySkillController::class, 'list']);

// Public Tutor Profile (for QR code verification)
Route::get('/public/tutor/{tutorIdCode}', [PublicTutorController::class, 'show']);

// Master Data (public - needed for tutor registration/profile)
Route::get('/boards', [\App\Http\Controllers\Api\MasterDataController::class, 'getBoards']);
Route::get('/classes', [\App\Http\Controllers\Api\MasterDataController::class, 'getClasses']);
Route::get('/subjects', [\App\Http\Controllers\Api\MasterDataController::class, 'getSubjects']);
Route::get('/classes/{classId}/subjects', [\App\Http\Controllers\Api\MasterDataController::class, 'getClassSubjects']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/tutor/logout', [AuthController::class, 'logout']);
    Route::get('/tutor/me', [AuthController::class, 'me']);

    // Profile routes
    Route::get('/tutor/profile', [TutorProfileController::class, 'show']);
    Route::post('/tutor/profile', [TutorProfileController::class, 'store']);
    Route::get('/tutor/status', [TutorProfileController::class, 'getStatus']);

    // Document routes
    Route::get('/tutor/documents', [DocumentController::class, 'index']);
    Route::post('/tutor/documents/upload', [DocumentController::class, 'upload']);
    Route::delete('/tutor/documents/{id}', [DocumentController::class, 'destroy']);

    // Skill Test routes
    Route::post('/tutor/skill-test/submit', [\App\Http\Controllers\Api\Tutor\SkillTestController::class, 'submit']);
    Route::get('/tutor/skill-test/results', [\App\Http\Controllers\Api\Tutor\SkillTestController::class, 'results']);

    // Video Upload routes
    Route::post('/tutor/upload-demo-video', [VideoUploadController::class, 'uploadDemoVideo']);
    Route::delete('/tutor/delete-demo-video', [VideoUploadController::class, 'deleteDemoVideo']);

    // Demo Booking Management
    Route::get('/tutor/demo-bookings', [\App\Http\Controllers\Api\Tutor\TutorDemoBookingController::class, 'index']);
    Route::get('/tutor/demo-bookings/statistics', [\App\Http\Controllers\Api\Tutor\TutorDemoBookingController::class, 'getStatistics']);
    Route::get('/tutor/demo-bookings/{id}', [\App\Http\Controllers\Api\Tutor\TutorDemoBookingController::class, 'show']);
    Route::patch('/tutor/demo-bookings/{id}/status', [\App\Http\Controllers\Api\Tutor\TutorDemoBookingController::class, 'updateStatus']);

    // Tutor ID Card
    Route::get('/tutor/id-card', [\App\Http\Controllers\Api\Tutor\TutorIdCardController::class, 'getIdCard']);
    Route::post('/tutor/id-card/regenerate', [\App\Http\Controllers\Api\Tutor\TutorIdCardController::class, 'regenerateQrCode']);
    Route::get('/tutor/id-card/stats', [\App\Http\Controllers\Api\Tutor\TutorIdCardController::class, 'getAccessStats']);
});




/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
|
| These routes are for the customer mobile app
|
*/

// Public routes (no authentication required)
Route::prefix('customer')->group(function () {
    // Authentication
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp']);

    // Master data (public access for dropdowns)
    Route::get('/subjects', [MasterDataController::class, 'subjects']);
    Route::get('/boards', [MasterDataController::class, 'boards']);
    Route::get('/classes', [MasterDataController::class, 'classes']);

    // Tutors (public access to browse)
    Route::get('/tutors', [CustomerTutorController::class, 'index']);
    Route::get('/tutors/{id}', [CustomerTutorController::class, 'show']);
});

// Protected routes (authentication required)
Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/me', [CustomerAuthController::class, 'me']);

    // Student management
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);

    // Demo Booking
    Route::get('/demo-bookings', [\App\Http\Controllers\Api\Customer\DemoBookingController::class, 'index']);
    Route::get('/demo-bookings/statistics', [\App\Http\Controllers\Api\Customer\DemoBookingController::class, 'getStatistics']);
    Route::get('/demo-bookings/{id}', [\App\Http\Controllers\Api\Customer\DemoBookingController::class, 'show']);
    Route::post('/book-demo', [\App\Http\Controllers\Api\Customer\DemoBookingController::class, 'store']);
    
    // Quiz APIs
    Route::prefix('quizzes')->group(function () {
        // Quiz listing and details
        Route::get('/', [\App\Http\Controllers\Api\Student\QuizController::class, 'index']);
        Route::get('/statistics', [\App\Http\Controllers\Api\Student\QuizController::class, 'statistics']);
        Route::get('/chapters', [\App\Http\Controllers\Api\Student\QuizController::class, 'getChapters']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Student\QuizController::class, 'show']);
        
        // Quiz attempts
        Route::post('/{quizId}/start', [\App\Http\Controllers\Api\Student\QuizAttemptController::class, 'start']);
        Route::post('/attempts/{attemptId}/answer', [\App\Http\Controllers\Api\Student\QuizAttemptController::class, 'saveAnswer']);
        Route::post('/attempts/{attemptId}/submit', [\App\Http\Controllers\Api\Student\QuizAttemptController::class, 'submit']);
        Route::get('/attempts/{attemptId}/result', [\App\Http\Controllers\Api\Student\QuizAttemptController::class, 'result']);
        Route::get('/attempts/{attemptId}/status', [\App\Http\Controllers\Api\Student\QuizAttemptController::class, 'status']);
        Route::get('/attempts/history', [\App\Http\Controllers\Api\Student\QuizAttemptController::class, 'history']);
    });
});
