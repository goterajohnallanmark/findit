<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FindIt - Reuniting People with Their Belongings')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/finditlogo.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --secondary-color: #6c757d;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1f2937;
            --light-bg: #f9fafb;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-bg);
            color: #374151;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            color: #4b5563 !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link.active {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: white;
            border: 2px solid #e5e7eb;
            color: #374151;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #1f2937;
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            border-radius: 1rem 1rem 0 0;
            height: 200px;
            object-fit: cover;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .footer {
            background: rgba(59, 130, 246, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-left: none;
            border-right: none;
            border-bottom: none;
            padding: 2rem 0;
            margin-top: auto;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 2px solid #e5e7eb;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .modal-content {
            border-radius: 1rem;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.25rem;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-box-lg {
            width: 64px;
            height: 64px;
            border-radius: 1rem;
            font-size: 2rem;
        }

        .content-wrapper {
            flex: 1;
            padding-top: 2rem;
            padding-bottom: 2rem;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .content-wrapper .container {
            max-width: 1200px;
        }

        @media (min-width: 768px) {
            .content-wrapper {
                padding-left: 4rem;
                padding-right: 4rem;
            }
        }

        @media (min-width: 1200px) {
            .content-wrapper {
                padding-left: 6rem;
                padding-right: 6rem;
            }
            .content-wrapper .container {
                max-width: 1100px;
            }
        }

        @media (min-width: 1400px) {
            .content-wrapper {
                padding-left: 8rem;
                padding-right: 8rem;
            }
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }

        .search-bar {
            position: relative;
        }

        .search-bar .form-control {
            padding-left: 2.75rem;
        }

        .search-bar .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .item-card-img {
            height: 200px;
            object-fit: cover;
            background: #e5e7eb;
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="content-wrapper">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Success/Error Notification Modals -->
    @if(session('success'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="icon-box-lg bg-success bg-opacity-10 text-success mx-auto mb-3">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="mb-2">Success!</h5>
                        <p class="text-muted mb-0">{{ session('success') }}</p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="icon-box-lg bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <h5 class="mb-2">Error!</h5>
                        <p class="text-muted mb-0">{{ session('error') }}</p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-show notification modals -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif

            @if(session('error'))
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
