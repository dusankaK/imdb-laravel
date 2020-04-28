<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Movie;

class MovieTableSeeder extends Seeder
{
    public function run()
    {
        factory(Movie::class, 50)->create();

        $genres = App\Genre::all();
        Movie::All()->each(function ($movie) use ($genres) {
            $movie->genres()->attach(
                $genres->random(rand(1, 2))->pluck('id')->toArray()
            ); 
        });
    }
}
