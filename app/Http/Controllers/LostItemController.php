<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AIController;
use App\Models\LostItem;
use App\Services\MatchMaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LostItemController extends Controller
{
    public function index(Request $request)
    {
        $items = LostItem::with('user')
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
                return $query->where('location_lost', 'like', "%{$location}%");
            })
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('lost-items.index', compact('items'));
    }

    public function create()
    {
        return view('lost-items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'category' => 'required|string|max:191',
            'description' => 'required|string',
            'location' => 'required|string|max:191',
            'lost_date' => 'required|date|before_or_equal:today',
            'images.*' => 'nullable|image|max:5120',
            'contact_info' => 'required|string|max:255',
        ]);

        $data['user_id'] = Auth::id();
        $data['location_lost'] = $data['location'];
        unset($data['location']);
        $data['lost_date'] = $request->input('lost_date');

        if ($request->hasFile('images')) {
            $firstImage = collect($request->file('images'))->first();
            if ($firstImage) {
                $data['image_path'] = $firstImage->store('lost-items', 'public');
            }
        }

        $data['embedding'] = AIController::generateEmbedding(
            trim("{$data['title']} {$data['description']} {$data['location_lost']}")
        );

        $lostItem = LostItem::create($data);

        MatchMaker::handleLostItem($lostItem);

        return redirect()
            ->route('lost-items.show', $lostItem)
            ->with('success', 'Lost item reported successfully! We will notify you if we find a match.');
    }

    public function show(LostItem $lostItem)
    {
        return view('lost-items.show', ['item' => $lostItem]);
    }

    public function edit(LostItem $lostItem)
    {
        $this->authorizeOwner($lostItem);

        return view('lost-items.edit', ['item' => $lostItem]);
    }

    public function update(Request $request, LostItem $lostItem)
    {
        $this->authorizeOwner($lostItem);

        $data = $request->validate([
            'title' => 'required|string|max:191',
            'category' => 'required|string|max:191',
            'description' => 'required|string',
            'location' => 'required|string|max:191',
            'lost_date' => 'required|date|before_or_equal:today',
            'images.*' => 'nullable|image|max:5120',
            'contact_info' => 'required|string|max:255',
        ]);

        $data['location_lost'] = $data['location'];
        unset($data['location']);
        $data['lost_date'] = $request->input('lost_date');

        if ($request->hasFile('images')) {
            $firstImage = collect($request->file('images'))->first();
            if ($firstImage) {
                if ($lostItem->image_path) {
                    Storage::disk('public')->delete($lostItem->image_path);
                }

                $data['image_path'] = $firstImage->store('lost-items', 'public');
            }
        }

        $data['embedding'] = AIController::generateEmbedding(
            trim("{$data['title']} {$data['description']} {$data['location_lost']}")
        );

        $lostItem->update($data);

        MatchMaker::handleLostItem($lostItem);

        return redirect()
            ->route('lost-items.show', $lostItem)
            ->with('success', 'Lost item updated successfully!');
    }

    public function destroy(LostItem $lostItem)
    {
        $this->authorizeOwner($lostItem);

        if ($lostItem->image_path) {
            Storage::disk('public')->delete($lostItem->image_path);
        }

        $lostItem->delete();

        return redirect()
            ->route('lost-items.index')
            ->with('success', 'Lost item deleted successfully.');
    }

    protected function authorizeOwner(LostItem $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403);
        }
    }
}
