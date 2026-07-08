<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-dark d-flex align-items-center" style="height: 100vh;">

<div class="position-absolute top-0 end-0 m-4">
    <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm btn-outline-light me-1 @if(app()->getLocale() == 'en') active @endif">🇬🇧 English</a>
    <a href="{{ route('lang.switch', 'mm') }}" class="btn btn-sm btn-outline-light @if(app()->getLocale() == 'mm') active @endif">🇲🇲 မြန်မာ</a>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg p-4 bg-white rounded-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">POS SYSTEM</h2>
                    <p class="text-muted">{{ __('messages.login_title') }}</p>
                </div>

                <form action="{{ route('login.perform') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.email_address') }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@pos.com" required autofocus>
                        @error('email')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('messages.password') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label text-muted" for="remember">{{ __('messages.remember_me') }}</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 fs-5 shadow-sm">
                         {{ __('messages.login_btn') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
