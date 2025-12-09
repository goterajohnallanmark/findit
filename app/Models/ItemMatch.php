<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'similarity_score',
        'status',
        'lost_user_viewed_at',
        'found_user_viewed_at',
    ];

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function getLost_itemAttribute()
    {
        return $this->getRelationValue('lostItem');
    }

    public function getFound_itemAttribute()
    {
        return $this->getRelationValue('foundItem');
    }

    public function scopeUnviewedByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->whereHas('lostItem', function($subQ) use ($userId) {
                $subQ->where('user_id', $userId);
            })->whereNull('lost_user_viewed_at')
            ->orWhereHas('foundItem', function($subQ) use ($userId) {
                $subQ->where('user_id', $userId);
            })->whereNull('found_user_viewed_at');
        });
    }
}
