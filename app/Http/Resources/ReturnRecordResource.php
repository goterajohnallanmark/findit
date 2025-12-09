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
        if ($this->lostItem && $this->lostItem->image_path) {
            $imageUrl = url('storage/' . $this->lostItem->image_path);
        } elseif ($this->foundItem && $this->foundItem->image_path) {
            $imageUrl = url('storage/' . $this->foundItem->image_path);
        }

        // Get finder name (either from found_item user or returned_by user)
        $finderName = 'Unknown';
        if ($this->foundItem && $this->foundItem->user) {
            $finderName = $this->foundItem->user->name;
        } elseif ($this->user) {
            $finderName = $this->user->name;
        }

        return [
            'id' => $this->id,
            'item_owner_name' => $this->lostItem && $this->lostItem->user ? $this->lostItem->user->name : 'Unknown',
            'finder_name' => $finderName,
            'item_image' => $imageUrl,
            'return_date' => $this->return_date ? $this->return_date->format('Y-m-d') : null,
            'return_time' => $this->created_at->format('H:i:s'),
            'created_at' => $this->created_at,
        ];
    }
}
