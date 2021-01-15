<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class devise extends Model
{
    public function Pays()
    {
        return $this->hasMany(pays::class);
    }
}
