<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Genre;
use App\Comment;
use App\MovieReaction;

class Movie extends Model
{
    public function genres() 
    {
        return $this->belongsToMany(Genre::class);
    }

    public function reactions() 
    {
        return $this->hasMany(MovieReaction::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
