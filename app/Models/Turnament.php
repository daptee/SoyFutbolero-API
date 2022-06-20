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

    public function estado(){
        return $this->BelongsTo(TurnamentState::class,'id_estado');
    }

    public function tipo(){
        return $this->BelongsTo(TurnamentType::class,'id_tipo_torneo');
    }

}
