<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AIController;
use App\Models\FoundItem;
use App\Services\MatchMaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoundItemController extends Controller
{
    public function index(Request $request)
    {
        $items = FoundItem::with('user')
            ->whereDoesntHave('returnRecord')
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->location, function ($query, $location) {
                return $query->where('location_found', 'like', "%{$location}%");
            })
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('found-items.index', compact('items'));
    }

    public function create()
    {
        return view('found-items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'category' => 'required|string|max:191',
            'description' => 'required|string',
            'location' => 'required|string|max:191',
            'found_date' => 'required|date|before_or_equal:today',
            'images.*' => 'nullable|image|max:5120',
            'contact_info' => 'required|string|max:255',
        ]);

        $data['user_id'] = Auth::id();
        $data['location_found'] = $data['location'];
        unset($data['location']);
        $data['found_date'] = $request->input('found_date');

        if ($request->hasFile('images')) {
            $firstImage = collect($request->file('images'))->first();
            if ($firstImage) {
                $data['image_path'] = $firstImage->store('found-items', 'public');
            }
        }

        $data['embedding'] = AIController::generateEmbedding(
            trim("{$data['title']} {$data['description']} {$data['location_found']}")
        );

        $foundItem = FoundItem::create($data);

        MatchMaker::handleFoundItem($foundItem);

        return redirect()
            ->route('found-items.show', $foundItem)
            ->with('success', 'Found item reported successfully! We will check for possible matches.');
    }

    public function show(FoundItem $foundItem)
    {
        return view('found-items.show', ['item' => $foundItem]);
    }

    public function edit(FoundItem $foundItem)
    {
        $this->authorizeOwner($foundItem);

        return view('found-items.edit', ['item' => $foundItem]);
    }

    public function update(Request $request, FoundItem $foundItem)
    {
        $this->authorizeOwner($foundItem);

        $data = $request->validate([
            'title' => 'required|string|max:191',
            'category' => 'required|string|max:191',
            'description' => 'required|string',
            'location' => 'required|string|max:191',
            'found_date' => 'required|date|before_or_equal:today',
            'images.*' => 'nullable|image|max:5120',
            'contact_info' => 'required|string|max:255',
        ]);

        $data['location_found'] = $data['location'];
        unset($data['location']);
        $data['found_date'] = $request->input('found_date');

        if ($request->hasFile('images')) {
            $firstImage = collect($request->file('images'))->first();
            if ($firstImage) {
                if ($foundItem->image_path) {
                    Storage::disk('public')->delete($foundItem->image_path);
                }

                $data['image_path'] = $firstImage->store('found-items', 'public');
            }
        }

        $data['embedding'] = AIController::generateEmbedding(
            trim("{$data['title']} {$data['description']} {$data['location_found']}")
        );

        $foundItem->update($data);

        MatchMaker::handleFoundItem($foundItem);

        return redirect()
            ->route('found-items.show', $foundItem)
            ->with('success', 'Found item updated successfully!');
    }

    public function destroy(FoundItem $foundItem)
    {
        $this->authorizeOwner($foundItem);

        if ($foundItem->image_path) {
            Storage::disk('public')->delete($foundItem->image_path);
        }

        $foundItem->delete();

        return redirect()
            ->route('found-items.index')
            ->with('success', 'Found item deleted successfully.');
    }

    protected function authorizeOwner(FoundItem $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403);
        }
    }
}
