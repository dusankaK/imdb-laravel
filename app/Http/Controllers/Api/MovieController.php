<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\MovieRequest;
use App\Http\Controllers\Controller;
use App\Movie;
use App\MovieReaction;
use App\User;
use App\Comment;
use App\Genre;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;



class MovieController extends Controller
{   

    public function __construct() 
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \App\Movie model
     */
    public function index(Request $request)
    {   

        $query = Movie::with('genres', 'reactions'); 

        if($request->genre) {
            $query->whereHas('genres', function($q) use ($request) {
                    $q->whereIn('genres.id', explode(",", $request->genre));
            });
        }
    
        if($request->search) { 
            $query->where('title', 'LIKE', "%$request->search%");
        }

        if($request->popular == true) {
            return Movie::orderBy('likes', 'desc')->take(10)->get();
        }
        
        return $query->paginate(10);

        
    }

    /**
     * Handle incoming movie reaction. Store new or update existing one
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleReaction(Request $request)
    {

        $movie = Movie::findOrFail($request->movie_id);

        $reaction = MovieReaction::updateOrCreate([
            'movie_id' => $movie->id,
            'user_id' => auth()->user()->id
        ], [
            'liked' => $request->reaction == 'like' ? 1 : 0,
            'disliked' => $request->reaction == 'like' ? 0 : 1
        ]);

        if ($reaction->wasRecentlyCreated) {
            $movie->increment($request->reaction == 'like' ? 'likes' : 'dislikes');

            $message = $request->reaction == 'like' ? "Movie {$movie->title} liked." : "Movie {$movie->title} disliked.";
        } else {
            $message = $request->reaction == 'like' ? "Movie {$movie->title} already liked." : "Movie {$movie->title} already disliked.";
        }
        
        return response()->json(compact('message'));
    }

    /**
     * Handle incoming watch mark. Store new or update existing one
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleWatchMark(Request $request)
    {
        $movie_id = $request->movie_id;
        $user = User::with('watchedMovies')->find(auth()->user()->id);
        $hasWatched = $user->watchedMovies->contains($movie_id);

        if ($hasWatched) {
            $user->watchedMovies()->detach($movie_id);
            return response()->json(['message' => 'Deleted from watchlist.', 'watched' => false], 200);
        }

        $user->watchedMovies()->attach($movie_id);
        return response()->json(['message' => 'Added to watchlist.', 'watched' => true], 200);
    } 


    public function relatedMovies(Request $request)
    {
        $genres = $request->genres;

        return Movie::with('genres')
            ->whereHas('genres', function ($q) use ($genres) {
                $q->whereIn('genres.name', [$genres]);
            })
            ->take(10)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovieRequest $request)
    {
        $movData = $request->only('title', 'description', 'image_url');
        $genData = $request->genres;
        $movie = Movie::create($movData);

        foreach ($genData as $genreId) {
            $movie->genres()->attach($genreId);
        }

        return response()->json(['message' => 'Movie ' . $movie->title . ' added successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Movie::with('genres')->findOrFail($id);
        $movie->visit_count += 1;
        $movie->save();
        $movie->setRelation('comments', $movie->comments()->paginate(2));
        
        return response()->json([
            'movie' => $movie,
            'watched' => $movie->usersWhoWatched()->where('user_id', auth()->user()->id)->exists(),
          ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
