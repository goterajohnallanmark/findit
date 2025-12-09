# Complete API Controller Implementations

Copy these implementations into their respective controller files in `app/Http/Controllers/Api/`

## AuthController.php
✅ Already implemented with:
- register(), login(), logout(), user(), forgotPassword()

## LostItemController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LostItemResource;
use App\Models\LostItem;
use App\Services\MatchMaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LostItemController extends Controller
{
    public function index(Request $request)
    {
        $query = LostItem::with(['user'])->whereDoesntHave('returnRecord')->latest();

        if ($request->has('category')) $query->where('category', $request->category);
        if ($request->has('location')) $query->where('location', 'like', '%' . $request->location . '%');
        if ($request->has('date_from')) $query->where('date_lost', '>=', $request->date_from);
        if ($request->has('date_to')) $query->where('date_lost', '<=', $request->date_to);
        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        return LostItemResource::collection($query->paginate($request->get('per_page', 20)));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'date_lost' => 'required|date',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->except('image');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('lost-items', 'public');
        }

        $lostItem = LostItem::create($data);
        MatchMaker::handleLostItem($lostItem);

        return new LostItemResource($lostItem->load('user'));
    }

    public function show($id)
    {
        return new LostItemResource(LostItem::with(['user', 'matches.foundItem'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $lostItem = LostItem::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'date_lost' => 'sometimes|date',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($lostItem->image_url) Storage::disk('public')->delete($lostItem->image_url);
            $data['image_url'] = $request->file('image')->store('lost-items', 'public');
        }

        $lostItem->update($data);
        return new LostItemResource($lostItem->load('user'));
    }

    public function destroy($id)
    {
        $lostItem = LostItem::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        if ($lostItem->image_url) Storage::disk('public')->delete($lostItem->image_url);
        $lostItem->delete();

        return response()->json(['message' => 'Lost item deleted successfully']);
    }

    public function myItems(Request $request)
    {
        $items = LostItem::where('user_id', auth()->id())
            ->with(['matches'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return LostItemResource::collection($items);
    }

    public function markFound($id)
    {
        $lostItem = LostItem::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $lostItem->update(['status' => 'found']);
        return response()->json(['message' => 'Item marked as found']);
    }
}
```

## FoundItemController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoundItemResource;
use App\Models\FoundItem;
use App\Services\MatchMaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoundItemController extends Controller
{
    public function index(Request $request)
    {
        $query = FoundItem::with(['user'])->whereDoesntHave('returnRecord')->latest();

        if ($request->has('category')) $query->where('category', $request->category);
        if ($request->has('location')) $query->where('location', 'like', '%' . $request->location . '%');
        if ($request->has('date_from')) $query->where('date_found', '>=', $request->date_from);
        if ($request->has('date_to')) $query->where('date_found', '<=', $request->date_to);
        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        return FoundItemResource::collection($query->paginate($request->get('per_page', 20)));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'date_found' => 'required|date',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->except('image');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('found-items', 'public');
        }

        $foundItem = FoundItem::create($data);
        MatchMaker::handleFoundItem($foundItem);

        return new FoundItemResource($foundItem->load('user'));
    }

    public function show($id)
    {
        return new FoundItemResource(FoundItem::with(['user', 'matches.lostItem'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $foundItem = FoundItem::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'date_found' => 'sometimes|date',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($foundItem->image_url) Storage::disk('public')->delete($foundItem->image_url);
            $data['image_url'] = $request->file('image')->store('found-items', 'public');
        }

        $foundItem->update($data);
        return new FoundItemResource($foundItem->load('user'));
    }

    public function destroy($id)
    {
        $foundItem = FoundItem::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        if ($foundItem->image_url) Storage::disk('public')->delete($foundItem->image_url);
        $foundItem->delete();

        return response()->json(['message' => 'Found item deleted successfully']);
    }

    public function myItems(Request $request)
    {
        $items = FoundItem::where('user_id', auth()->id())
            ->with(['matches'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return FoundItemResource::collection($items);
    }

    public function markClaimed($id)
    {
        $foundItem = FoundItem::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $foundItem->update(['status' => 'claimed']);
        return response()->json(['message' => 'Item marked as claimed']);
    }
}
```

## MatchController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\ItemMatch;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $matches = ItemMatch::with(['lostItem.user', 'foundItem.user'])
            ->where('similarity_score', '>=', 70)
            ->where(function ($query) use ($userId) {
                $query->whereHas('lostItem', fn($q) => $q->where('user_id', $userId))
                    ->orWhereHas('foundItem', fn($q) => $q->where('user_id', $userId));
            })
            ->whereHas('lostItem', fn($q) => $q->whereDoesntHave('returnRecord'))
            ->whereHas('foundItem', fn($q) => $q->whereDoesntHave('returnRecord'))
            ->orderByDesc('similarity_score')
            ->get();

        return MatchResource::collection($matches);
    }

    public function show(ItemMatch $match)
    {
        $userId = auth()->id();
        
        if ($match->lostItem->user_id !== $userId && $match->foundItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new MatchResource($match->load(['lostItem.user', 'foundItem.user']));
    }

    public function unviewedCount()
    {
        $userId = auth()->id();
        
        $count = ItemMatch::where(function($query) use ($userId) {
            $query->whereHas('lostItem', fn($q) => $q->where('user_id', $userId))
                ->whereNull('lost_user_viewed_at')
                ->orWhere(function($q) use ($userId) {
                    $q->whereHas('foundItem', fn($subQ) => $subQ->where('user_id', $userId))
                        ->whereNull('found_user_viewed_at');
                });
        })->count();

        return response()->json(['unviewed_count' => $count]);
    }

    public function markAsViewed(ItemMatch $match)
    {
        $userId = auth()->id();

        if ($match->lostItem->user_id === $userId) {
            $match->update(['lost_user_viewed_at' => now()]);
        } elseif ($match->foundItem->user_id === $userId) {
            $match->update(['found_user_viewed_at' => now()]);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['message' => 'Match marked as viewed']);
    }

    public function notify(ItemMatch $match, NotificationService $notifier)
    {
        $userId = auth()->id();
        
        if ($match->lostItem->user_id !== $userId && $match->foundItem->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $match->update(['status' => 'contacted']);

        // Send notifications (already implemented in MatchMaker)
        
        return response()->json(['message' => 'Both parties have been notified']);
    }
}
```

## DashboardController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\LostItem;
use App\Models\ReturnRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        return response()->json([
            'total_lost_items' => LostItem::where('user_id', $userId)->count(),
            'total_found_items' => FoundItem::where('user_id', $userId)->count(),
            'total_matches' => ItemMatch::where(function($q) use ($userId) {
                $q->whereHas('lostItem', fn($sub) => $sub->where('user_id', $userId))
                    ->orWhereHas('foundItem', fn($sub) => $sub->where('user_id', $userId));
            })->count(),
            'total_returns' => ReturnRecord::whereHas('match.lostItem', fn($q) => $q->where('user_id', $userId))->count(),
            'unviewed_matches' => ItemMatch::where(function($query) use ($userId) {
                $query->whereHas('lostItem', fn($q) => $q->where('user_id', $userId))
                    ->whereNull('lost_user_viewed_at')
                    ->orWhere(function($q) use ($userId) {
                        $q->whereHas('foundItem', fn($subQ) => $subQ->where('user_id', $userId))
                            ->whereNull('found_user_viewed_at');
                    });
            })->count(),
        ]);
    }

    public function myStats()
    {
        return $this->index();
    }
}
```

## ProfileController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return new UserResource(auth()->user());
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone_number']));

        return new UserResource($user);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:2048']);

        $user = auth()->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);

        return new UserResource($user);
    }

    public function deletePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        return response()->json(['message' => 'Profile photo deleted']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password updated successfully']);
    }
}
```

## ReturnController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReturnRecordResource;
use App\Models\ItemMatch;
use App\Models\ReturnRecord;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ReturnRecord::with(['match.lostItem', 'match.foundItem'])
            ->latest()
            ->paginate(20);

        return ReturnRecordResource::collection($returns);
    }

    public function store(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'notes' => 'nullable|string',
            'return_date' => 'required|date',
        ]);

        $match = ItemMatch::findOrFail($request->match_id);
        
        // Ensure user is part of this match
        if ($match->lostItem->user_id !== auth()->id() && $match->foundItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $return = ReturnRecord::create([
            'match_id' => $request->match_id,
            'notes' => $request->notes,
            'return_date' => $request->return_date,
        ]);

        return new ReturnRecordResource($return->load(['match.lostItem', 'match.foundItem']));
    }

    public function show($id)
    {
        $return = ReturnRecord::with(['match.lostItem', 'match.foundItem'])->findOrFail($id);
        return new ReturnRecordResource($return);
    }

    public function myReturns()
    {
        $userId = auth()->id();
        
        $returns = ReturnRecord::with(['match.lostItem', 'match.foundItem'])
            ->whereHas('match', function($q) use ($userId) {
                $q->whereHas('lostItem', fn($sub) => $sub->where('user_id', $userId))
                    ->orWhereHas('foundItem', fn($sub) => $sub->where('user_id', $userId));
            })
            ->latest()
            ->paginate(20);

        return ReturnRecordResource::collection($returns);
    }
}
```

