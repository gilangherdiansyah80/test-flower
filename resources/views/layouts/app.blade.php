<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MovieApp') - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            margin: 0;
            line-height: 1.6;
        }
        .navbar {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 2rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            gap: 1rem;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            order: 2;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
        }
        .nav-links a:hover, .nav-links a.active {
            color: var(--text-main);
        }
        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }
        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        .container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border: none;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--text-muted);
            color: var(--text-muted);
        }
        .btn-outline:hover {
            border-color: var(--text-main);
            color: var(--text-main);
        }
        /* Grid System */
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        /* Card Styles */
        .movie-card {
            background: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }
        .movie-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            border-color: rgba(99, 102, 241, 0.5);
        }
        .movie-poster {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            background: #1e293b;
        }
        .movie-info {
            padding: 1.2rem;
        }
        .movie-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .movie-meta {
            display: flex;
            justify-content: space-between;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .fav-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            cursor: pointer;
            transition: all 0.3s;
        }
        .fav-btn.active {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }
        .lang-switch {
            display: flex;
            background: var(--glass-bg);
            padding: 0.3rem;
            border-radius: 0.5rem;
            gap: 0.3rem;
        }
        .lang-btn {
            padding: 0.2rem 0.6rem;
            border-radius: 0.3rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }
        .lang-btn.active {
            background: var(--primary);
            color: #fff;
        }
        .nav-search {
            flex-grow: 1;
            max-width: 400px;
            margin: 0;
            position: relative;
            order: 1;
        }
        .nav-search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border-radius: 2rem;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s;
        }
        .nav-search-input:focus {
            border-color: var(--primary);
            background: rgba(30, 41, 59, 0.9);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .nav-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .nav-auth {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: 1rem;
            border-left: 1px solid rgba(255,255,255,0.1);
            padding-left: 1.5rem;
        }
        .user-name {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .login-link {
            padding: 0.4rem 1.2rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }
        /* Responsive Design */
        @media (max-width: 1150px) {
            .navbar {
                padding: 1rem 1.5rem;
            }
            .nav-search {
                order: 3;
                max-width: 100%;
                width: 100%;
                margin: 0.5rem 0 0 0;
            }
            .nav-links {
                gap: 1rem;
            }
        }
        @media (max-width: 900px) {
            .nav-links span, .nav-auth span, .user-name {
                display: none;
            }
            .nav-links {
                gap: 1.2rem;
            }
            .nav-auth {
                margin-left: 0.5rem;
                padding-left: 1rem;
            }
            .logout-btn, .login-link {
                padding: 0.5rem;
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        @media (max-width: 600px) {
            .navbar-brand {
                font-size: 1.4rem;
            }
            .nav-links {
                width: 100%;
                order: 4;
                justify-content: space-between;
                padding-top: 1rem;
                border-top: 1px solid rgba(255,255,255,0.1);
                margin-top: 0.5rem;
            }
            .nav-auth {
                border-left: none;
                padding-left: 0;
            }
            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }
            .movie-info {
                padding: 0.8rem;
            }
            .movie-title {
                font-size: 1rem;
            }
            .movie-meta {
                flex-direction: column;
                gap: 0.2rem;
                font-size: 0.8rem;
            }
            .container {
                margin: 1rem auto;
            }
        }
        @yield('styles')
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('home') }}" class="navbar-brand">
            <i class="fas fa-play-circle"></i> MOVIEBOOK
        </a>

        @if(Request::is('movies') || Request::is('/'))
        <div class="nav-search">
            <i class="fas fa-search nav-search-icon"></i>
            <form id="global-search-form" action="{{ route('home') }}" method="GET">
                <input type="text" id="global-search-box" name="s" class="nav-search-input" placeholder="{{ __('Search movies...') }}" value="{{ request('s', '') == 'movie' ? '' : request('s') }}" autocomplete="off">
            </form>
        </div>
        @endif

        <div class="nav-links">
            <a href="{{ route('home') }}" class="{{ Request::is('/') || Request::is('movies') ? 'active' : '' }}">
                <i class="fas fa-compass"></i> <span>{{ __('Explore') }}</span>
            </a>
            <a href="{{ route('favorites.list') }}" class="{{ Request::is('favorites*') ? 'active' : '' }}">
                <i class="fas fa-heart"></i> <span>{{ __('My Favorites') }}</span>
            </a>
            
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}" style="text-decoration:none">EN</a>
                <a href="{{ route('lang.switch', 'id') }}" class="lang-btn {{ app()->getLocale() == 'id' ? 'active' : '' }}" style="text-decoration:none">ID</a>
            </div>

            <div class="nav-auth">
                @if(session()->has('user'))
                    <span class="user-name">{{ session('user') }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> <span>{{ __('Logout') }}</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline login-link">
                        <i class="fas fa-user"></i> <span>{{ __('Login') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 1rem;">
        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #34d399; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add a clean way to submit search if not on the home page
        $('#global-search-form').on('submit', function(e) {
            if(!window.location.pathname.match(/^\/?(movies)?$/)) {
                // Let the normal form submission happen
                return true;
            }
            // If on home page, javascript handles it (see index.blade.php)
        });

        function changeLang(lang) {
            window.location.href = "{{ url('/lang') }}/" + lang;
        }

        function toggleFavorite(imdbID, Title, Year, Type, Poster, btn) {
            @if(!session()->has('user'))
                window.location.href = "{{ route('login') }}";
                return;
            @endif

            $.post("{{ route('favorites.toggle') }}", {
                imdbID: imdbID,
                Title: Title,
                Year: Year,
                Type: Type,
                Poster: Poster
            }, function(data) {
                if (data.status === 'added') {
                    $(btn).addClass('active').find('i').removeClass('far').addClass('fas');
                } else {
                    $(btn).removeClass('active').find('i').removeClass('fas').addClass('far');
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
