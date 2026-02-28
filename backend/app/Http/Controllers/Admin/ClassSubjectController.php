<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassSubjectController extends Controller
{
    /**
     * Get subjects for a specific class.
     */
    public function index($classId)
    {
        $class = ClassLevel::with('allSubjects')->findOrFail($classId);

        return response()->json([
            'success' => true,
            'data' => [
                'class' => $class,
                'subjects' => $class->allSubjects
            ]
        ]);
    }

    /**
     * Sync subjects for a class.
     */
    public function sync(Request $request, $classId)
    {
        $class = ClassLevel::findOrFail($classId);

        $validator = Validator::make($request->all(), [
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Sync subjects with is_active = true
        $syncData = [];
        foreach ($request->subject_ids as $subjectId) {
            $syncData[$subjectId] = ['is_active' => true];
        }

        $class->allSubjects()->sync($syncData);

        return response()->json([
            'success' => true,
            'message' => 'Subjects synced successfully for ' . $class->name
        ]);
    }

    /**
     * Bulk assign subjects to multiple classes.
     */
    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->class_ids as $classId) {
                $class = ClassLevel::find($classId);
                
                // Get existing subject IDs
                $existingSubjectIds = $class->allSubjects()->pluck('subject_id')->toArray();
                
                // Merge with new subject IDs
                $allSubjectIds = array_unique(array_merge($existingSubjectIds, $request->subject_ids));
                
                // Sync with is_active = true
                $syncData = [];
                foreach ($allSubjectIds as $subjectId) {
                    $syncData[$subjectId] = ['is_active' => true];
                }
                
                $class->allSubjects()->sync($syncData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subjects assigned to classes successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle subject status for a class.
     */
    public function toggleSubject(Request $request, $classId, $subjectId)
    {
        $class = ClassLevel::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        $pivot = DB::table('class_subject')
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$pivot) {
            // Add the subject
            DB::table('class_subject')->insert([
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject added to class',
                'is_active' => true
            ]);
        } else {
            // Toggle active status
            $newStatus = !$pivot->is_active;
            DB::table('class_subject')
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->update([
                    'is_active' => $newStatus,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject status updated',
                'is_active' => $newStatus
            ]);
        }
    }
}
