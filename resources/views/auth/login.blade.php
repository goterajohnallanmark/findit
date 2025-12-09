<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - FindIt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            min-height: 100vh;
        }
    </style>
</head>
<body>
<section class="d-flex align-items-center justify-content-center" style="min-height: 100vh; padding: 2rem 0;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow-lg border-0">
          <div class="card-body p-4">
            <!-- Logo -->
            <div class="text-center mb-4">
              <img src="{{ asset('img/finditlogo.png') }}" alt="FindIt Logo" class="mx-auto d-block" style="max-width: 150px; height: auto;">
            </div>
        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <div class="text-center mb-4">
            <h4 class="fw-bold">Sign in to FindIt</h4>
          </div>

          <!-- Email input -->
          <div class="form-outline mb-3">
            <label class="form-label" for="email">Email address</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
              placeholder="Enter a valid email address" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Password input -->
          <div class="form-outline mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Enter password" required autocomplete="current-password" />
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleLoginPasswordIcon')">
                <i class="bi bi-eye" id="toggleLoginPasswordIcon"></i>
              </button>
              @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Checkbox -->
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" name="remember" id="remember_me" />
              <label class="form-check-label" for="remember_me">
                Remember me
              </label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
            @endif
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Login</button>
            <p class="small mb-0">Don't have an account? <a href="{{ route('register') }}"
                class="fw-bold text-decoration-none">Register</a></p>
          </div>

        </form>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-4">
          <p class="text-white mb-2">Copyright © {{ date('Y') }}. All rights reserved.</p>
          <div>
            <a href="#!" class="text-white me-3">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#!" class="text-white me-3">
              <i class="bi bi-twitter"></i>
            </a>
            <a href="#!" class="text-white me-3">
              <i class="bi bi-google"></i>
            </a>
            <a href="#!" class="text-white">
              <i class="bi bi-linkedin"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function togglePassword(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);
  
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
