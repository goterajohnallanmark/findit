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
        // Get the image from lost or found item
        $imageUrl = null;
        if ($this->lostItem?->image_path) {
            $imageUrl = url('storage/' . $this->lostItem->image_path);
        } elseif ($this->foundItem?->image_path) {
            $imageUrl = url('storage/' . $this->foundItem->image_path);
        }

        return [
            'id' => $this->id,
            'item_owner_name' => $this->lostItem?->user?->name ?? 'Unknown',
            'finder_name' => $this->foundItem?->user?->name ?? 'Unknown',
            'item_image' => $imageUrl,
            'return_date' => $this->return_date?->format('Y-m-d'),
            'return_time' => $this->created_at->format('H:i:s'),
            'created_at' => $this->created_at,
        ];
    }
}
