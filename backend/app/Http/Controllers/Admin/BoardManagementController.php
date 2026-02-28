<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardManagementController extends Controller
{
    /**
     * Display a listing of boards.
     */
    public function index()
    {
        $boards = Board::ordered()->get();

        // Return view for web requests, JSON for API requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $boards
            ]);
        }

        return view('admin.boards.index', compact('boards'));
    }

    /**
     * Store a newly created board.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:boards,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $board = Board::create([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Board created successfully',
            'data' => $board
        ], 201);
    }

    /**
     * Update the specified board.
     */
    public function update(Request $request, $id)
    {
        $board = Board::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:boards,name,' . $id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $board->update([
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? $board->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Board updated successfully',
            'data' => $board
        ]);
    }

    /**
     * Remove the specified board.
     */
    public function destroy($id)
    {
        $board = Board::findOrFail($id);
        $board->delete();

        return response()->json([
            'success' => true,
            'message' => 'Board deleted successfully'
        ]);
    }

    /**
     * Toggle board active status.
     */
    public function toggleActive($id)
    {
        $board = Board::findOrFail($id);
        $board->is_active = !$board->is_active;
        $board->save();

        return response()->json([
            'success' => true,
            'message' => 'Board status updated successfully',
            'data' => $board
        ]);
    }

    /**
     * Reorder boards.
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'boards' => 'required|array',
            'boards.*.id' => 'required|exists:boards,id',
            'boards.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->boards as $boardData) {
            Board::where('id', $boardData['id'])->update(['sort_order' => $boardData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Boards reordered successfully'
        ]);
    }
}
