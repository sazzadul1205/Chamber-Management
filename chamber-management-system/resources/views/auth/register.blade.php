<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register | {{ config('app.name', 'Chamber Management System') }}</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">

    {{-- Custom Styling --}}
    <style>
        body.login-page {
            background: #f6f7f9;
            font-family: "Source Sans 3", system-ui, -apple-system, BlinkMacSystemFont;
        }

        .login-box {
            width: 400px;
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
            text-align: center;
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
            text-align: center;
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
            letter-spacing: 0.3px;
        }

        .form-check-label,
        a {
            font-size: 0.85rem;
            color: #6c757d;
        }

        a:hover {
            color: #0d6efd;
            text-decoration: none;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
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
            <div class="card-header">
                <img src="{{ asset('login_logo.png') }}" alt="System Logo" class="login-logo">
            </div>

            {{-- Register Form --}}
            <div class="card-body login-card-body">
                <p class="login-box-msg">Create a new account</p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                            id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus>
                        @error('full_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email (Optional) --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Register Button --}}
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        Register
                    </button>

                    {{-- Already Registered --}}
                    <div class="text-center">
                        <a href="{{ route('login') }}">Already registered? Sign in</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
</body>

</html>
