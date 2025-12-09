<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReturnRecordResource;
use App\Models\ReturnRecord;
use App\Models\ItemMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReturnController extends Controller
{
    /**
     * Display all success stories (public - no auth required)
     */
    public function getSuccessStories()
    {
        $returns = ReturnRecord::with(['match.lostItem.user', 'match.foundItem.user'])
            ->where('status', 'completed')
            ->latest('return_date')
            ->paginate(20);

        return ReturnRecordResource::collection($returns);
    }

    /**
     * Display a listing of the user's returns (protected)
     */
    public function myReturns()
    {
        $userId = auth()->id();
        
        $returns = ReturnRecord::with(['match.lostItem.user', 'match.foundItem.user'])
            ->whereHas('match', function($query) use ($userId) {
                $query->where(function($q) use ($userId) {
                    $q->whereHas('lostItem', function($q2) use ($userId) {
                        $q2->where('user_id', $userId);
                    })->orWhereHas('foundItem', function($q2) use ($userId) {
                        $q2->where('user_id', $userId);
                    });
                });
            })
            ->latest('return_date')
            ->paginate(20);

        return ReturnRecordResource::collection($returns);
    }

    /**
     * Display a listing of all returns (protected admin only)
     */
    public function index()
    {
        $returns = ReturnRecord::with(['match.lostItem.user', 'match.foundItem.user'])
            ->latest('return_date')
            ->paginate(20);

        return ReturnRecordResource::collection($returns);
    }

    /**
     * Store a newly created return record
     */
    public function store(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'notes' => 'nullable|string|max:1000',
            'proof_image' => 'nullable|image|max:5120',
            'return_date' => 'required|date',
        ]);

        $match = ItemMatch::findOrFail($request->match_id);
        $userId = auth()->id();

        // Verify user is part of this match
        if ($match->lostItem->user_id !== $userId && $match->foundItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $returnRecord = new ReturnRecord();
        $returnRecord->match_id = $request->match_id;
        $returnRecord->notes = $request->notes;
        $returnRecord->return_date = $request->return_date;
        $returnRecord->status = 'completed';

        // Handle proof image upload
        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('return-proofs', 'public');
            $returnRecord->proof_image = $path;
        }

        $returnRecord->save();

        // Update match status
        $match->update(['status' => 'returned']);

        return new ReturnRecordResource($returnRecord->load(['match.lostItem.user', 'match.foundItem.user']));
    }

    /**
     * Display the specified return record
     */
    public function show(string $id)
    {
        $return = ReturnRecord::with(['match.lostItem.user', 'match.foundItem.user'])
            ->findOrFail($id);

        return new ReturnRecordResource($return);
    }

    /**
     * Update the specified return record
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'status' => 'in:pending,completed,cancelled',
        ]);

        $return = ReturnRecord::findOrFail($id);
        $userId = auth()->id();

        // Verify user owns this return record
        if ($return->match->lostItem->user_id !== $userId && $return->match->foundItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $return->update($request->only('notes', 'status'));

        return new ReturnRecordResource($return->load(['match.lostItem.user', 'match.foundItem.user']));
    }

    /**
     * Remove the specified return record
     */
    public function destroy(string $id)
    {
        $return = ReturnRecord::findOrFail($id);
        $userId = auth()->id();

        // Verify user owns this return record
        if ($return->match->lostItem->user_id !== $userId && $return->match->foundItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete proof image if exists
        if ($return->proof_image) {
            Storage::disk('public')->delete($return->proof_image);
        }

        $return->delete();

        return response()->json(['message' => 'Return record deleted successfully']);
    }
}
