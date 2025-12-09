<?php

namespace App\Http\Controllers;

use App\Models\ItemMatch;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class MatchController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Mark matches as viewed when user visits the matches page
        ItemMatch::whereHas('lostItem', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->whereNull('lost_user_viewed_at')
        ->update(['lost_user_viewed_at' => now()]);
        
        ItemMatch::whereHas('foundItem', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->whereNull('found_user_viewed_at')
        ->update(['found_user_viewed_at' => now()]);
        
        // Show all matches with similarity >= 70% where items haven't been returned
        $matches = ItemMatch::with(['lostItem.user', 'foundItem.user'])
            ->where('similarity_score', '>=', 70)
            ->whereHas('lostItem', function ($query) {
                $query->whereDoesntHave('returnRecord');
            })
            ->whereHas('foundItem', function ($query) {
                $query->whereDoesntHave('returnRecord');
            })
            ->orderByDesc('similarity_score')
            ->get();

        return view('matches.index', ['matches' => $matches]);
    }
    
    /**
     * Show a single match and reuse the index view.
     */
    public function show(ItemMatch $match)
    {
        $this->ensureUserInMatch($match);

            $matches = ItemMatch::with(['lostItem.user', 'foundItem.user'])
            ->where('id', $match->id)
            ->orderByDesc('similarity_score')
            ->get();

        return view('matches.index', ['matches' => $matches]);
    }

    /**
     * Update the match status to notify both users.
     */
    public function notify(ItemMatch $match, NotificationService $notifier)
    {
        $this->ensureUserInMatch($match);

        $match->update(['status' => 'contacted']);
        
        // Build item payload (example for lost item owner)
        $item = [
            'name' => $match->lostItem->title ?? 'Lost Item',
            'description' => $match->lostItem->description ?? '',
            'id' => $match->id,
            'link' => route('matches.show', $match),
        ];

        // Choose recipient(s)
        $recipientEmail = $match->lostItem->user->email ?? null;

        if ($recipientEmail) {
            $notifier->notifyMatch($recipientEmail, $item, [
                'matched_date' => now()->toDateTimeString(),
                'finder_name' => $match->foundItem->user->name ?? null,
            ]);
        }

        // Optionally notify the finder as well
        $finderEmail = $match->foundItem->user->email ?? null;
        if ($finderEmail) {
            $notifier->notifyMatch($finderEmail, $item, [
                'matched_date' => now()->toDateTimeString(),
                'finder_name' => $match->foundItem->user->name ?? null,
            ]);
        }

        return redirect()
            ->route('matches.index')
            ->with('status', 'We notified both parties about this match.');
    }

    protected function ensureUserInMatch(ItemMatch $match)
    {
        $userId = Auth::id();
        if ($match->lostItem->user_id !== $userId && $match->foundItem->user_id !== $userId) {
            abort(403);
        }
    }
}
