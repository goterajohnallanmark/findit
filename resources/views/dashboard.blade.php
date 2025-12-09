@extends('layouts.app')

@section('title', 'Dashboard - FindIt')

@section('content')
<style>
    .typewriter-dash {
        display: inline-block;
        border-right: 3px solid #3b82f6;
        padding-right: 5px;
    }
</style>

<div class="container">
    <!-- Introduction Section -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h1 class="display-3 fw-bold mb-2">
                    <span id="typewriterDash" class="typewriter-dash"></span>
                </h1>
                <p class="fs-5 mb-0">Helping reunite people with their lost belongings</p>
            </div>
        </div>
        
        <div class="card" style="background: rgba(59, 130, 246, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(59, 130, 246, 0.3);">
            <div class="card-body p-4 p-lg-5">
                <h3 class="h4 mb-4 text-primary fw-bold">How It Works</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="icon-box bg-primary bg-opacity-25 text-primary flex-shrink-0">
                                <i class="bi bi-search"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-2">Report Lost Items</h5>
                                <p class="text-muted mb-0">Lost something? Post details and photos to help others identify your item.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="icon-box bg-primary bg-opacity-25 text-primary flex-shrink-0">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-2">Report Found Items</h5>
                                <p class="text-muted mb-0">Found something? Post it here and help return it to its rightful owner.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="icon-box bg-primary bg-opacity-25 text-primary flex-shrink-0">
                                <i class="bi bi-cpu"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-2">AI-Powered Matching</h5>
                                <p class="text-muted mb-0">Our intelligent AI system analyzes descriptions and text using advanced embeddings to automatically match lost items with found items based on similarity scores.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Stories Section -->
    <div class="mb-3">
        <h2 class="h3 mb-2">Recent Success Stories</h2>
        <p class="text-muted">Items that have been successfully returned to their owners</p>
    </div>

    <!-- Returned Items Grid -->
    <div class="row g-3 mb-5">
        @forelse($returnedItems ?? [] as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x200?text=Item+Image' }}" 
                         class="card-img-top item-card-img" 
                         alt="{{ $item->title }}">
                    <span class="badge bg-success status-badge">
                        <i class="bi bi-check-circle me-1"></i> Returned
                    </span>
                </div>
                
                <div class="card-body p-3">
                    <h5 class="card-title mb-2">{{ $item->title }}</h5>
                    <p class="card-text text-muted mb-2">{{ Str::limit($item->description, 100) }}</p>
                    
                    <div class="mb-2">
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-geo-alt me-2"></i>
                            <small>{{ $item->location }}</small>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-calendar me-2"></i>
                            <small>Returned: {{ $item->return_date?->format('M d, Y') }}</small>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-person me-2"></i>
                            <small>By: {{ $item->user?->name ?? 'Unknown' }}</small>
                        </div>
                    </div>

                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-dark">{{ $item->category }}</span>
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
                    <h5 class="text-muted">No returned items yet</h5>
                    <p class="text-muted mb-0">Success stories will appear here once items are returned</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- View More Button -->
    @if(count($returnedItems ?? []) > 0)
    <div class="text-center mb-5">
        <a href="{{ route('returns.index') }}" class="btn btn-outline-primary btn-lg">
            View All Returns
        </a>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-search"></i>
                        </div>
                        <div>
                            <h4 class="h5 mb-1">Lost Something?</h4>
                            <p class="text-muted mb-0">Report your lost item</p>
                        </div>
                    </div>
                    <a href="{{ route('lost-items.create') }}" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-plus-circle me-2"></i> Post Lost Item
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h4 class="h5 mb-1">Found Something?</h4>
                            <p class="text-muted mb-0">Help return an item</p>
                        </div>
                    </div>
                    <a href="{{ route('found-items.create') }}" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-plus-circle me-2"></i> Post Found Item
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const textDash = "Welcome to FindIt";
    const typewriterElementDash = document.getElementById('typewriterDash');
    let indexDash = 0;
    
    function typeWriterDash() {
        if (indexDash < textDash.length) {
            typewriterElementDash.textContent += textDash.charAt(indexDash);
            indexDash++;
            setTimeout(typeWriterDash, 100);
        } else {
            setTimeout(() => {
                typewriterElementDash.textContent = '';
                indexDash = 0;
                typeWriterDash();
            }, 3000);
        }
    }
    
    typeWriterDash();
</script>
@endsection
