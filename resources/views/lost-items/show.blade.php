@extends('layouts.app')

@section('title', ($item->title ?? 'Lost Item') . ' - FindIt')

@section('content')
<div class="container">
    <div class="row">
        <!-- Back Button -->
        <div class="col-12 mb-4">
            <a href="{{ route('lost-items.index') }}" class="btn btn-link text-decoration-none px-0">
                <i class="bi bi-arrow-left me-2"></i> Back to Lost Items
            </a>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Item Images -->
            <div class="card mb-4">
                <div class="position-relative">
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/800x400?text=Lost+Item' }}" 
                         class="card-img-top" 
                         alt="{{ $item->title }}"
                         style="max-height: 400px; object-fit: cover;">
                    <span class="badge bg-danger position-absolute top-0 end-0 m-3 fs-6">
                        <i class="bi bi-search me-1"></i> Lost Item
                    </span>
                </div>
            </div>

            <!-- Item Details -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h3 mb-2">{{ $item->title }}</h2>
                            <span class="badge bg-light text-dark">{{ ucfirst($item->category) }}</span>
                        </div>
                        @auth
                            @if(auth()->id() === $item->user_id)
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('lost-items.edit', $item->id) }}">
                                            <i class="bi bi-pencil me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        @endauth
                    </div>

                    <h5 class="mb-3">Description</h5>
                    <p class="text-muted">{{ $item->description }}</p>

                    <hr class="my-4">

                    <h5 class="mb-3">Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Lost Location</small>
                                    <strong>{{ $item->location }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                                    <i class="bi bi-calendar"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Date Lost</small>
                                    <strong>{{ $item->lost_date?->format('M d, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Posted By</small>
                                    <strong>{{ $item->user?->name }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-secondary bg-opacity-10 text-secondary me-3">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Posted On</small>
                                    <strong>{{ $item->created_at?->format('M d, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-envelope me-2"></i> Contact Information</h5>
                    <p class="text-muted mb-0">{{ $item->contact_info }}</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Action Card -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Found This Item?</h5>
                    <p class="text-muted mb-4">If you have found this item, you can help return it to the owner.</p>
                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#returnModal">
                        <i class="bi bi-check-circle me-2"></i> I Have This Item
                    </button>
                    <a href="{{ route('matches.index') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-arrow-left-right me-2"></i> View Similar Items
                    </a>
                </div>
            </div>

            <!-- Safety Tips -->
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="bi bi-shield-check text-success me-2"></i> Safety Tips</h6>
                    <ul class="mb-0 text-muted small">
                        <li class="mb-2">Meet in a public place</li>
                        <li class="mb-2">Verify identity before handing over items</li>
                        <li class="mb-2">Ask for proof of ownership</li>
                        <li class="mb-2">Never share sensitive personal information</li>
                        <li>Report suspicious behavior</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="icon-box-lg bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                    <i class="bi bi-trash"></i>
                </div>
                <h5 class="mb-2">Delete This Item?</h5>
                <p class="text-muted mb-0">This action cannot be undone. Are you sure you want to delete this lost item?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('lost-items.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return This Item?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="icon-box-lg bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h6>Great! You're helping reunite someone with their lost item.</h6>
                    <p class="text-muted mb-0">Please provide details about the return process.</p>
                </div>
                
                <div class="d-flex gap-3 p-3 bg-light rounded">
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/80' }}" 
                         alt="{{ $item->title }}" 
                         class="rounded"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h6 class="mb-1">{{ $item->title }}</h6>
                        <p class="text-muted small mb-0">{{ $item->location }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('returns.create', ['item_id' => $item->id, 'type' => 'lost']) }}" class="btn btn-success">
                    <i class="bi bi-arrow-right me-2"></i> Continue to Return Form
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
