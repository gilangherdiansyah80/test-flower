<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $connection = 'sqlite';

    protected $fillable = [
        'imdbID', 'title', 'year', 'type', 'poster'
    ];
}
