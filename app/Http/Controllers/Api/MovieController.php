<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Movie;
use App\MovieReaction;
use App\User;
use App\Comment;
use Illuminate\Pagination\LengthAwarePaginator;


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
        if($request->search && !$request->genre) {
            return Movie::with('reactions')->where('title', 'LIKE', "%$request->search%")->paginate(10);
        }

        if($request->genre && !$request->search) {
            $genres = explode(",", $request->genre);
            return Movie::with('genres', 'reactions')
                ->whereHas('genres', function($q) use ($genres) {
                    $q->whereIn('genres.id', $genres);
                })->paginate(10);
        }

        if($request->genre && $request->search) {
            $genres = explode(",", $request->genre);
            return Movie::with('genres', 'reactions')
                ->whereHas('genres', function($q) use ($genres) {
                    $q->whereIn('genres.id', $genres);
                })
                ->where('title', 'LIKE', "%$request->search%")->paginate(10);
        }

        return Movie::with('genres', 'reactions')->paginate(10);
    }

    /**
     * Handle incoming movie reaction. Store new or update existing one
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleReaction(Request $request)
    {
        $uid = auth()->user()->id;
        $mid = $request->movie_id;
        $movie = Movie::find($mid);
        $reaction = MovieReaction::where('movie_id', $mid)
            ->where('user_id', $uid)
            ->first();

        if ($request->reaction == 'like') {
            if (!$reaction) {
                $nr = new MovieReaction;
                $nr->movie_id = $mid;
                $nr->user_id = $uid;
                $nr->liked = 1;
                $nr->save();

                $movie->likes = $movie->likes + 1;
                $movie->save();
                return response()->json(['message' => 'Movie ' . $movie->title . ' liked.'],  200);
            }

            if ($reaction->liked) {
                return response()->json(['message' => 'Movie ' . $movie->title . ' already liked.'],  200);
            }

            $reaction->liked = 1;
            $movie->likes = $movie->likes + 1;
            $reaction->save();
            $movie->save();
            return response()->json(['message' => 'Movie ' . $movie->title . ' liked.'],  200);
        }
        if ($request->reaction == 'dislike') {
            if (!$reaction) {
                $nr = new MovieReaction;
                $nr->movie_id = $mid;
                $nr->user_id = $uid;
                $nr->disliked = 1;
                $nr->save();

                $movie->dislikes = $movie->dislikes + 1;
                $movie->save();
                return response()->json(['message' => 'Movie ' . $movie->title . ' disliked.'],  200);
            }

            if ($reaction->disliked) {
                return response()->json(['message' => 'Movie ' . $movie->title . ' already disliked.'],  200);
            }

            $reaction->disliked = 1;
            $movie->dislikes = $movie->dislikes + 1;
            $reaction->save();
            $movie->save();
            return response()->json(['message' => 'Movie ' . $movie->title . ' disliked.'],  200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Movie::with('genres', 'reactions', 'comments')->findOrFail($id);
        $movie->visit_count += 1;
        $movie->save();
        return $movie;
        
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
