@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-heart text-danger"></i> {{ __('My Favorite Movies') }}
    </h2>
    <p style="color: var(--text-muted);">{{ __('Your personal collection of favorite films.') }}</p>
</div>

@if($favorites->isEmpty())
    <div style="text-align: center; padding: 4rem 2rem; background: var(--card-bg); border-radius: 1rem; border: 1px dashed rgba(255,255,255,0.1);">
        <i class="far fa-folder-open" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">{{ __('No favorites yet') }}</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">{{ __('Start exploring and adding movies to your favorite list!') }}</p>
        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Explore Movies') }}</a>
    </div>
@else
    <div class="movie-grid">
        @foreach($favorites as $movie)
            <div class="movie-card" id="fav-card-{{ $movie->imdbID }}">
                <a href="{{ route('movies.show', $movie->imdbID) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $movie->poster == 'N/A' ? 'https://via.placeholder.com/300x450?text=No+Poster' : $movie->poster }}" 
                         alt="{{ $movie->title }}" 
                         class="movie-poster"
                         loading="lazy">
                    
                    <button class="fav-btn active" 
                            onclick="event.preventDefault(); removeFavorite('{{ $movie->imdbID }}', this)"
                            title="{{ __('Remove from favorites') }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>

                    <div class="movie-info">
                        <h3 class="movie-title">{{ $movie->title }}</h3>
                        <div class="movie-meta">
                            <span>{{ $movie->year }}</span>
                            <span style="text-transform: capitalize;">{{ $movie->type }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection

@section('scripts')
<script>
    function removeFavorite(imdbID, btn) {
        if(confirm("{{ __('Are you sure you want to remove this movie from your favorites?') }}")) {
            $.post("{{ route('favorites.toggle') }}", {
                imdbID: imdbID
            }, function(data) {
                if (data.status === 'removed') {
                    $('#fav-card-' + imdbID).fadeOut(300, function() {
                        $(this).remove();
                        // Check if grid is empty after removal
                        if($('.movie-card').length === 0) {
                            location.reload(); // Reload to show the empty state
                        }
                    });
                }
            });
        }
    }
</script>
@endsection
