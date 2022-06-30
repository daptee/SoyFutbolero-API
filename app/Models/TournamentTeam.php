<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentTeam extends Model
{
    use HasFactory;

    protected $table = 'torneos_equipos';

    protected $fillable = ['id_equipo','id_grupo'];

    protected $hidden = ['id_equipo','id_grupo'];

    public $timestamps = false;


    public function team(){
        return $this->belongsTo(Team::class,'id_equipo');
    }

    public function group(){
        return $this->belongsTo(TournamentGroups::class,'id_grupo');
    }

}
