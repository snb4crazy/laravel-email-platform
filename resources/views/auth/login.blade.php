<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Email Platform</title>
</head>
<body>
    <h1>Email Platform Login</h1>

    @if ($errors->any())
        <div style="color: #b00020;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <br>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <br>

        <label>
            <input type="checkbox" name="remember" value="1"> Remember me
        </label>

        <br>

        <button type="submit">Login</button>
    </form>

    <p>Public signup is disabled. Ask an administrator to create your account.</p>
</body>
</html>

