<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoTipo extends Model
{
    use HasFactory;

    protected $table = 'equipos_tipo';
    public $timestamps = false;

    public function team(){

        return $this->hasMany(Team::class);
    }

}
