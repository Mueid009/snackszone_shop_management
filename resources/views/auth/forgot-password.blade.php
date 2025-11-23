<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snacks Zone | Forgot Password</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #FFD93D;
            font-family: "Poppins", sans-serif;
        }
        .fp-card {
            background: linear-gradient(135deg, #0B075E, #1E0EFF);
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
        .small-text {
            color: #FFD93D;
            opacity: 0.9;
        }
    </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="fp-card w-100" style="max-width: 450px;">

        <!-- Logo -->
        <div class="text-center mb-3">
            <img src="/images/snackszone-logo.png"
                 alt="Snacks Zone"
                 class="img-fluid"
                 style="width:120px; border-radius:55%; border:2px solid #FFD93D;">
            <h2 class="mt-3">Forgot Password?</h2>
            <p class="small-text">Reset your Snacks Zone account password</p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="alert alert-success mb-3">
                {{ session('status') }}
            </div>
        @endif

        <p class="small-text mb-3">
            No problem! Enter your email and we’ll send you a password reset link.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label class="label-text">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <small class="text-warning">{{ $message }}</small>
                @enderror
            </div>

            <!-- Send Link Button -->
            <button type="submit" class="btn btn-custom w-100 py-2">
                Send Password Reset Link
            </button>

            <!-- Back to login -->
            <div class="text-center mt-3">
                <a href="{{ route('welcome') }}" class="text-warning text-decoration-none">
                    Back to Login
                </a>
            </div>

        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
