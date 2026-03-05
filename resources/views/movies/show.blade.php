@extends('layouts.app')

@section('title', $movie['Title'])

@section('styles')
<style>
    .movie-detail {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 3rem;
        margin-top: 2rem;
        background: var(--card-bg);
        padding: 2.5rem;
        border-radius: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .detail-poster {
        width: 100%;
        border-radius: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .detail-info h1 {
        font-size: 3rem;
        margin: 0 0 1rem 0;
        font-weight: 700;
        line-height: 1.1;
    }
    .detail-meta {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
        color: var(--text-muted);
        align-items: center;
    }
    .badge {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.3rem 0.8rem;
        border-radius: 0.5rem;
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.9rem;
    }
    .rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #fbbf24;
        font-weight: 700;
        font-size: 1.2rem;
    }
    .plot {
        font-size: 1.1rem;
        color: #cbd5e1;
        margin-bottom: 2rem;
        line-height: 1.8;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }
    .detail-item h4 {
        color: var(--text-muted);
        margin: 0 0 0.5rem 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
    }
    .detail-item p {
        margin: 0;
        font-weight: 600;
    }
    @media (max-width: 900px) {
        .movie-detail {
            grid-template-columns: 1fr;
        }
        .detail-poster {
            max-width: 300px;
            margin: 0 auto;
            display: block;
        }
    }
    @media (max-width: 600px) {
        .movie-detail {
            padding: 1.5rem;
            gap: 1.5rem;
        }
        .detail-info h1 {
            font-size: 2rem;
        }
        .detail-meta {
            flex-wrap: wrap;
            gap: 0.8rem;
        }
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <a href="{{ route('home') }}" class="btn btn-outline" style="margin-bottom: 2rem">
        <i class="fas fa-arrow-left"></i> {{ __('Back to Explore') }}
    </a>

    <div class="movie-detail">
        <div class="poster-container">
            @php
                $highResPoster = $movie['Poster'] != 'N/A' ? str_replace('SX300.jpg', 'SX1000.jpg', $movie['Poster']) : 'https://via.placeholder.com/500x750?text=No+Poster';
            @endphp
            <img src="{{ $highResPoster }}" class="detail-poster" alt="{{ $movie['Title'] }}">
            
            <div style="margin-top:2rem">
                <button class="btn btn-primary {{ $isFavorite ? 'active' : '' }}" style="width:100%; justify-content:center"
                        onclick="toggleFavorite('{{ $movie['imdbID'] }}', '{{ addslashes($movie['Title']) }}', '{{ $movie['Year'] }}', '{{ $movie['Type'] }}', '{{ $movie['Poster'] }}', this)">
                    <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-heart"></i>
                    {{ $isFavorite ? __('Remove from Favorites') : __('Add to Favorites') }}
                </button>
            </div>
        </div>

        <div class="detail-info">
            <h1>{{ $movie['Title'] }}</h1>
            
            <div class="detail-meta">
                <span class="rating">
                    <i class="fas fa-star"></i> {{ $movie['imdbRating'] ?? 'N/A' }}
                </span>
                <span class="badge">{{ $movie['Year'] }}</span>
                <span class="badge">{{ $movie['Rated'] ?? 'N/A' }}</span>
                <span class="badge">{{ $movie['Runtime'] ?? 'N/A' }}</span>
            </div>

            <div class="plot">
                {{ $movie['Plot'] }}
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <h4>{{ __('Genre') }}</h4>
                    <p>{{ $movie['Genre'] }}</p>
                </div>
                <div class="detail-item">
                    <h4>{{ __('Director') }}</h4>
                    <p>{{ $movie['Director'] }}</p>
                </div>
                <div class="detail-item">
                    <h4>{{ __('Actors') }}</h4>
                    <p>{{ $movie['Actors'] }}</p>
                </div>
                <div class="detail-item">
                    <h4>{{ __('Released') }}</h4>
                    <p>{{ $movie['Released'] }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Override toggleFavorite to update button text
    const originalToggle = toggleFavorite;
    toggleFavorite = function(imdbID, Title, Year, Type, Poster, btn) {
        $.post("{{ route('favorites.toggle') }}", {
            imdbID: imdbID,
            Title: Title,
            Year: Year,
            Type: Type,
            Poster: Poster
        }, function(data) {
            if (data.status === 'added') {
                $(btn).addClass('active').html('<i class="fas fa-heart"></i> Remove from Favorites');
            } else {
                $(btn).removeClass('active').html('<i class="far fa-heart"></i> Add to Favorites');
            }
        });
    }
</script>
@endsection
