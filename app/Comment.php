<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Movie;

class Comment extends Model
{
    public function movies() 
    {
        return $this->belongsTo(Movie::class);
    }
}
