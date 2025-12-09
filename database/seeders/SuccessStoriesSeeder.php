<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\ReturnRecord;
use Illuminate\Database\Seeder;

class SuccessStoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        $user1 = User::firstOrCreate(
            ['email' => 'item_owner@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'phone_number' => '+1234567890',
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'finder@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => bcrypt('password'),
                'phone_number' => '+0987654321',
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'another_owner@example.com'],
            [
                'name' => 'Michael Brown',
                'password' => bcrypt('password'),
                'phone_number' => '+1122334455',
            ]
        );

        $user4 = User::firstOrCreate(
            ['email' => 'another_finder@example.com'],
            [
                'name' => 'Sarah Johnson',
                'password' => bcrypt('password'),
                'phone_number' => '+5544332211',
            ]
        );

        // Create lost items
        $lostItem1 = LostItem::firstOrCreate(
            ['title' => 'iPhone 14 Pro', 'user_id' => $user1->id],
            [
                'description' => 'Silver iPhone 14 Pro with a crack on the right side',
                'category' => 'electronics',
                'location_lost' => 'City Mall parking lot',
                'lost_date' => now()->subDays(5)->toDateString(),
                'contact_info' => '+1234567890',
                'image_path' => 'items/iphone.jpg',
            ]
        );

        $lostItem2 = LostItem::firstOrCreate(
            ['title' => 'Black Leather Wallet', 'user_id' => $user3->id],
            [
                'description' => 'Black leather wallet with credit cards and ID',
                'category' => 'wallet',
                'location_lost' => 'Restaurant downtown',
                'lost_date' => now()->subDays(3)->toDateString(),
                'contact_info' => '+1122334455',
                'image_path' => 'items/wallet.jpg',
            ]
        );

        // Create found items
        $foundItem1 = FoundItem::firstOrCreate(
            ['title' => 'iPhone found at mall', 'user_id' => $user2->id],
            [
                'description' => 'Found a silver iPhone 14 Pro at the mall parking lot',
                'category' => 'electronics',
                'location_found' => 'City Mall parking lot',
                'found_date' => now()->subDays(5)->toDateString(),
                'contact_info' => '+0987654321',
                'image_path' => 'items/iphone_found.jpg',
            ]
        );

        $foundItem2 = FoundItem::firstOrCreate(
            ['title' => 'Wallet found at restaurant', 'user_id' => $user4->id],
            [
                'description' => 'Found a black leather wallet with cards inside',
                'category' => 'wallet',
                'location_found' => 'Restaurant downtown',
                'found_date' => now()->subDays(3)->toDateString(),
                'contact_info' => '+5544332211',
                'image_path' => 'items/wallet_found.jpg',
            ]
        );

        // Create return records (success stories)
        ReturnRecord::firstOrCreate(
            [
                'lost_item_id' => $lostItem1->id,
                'found_item_id' => $foundItem1->id,
            ],
            [
                'returned_by' => $user2->id,
                'return_date' => now()->subDays(1)->toDateString(),
                'return_location' => 'City Mall',
                'return_method' => 'direct',
                'contact_info' => '+0987654321',
                'notes' => 'Item was successfully returned to the owner in perfect condition.',
                'return_photo_path' => null,
            ]
        );

        ReturnRecord::firstOrCreate(
            [
                'lost_item_id' => $lostItem2->id,
                'found_item_id' => $foundItem2->id,
            ],
            [
                'returned_by' => $user4->id,
                'return_date' => now()->toDateString(),
                'return_location' => 'Restaurant downtown',
                'return_method' => 'direct',
                'contact_info' => '+5544332211',
                'notes' => 'Wallet returned with all contents intact.',
                'return_photo_path' => null,
            ]
        );
    }
}
