<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Example Models for Lost & Found Application
 * Create separate files for each model in app/Models/
 */

// ============================================
// LostItem Model
// File: app/Models/LostItem.php
// ============================================
class LostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'lost_date',
        'contact_info',
        'image_url',
        'status',
    ];

    protected $casts = [
        'lost_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who reported this lost item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get matches for this lost item
     */
    public function matches(): HasMany
    {
        return $this->hasMany(Match::class, 'lost_item_id');
    }

    /**
     * Get the return record if item was returned
     */
    public function return()
    {
        return $this->morphOne(ItemReturn::class, 'item');
    }

    /**
     * Scope for active items only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for returned items
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    /**
     * Check if item has been returned
     */
    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    /**
     * Get formatted lost date
     */
    public function getFormattedLostDateAttribute(): string
    {
        return $this->lost_date?->format('F j, Y') ?? '';
    }
}

// ============================================
// FoundItem Model
// File: app/Models/FoundItem.php
// ============================================
class FoundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'found_date',
        'contact_info',
        'image_url',
        'status',
    ];

    protected $casts = [
        'found_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who reported this found item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get matches for this found item
     */
    public function matches(): HasMany
    {
        return $this->hasMany(Match::class, 'found_item_id');
    }

    /**
     * Get the return record if item was returned
     */
    public function return()
    {
        return $this->morphOne(ItemReturn::class, 'item');
    }

    /**
     * Scope for active items only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for claimed items
     */
    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }

    /**
     * Check if item has been claimed
     */
    public function isClaimed(): bool
    {
        return $this->status === 'claimed' || $this->status === 'returned';
    }

    /**
     * Get formatted found date
     */
    public function getFormattedFoundDateAttribute(): string
    {
        return $this->found_date?->format('F j, Y') ?? '';
    }
}

// ============================================
// Match Model
// File: app/Models/Match.php
// ============================================
class Match extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'similarity_score',
        'status',
    ];

    protected $casts = [
        'similarity_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the lost item for this match
     */
    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class, 'lost_item_id');
    }

    /**
     * Get the found item for this match
     */
    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(FoundItem::class, 'found_item_id');
    }

    /**
     * Scope for high confidence matches (80%+)
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('similarity_score', '>=', 80);
    }

    /**
     * Scope for medium confidence matches (60-79%)
     */
    public function scopeMediumConfidence($query)
    {
        return $query->whereBetween('similarity_score', [60, 79]);
    }

    /**
     * Scope for pending matches
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if this is a high confidence match
     */
    public function isHighConfidence(): bool
    {
        return $this->similarity_score >= 80;
    }

    /**
     * Get match confidence level
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->similarity_score >= 80) {
            return 'high';
        } elseif ($this->similarity_score >= 60) {
            return 'medium';
        }
        return 'low';
    }

    /**
     * Get match confidence color for UI
     */
    public function getConfidenceColorAttribute(): string
    {
        return match($this->confidence_level) {
            'high' => 'success',
            'medium' => 'warning',
            'low' => 'secondary',
        };
    }
}

// ============================================
// ItemReturn Model
// File: app/Models/ItemReturn.php
// ============================================
class ItemReturn extends Model
{
    use HasFactory;

    protected $table = 'returns'; // or 'item_returns' if you prefer

    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'return_date',
        'return_location',
        'return_method',
        'contact_info',
        'notes',
        'proof_image',
    ];

    protected $casts = [
        'return_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who completed the return
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item (lost or found) that was returned
     * Polymorphic relationship
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for recent returns
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('return_date', '>=', now()->subDays($days));
    }

    /**
     * Scope for returns this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('return_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope for returns this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('return_date', now()->month)
                    ->whereYear('return_date', now()->year);
    }

    /**
     * Get formatted return date
     */
    public function getFormattedReturnDateAttribute(): string
    {
        return $this->return_date?->format('F j, Y') ?? '';
    }

    /**
     * Check if return has proof image
     */
    public function hasProofImage(): bool
    {
        return !empty($this->proof_image);
    }
}

// ============================================
// User Model Extension
// Add these relationships to your existing User model
// ============================================
/*
class User extends Authenticatable
{
    // ... existing code ...

    // Add these relationships:

    public function lostItems(): HasMany
    {
        return $this->hasMany(LostItem::class);
    }

    public function foundItems(): HasMany
    {
        return $this->hasMany(FoundItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ItemReturn::class);
    }

    // Get total items posted by user
    public function getTotalItemsAttribute(): int
    {
        return $this->lostItems()->count() + $this->foundItems()->count();
    }

    // Get total successful returns by user
    public function getTotalReturnsAttribute(): int
    {
        return $this->returns()->count();
    }

    // Check if user has active lost items
    public function hasActiveLostItems(): bool
    {
        return $this->lostItems()->where('status', 'active')->exists();
    }

    // Check if user has active found items
    public function hasActiveFoundItems(): bool
    {
        return $this->foundItems()->where('status', 'active')->exists();
    }
}
*/

// ============================================
// Additional Helper Traits
// ============================================

/**
 * Trait for models that have images
 * File: app/Traits/HasImages.php
 */
trait HasImages
{
    /**
     * Get the full URL for the image
     */
    public function getImageAttribute(): ?string
    {
        if (!$this->image_url) {
            return null;
        }

        // If already a full URL, return as is
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        // Otherwise, prepend storage URL
        return asset($this->image_url);
    }

    /**
     * Delete the image file
     */
    public function deleteImage(): bool
    {
        if ($this->image_url) {
            $path = str_replace('/storage/', '', $this->image_url);
            return \Storage::disk('public')->delete($path);
        }

        return false;
    }
}

/**
 * Trait for searchable models
 * File: app/Traits/Searchable.php
 */
trait Searchable
{
    /**
     * Search scope for title and description
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Filter by category
     */
    public function scopeCategory($query, $category)
    {
        if (empty($category)) {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Filter by location
     */
    public function scopeLocation($query, $location)
    {
        if (empty($location)) {
            return $query;
        }

        return $query->where('location', 'like', "%{$location}%");
    }
}

// ============================================
// Usage Example
// ============================================
/*

// In your models, use the traits:

class LostItem extends Model
{
    use HasFactory, HasImages, Searchable;
    
    // ... rest of the model
}

class FoundItem extends Model
{
    use HasFactory, HasImages, Searchable;
    
    // ... rest of the model
}

// In your controllers, you can now do:

$items = LostItem::search($request->search)
    ->category($request->category)
    ->location($request->location)
    ->active()
    ->latest()
    ->paginate(12);

*/
