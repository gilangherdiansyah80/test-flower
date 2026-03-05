# MovieBook - Movie Catalog Application

A premium movie catalog application built with Laravel 5.8, featuring real-time search, favorites management, and multi-language support.

## 🚀 Features

- **Authentication**: Static login (Username: `aldmic`, Password: `123abc123`).
- **Movie Exploration**: Browse movies via OMDB API integration.
- **Real-time Search**: Search for movies, series, or games with instant results.
- **Infinite Scroll**: Seamlessly load more movies as you scroll.
- **Movie Details**: Comprehensive view including ratings, plot, cast, and more.
- **Favorites Management**: Save your favorite movies to a dedicated list (stored in SQLite).
- **Multi-language Support**: Switch between English (EN) and Indonesian (ID).
- **Premium UI/UX**: Modern glassmorphism design with responsive layouts and smooth transitions.
- **Lazy Loading**: Optimized image loading for better performance.

## 🛠️ Tech Stack

- **Backend**: Laravel 5.8 (PHP 8.2 compatible)
- **Database**: SQLite
- **API**: OMDB API
- **Frontend**: Blade Templates, Vanilla CSS (Modern), jQuery
- **Icons**: Font Awesome 6
- **Typography**: Google Fonts (Outfit)

## 🏗️ Architecture

The application follows the **MVC (Model-View-Controller)** pattern:

- **Models**: `Favorite.php` handles database interactions for saved movies.
- **Views**: Blade templates using a unified layout (`app.blade.php`) for consistency.
- **Controllers**:
    - `LoginController`: Handles static authentication.
    - `MovieController`: Orchestrates OMDB API calls, search logic, and favorites processing.
- **Middleware**: `LocaleMiddleware` manages real-time language switching and persistence.

## 📦 Installation

1. Clone the repository.
2. Copy `.env.example` to `.env`.
3. Set your `OMDB_API_KEY` in `.env`.
4. Run `composer install`.
5. Run `php artisan migrate`.
6. Run `php artisan serve`.

## 📸 Screenshots

_(Screenshots will be placed here)_

### Explore Page

![Explore Page](public/screenshots/explore.png)

### Movie Detail

![Movie Detail](public/screenshots/detail.png)

### Favorites

![Favorites](public/screenshots/favorites.png)

### Mobile View

![Mobile View](public/screenshots/mobile.png)

---

Developed as part of the Web Developer Technical Test.
