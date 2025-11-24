<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/snackszone-logo.ico">
    <title>Snacks Zone | Welcome</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #0B075E;
        font-family: "Poppins", sans-serif;
    }

    .main-wrapper {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 50px;
    }

    /* Default Card */
    .card-box {
        width: 420px;
        padding: 40px 35px;
        border-radius: 18px;
        background: linear-gradient(135deg, #0B075E, #1E0EFF);
        color: white;
        box-shadow: 0px 10px 25px rgba(0,0,0,0.3);
    }

    /* LEFT WELCOME CARD WITHOUT BACKGROUND */
    .welcome-box {
        background: none !important;
        box-shadow: none !important;
    }

    .btn-custom {
        background: #FFD93D;
        color: #0B075E;
        font-weight: bold;
        border-radius: 10px;
    }

    .btn-custom:hover {
        background: #ffca00;
    }

    .label-text {
        color: #FFD93D;
        font-weight: 600;
    }

    .hidden {
        display: none;
    }
</style>
</head>

<body>

<div class="main-wrapper">

    <!-- LEFT WELCOME CARD -->
    <div class="card-box welcome-box text-center">

        <img src="/images/snackszone-logo.png"
            style="width:120px; border-radius:50%; border:2px solid #FFD93D;"
            class="img-fluid mb-3">

        <h1 class="fw-bold">Snacks Zone</h1>
        <p class="text-warning">
            Welcome to the Snacks Zone Admin Panel — please login or create an account.
        </p>

    </div>

    <!-- RIGHT AUTH CARD -->
    <div class="card-box">

        <!-- Toggle Buttons -->
        <div class="d-flex justify-content-center gap-3 mb-4">
            <button class="btn btn-custom" onclick="showLogin()">Login</button>
            <button class="btn btn-custom" onclick="showRegister()">Register</button>
        </div>

        <!-- Login Form -->
        <div id="loginForm">
            <h3 class="text-center mb-3">Login</h3>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="label-text">Email</label>
                    <input type="email" class="form-control" name="email" required autofocus>
                    @error('email')
                        <small class="text-warning">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="label-text">Password</label>
                    <input type="password" class="form-control" name="password" required>
                    @error('password')
                        <small class="text-warning">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label text-warning" for="remember">Remember Me</label>
                </div>

                <!-- Forgot Password -->
                @if(Route::has('password.request'))
                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="text-warning text-decoration-none">
                        Forgot password?
                    </a>
                </div>
                @endif

                <button class="btn btn-custom w-100 py-2">Login</button>
            </form>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="hidden">
            <h3 class="text-center mb-3">Register</h3>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label class="label-text">Full Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-3">
                    <label class="label-text">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="label-text">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="mb-3">
                    <label class="label-text">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>

                <button class="btn btn-custom w-100 py-2">Create Account</button>
            </form>
        </div>

    </div>

</div>

<script>
    function showLogin() {
        document.getElementById("loginForm").classList.remove("hidden");
        document.getElementById("registerForm").classList.add("hidden");
    }

    function showRegister() {
        document.getElementById("registerForm").classList.remove("hidden");
        document.getElementById("loginForm").classList.add("hidden");
    }
</script>

</body>
</html>
