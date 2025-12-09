<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Match;
use App\Models\ItemReturn;

/**
 * Example Controllers for Lost & Found Application
 * Copy these methods to your actual controller files
 */

// ============================================
// DashboardController
// ============================================
class DashboardController extends Controller
{
    public function index()
    {
        // Get recent successful returns with relationships
        $returnedItems = ItemReturn::with(['item', 'user'])
            ->latest('return_date')
            ->take(6)
            ->get();
        
        return view('dashboard', compact('returnedItems'));
    }
}

// ============================================
// LostItemController
// ============================================
class LostItemController extends Controller
{
    /**
     * Display a listing of lost items with search/filter
     */
    public function index(Request $request)
    {
        $query = LostItem::with('user')->where('status', 'active');
        
        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        $items = $query->latest()->paginate(12);
        
        return view('lost-items.index', compact('items'));
    }

    /**
     * Show the form for creating a new lost item
     */
    public function create()
    {
        return view('lost-items.create');
    }

    /**
     * Store a newly created lost item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category' => 'required|string|in:electronics,wallet,keys,bag,jewelry,clothing,documents,other',
            'location' => 'required|string|max:255',
            'lost_date' => 'required|date|before_or_equal:today',
            'contact_info' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);
        
        // Handle image upload
        if ($request->hasFile('images')) {
            $image = $request->file('images')[0];
            $path = $image->store('lost-items', 'public');
            $validated['image_url'] = Storage::url($path);
        }
        
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';
        
        $item = LostItem::create($validated);
        
        // Optionally: Trigger AI matching here
        // dispatch(new FindMatches($item));
        
        return redirect()->route('lost-items.index')
            ->with('success', 'Lost item reported successfully! We\'ll notify you if we find a match.');
    }

    /**
     * Display the specified lost item
     */
    public function show($id)
    {
        $item = LostItem::with('user')->findOrFail($id);
        
        return view('lost-items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified lost item
     */
    public function edit($id)
    {
        $item = LostItem::findOrFail($id);
        
        // Ensure user can only edit their own items
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('lost-items.edit', compact('item'));
    }

    /**
     * Update the specified lost item
     */
    public function update(Request $request, $id)
    {
        $item = LostItem::findOrFail($id);
        
        // Ensure user can only edit their own items
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category' => 'required|string|in:electronics,wallet,keys,bag,jewelry,clothing,documents,other',
            'location' => 'required|string|max:255',
            'lost_date' => 'required|date|before_or_equal:today',
            'contact_info' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        // Handle new image upload
        if ($request->hasFile('images')) {
            // Delete old image if exists
            if ($item->image_url) {
                $oldPath = str_replace('/storage/', '', $item->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $image = $request->file('images')[0];
            $path = $image->store('lost-items', 'public');
            $validated['image_url'] = Storage::url($path);
        }
        
        $item->update($validated);
        
        return redirect()->route('lost-items.show', $item->id)
            ->with('success', 'Lost item updated successfully!');
    }

    /**
     * Remove the specified lost item
     */
    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);
        
        // Ensure user can only delete their own items
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete associated image
        if ($item->image_url) {
            $path = str_replace('/storage/', '', $item->image_url);
            Storage::disk('public')->delete($path);
        }
        
        $item->delete();
        
        return redirect()->route('lost-items.index')
            ->with('success', 'Lost item deleted successfully.');
    }

    /**
     * Mark a lost item as found
     */
    public function markFound($id)
    {
        $item = LostItem::findOrFail($id);
        
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $item->update(['status' => 'found']);
        
        return redirect()->route('lost-items.show', $item->id)
            ->with('success', 'Item marked as found!');
    }
}

// ============================================
// FoundItemController
// ============================================
class FoundItemController extends Controller
{
    /**
     * Display a listing of found items
     */
    public function index(Request $request)
    {
        $query = FoundItem::with('user')->where('status', 'active');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        $items = $query->latest()->paginate(12);
        
        return view('found-items.index', compact('items'));
    }

    /**
     * Store a newly created found item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category' => 'required|string|in:electronics,wallet,keys,bag,jewelry,clothing,documents,other',
            'location' => 'required|string|max:255',
            'found_date' => 'required|date|before_or_equal:today',
            'contact_info' => 'required|string|max:255',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        if ($request->hasFile('images')) {
            $image = $request->file('images')[0];
            $path = $image->store('found-items', 'public');
            $validated['image_url'] = Storage::url($path);
        }
        
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';
        
        $item = FoundItem::create($validated);
        
        return redirect()->route('found-items.index')
            ->with('success', 'Found item reported successfully! The owner will be able to find it.');
    }

    // Similar methods as LostItemController (show, edit, update, destroy)
    // ... implementation follows the same pattern
}

// ============================================
// MatchController
// ============================================
class MatchController extends Controller
{
    /**
     * Display AI-powered matches between lost and found items
     */
    public function index()
    {
        $matches = Match::with(['lostItem.user', 'foundItem.user'])
            ->where('similarity_score', '>=', 60) // Only show matches with 60%+ similarity
            ->orderByDesc('similarity_score')
            ->get();
        
        return view('matches.index', compact('matches'));
    }

    /**
     * Show details of a specific match
     */
    public function show($id)
    {
        $match = Match::with(['lostItem.user', 'foundItem.user'])
            ->findOrFail($id);
        
        return view('matches.show', compact('match'));
    }

    /**
     * Notify both parties about a match
     */
    public function notify($id)
    {
        $match = Match::with(['lostItem.user', 'foundItem.user'])
            ->findOrFail($id);
        
        // Send notifications to both parties
        // Notification::send($match->lostItem->user, new MatchFoundNotification($match));
        // Notification::send($match->foundItem->user, new MatchFoundNotification($match));
        
        $match->update(['status' => 'contacted']);
        
        return redirect()->back()
            ->with('success', 'Both parties have been notified about this match!');
    }
}

// ============================================
// ReturnController
// ============================================
class ReturnController extends Controller
{
    /**
     * Display successful returns
     */
    public function index(Request $request)
    {
        $query = ItemReturn::with(['item', 'user']);
        
        if ($request->filled('category')) {
            $query->whereHasMorph('item', [LostItem::class, FoundItem::class], function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }
        
        if ($request->filled('date_from')) {
            $query->where('return_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('return_date', '<=', $request->date_to);
        }
        
        $returns = $query->latest('return_date')->paginate(12);
        
        // Calculate stats
        $totalReturns = ItemReturn::count();
        $returnsThisWeek = ItemReturn::whereBetween('return_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $returnsThisMonth = ItemReturn::whereMonth('return_date', now()->month)->count();
        
        return view('returns.index', compact('returns', 'totalReturns', 'returnsThisWeek', 'returnsThisMonth'));
    }

    /**
     * Show the form for creating a new return
     */
    public function create(Request $request)
    {
        $item = null;
        
        if ($request->filled('item_id') && $request->filled('type')) {
            $itemClass = $request->type === 'lost' ? LostItem::class : FoundItem::class;
            $item = $itemClass::findOrFail($request->item_id);
        }
        
        return view('returns.create', compact('item'));
    }

    /**
     * Store a newly created return
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer',
            'item_type' => 'required|in:lost,found',
            'return_date' => 'required|date|before_or_equal:today',
            'return_location' => 'required|string|max:255',
            'return_method' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        // Handle proof image upload
        if ($request->hasFile('proof_image')) {
            $image = $request->file('proof_image');
            $path = $image->store('returns', 'public');
            $validated['proof_image'] = Storage::url($path);
        }
        
        $validated['user_id'] = auth()->id();
        
        // Get the item and create polymorphic relationship
        $itemClass = $validated['item_type'] === 'lost' ? LostItem::class : FoundItem::class;
        $item = $itemClass::findOrFail($validated['item_id']);
        
        // Create return record
        $return = new ItemReturn($validated);
        $return->item()->associate($item);
        $return->save();
        
        // Update item status to returned
        $item->update(['status' => 'returned']);
        
        return redirect()->route('returns.index')
            ->with('success', 'Return confirmed! Thank you for helping reunite someone with their item.');
    }

    /**
     * Display details of a specific return
     */
    public function show($id)
    {
        $return = ItemReturn::with(['item', 'user'])->findOrFail($id);
        
        return view('returns.show', compact('return'));
    }
}

// ============================================
// SearchController
// ============================================
class SearchController extends Controller
{
    /**
     * Display search page
     */
    public function index()
    {
        return view('search.index');
    }

    /**
     * Handle search results
     */
    public function results(Request $request)
    {
        if (!$request->hasAny(['query', 'type', 'category', 'location', 'date_from', 'date_to'])) {
            return redirect()->route('search.index');
        }
        
        // Initialize results collection
        $results = collect();
        
        // Search lost items
        if (!$request->filled('type') || $request->type === 'lost') {
            $lostQuery = LostItem::where('status', 'active');
            $this->applySearchFilters($lostQuery, $request);
            $lostItems = $lostQuery->get()->map(function($item) {
                $item->type = 'lost';
                $item->date = $item->lost_date;
                return $item;
            });
            $results = $results->concat($lostItems);
        }
        
        // Search found items
        if (!$request->filled('type') || $request->type === 'found') {
            $foundQuery = FoundItem::where('status', 'active');
            $this->applySearchFilters($foundQuery, $request);
            $foundItems = $foundQuery->get()->map(function($item) {
                $item->type = 'found';
                $item->date = $item->found_date;
                return $item;
            });
            $results = $results->concat($foundItems);
        }
        
        // Sort by date descending
        $results = $results->sortByDesc('date');
        
        // Manual pagination
        $perPage = 12;
        $page = $request->get('page', 1);
        $total = $results->count();
        $results = $results->forPage($page, $perPage);
        
        $results = new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => route('search.results')]
        );
        
        return view('search.index', compact('results'));
    }

    /**
     * Apply search filters to query
     */
    private function applySearchFilters($query, $request)
    {
        if ($request->filled('query')) {
            $search = $request->query;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        if ($request->filled('date_from')) {
            $dateField = $query->getModel() instanceof LostItem ? 'lost_date' : 'found_date';
            $query->where($dateField, '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $dateField = $query->getModel() instanceof LostItem ? 'lost_date' : 'found_date';
            $query->where($dateField, '<=', $request->date_to);
        }
        
        return $query;
    }
}
