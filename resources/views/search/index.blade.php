@extends('layouts.app')

@section('title', 'Search Items - FindIt')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Search Items</h1>
        <p class="page-subtitle mb-0">Search through all lost and found items</p>
    </div>

    <!-- Advanced Search Form -->
    <div class="card mb-5">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('search.results') }}" method="GET">
                <div class="row g-4">
                    <!-- Search Query -->
                    <div class="col-12">
                        <label for="query" class="form-label">Search Keywords</label>
                        <div class="search-bar">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" 
                                   name="query" 
                                   id="query"
                                   class="form-control" 
                                   placeholder="Search by title, description, or keywords..."
                                   value="{{ request('query') }}">
                        </div>
                    </div>

                    <!-- Item Type -->
                    <div class="col-md-6">
                        <label for="type" class="form-label">Item Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Items</option>
                            <option value="lost" {{ request('type') == 'lost' ? 'selected' : '' }}>Lost Items Only</option>
                            <option value="found" {{ request('type') == 'found' ? 'selected' : '' }}>Found Items Only</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="electronics" {{ request('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="wallet" {{ request('category') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="keys" {{ request('category') == 'keys' ? 'selected' : '' }}>Keys</option>
                            <option value="bag" {{ request('category') == 'bag' ? 'selected' : '' }}>Bag/Backpack</option>
                            <option value="jewelry" {{ request('category') == 'jewelry' ? 'selected' : '' }}>Jewelry</option>
                            <option value="clothing" {{ request('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                            <option value="documents" {{ request('category') == 'documents' ? 'selected' : '' }}>Documents</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="col-md-6">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" 
                               name="location" 
                               id="location"
                               class="form-control" 
                               placeholder="City, Area, or Landmark"
                               value="{{ request('location') }}">
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" 
                               name="date_from" 
                               id="date_from"
                               class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" 
                               name="date_to" 
                               id="date_to"
                               class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>

                    <!-- Search Button -->
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search me-2"></i> Search
                            </button>
                            <a href="{{ route('search.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-2"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    @if(request()->has('query') || request()->has('type') || request()->has('category'))
    <div class="mb-4">
        <h4 class="mb-3">
            Search Results 
            @if(isset($results))
            <span class="text-muted">({{ $results->total() }} items found)</span>
            @endif
        </h4>
    </div>

    <div class="row g-4 mb-5">
        @forelse($results ?? [] as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x200?text=Item' }}" 
                         class="card-img-top item-card-img" 
                         alt="{{ $item->title }}">
                    <span class="badge {{ $item->type == 'lost' ? 'bg-danger' : 'bg-success' }} status-badge">
                        <i class="bi {{ $item->type == 'lost' ? 'bi-search' : 'bi-box-seam' }} me-1"></i> 
                        {{ ucfirst($item->type) }}
                    </span>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-2">{{ $item->title }}</h5>
                    <p class="card-text text-muted mb-3">{{ Str::limit($item->description, 100) }}</p>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center text-muted mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            <small>{{ $item->location }}</small>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-2">
                            <i class="bi bi-calendar me-2"></i>
                            <small>{{ $item->date?->format('M d, Y') }}</small>
                        </div>
                    </div>

                    <div class="pt-3 border-top mb-3">
                        <span class="badge bg-light text-dark">{{ ucfirst($item->category) }}</span>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ $item->type == 'lost' ? route('lost-items.show', $item->id) : route('found-items.show', $item->id) }}" 
                           class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="icon-box-lg bg-light text-muted mx-auto mb-3">
                        <i class="bi bi-search"></i>
                    </div>
                    <h5 class="text-muted">No Results Found</h5>
                    <p class="text-muted mb-0">Try adjusting your search criteria or browse all items</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($results) && $results->hasPages())
    <div class="d-flex justify-content-center">
        {{ $results->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <!-- Browse All Items -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5 text-center">
                    <div class="icon-box-lg bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4 class="mb-3">Browse Lost Items</h4>
                    <p class="text-muted mb-4">View all items people are looking for</p>
                    <a href="{{ route('lost-items.index') }}" class="btn btn-danger">
                        <i class="bi bi-arrow-right me-2"></i> View Lost Items
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5 text-center">
                    <div class="icon-box-lg bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h4 class="mb-3">Browse Found Items</h4>
                    <p class="text-muted mb-4">View all items waiting to be claimed</p>
                    <a href="{{ route('found-items.index') }}" class="btn btn-success">
                        <i class="bi bi-arrow-right me-2"></i> View Found Items
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
