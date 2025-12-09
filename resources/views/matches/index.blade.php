@extends('layouts.app')

@section('title', 'Potential Matches - FindIt')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Potential Matches</h1>
        <p class="page-subtitle mb-0">AI-powered matching between lost and found items</p>
    </div>

    <!-- Info Banner -->
    <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
        <i class="bi bi-info-circle flex-shrink-0 me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h6 class="alert-heading mb-2">How Matching Works</h6>
            <p class="mb-0">Our AI system analyzes descriptions, locations, and dates to find potential matches between lost and found items. Review the matches below and contact the other party if you recognize your item.</p>
        </div>
    </div>

    <!-- Matches Grid -->
    <div class="row g-4 mb-5">
        @forelse($matches ?? [] as $match)
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Lost Item -->
                        <div class="col-md-5">
                            <div class="d-flex align-items-start gap-3">
                                <img src="{{ $match->lostItem->image_url ?? 'https://via.placeholder.com/120' }}" 
                                     alt="{{ $match->lostItem->title }}" 
                                     class="rounded"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <div class="flex-fill">
                                    <span class="badge bg-danger mb-2">
                                        <i class="bi bi-search me-1"></i> Lost
                                    </span>
                                    <h5 class="mb-2">{{ $match->lostItem->title }}</h5>
                                    <p class="text-muted mb-2 small">{{ Str::limit($match->lostItem->description, 80) }}</p>
                                    <div class="text-muted small">
                                        <div><i class="bi bi-geo-alt me-1"></i> {{ $match->lostItem->location }}</div>
                                        <div><i class="bi bi-calendar me-1"></i> {{ $match->lostItem->lost_date?->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Match Score -->
                        <div class="col-md-2 text-center my-3 my-md-0">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <div class="mb-2">
                                    <div class="rounded-circle d-flex align-items-center pt-2 justify-content-center border border-2" 
                                         style="width: 70px; height: 70px; border-color: {{ $match->similarity_score >= 80 ? '#10b981' : ($match->similarity_score >= 60 ? '#f59e0b' : '#6b7280') }} !important;">
                                        <div class="text-center">
                                            <div style="font-size: 1rem; font-weight: 600; line-height: .5; color: #000; margin-bottom: 2px;">{{ $match->similarity_score }}%</div>
                                            <small style="font-size: 0.65rem; color: #6b7280; line-height: 1;">Match</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-2">
                                    <i class="bi bi-arrow-left-right text-muted"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Found Item -->
                        <div class="col-md-5">
                            <div class="d-flex align-items-start gap-3">
                                <img src="{{ $match->foundItem->image_url ?? 'https://via.placeholder.com/120' }}" 
                                     alt="{{ $match->foundItem->title }}" 
                                     class="rounded"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <div class="flex-fill">
                                    <span class="badge bg-success mb-2">
                                        <i class="bi bi-box-seam me-1"></i> Found
                                    </span>
                                    <h5 class="mb-2">{{ $match->foundItem->title }}</h5>
                                    <p class="text-muted mb-2 small">{{ Str::limit($match->foundItem->description, 80) }}</p>
                                    <div class="text-muted small">
                                        <div><i class="bi bi-geo-alt me-1"></i> {{ $match->foundItem->location }}</div>
                                        <div><i class="bi bi-calendar me-1"></i> {{ $match->foundItem->found_date?->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Match Details -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="mb-3">Why These Match</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tag text-primary me-2"></i>
                                    <small class="text-muted">Same category: <strong>{{ ucfirst($match->lostItem->category) }}</strong></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-success me-2"></i>
                                    <small class="text-muted">Similar timeframe</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo text-warning me-2"></i>
                                    <small class="text-muted">Close proximity</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('lost-items.show', $match->lostItem->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> View Lost Item
                            </a>
                            <a href="{{ route('found-items.show', $match->foundItem->id) }}" class="btn btn-outline-success">
                                <i class="bi bi-eye me-1"></i> View Found Item
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal{{ $match->id }}">
                                <i class="bi bi-envelope me-1"></i> Contact Parties
                            </button>
                            <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#returnModal{{ $match->id }}">
                                <i class="bi bi-check-circle me-1"></i> Mark as Returned
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Modal -->
        <div class="modal fade" id="contactModal{{ $match->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Contact Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="mb-3">Lost Item Owner</h6>
                            <div class="p-3 bg-light rounded">
                                <div class="mb-2">
                                    <i class="bi bi-person me-2"></i>
                                    <strong>{{ $match->lostItem->user?->name }}</strong>
                                </div>
                                <div>
                                    <i class="bi bi-envelope me-2"></i>
                                    {{ $match->lostItem->contact_info }}
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h6 class="mb-3">Found Item Finder</h6>
                            <div class="p-3 bg-light rounded">
                                <div class="mb-2">
                                    <i class="bi bi-person me-2"></i>
                                    <strong>{{ $match->foundItem->user?->name }}</strong>
                                </div>
                                <div>
                                    <i class="bi bi-envelope me-2"></i>
                                    {{ $match->foundItem->contact_info }}
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4 mb-0" role="alert">
                            <small>
                                <i class="bi bi-shield-exclamation me-2"></i>
                                Remember to verify identity and meet in a public place when returning items.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Modal -->
        <div class="modal fade" id="returnModal{{ $match->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Mark as Returned</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $match->lostItem->id }}">
                        <input type="hidden" name="item_type" value="lost">
                        
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <div class="icon-box-lg bg-success bg-opacity-10 text-success mx-auto mb-3">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <h6>Confirm Item Return</h6>
                                <p class="text-muted mb-0">This will mark the item as returned and remove it from lost/found listings.</p>
                            </div>
                            
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body p-3">
                                    <h6 class="mb-3">Item Details</h6>
                                    <div class="d-flex gap-3 mb-3">
                                        <img src="{{ $match->lostItem->image_url ?? 'https://via.placeholder.com/60' }}" 
                                             alt="{{ $match->lostItem->title }}" 
                                             class="rounded"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">{{ $match->lostItem->title }}</h6>
                                            <p class="text-muted small mb-0">{{ $match->lostItem->category }}</p>
                                        </div>
                                    </div>
                                    <div class="row g-2 small">
                                        <div class="col-6">
                                            <div class="text-muted">Owner</div>
                                            <strong>{{ $match->lostItem->user?->name }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted">Finder</div>
                                            <strong>{{ $match->foundItem->user?->name }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="return_date{{ $match->id }}" class="form-label">Return Date</label>
                                <input type="date" class="form-control" id="return_date{{ $match->id }}" name="return_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="return_location{{ $match->id }}" class="form-label">Return Location</label>
                                <input type="text" class="form-control" id="return_location{{ $match->id }}" name="return_location" placeholder="Where was the item returned?" required>
                            </div>

                            <div class="mb-3">
                                <label for="return_method{{ $match->id }}" class="form-label">Return Method</label>
                                <select class="form-select" id="return_method{{ $match->id }}" name="return_method" required>
                                    <option value="">Select method...</option>
                                    <option value="In Person">In Person</option>
                                    <option value="Mail">Mail</option>
                                    <option value="Pickup">Pickup</option>
                                    <option value="Drop-off">Drop-off</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="contact_info{{ $match->id }}" class="form-label">Contact Information</label>
                                <input type="text" class="form-control" id="contact_info{{ $match->id }}" name="contact_info" placeholder="Email or phone number" required>
                            </div>

                            <div class="mb-3">
                                <label for="notes{{ $match->id }}" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes{{ $match->id }}" name="notes" rows="2" placeholder="Any additional details about the return..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="proof_image{{ $match->id }}" class="form-label">Proof of Return Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="proof_image{{ $match->id }}" name="proof_image" accept="image/*" required>
                                <small class="text-muted">Upload a photo showing the item was returned</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i> Confirm Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="icon-box-lg bg-light text-muted mx-auto mb-3">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <h5 class="text-muted">No Matches Found</h5>
                    <p class="text-muted mb-4">Our AI hasn't found any potential matches yet. Check back later as new items are posted.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('lost-items.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i> Report Lost Item
                        </a>
                        <a href="{{ route('found-items.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i> Report Found Item
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
