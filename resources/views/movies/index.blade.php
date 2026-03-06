@extends('layouts.app')

@section('title', 'Explore Movies')

@section('styles')
    .loading-spinner {
        text-align: center;
        padding: 2rem;
        display: none;
    }
    .load-more-trigger {
        height: 10px;
        margin-top: 2rem;
    }
    .no-results {
        text-align: center;
        padding: 5rem 0;
        color: var(--text-muted);
    }
    .no-results i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        display: block;
        opacity: 0.5;
    }
@endsection

@section('content')
    <div id="movie-list" class="movie-grid">
        @forelse($movies as $movie)
            <div class="movie-card">
                <button class="fav-btn {{ in_array($movie['imdbID'], $favIDs) ? 'active' : '' }}" 
                        onclick="toggleFavorite('{{ $movie['imdbID'] }}', '{{ addslashes($movie['Title']) }}', '{{ $movie['Year'] }}', '{{ $movie['Type'] }}', '{{ $movie['Poster'] }}', this)">
                    <i class="{{ in_array($movie['imdbID'], $favIDs) ? 'fas' : 'far' }} fa-heart"></i>
                </button>
                <a href="{{ route('movies.show', $movie['imdbID']) }}" style="text-decoration:none; color:inherit">
                    <img src="{{ $movie['Poster'] != 'N/A' ? $movie['Poster'] : 'https://via.placeholder.com/300x450?text=No+Poster' }}" class="movie-poster" alt="{{ $movie['Title'] }}" loading="lazy">
                    <div class="movie-info">
                        <h3 class="movie-title">{{ $movie['Title'] }}</h3>
                        <div class="movie-meta">
                            <span>{{ $movie['Year'] }}</span>
                            <span style="text-transform: capitalize">{{ $movie['Type'] }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="no-results" style="grid-column: 1/-1">
                <i class="fas fa-film"></i>
                <h3>{{ __('No movies found') }}</h3>
                <p>{{ __('Try searching for something else') }}</p>
            </div>
        @endforelse
    </div>

    <div id="loading" class="loading-spinner">
        <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--primary)"></i>
    </div>

    <div id="load-more-trigger" class="load-more-trigger"></div>
@endsection

@section('scripts')
<script>
    let page = 1;
    let loading = false;
    let hasMore = true;
    let search = $('#global-search-box').val() || '';
    let favIDs = @json($favIDs);

    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if(!loading && hasMore) {
                loadMore();
            }
        }
    });

    $('#global-search-form').submit(function(e) {
        e.preventDefault();
        search = $('#global-search-box').val();
        if (search.length < 3) return;
        
        page = 1;
        $('#movie-list').empty();
        hasMore = true;
        loadMore();
    });

    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        $('#loading').show();

        $.get("{{ route('home') }}", { s: search, page: page }, function(data) {
            loading = false;
            $('#loading').hide();

            if (data.movies && data.movies.length > 0) {
                renderMovies(data.movies, data.favIDs);
                page++;
                if (data.movies.length < 10) {
                    hasMore = false;
                }
            } else {
                hasMore = false;
                if (page === 1) {
                    $('#movie-list').html('<div class="no-results" style="grid-column: 1/-1"><i class="fas fa-film"></i><h3>No movies found</h3><p>Try searching for something else</p></div>');
                }
            }
        }).fail(function(xhr) {
            loading = false;
            $('#loading').hide();
            const message = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Failed to load movies.';
            alert(message);
        });
    }

    function renderMovies(movies, currentFavIDs) {
        movies.forEach(movie => {
            const isFav = currentFavIDs.includes(movie.imdbID);
            const poster = movie.Poster !== 'N/A' ? movie.Poster : 'https://via.placeholder.com/300x450?text=No+Poster';
            const html = `
                <div class="movie-card">
                    <button class="fav-btn ${isFav ? 'active' : ''}" 
                            onclick="toggleFavorite('${movie.imdbID}', '${movie.Title.replace(/'/g, "\\'")}', '${movie.Year}', '${movie.Type}', '${movie.Poster}', this)">
                        <i class="${isFav ? 'fas' : 'far'} fa-heart"></i>
                    </button>
                    <a href="/movies/${movie.imdbID}" style="text-decoration:none; color:inherit">
                        <img src="${poster}" class="movie-poster" alt="${movie.Title}" loading="lazy">
                        <div class="movie-info">
                            <h3 class="movie-title">${movie.Title}</h3>
                            <div class="movie-meta">
                                <span>${movie.Year}</span>
                                <span style="text-transform: capitalize">${movie.Type}</span>
                            </div>
                        </div>
                    </a>
                </div>
            `;
            $('#movie-list').append(html);
        });
    }
</script>
@endsection
