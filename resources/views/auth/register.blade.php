<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snacks Zone | Register</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #FFD93D;
            font-family: "Poppins", sans-serif;
        }
        .register-card {
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
    </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="register-card w-100" style="max-width: 450px;">

        <!-- Logo -->
        <div class="text-center mb-3">
            <img src="/images/snackszone-logo.png" 
                 alt="Snacks Zone" 
                 class="img-fluid" 
                 style="width:120px; border-radius:55%; border:2px solid #FFD93D;">
            <h2 class="mt-3">Create Account</h2>
            <p class="text-warning">Snacks Zone Admin Panel</p>
        </div>

        <!-- Errors -->
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label class="label-text">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="label-text">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="label-text">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label class="label-text">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn btn-custom w-100 py-2">
                Register
            </button>

            <!-- Already have account? -->
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-warning text-decoration-none">
                    Already have an account?
                </a>
            </div>

        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
