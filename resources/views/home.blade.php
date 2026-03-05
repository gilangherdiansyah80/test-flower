<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            color: #333;
            font-family: 'Nunito', sans-serif;
            margin: 0;
        }
        .navbar {
            background: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4a5568;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #718096;
            transition: color 0.2s;
        }
        .nav-links a:hover {
            color: #4a5568;
        }
        .logout-btn {
            background: none;
            border: none;
            color: #e53e3e;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('home') }}" class="navbar-brand">MovieApp</a>
        <div class="nav-links">
            <a href="{{ route('home') }}">Movies</a>
            <a href="#">Favorites</a>
            <span>Welcome, {{ session('user') }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <h1>Movie List</h1>
        <p>OMDB API key: {{ env('OMDB_API_KEY') }}</p>
        <!-- Movie implementation will go here -->
    </div>
</body>
</html>
