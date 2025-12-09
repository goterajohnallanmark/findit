<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReturnRecord extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'returned_by',
        'return_date',
        'return_location',
        'return_method',
        'contact_info',
        'notes',
        'return_photo_path',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function getItemAttribute()
    {
        return $this->lostItem ?? $this->foundItem;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->item?->title;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->item?->description;
    }

    public function getLocationAttribute(): ?string
    {
        return $this->item?->location;
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->item?->category;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->item?->image_url;
    }

    public function getReturnPhotoUrlAttribute(): ?string
    {
        if (! $this->return_photo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->return_photo_path);
    }

    public function getReturnedByNameAttribute(): ?string
    {
        return $this->user?->name;
    }
}
