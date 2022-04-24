<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Direction extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'directions';
    protected $guarded = [];
}
