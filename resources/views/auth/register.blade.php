<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <p class="subtitle">Access your leave and mission request dashboard.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="input-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn">Register</button>

            <div class="footer-text">
                <a href="{{ route('login') }}" class="footer-link">Already have an account? <b>Login here</b></a>
            </div>
        </form>
    </div>
</body>
</html>
