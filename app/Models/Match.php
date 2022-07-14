<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    protected $fillable = [
        'id_torneo',
        'id_fase',
        'id_estadio',
        'id_equipo_1',
        'id_equipo_2',
        'goles_1',
        'goles_2',
        'penales_1',
        'penales_2',
        'id_estado',
        'fecha',
        'fecha_vencimiento_pronostico',
        'hora'
    ];

    protected $hidden = [
        'id_torneo',
        'id_fase',
        'id_estadio',
        'id_equipo_1',
        'id_equipo_2',
    ];

    public $timestamps = false;

    protected $attributes = [
        'goles_1' => 0,
        'goles_2' => 0,
        'penales_1' => 0,
        'penales_2' => 0,
        'id_estado' => 1
    ];

    public function fase(){
        return $this->belongsTo(Stage::class, 'id_fase')->with('tipoFase','tipoPartido');
    }

    public function torneo(){
        return $this->belongsTo(Turnament::class, 'id_torneo');
    }

    public function estadio(){
        return $this->belongsTo(Stadium::class, 'id_estadio');
    }

    public function equipo_local(){
        return $this->belongsTo(Team::class, 'id_equipo_1');
    }

    public function equipo_visitante(){
        return $this->belongsTo(Team::class, 'id_equipo_2');
    }
}
