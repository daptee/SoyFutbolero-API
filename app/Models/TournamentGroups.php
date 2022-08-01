<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentGroups extends Model
{
    use HasFactory;

    protected $table = 'torneos_grupos';

    protected $fillable = ['id_torneo','grupo'];

    protected $hidden = ['id_torneo'];

    public $timestamps = false;

    public function tournament(){
        return $this->belongsTo(Turnament::class,'id_torneo');
    }

    public function teams(){
        return $this->hasMany(TournamentTeam::class,'id_grupo','id')->with('team');
    }

    public function match_group(){
        return $this->hasMany(MatchGroup::class);
    }
}
