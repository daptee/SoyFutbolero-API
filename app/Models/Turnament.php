<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turnament extends Model
{
    use HasFactory;

    protected $table = 'torneos';

    protected $hidden = ['id_estado','id_tipo_torneo'];

    protected $fillable = ['nombre','directorio','user_crea','fecha_crea','hora_crea','precio','ganadores','id_tipo_torneo','id_estado'];

    public $timestamps = false;

    protected $attributes = [
        'id_estado' => 1,
        'fecha_crea' => 213,
        'hora_crea' => 1,
        'id_estado' => 1
    ];

    public function estado(){
        return $this->BelongsTo(TurnamentState::class,'id_estado');
    }

    public function tipo(){
        return $this->BelongsTo(TurnamentType::class,'id_tipo_torneo');
    }

    public function torneoFase(){
        return $this->hasMany(TurnamentStage::class,'id_torneo','id')->with('fase');
    }

    public function torneoGrupos(){
        return $this->hasMany(TournamentGroups::class,'id_torneo','id')->with('teams');
    }

    public function partidos(){
        return $this->hasMany(Match::class);
    }

    public function usuarios_torneo(){
        return $this->hasMany(UserTournamet::class,'id_torneo','id')->with(['usuario','estado']);
    }

    public function notificaciones(){
        return $this->hasMany(Notification::class,'id_torneo','id');
    }

    public function medallero(){
        return $this->hasMany(Medallero::class,'torneo_id','id')->with(['usuarios']);
    }
}
