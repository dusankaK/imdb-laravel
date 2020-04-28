<?php

use Illuminate\Database\Seeder;
use App\Genre;

class GenreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genres = [
            'action',
            'adventure',
            'comedy',
            'crime',
            'drama',
            'fantasy',
            'horror',
            'sci-fi',
            'war',
            'western'
        ];

        foreach($genres as $genre) 
        {
            Genre::create(['name' => $genre]);
        }
    }
}
