<?php

namespace App\Services;

use App\Http\Controllers\AIController;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\ItemMatch;

class MatchMaker
{
    public static function handleLostItem(LostItem $lostItem)
    {
        if (empty($lostItem->embedding)) {
            return;
        }

        // Don't match if this lost item already has a return record
        if ($lostItem->returnRecord()->exists()) {
            return;
        }

        $foundItems = FoundItem::whereNotNull('embedding')
            ->whereDoesntHave('returnRecord')
            ->get();

        foreach ($foundItems as $foundItem) {
            static::attemptMatch($lostItem, $foundItem);
        }
    }

    public static function handleFoundItem(FoundItem $foundItem)
    {
        if (empty($foundItem->embedding)) {
            return;
        }

        // Don't match if this found item already has a return record
        if ($foundItem->returnRecord()->exists()) {
            return;
        }

        $lostItems = LostItem::whereNotNull('embedding')
            ->whereDoesntHave('returnRecord')
            ->get();

        foreach ($lostItems as $lostItem) {
            static::attemptMatch($lostItem, $foundItem);
        }
    }

    protected static function attemptMatch(LostItem $lostItem, FoundItem $foundItem)
    {
        if (empty($foundItem->embedding)) {
            return;
        }

        $score = AIController::cosineSimilarity($lostItem->embedding ?? [], $foundItem->embedding ?? []);

        if ($score < 0.7) {
            return;
        }

        $matchData = [
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
        ];

        $match = ItemMatch::firstOrNew($matchData);
        $isNew = ! $match->exists;
        $match->similarity_score = round($score * 100, 2);

        if ($isNew) {
            $match->status = 'pending';
        }

        $match->save();

        if ($isNew) {
            AIController::dispatchMatchWebhooks($match);
            
            // Send email notifications to both parties
            static::sendMatchNotifications($match);
        }
    }
    
    protected static function sendMatchNotifications(ItemMatch $match)
    {
        $notifier = app(NotificationService::class);
        
        // Notify the lost item owner
        if ($match->lostItem && $match->lostItem->user) {
            $item = [
                'name' => $match->lostItem->title ?? 'Lost Item',
                'description' => $match->lostItem->description ?? '',
                'id' => $match->id,
                'link' => route('matches.show', $match),
            ];
            
            $notifier->notifyMatch(
                $match->lostItem->user->email,
                $item,
                [
                    'matched_date' => now()->toDateTimeString(),
                    'finder_name' => $match->foundItem->user->name ?? 'Someone',
                ]
            );
        }
        
        // Notify the found item owner
        if ($match->foundItem && $match->foundItem->user) {
            $item = [
                'name' => $match->foundItem->title ?? 'Found Item',
                'description' => $match->foundItem->description ?? '',
                'id' => $match->id,
                'link' => route('matches.show', $match),
            ];
            
            $notifier->notifyMatch(
                $match->foundItem->user->email,
                $item,
                [
                    'matched_date' => now()->toDateTimeString(),
                    'finder_name' => $match->lostItem->user->name ?? 'Someone',
                ]
            );
        }
    }
}
