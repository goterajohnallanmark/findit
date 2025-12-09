@extends('layouts.app')

@section('title', 'Successful Returns - FindIt')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="mb-3">
        <div class="d-flex align-items-center mb-2">
            <div>
                <h1 class="page-title mb-1">Successful Returns</h1>
                <p class="page-subtitle mb-0">Celebrating items reunited with their owners</p>
            </div>
        </div>
    </div>

    <!-- Success Stories Grid -->
    <div class="row g-3 mb-3">
        @forelse($returns ?? [] as $return)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ $return->return_photo_url ?? $return->item->image_url ?? 'https://via.placeholder.com/400x200?text=Returned+Item' }}" 
                         class="card-img-top item-card-img" 
                         alt="{{ $return->item->title }}">
                    <span class="badge bg-success status-badge">
                        <i class="bi bi-check-circle me-1"></i> Returned
                    </span>
                </div>
                
                <div class="card-body p-3">
                    <h5 class="card-title mb-2">{{ $return->item->title }}</h5>
                    <p class="card-text text-muted mb-2">{{ Str::limit($return->notes ?? $return->item->description, 100) }}</p>
                    
                    <div class="mb-2">
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-geo-alt me-2"></i>
                            <small>{{ $return->return_location }}</small>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-calendar me-2"></i>
                            <small>Returned: {{ $return->return_date?->format('M d, Y') }}</small>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-person me-2"></i>
                            <small>By: {{ $return->user?->name }}</small>
                        </div>
                        @if($return->return_method)
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            <small>Method: {{ $return->return_method }}</small>
                        </div>
                        @endif
                    </div>

                    <div class="pt-2 border-top">
                        <span class="badge bg-light text-dark">{{ ucfirst($return->item->category) }}</span>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-3">
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $return->id }}">
                        <i class="bi bi-eye me-1"></i> View Details
                    </button>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div class="modal fade" id="detailsModal{{ $return->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Return Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="{{ $return->return_photo_url ?? $return->item->image_url ?? 'https://via.placeholder.com/400' }}" 
                                     alt="{{ $return->item->title }}" 
                                     class="img-fluid rounded mb-3">
                                @if($return->return_photo_url)
                                <p class="text-muted small"><i class="bi bi-camera me-1"></i> Proof of return photo</p>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <h4 class="mb-3">{{ $return->item->title }}</h4>
                                <p class="text-muted mb-4">{{ $return->item->description }}</p>
                                
                                <h6 class="mb-3">Return Information</h6>
                                <div class="mb-3">
                                    <div class="d-flex mb-2">
                                        <i class="bi bi-geo-alt me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Return Location</small>
                                            <strong>{{ $return->return_location }}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <i class="bi bi-calendar me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Return Date</small>
                                            <strong>{{ $return->return_date?->format('M d, Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <i class="bi bi-person me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Returned By</small>
                                            <strong>{{ $return->user?->name }}</strong>
                                        </div>
                                    </div>
                                    @if($return->return_method)
                                    <div class="d-flex mb-2">
                                        <i class="bi bi-box-arrow-right me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Return Method</small>
                                            <strong>{{ $return->return_method }}</strong>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                @if($return->notes)
                                <h6 class="mb-2">Additional Notes</h6>
                                <p class="text-muted">{{ $return->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    <h5 class="text-muted">No Returns Yet</h5>
                    <p class="text-muted mb-4">Success stories will appear here once items are returned to their owners</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($returns) && $returns->hasPages())
    <div class="d-flex justify-content-center">
        {{ $returns->links() }}
    </div>
    @endif
</div>
@endsection
