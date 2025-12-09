@extends('layouts.app')

@section('title', 'Lost Items - FindIt')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-2">Lost Items</h1>
            <p class="page-subtitle mb-0">Browse items that people have lost</p>
        </div>
        <a href="{{ route('lost-items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Report Lost Item
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('lost-items.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-bar">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search lost items..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="electronics" {{ request('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="wallet" {{ request('category') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="keys" {{ request('category') == 'keys' ? 'selected' : '' }}>Keys</option>
                            <option value="bag" {{ request('category') == 'bag' ? 'selected' : '' }}>Bag</option>
                            <option value="jewelry" {{ request('category') == 'jewelry' ? 'selected' : '' }}>Jewelry</option>
                            <option value="clothing" {{ request('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                            <option value="documents" {{ request('category') == 'documents' ? 'selected' : '' }}>Documents</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" 
                               name="location" 
                               class="form-control" 
                               placeholder="Location"
                               value="{{ request('location') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="row g-4 mb-5">
        @forelse($items ?? [] as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x200?text=Lost+Item' }}" 
                         class="card-img-top item-card-img" 
                         alt="{{ $item->title }}">
                    <span class="badge bg-danger status-badge">
                        <i class="bi bi-search me-1"></i> Lost
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
                            <small>Lost on: {{ $item->lost_date?->format('M d, Y') }}</small>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-2">
                            <i class="bi bi-person me-2"></i>
                            <small>Posted by: {{ $item->user?->name }}</small>
                        </div>
                    </div>

                    <div class="pt-3 border-top mb-3">
                        <span class="badge bg-light text-dark">{{ ucfirst($item->category) }}</span>
                    </div>

                    <div class="mt-auto">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('lost-items.show', $item->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#returnModal{{ $item->id }}">
                                <i class="bi bi-check-circle me-1"></i> Return
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Modal for each item -->
        <div class="modal fade" id="returnModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Return This Item?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Do you have this item and want to return it to the owner?</p>
                        <div class="d-flex gap-3 mt-3">
                            <img src="{{ $item->image_url ?? 'https://via.placeholder.com/100' }}" 
                                 alt="{{ $item->title }}" 
                                 class="rounded"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                            <div>
                                <h6>{{ $item->title }}</h6>
                                <p class="text-muted mb-0">{{ $item->location }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="{{ route('returns.create', ['item_id' => $item->id, 'type' => 'lost']) }}" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Yes, I Have It
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
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h5 class="text-muted">No lost items found</h5>
                    <p class="text-muted mb-4">Try adjusting your search filters or be the first to report a lost item</p>
                    <a href="{{ route('lost-items.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i> Report Lost Item
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($items) && $items->hasPages())
    <div class="d-flex justify-content-center">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
