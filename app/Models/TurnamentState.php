<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnamentState extends Model
{
    use HasFactory;

    protected $table = 'torneos_estados';


    public function torneos(){
        return $this->hasMany(Turnament::class);
    }

}
