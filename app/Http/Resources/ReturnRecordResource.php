<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_owner_name' => $this->lostItem?->user?->name ?? 'Unknown',
            'finder_name' => $this->foundItem?->user?->name ?? 'Unknown',
            'item_image' => $this->lostItem?->image_path ? asset('storage/' . $this->lostItem->image_path) : ($this->foundItem?->image_path ? asset('storage/' . $this->foundItem->image_path) : null),
            'return_date' => $this->return_date,
            'return_time' => $this->created_at->format('H:i:s'),
            'created_at' => $this->created_at,
        ];
    }
}
