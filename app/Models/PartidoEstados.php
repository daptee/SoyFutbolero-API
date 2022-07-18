<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidoEstados extends Model
{
    use HasFactory;

    protected $table = 'partidos_estados';


    public $timestamps = false;

    public function partidos(){
        return $this->hasMany(Match::class);
    }

}
