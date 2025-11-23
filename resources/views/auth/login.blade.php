<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snacks Zone | Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #FFD93D; /* Yellow background */
            font-family: "Poppins", sans-serif;
        }
        .login-card {
            background: linear-gradient(135deg, #0B075E, #1E0EFF); /* Deep blue/purple gradient */
            color: white;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            padding: 40px 35px;
        }
        .btn-custom {
            background: #FFD93D;
            color: #0B075E;
            font-weight: bold;
        }
        .btn-custom:hover {
            background: #ffca00;
        }
        .label-text {
            color: #FFD93D;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="login-card w-100" style="max-width: 420px;">

        <!-- Logo -->
        <div class="text-center mb-3">
            <img src="/images/snackszone-logo.png" alt="Snacks Zone" class="img-fluid" style="width:120px; border-radius:55%; border:2px solid #FFD93D;">
            <h2 class="mt-3">Welcome Back!</h2>
            <p class="text-warning">Snacks Zone Admin Panel</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label class="label-text">Email</label>
                <input type="email" name="email" class="form-control" required autofocus>
                @error('email')
                    <small class="text-warning">{{ $message }}</small>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="label-text">Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password')
                    <small class="text-warning">{{ $message }}</small>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label text-warning" for="remember">Remember Me</label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-custom w-100 py-2">
                Log In
            </button>

            <!-- Forgot Password -->
            @if(Route::has('password.request'))
            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" class="text-warning text-decoration-none">
                    Forgot password?
                </a>
            </div>
            @endif

        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
