<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TutorController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not logged in)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });
    
    // Protected routes (logged in)
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Questions Management
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('/', [QuestionController::class, 'index'])->name('index');
            Route::post('/', [QuestionController::class, 'store'])->name('store');
            Route::get('/{id}', [QuestionController::class, 'show'])->name('show');
            Route::put('/{id}', [QuestionController::class, 'update'])->name('update');
            Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');
        });
        
        // Tutors Management
        Route::prefix('tutors')->name('tutors.')->group(function () {
            Route::get('/', [TutorController::class, 'index'])->name('index');
            Route::get('/{id}', [TutorController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [TutorController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [TutorController::class, 'reject'])->name('reject');
            Route::post('/{id}/status', [TutorController::class, 'updateStatus'])->name('status');
        });
        
        // Boards Management
        Route::prefix('boards')->name('boards.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BoardManagementController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\BoardManagementController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\BoardManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\BoardManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\BoardManagementController::class, 'toggleActive'])->name('toggle');
            Route::post('/reorder', [\App\Http\Controllers\Admin\BoardManagementController::class, 'reorder'])->name('reorder');
        });
        
        // Classes Management
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ClassController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\ClassController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\ClassController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\ClassController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\ClassController::class, 'toggleActive'])->name('toggle');
            Route::post('/reorder', [\App\Http\Controllers\Admin\ClassController::class, 'reorder'])->name('reorder');
        });
        
        // Subjects Management
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SubjectManagementController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SubjectManagementController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\SubjectManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\SubjectManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\SubjectManagementController::class, 'toggleActive'])->name('toggle');
            Route::post('/reorder', [\App\Http\Controllers\Admin\SubjectManagementController::class, 'reorder'])->name('reorder');
        });
        
        // Class-Subject Mapping
        Route::prefix('class-subjects')->name('class-subjects.')->group(function () {
            Route::get('/{classId}', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'index'])->name('index');
            Route::post('/{classId}/sync', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'sync'])->name('sync');
            Route::post('/bulk-assign', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'bulkAssign'])->name('bulk-assign');
            Route::post('/{classId}/subjects/{subjectId}/toggle', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'toggleSubject'])->name('toggle-subject');
        });
        
        // Chapters Management
        Route::prefix('chapters')->name('chapters.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ChapterController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\ChapterController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\ChapterController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\ChapterController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\ChapterController::class, 'toggleActive'])->name('toggle');
            Route::get('/by-class-subject', [\App\Http\Controllers\Admin\ChapterController::class, 'getByClassSubject'])->name('by-class-subject');
        });
        
        // API Endpoints for Cascading Dropdowns
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/subjects-by-class/{classId}', function($classId) {
                $class = \App\Models\ClassLevel::findOrFail($classId);
                return response()->json($class->subjects()->get(['subjects.id', 'subjects.name']));
            })->name('subjects-by-class');
        });
        
        // Quizzes Management
        Route::prefix('quizzes')->name('quizzes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuizController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuizController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuizController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\QuizController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\QuizController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\QuizController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/publish', [\App\Http\Controllers\Admin\QuizController::class, 'publish'])->name('publish');
            Route::post('/{id}/unpublish', [\App\Http\Controllers\Admin\QuizController::class, 'unpublish'])->name('unpublish');
            Route::post('/{id}/duplicate', [\App\Http\Controllers\Admin\QuizController::class, 'duplicate'])->name('duplicate');
            
            // Quiz Questions
            Route::prefix('/{quizId}/questions')->name('questions.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'edit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'update'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'destroy'])->name('destroy');
                Route::post('/reorder', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'reorder'])->name('reorder');
                
                // Import routes
                Route::get('/import', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'showImportForm'])->name('import');
                Route::post('/import', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'import'])->name('import.process');
                Route::get('/template', [\App\Http\Controllers\Admin\QuizQuestionController::class, 'downloadTemplate'])->name('template');
            });
        });
    });
});
