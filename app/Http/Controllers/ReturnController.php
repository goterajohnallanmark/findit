<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\ItemMatch;
use App\Models\ReturnRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturnRecord::with(['user', 'lostItem', 'foundItem']);

        if ($request->filled('category')) {
            $category = $request->category;
            $query->where(function ($sub) use ($category) {
                $sub->whereHas('lostItem', function ($builder) use ($category) {
                    return $builder->where('category', $category);
                })
                    ->orWhereHas('foundItem', function ($builder) use ($category) {
                        return $builder->where('category', $category);
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->where('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('return_date', '<=', $request->date_to);
        }

        $returns = $query->orderByDesc('return_date')->paginate(9)->withQueryString();

        $totalReturns = ReturnRecord::count();
        $returnsThisWeek = ReturnRecord::whereBetween('return_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $returnsThisMonth = ReturnRecord::whereBetween('return_date', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return view('returns.index', compact('returns', 'totalReturns', 'returnsThisWeek', 'returnsThisMonth'));
    }

    public function create(Request $request)
    {
        $item = null;

        if ($request->input('type') === 'found') {
            $item = FoundItem::find($request->input('item_id'));
        } elseif ($request->filled('item_id')) {
            $item = LostItem::find($request->input('item_id'));
        }

        return view('returns.create', compact('item'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'nullable|integer',
            'item_type' => 'nullable|string',
            'return_date' => 'required|date',
            'return_location' => 'required|string|max:191',
            'return_method' => 'required|string|max:191',
            'contact_info' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'proof_image' => 'nullable|image|max:5120',
        ]);

        $lostItem = null;
        $foundItem = null;

        if ($data['item_type'] === 'found') {
            $foundItem = FoundItem::find($data['item_id']);
        } elseif (! empty($data['item_id'])) {
            $lostItem = LostItem::find($data['item_id']);
        }

        if (! $lostItem && ! $foundItem) {
            abort(404);
        }

        $payload = [
            'lost_item_id' => isset($lostItem) ? $lostItem->id : null,
            'found_item_id' => isset($foundItem) ? $foundItem->id : null,
            'returned_by' => Auth::id(),
            'return_date' => $data['return_date'],
            'return_location' => $data['return_location'],
            'return_method' => $data['return_method'],
            'contact_info' => $data['contact_info'],
            'notes' => isset($data['notes']) ? $data['notes'] : null,
        ];

        if ($request->hasFile('proof_image')) {
            $payload['return_photo_path'] = $request->file('proof_image')->store('returns', 'public');
        }

        $returnRecord = ReturnRecord::create($payload);

        $matchQuery = ItemMatch::query();

        if ($lostItem) {
            $matchQuery->where('lost_item_id', $lostItem->id);
        }

        if ($foundItem) {
            $matchQuery->where('found_item_id', $foundItem->id);
        }

        $match = $matchQuery->first();

        if ($match) {
            $match->update(['status' => 'resolved']);
        }

        return redirect()
            ->route('returns.index')
            ->with('success', 'Return recorded successfully! Thank you for helping reunite this item with its owner.');
    }
}
