<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    use HasFactory;

    protected $table = 'estadios';

    protected $fillable = ['nombre','foto','id_equipo','estado'];

    protected $hidden = ['id_equipo'];

    public $timestamps = false;

    public function team(){

        return $this->belongsTo(Team::class,'id_equipo');
    }

    public function partidos(){
        return $this->hasMany(Match::class);
    }
}
