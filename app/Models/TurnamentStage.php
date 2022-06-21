<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnamentStage extends Model
{
    use HasFactory;

    protected $table = 'torneos_fases';

    protected $fillable = ['id_fase','id_torneo'];

    protected $hidden = ['id_fase','id_torneo'];

    public $timestamps = false;

    public function torneo(){
        return $this->belongsTo(Turnament::class,'id_torneo');
    }

    public function fase(){
        return $this->belongsTo(Stage::class,'id_fase')->with(['tipoFase','tipoPartido']);
    }
}
