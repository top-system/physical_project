<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootBallEvent extends Model {

    protected $connection = 'mongodb';

    protected $collection = 'events';
    protected $primaryKey = '_id';

    protected $fillable = ['*'];
}
