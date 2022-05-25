<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class statistiqueStagiaire extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'statistique_stagiaires';
    protected $guarded = [];
}
