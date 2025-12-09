<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemMatch;

class LostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location_lost',
        'lost_date',
        'image_path',
        'embedding',
        'contact_info',
    ];

    protected $casts = [
        'embedding' => 'array',
        'lost_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function match()
    {
        return $this->hasOne(ItemMatch::class);
    }

    public function returnRecord()
    {
        return $this->hasOne(ReturnRecord::class, 'lost_item_id');
    }

    public function getLocationAttribute(): ?string
    {
        return $this->location_lost;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
