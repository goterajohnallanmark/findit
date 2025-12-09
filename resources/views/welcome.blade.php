<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('img/finditlogo.png') }}">

        <title>FindIt - Reuniting People with Their Belongings</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        
        <style>
            body {
                background: white;
                min-height: 100vh;
                margin: 0;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            
            .welcome-title {
                display: inline-block;
            }
            
            .typewriter {
                display: inline-block;
                border-right: 3px solid white;
                padding-right: 5px;
            }
            
            .welcome-card {
                background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                border-radius: 15px;
                padding: 2rem;
                box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
                border: 1px solid rgba(59, 130, 246, 0.2);
            }
            
            .logo-animate {
                animation: bounce 1.5s cubic-bezier(0.28, 0.84, 0.42, 1) infinite;
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
                filter: contrast(1.1) brightness(1.05);
                -webkit-filter: contrast(1.1) brightness(1.05);
            }
            
            @keyframes bounce {
                0% {
                    transform: translateY(0) scaleY(0.85) scaleX(1.1);
                    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
                }
                15% {
                    transform: translateY(0) scaleY(1) scaleX(1);
                }
                50% {
                    transform: translateY(-20px) scaleY(1) scaleX(1);
                    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
                }
                85% {
                    transform: translateY(0) scaleY(1) scaleX(1);
                }
                100% {
                    transform: translateY(0) scaleY(0.85) scaleX(1.1);
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row min-vh-100 align-items-center">
                <!-- Left Column - Text Content -->
                <div class="col-md-6 px-4">
                    <div class="welcome-card text-white">
                        <h1 class="display-5 fw-bold mb-4 welcome-title">
                            <span class="typewriter">Welcome to FindIt!</span>
                        </h1>
                        <p class="lead mb-4">The smart solution for reuniting people with their lost belongings using AI-powered matching.</p>
                        <p class="fs-5 mb-5">Lost something? Found something? We're here to help connect owners with their items through intelligent matching and easy communication.</p>
                        
                        <div class="d-flex gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg px-5">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5">
                                        <i class="bi bi-person-plus me-2"></i>Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Right Column - Logo -->
                <div class="col-md-6 text-center px-4">
                    <img src="{{ asset('img/finditlogo.png') }}" alt="FindIt Logo" class="img-fluid logo-animate" style="max-width: 600px; height: auto; image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;">
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const text = "Welcome to FindIt";
            const typewriterElement = document.querySelector('.typewriter');
            let index = 0;
            
            function typeWriter() {
                if (index < text.length) {
                    typewriterElement.textContent += text.charAt(index);
                    index++;
                    setTimeout(typeWriter, 150);
                } else {
                    setTimeout(() => {
                        typewriterElement.textContent = '';
                        index = 0;
                        typeWriter();
                    }, 3000);
                }
            }
            
            typeWriter();
        </script>
    </body>
</html>
