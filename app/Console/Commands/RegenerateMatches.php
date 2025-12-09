<?php

namespace App\Console\Commands;

use App\Http\Controllers\AIController;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Services\MatchMaker;
use Illuminate\Console\Command;

class RegenerateMatches extends Command
{
    protected $signature = 'matches:regenerate';
    protected $description = 'Regenerate embeddings and matches for all items';

    public function handle()
    {
        $this->info('Regenerating embeddings and matches...');
        
        // Regenerate embeddings for lost items
        $this->info('Processing lost items...');
        $lostItems = LostItem::all();
        foreach ($lostItems as $item) {
            $embedding = AIController::generateEmbedding(
                trim("{$item->title} {$item->description} {$item->location_lost}")
            );
            
            if ($embedding) {
                $item->embedding = $embedding;
                $item->save();
                $this->line("✓ Lost item #{$item->id}: {$item->title}");
            } else {
                $this->error("✗ Failed to generate embedding for lost item #{$item->id}");
            }
        }

        // Regenerate embeddings for found items
        $this->info('Processing found items...');
        $foundItems = FoundItem::all();
        foreach ($foundItems as $item) {
            $embedding = AIController::generateEmbedding(
                trim("{$item->title} {$item->description} {$item->location_found}")
            );
            
            if ($embedding) {
                $item->embedding = $embedding;
                $item->save();
                $this->line("✓ Found item #{$item->id}: {$item->title}");
            } else {
                $this->error("✗ Failed to generate embedding for found item #{$item->id}");
            }
        }

        // Run matching for all items
        $this->info('Running match maker...');
        
        // Check for potential matches manually
        $this->info('Checking for potential matches...');
        $lostItems = LostItem::whereNotNull('embedding')->get();
        $foundItems = FoundItem::whereNotNull('embedding')->get();
        
        foreach ($lostItems as $lost) {
            foreach ($foundItems as $found) {
                $lostEmbedding = is_string($lost->embedding) ? json_decode($lost->embedding, true) : $lost->embedding;
                $foundEmbedding = is_string($found->embedding) ? json_decode($found->embedding, true) : $found->embedding;
                
                $score = AIController::cosineSimilarity(
                    $lostEmbedding ?? [], 
                    $foundEmbedding ?? []
                );
                $percentage = round($score * 100, 2);
                
                $this->line("Lost #{$lost->id} ({$lost->title}) <-> Found #{$found->id} ({$found->title}): {$percentage}%");
                
                if ($percentage >= 70) {
                    $this->info("  → This is a match! Creating...");
                }
            }
        }
        
        // Now run the actual match maker
        foreach ($lostItems as $lostItem) {
            MatchMaker::handleLostItem($lostItem);
        }
        
        foreach ($foundItems as $foundItem) {
            MatchMaker::handleFoundItem($foundItem);
        }

        $matchCount = \App\Models\ItemMatch::count();
        $this->info("✓ Complete! Created {$matchCount} matches.");
        
        return 0;
    }
}
