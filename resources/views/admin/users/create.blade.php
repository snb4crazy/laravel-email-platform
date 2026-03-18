<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - Admin</title>
</head>
<body>
    <h1>Create User</h1>

    @if ($errors->any())
        <div style="color: #b00020;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required>

        <br>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <br>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="user" @selected(old('role') === 'user')>User</option>
            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
        </select>

        <br>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <br>

        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <br>

        <label>
            <input type="checkbox" name="email_verified" value="1" @checked(old('email_verified'))>
            Mark email as verified
        </label>

        <br>

        <button type="submit">Create User</button>
    </form>

    <p><a href="{{ route('admin.users.index') }}">Back to users</a></p>
</body>
</html>

