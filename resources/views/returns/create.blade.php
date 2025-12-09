@extends('layouts.app')

@section('title', 'Complete Return Process - FindIt')

@section('content')
<div class="container">
    <div class="row">
        <!-- Back Button -->
        <div class="col-12 mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none px-0">
                <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>

        <!-- Page Header -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <h1 class="page-title mb-1">Complete Return Process</h1>
                    <p class="page-subtitle mb-0">Provide details about returning this item</p>
                </div>
            </div>
        </div>

        <!-- Item Summary Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Item Being Returned</h5>
                    @if(isset($item))
                    <div class="mb-3">
                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/400x200?text=Item' }}" 
                             alt="{{ $item->title }}" 
                             class="img-fluid rounded"
                             style="width: 100%; height: 200px; object-fit: cover;">
                    </div>
                    <h6 class="mb-2">{{ $item->title }}</h6>
                    <p class="text-muted small mb-0">{{ Str::limit($item->description, 100) }}</p>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Item details will appear here</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Return Tips -->
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="bi bi-lightbulb text-warning me-2"></i> Return Tips</h6>
                    <ul class="mb-0 text-muted small">
                        <li class="mb-2">Meet in a public place</li>
                        <li class="mb-2">Verify owner identity</li>
                        <li class="mb-2">Take a photo as proof</li>
                        <li>Get confirmation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Return Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @php
                            $itemId = old('item_id', request('item_id'));
                            $itemType = old('item_type', request('type'));
                        @endphp

                        @if($itemId)
                        <input type="hidden" name="item_id" value="{{ $itemId }}">
                        @endif

                        @if($itemType)
                        <input type="hidden" name="item_type" value="{{ $itemType }}">
                        @endif

                        <h5 class="mb-4">Return Details</h5>

                        <div class="mb-4">
                            <label for="return_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('return_date') is-invalid @enderror" 
                                   id="return_date" 
                                   name="return_date" 
                                   value="{{ old('return_date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('return_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">When did you return the item?</div>
                        </div>

                        <div class="mb-4">
                            <label for="return_location" class="form-label">Return Location <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('return_location') is-invalid @enderror" 
                                   id="return_location" 
                                   name="return_location" 
                                   placeholder="e.g., Central Park Entrance, Coffee Shop on Main St"
                                   value="{{ old('return_location') }}"
                                   required>
                            @error('return_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Where did you return the item?</div>
                        </div>

                        <div class="mb-4">
                            <label for="return_method" class="form-label">Return Method <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('return_method') is-invalid @enderror" 
                                   id="return_method" 
                                   name="return_method" 
                                   placeholder="e.g., In-person meetup, Left at reception, Mailed"
                                   value="{{ old('return_method') }}"
                                   required>
                            @error('return_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">How was the item returned?</div>
                        </div>

                        <div class="mb-4">
                            <label for="contact_info" class="form-label">Contact Information <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('contact_info') is-invalid @enderror" 
                                   id="contact_info" 
                                   name="contact_info" 
                                   placeholder="Your email or phone number"
                                   value="{{ old('contact_info') }}"
                                   required>
                            @error('contact_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">So the owner can thank you</div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      placeholder="Any additional information about the return process...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: Share any relevant details</div>
                        </div>

                        <!-- Proof of Return Upload -->
                        <div class="mb-4">
                            <label for="proof_image" class="form-label">Proof of Return (Optional)</label>
                            <div class="border border-2 border-dashed rounded p-4 text-center" style="border-color: #d1d5db !important;">
                                <input type="file" 
                                       class="form-control @error('proof_image') is-invalid @enderror" 
                                       id="proof_image" 
                                       name="proof_image" 
                                       accept="image/*"
                                       onchange="previewReturnImage(event)">
                                <div class="mt-3" id="uploadPlaceholder">
                                    <i class="bi bi-cloud-upload text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mb-1 mt-2">Upload a photo of the return</p>
                                    <p class="text-muted small mb-0">PNG, JPG or GIF (max. 5MB)</p>
                                </div>
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearReturnImage()">
                                        <i class="bi bi-x-circle me-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                            @error('proof_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: Photo showing the item being returned to the owner</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-3 mt-5 pt-4 border-top">
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="bi bi-check-circle me-2"></i> Confirm Return
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewReturnImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function clearReturnImage() {
    document.getElementById('proof_image').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}
</script>
@endpush
@endsection
