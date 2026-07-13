<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
</head>
<body>
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Password</label>
        <input type="password" name="password" required>
        @error('password') <span>{{ $message }}</span> @enderror
    </div>

    <button type="submit">Login</button>
</form>
</body>
</html>