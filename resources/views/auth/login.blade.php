<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Administrator Login | IProFixer</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #071d2e;
            color: #10273b;
            font-family: Arial, Helvetica, sans-serif;
        }
        .login-card {
            width: min(100%, 440px);
            padding: 42px;
            background: #fff;
            border: 1px solid rgba(16, 39, 59, 0.08);
            border-radius: 6px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.24);
        }
        .brand {
            margin-bottom: 8px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 30px;
            font-weight: 700;
        }
        .subtitle {
            margin: 0 0 32px;
            color: #66727d;
            font-size: 15px;
            line-height: 1.5;
        }
        .field { margin-bottom: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid #cbd2d8;
            border-radius: 3px;
            background: #fff;
            color: #10273b;
            font-size: 16px;
        }
        input:focus {
            border-color: #c99a3d;
            outline: 2px solid rgba(201, 154, 61, 0.18);
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            color: #4c5964;
            font-size: 14px;
        }
        .remember input { width: 16px; height: 16px; }
        button {
            width: 100%;
            min-height: 50px;
            border: 0;
            border-radius: 3px;
            background: #0b2235;
            color: #fff;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
        }
        button:hover { background: #14344e; }
        .errors {
            margin-bottom: 22px;
            padding: 14px 16px;
            border: 1px solid #d99b9b;
            background: #fff1f1;
            color: #8b1f1f;
            font-size: 14px;
            line-height: 1.5;
        }
        .home-link {
            display: block;
            margin-top: 22px;
            color: #66727d;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
        }
        .home-link:hover { color: #10273b; }
    </style>
</head>
<body>
<main class="login-card">
    <div class="brand">IProFixer</div>
    <p class="subtitle">Authorized administration access only.</p>

    @if ($errors->any())
        <div class="errors" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <label class="remember">
            <input name="remember" type="checkbox" value="1">
            <span>Keep me signed in</span>
        </label>
        <button type="submit">Sign in</button>
    </form>

    <a class="home-link" href="{{ route('home') }}">Return to website</a>
</main>
</body>
</html>
