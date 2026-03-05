<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Welcome</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            color: #fff;
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            font-weight: 200;
            letter-spacing: 2px;
        }
        p {
            font-size: 1.2rem;
            color: #a0aec0;
            max-width: 600px;
            margin-bottom: 3rem;
        }
        .btn-start {
            display: inline-block;
            padding: 1rem 3rem;
            border-radius: 2rem;
            background: #667eea;
            color: #fff;
            font-weight: bold;
            font-size: 1.1rem;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-start:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <h1>Welcome to MovieApp</h1>
    <p>Discover your favorite movies, search through an extensive database, and keep track of the films you love. Please log in to access the full features.</p>
    <a href="{{ route('home') }}" class="btn-start">Get Started</a>
</body>
</html>
