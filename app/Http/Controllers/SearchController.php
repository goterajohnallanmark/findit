<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;
use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }

    public function results(Request $request)
    {
        $results = $this->buildResults($request);

        return view('search.index', ['results' => $results]);
    }

    protected function buildResults(Request $request): LengthAwarePaginator
    {
        $lostItems = LostItem::with('user')
            ->when($request->filled('search'), function ($query, $search) {
                return $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->filled('location'), function ($query, $location) {
                return $query->where('location_lost', 'like', "%{$location}%");
            })
            ->when($request->filled('date_from'), function ($query, $date) {
                return $query->where('lost_date', '>=', $date);
            })
            ->when($request->filled('date_to'), function ($query, $date) {
                return $query->where('lost_date', '<=', $date);
            })
            ->get();

        $foundItems = FoundItem::with('user')
            ->when($request->filled('search'), function ($query, $search) {
                return $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->filled('location'), function ($query, $location) {
                return $query->where('location_found', 'like', "%{$location}%");
            })
            ->when($request->filled('date_from'), function ($query, $date) {
                return $query->where('found_date', '>=', $date);
            })
            ->when($request->filled('date_to'), function ($query, $date) {
                return $query->where('found_date', '<=', $date);
            })
            ->get();

        $collection = collect();

        if ($request->input('type') !== 'found') {
            $collection = $collection->merge($lostItems->map(function ($item) {
                return $this->toResult($item, 'lost');
            }));
        }

        if ($request->input('type') !== 'lost') {
            $collection = $collection->merge($foundItems->map(function ($item) {
                return $this->toResult($item, 'found');
            }));
        }

        $sorted = $collection->sortByDesc('date')->values();

        $perPage = 9;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginated;
    }

    protected function toResult($item, string $type): object
    {
        return (object) [
            'id' => $item->id,
            'type' => $type,
            'title' => $item->title,
            'description' => $item->description,
            'category' => $item->category,
            'location' => $item->location,
            'date' => $type === 'lost' ? $item->lost_date : $item->found_date,
            'image_url' => $item->image_url,
        ];
    }
}
