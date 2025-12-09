@extends('layouts.app')

@section('title', 'Edit Lost Item - FindIt')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('lost-items.show', $item->id ?? '#') }}" class="btn btn-link text-decoration-none mb-4 px-0">
                <i class="bi bi-arrow-left me-2"></i> Back to Item
            </a>

            <!-- Page Header -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                        <i class="bi bi-pencil"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1">Edit Lost Item</h1>
                        <p class="page-subtitle mb-0">Update the details of your lost item</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card">
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('lost-items.update', $item->id ?? '#') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Item Details Section -->
                        <h5 class="mb-4">Item Details</h5>

                        <div class="mb-4">
                            <label for="title" class="form-label">Item Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $item->title ?? '') }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    required>
                                <option value="">Select a category</option>
                                <option value="electronics" {{ old('category', $item->category ?? '') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="wallet" {{ old('category', $item->category ?? '') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                                <option value="keys" {{ old('category', $item->category ?? '') == 'keys' ? 'selected' : '' }}>Keys</option>
                                <option value="bag" {{ old('category', $item->category ?? '') == 'bag' ? 'selected' : '' }}>Bag/Backpack</option>
                                <option value="jewelry" {{ old('category', $item->category ?? '') == 'jewelry' ? 'selected' : '' }}>Jewelry</option>
                                <option value="clothing" {{ old('category', $item->category ?? '') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                                <option value="documents" {{ old('category', $item->category ?? '') == 'documents' ? 'selected' : '' }}>Documents</option>
                                <option value="other" {{ old('category', $item->category ?? '') == 'other' ? 'selected' : '' }}>Other</option>
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
                                      required>{{ old('description', $item->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location & Date Section -->
                        <h5 class="mb-4 mt-5">Location & Date</h5>

                        <div class="mb-4">
                            <label for="location" class="form-label">Lost Location <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location', $item->location ?? '') }}"
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="lost_date" class="form-label">Lost Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('lost_date') is-invalid @enderror" 
                                   id="lost_date" 
                                   name="lost_date" 
                                   value="{{ old('lost_date', $item->lost_date?->format('Y-m-d') ?? '') }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('lost_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Images -->
                        @if(isset($item) && $item->image_url)
                        <h5 class="mb-4 mt-5">Current Images</h5>
                        <div class="mb-4">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                        @endif

                        <!-- New Images Section -->
                        <h5 class="mb-4 mt-5">Update Images</h5>

                        <div class="mb-4">
                            <label for="images" class="form-label">Upload New Photos</label>
                            <div class="border border-2 border-dashed rounded p-4 text-center" style="border-color: #d1d5db !important;">
                                <input type="file" 
                                       class="form-control @error('images') is-invalid @enderror" 
                                       id="images" 
                                       name="images[]" 
                                       accept="image/*"
                                       multiple>
                                <div class="mt-3">
                                    <i class="bi bi-cloud-upload text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mb-1 mt-2">Click to upload or drag and drop</p>
                                    <p class="text-muted small mb-0">PNG, JPG or GIF (max. 5MB each)</p>
                                </div>
                            </div>
                            @error('images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty to keep current images</div>
                        </div>

                        <!-- Contact Information -->
                        <h5 class="mb-4 mt-5">Contact Information</h5>

                        <div class="mb-4">
                            <label for="contact_info" class="form-label">Contact Details <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('contact_info') is-invalid @enderror" 
                                   id="contact_info" 
                                   name="contact_info" 
                                   value="{{ old('contact_info', $item->contact_info ?? '') }}"
                                   required>
                            @error('contact_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-3 mt-5 pt-4 border-top">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="bi bi-check-circle me-2"></i> Update Item
                            </button>
                            <a href="{{ route('lost-items.show', $item->id ?? '#') }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
