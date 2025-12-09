<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - FindIt</title>
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
      <div class="col-md-8 col-lg-7 col-xl-6">
        <div class="card shadow-lg border-0">
          <div class="card-body p-4">
            <!-- Logo -->
            <div class="text-center mb-4">
              <img src="{{ asset('img/finditlogo.png') }}" alt="FindIt Logo" class="mx-auto d-block" style="max-width: 150px; height: auto;">
            </div>
        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="text-center mb-4">
            <h4 class="fw-bold">Create Account</h4>
          </div>

          <div class="row">
            <!-- Name input -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="name">Full Name</label>
              <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                placeholder="Enter your full name" value="{{ old('name') }}" required autofocus autocomplete="name" />
              @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Email input -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="email">Email address</label>
              <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                placeholder="Enter a valid email address" value="{{ old('email') }}" required autocomplete="username" />
              @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Phone Number input -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="phone_number">Phone Number</label>
              <input type="text" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                placeholder="Enter your phone number" value="{{ old('phone_number') }}" required autocomplete="tel" />
              @error('phone_number')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Password input -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="password">Password</label>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                  placeholder="Enter password" required autocomplete="new-password" />
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'togglePasswordIcon')">
                  <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Confirm Password input -->
            <div class="col-md-6 mb-3">
              <label class="form-label" for="password_confirmation">Confirm Password</label>
              <div class="input-group">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                  placeholder="Confirm password" required autocomplete="new-password" />
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', 'togglePasswordConfirmIcon')">
                  <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                </button>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Register</button>
            <p class="small mb-0">Already have an account? <a href="{{ route('login') }}"
                class="fw-bold text-decoration-none">Login</a></p>
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
