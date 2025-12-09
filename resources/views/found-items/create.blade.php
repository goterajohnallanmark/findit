@extends('layouts.app')

@section('title', 'Report Found Item - FindIt')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('found-items.index') }}" class="btn btn-link text-decoration-none mb-4 px-0">
                <i class="bi bi-arrow-left me-2"></i> Back to Found Items
            </a>

            <!-- Page Header -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1">Report Found Item</h1>
                        <p class="page-subtitle mb-0">Help return this item to its rightful owner</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card">
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('found-items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Item Details Section -->
                        <h5 class="mb-4">Item Details</h5>

                        <div class="mb-4">
                            <label for="title" class="form-label">Item Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   placeholder="e.g., Blue Backpack with Laptop"
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Give a clear, descriptive title</div>
                        </div>

                        <div class="mb-4">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    required>
                                <option value="">Select a category</option>
                                <option value="electronics" {{ old('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="wallet" {{ old('category') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                                <option value="keys" {{ old('category') == 'keys' ? 'selected' : '' }}>Keys</option>
                                <option value="bag" {{ old('category') == 'bag' ? 'selected' : '' }}>Bag/Backpack</option>
                                <option value="jewelry" {{ old('category') == 'jewelry' ? 'selected' : '' }}>Jewelry</option>
                                <option value="clothing" {{ old('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                                <option value="documents" {{ old('category') == 'documents' ? 'selected' : '' }}>Documents</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Describe the item you found (color, brand, unique features, etc.)"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Include details to help the owner identify their item</div>
                        </div>

                        <!-- Location & Date Section -->
                        <h5 class="mb-4 mt-5">Location & Date</h5>

                        <div class="mb-4">
                            <label for="location" class="form-label">Where did you find it? <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   id="location" 
                                   name="location" 
                                   placeholder="e.g., City Library, Main Street"
                                   value="{{ old('location') }}"
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="found_date" class="form-label">When did you find it? <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('found_date') is-invalid @enderror" 
                                   id="found_date" 
                                   name="found_date" 
                                   value="{{ old('found_date') }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('found_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Images Section -->
                        <h5 class="mb-4 mt-5">Images</h5>

                        <div class="mb-4">
                            <label for="images" class="form-label">Upload Photos <span class="text-danger">*</span></label>
                            <div class="border border-2 border-dashed rounded p-4 text-center" style="border-color: #d1d5db !important;">
                                <input type="file" 
                                       class="form-control @error('images') is-invalid @enderror" 
                                       id="images" 
                                       name="images[]" 
                                       accept="image/*"
                                       multiple
                                       required>
                                <div class="mt-3">
                                    <i class="bi bi-cloud-upload text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mb-1 mt-2">Click to upload or drag and drop</p>
                                    <p class="text-muted small mb-0">PNG, JPG or GIF (max. 5MB each)</p>
                                </div>
                            </div>
                            @error('images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload clear photos to help the owner recognize their item</div>
                        </div>

                        <!-- Contact Information -->
                        <h5 class="mb-4 mt-5">Contact Information</h5>

                        <div class="mb-4">
                            <label for="contact_info" class="form-label">How can the owner reach you? <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('contact_info') is-invalid @enderror" 
                                   id="contact_info" 
                                   name="contact_info" 
                                   placeholder="Email or Phone Number"
                                   value="{{ old('contact_info') }}"
                                   required>
                            @error('contact_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">This will be shared with potential owners</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-3 mt-5 pt-4 border-top">
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="bi bi-check-circle me-2"></i> Submit Report
                            </button>
                            <a href="{{ route('found-items.index') }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card mt-4 mb-5">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="bi bi-lightbulb text-warning me-2"></i> Tips for Helping the Owner</h6>
                    <ul class="mb-0 text-muted">
                        <li class="mb-2">Upload clear photos from multiple angles</li>
                        <li class="mb-2">Be specific about where you found the item</li>
                        <li class="mb-2">Keep the item in a safe place</li>
                        <li class="mb-2">Respond promptly to inquiries from potential owners</li>
                        <li>Ask for proof of ownership before returning the item</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
