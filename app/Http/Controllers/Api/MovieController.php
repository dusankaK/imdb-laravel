<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Movie;
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
            return Movie::where('title', 'LIKE', "%$request->search%")->paginate(10);
        }

        if($request->genre && !$request->search) {
            $genres = explode(",", $request->genre);
            return Movie::with('genres')
                ->whereHas('genres', function($q) use ($genres) {
                    $q->whereIn('genres.id', $genres);
                })->paginate(10);
        }

        if($request->genre && $request->search) {
            $genres = explode(",", $request->genre);
            return Movie::with('genres')
                ->whereHas('genres', function($q) use ($genres) {
                    $q->whereIn('genres.id', $genres);
                })
                ->where('title', 'LIKE', "%$request->search%")->paginate(10);
        }

        return Movie::with('genres')->paginate(10);
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
        return Movie::where('id', $id)->with('genres')->first();
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
