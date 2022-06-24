<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnamentType extends Model
{
    use HasFactory;

    protected $table = 'torneos_tipos';


    public function torneos(){
        return $this->hasMany(Turnament::class);
    }


}
