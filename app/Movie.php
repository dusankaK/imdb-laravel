<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Genre;
use App\Comment;
use App\MovieReaction;
use Elasticquent\ElasticquentTrait;


class Movie extends Model
{   
    use ElasticquentTrait;

    protected $guarded = [
        'id'
    ];

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

    public function usersWhoWatched()
    {
        return $this->belongsToMany(User::class, 'movie_user', 'movie_id', 'user_id');
    }
}
