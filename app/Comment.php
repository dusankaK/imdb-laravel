<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Movie;
use Elasticquent\ElasticquentTrait;


class Comment extends Model
{   
    use ElasticquentTrait;

    public function movies() 
    {
        return $this->belongsTo(Movie::class);
    }
}
