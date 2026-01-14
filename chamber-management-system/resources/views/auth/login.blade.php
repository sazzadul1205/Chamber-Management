<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ config('app.name', 'Chamber Management System') }}</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">

    {{-- Auth Page Styling --}}
    <style>
        body.login-page {
            background: #f6f7f9;
            font-family: "Source Sans 3", system-ui, -apple-system, BlinkMacSystemFont;
        }

        .login-box {
            width: 380px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: #ffffff;
            border-bottom: none;
            padding: 2rem 1.5rem 1rem;
        }

        .login-logo {
            max-width: 160px;
            height: auto;
            object-fit: contain;
        }

        .login-card-body {
            padding: 1.75rem 1.75rem 2rem;
        }

        .login-box-msg {
            color: #495057;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
        }

        .input-group-text {
            border-radius: 0 8px 8px 0;
            background: #ffffff;
        }

        .btn-primary {
            border-radius: 8px;
            font-weight: 600;
        }

        .form-check-label,
        .login-card-body a {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .login-card-body a:hover {
            color: #0d6efd;
            text-decoration: none;
        }

        .back-btn-wrapper {
            text-align: left;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.9rem;
            background: #ffffff;
            color: #0d6efd;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            border: 1px solid #dee2e6;
        }

        .back-btn:hover {
            background: #0d6efd;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="login-page">


    <div class="login-box">

        {{-- Back Button --}}
        <div class="back-btn-wrapper mb-3">
            <a href="javascript:history.back()" class="back-btn">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <div class="card card-outline card-primary">

            {{-- Logo --}}
            <div class="card-header text-center">
                <img src="{{ asset('login_logo.png') }}" alt="System Logo" class="login-logo">
            </div>

            {{-- Body --}}
            <div class="card-body login-card-body">

                <p class="login-box-msg text-center">
                    Sign in to your account
                </p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email or Phone --}}
                    <div class="input-group mb-3">
                        <div class="form-floating flex-grow-1">
                            <input type="text" name="login" id="login"
                                class="form-control @error('login') is-invalid @enderror" value="{{ old('login') }}"
                                placeholder="Email or Phone" required autofocus>
                            <label for="login">Email or Phone</label>
                        </div>
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                    </div>
                    @error('login')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror

                    {{-- Password --}}
                    <div class="input-group mb-4">
                        <div class="form-floating flex-grow-1">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                                required>
                            <label for="password">Password</label>
                        </div>
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                    </div>
                    @error('password')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror

                    {{-- Remember + Submit --}}
                    <div class="row align-items-center mb-3">
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Sign In
                            </button>
                        </div>
                    </div>

                </form>

                {{-- Links --}}
                <div class="text-center flex">
                    @if (Route::has('password.request'))
                        <p class="mb-1">
                            <a href="{{ route('password.request') }}">Forgot your password?</a>
                        </p>
                    @endif

                    @if (Route::has('register'))
                        <p class="mb-0">
                            <a href="{{ route('register') }}">Register a new account</a>
                        </p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
</body>

</html>
