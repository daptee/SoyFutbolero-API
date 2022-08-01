<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchGroup extends Model
{
    use HasFactory;

    protected $table = 'partidos_grupos';

    protected $fillable = [
        'id_grupo',
        'id_partido',
        'ronda'
    ];

    public $timestamps = false;

    public function partido(){
        return $this->belongsTo(Match::class, 'id_partido');
    }

    public function grupo(){
        return $this->belongsTo(TournamentGroups::class, 'id_grupo');
    }
}
