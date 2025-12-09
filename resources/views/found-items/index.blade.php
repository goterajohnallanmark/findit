@extends('layouts.app')

@section('title', 'Found Items - FindIt')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-2">Found Items</h1>
            <p class="page-subtitle mb-0">Browse items that people have found</p>
        </div>
        <a href="{{ route('found-items.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i> Report Found Item
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('found-items.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-bar">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search found items..."
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
                        <button type="submit" class="btn btn-success w-100">
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
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x200?text=Found+Item' }}" 
                         class="card-img-top item-card-img" 
                         alt="{{ $item->title }}">
                    <span class="badge bg-success status-badge">
                        <i class="bi bi-box-seam me-1"></i> Found
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
                            <small>Found on: {{ $item->found_date?->format('M d, Y') }}</small>
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
                            <a href="{{ route('found-items.show', $item->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#claimModal{{ $item->id }}">
                                <i class="bi bi-hand-thumbs-up me-1"></i> Claim
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Claim Modal for each item -->
        <div class="modal fade" id="claimModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Contact Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="icon-box-lg bg-success bg-opacity-10 text-success mx-auto mb-3">
                                <i class="bi bi-hand-thumbs-up"></i>
                            </div>
                            <h6>Is This Your Item?</h6>
                            <p class="text-muted mb-0">Follow these steps to get your item back.</p>
                        </div>
                        
                        <div class="d-flex gap-3 p-3 bg-light rounded mb-3">
                            <img src="{{ $item->image_url ?? 'https://via.placeholder.com/80' }}" 
                                 alt="{{ $item->title }}" 
                                 class="rounded"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <h6 class="mb-1">{{ $item->title }}</h6>
                                <p class="text-muted small mb-1">Found at: {{ $item->location }}</p>
                                <p class="text-muted small mb-0">Date: {{ $item->found_date?->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="card border-0 bg-primary bg-opacity-10 mb-3">
                            <div class="card-body p-3">
                                <h6 class="mb-3"><i class="bi bi-person-circle me-2"></i>Finder's Contact Information</h6>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Posted by</small>
                                    <strong>{{ $item->user?->name }}</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Contact</small>
                                    <strong class="text-primary">{{ $item->contact_info }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-warning bg-opacity-10">
                            <div class="card-body p-3">
                                <h6 class="mb-2"><i class="bi bi-shield-check me-2"></i>Safety Tips</h6>
                                <ul class="mb-0 small">
                                    <li class="mb-1">Meet in a public, well-lit place</li>
                                    <li class="mb-1">Bring proof of ownership (photos, receipts, serial numbers)</li>
                                    <li class="mb-1">Verify the item's details before meeting</li>
                                    <li class="mb-1">Consider bringing a friend with you</li>
                                    <li class="mb-0">Never share sensitive personal information upfront</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">
                            <i class="bi bi-check-circle me-2"></i> Got It, Thanks!
                        </button>
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
                    <h5 class="text-muted">No found items yet</h5>
                    <p class="text-muted mb-4">Try adjusting your search filters or be the first to report a found item</p>
                    <a href="{{ route('found-items.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i> Report Found Item
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
