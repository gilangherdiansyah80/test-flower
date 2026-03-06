<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Favorite;
use Illuminate\Support\Facades\Session;

class MovieController extends Controller
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://www.omdbapi.com/']);
        $this->apiKey = env('OMDB_API_KEY', '699b04d5');

        // Simple middleware-like check, excluding the index method
        $this->middleware(function ($request, $next) {
            if (!Session::has('user')) {
                return redirect()->route('login');
            }
            return $next($request);
        })->only(['show', 'toggleFavorite', 'favorites']);
    }

    public function index(Request $request)
    {
        $search = $request->get('s', ''); // Form value
        $page = $request->get('page', 1);

        $queryParam = empty($search) ? 'movie' : $search;

        $movies = [];
        $totalResults = 0;

        try {
            $response = $this->client->get('/', [
                'query' => [
                    'apikey' => $this->apiKey,
                    's' => $queryParam,
                    'page' => $page
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Explicitly handle OMDB API errors
            if (isset($data['Response']) && $data['Response'] === 'False') {
                throw new \Exception($data['Error'] ?? 'Unknown API Error');
            }

            $movies = isset($data['Search']) ? $data['Search'] : [];
            $totalResults = isset($data['totalResults']) ? $data['totalResults'] : 0;
            
            $favIDs = Session::has('user') ? Favorite::pluck('imdbID')->toArray() : [];

            if ($request->ajax()) {
                return response()->json([
                    'movies' => $movies,
                    'totalResults' => $totalResults,
                    'favIDs' => $favIDs
                ]);
            }

            return view('movies.index', compact('movies', 'totalResults', 'search', 'favIDs'));
        } catch (\Exception $e) {
            $movies = [];
            $totalResults = 0;
            $favIDs = [];
            return view('movies.index', compact('movies', 'totalResults', 'search', 'favIDs'))
                   ->with('error', 'Ready to start? Error loading data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = $this->client->get('/', [
                'query' => [
                    'apikey' => $this->apiKey,
                    'i' => $id,
                    'plot' => 'full'
                ]
            ]);

            $movie = json_decode($response->getBody()->getContents(), true);
            $isFavorite = Favorite::where('imdbID', $id)->exists();

            return view('movies.show', compact('movie', 'isFavorite'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch movie details.');
        }
    }

    public function toggleFavorite(Request $request)
    {
        $imdbID = $request->get('imdbID');

        $favorite = Favorite::where('imdbID', $imdbID)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        }

        Favorite::create([
            'imdbID' => $imdbID,
            'title' => $request->get('Title'),
            'year' => $request->get('Year'),
            'type' => $request->get('Type'),
            'poster' => $request->get('Poster'),
        ]);

        return response()->json(['status' => 'added']);
    }

    public function favorites()
    {
        $favorites = Favorite::latest()->get();
        return view('movies.favorites', compact('favorites'));
    }
}
