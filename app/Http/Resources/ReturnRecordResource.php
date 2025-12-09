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
            'item_owner_name' => $this->match->lostItem->user->name,
            'finder_name' => $this->match->foundItem->user->name,
            'item_image' => $this->match->lostItem->image_url ? asset('storage/' . $this->match->lostItem->image_url) : null,
            'return_date' => $this->return_date,
            'return_time' => $this->created_at->format('H:i:s'),
            'created_at' => $this->created_at,
        ];
    }
}
