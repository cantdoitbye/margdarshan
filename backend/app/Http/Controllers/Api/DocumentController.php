<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TutorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = $request->user()->documents;

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:id_proof,certificate,resume,other',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = TutorDocument::create([
            'user_id' => $request->user()->id,
            'document_type' => $request->document_type,
            'document_name' => $file->getClientOriginalName(),
            'document_path' => $filePath,
            'file_size' => $file->getSize(),
            'verification_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => $document
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $document = TutorDocument::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->document_path)) {
            Storage::disk('public')->delete($document->document_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }
}
