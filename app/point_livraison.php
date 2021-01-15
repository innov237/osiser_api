<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class point_livraison extends Model
{
    protected $fillable = [
        'pays','ville','rue','Description'
    ];
}
