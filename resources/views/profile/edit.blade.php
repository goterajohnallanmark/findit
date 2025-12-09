@extends('layouts.app')

@section('title', 'Profile - FindIt')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2">Profile Settings</h1>
        <p class="page-subtitle mb-0">Manage your account information and settings</p>
    </div>

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                    
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4"><i class="bi bi-shield-lock me-2"></i>Update Password</h5>
                    
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card border-danger">
                <div class="card-body p-4">
                    <h5 class="mb-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</h5>
                    
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <!-- Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4 text-center">
                    @if(auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="icon-box-lg bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                            <i class="bi bi-person-circle"></i>
                        </div>
                    @endif
                    <h5 class="mb-2">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                    <small class="text-muted">Member since {{ auth()->user()->created_at->format('M Y') }}</small>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <h6 class="mb-3">Your Activity</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-search"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ auth()->user()->lostItems()->count() }}</div>
                            <small class="text-muted">Lost Items</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ auth()->user()->foundItems()->count() }}</div>
                            <small class="text-muted">Found Items</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ auth()->user()->returnRecords()->count() }}</div>
                            <small class="text-muted">Returns Made</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
