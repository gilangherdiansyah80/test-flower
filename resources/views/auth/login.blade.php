<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 400px;
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 200;
            letter-spacing: 2px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #e2e8f0;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border-radius: 0.5rem;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
            transition: background 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            background: rgba(255, 255, 255, 0.3);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            border-radius: 0.5rem;
            border: none;
            background: #fff;
            color: #764ba2;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
        }
        .btn-login:hover {
            background: #f8fafc;
            transform: translateY(-2px);
        }
        .error {
            color: #feb2b2;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>{{ __('Login') }}</h2>
        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="form-group">
                <label>{{ __('Username') }}</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus>
                @if ($errors->has('username'))
                    <span class="error">{{ $errors->first('username') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ __('Password') }}</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">{{ __('SIGN IN') }}</button>
        </form>
    </div>
</body>
</html>
