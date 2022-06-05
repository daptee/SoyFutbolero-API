<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'equipos';

    protected $hidden = ['id_tipo'];

    protected $fillable = ['nombre','escudo','bandera','estado','id_tipo'];

    public $timestamps = false;


    public function stadium(){

        return $this->hasMany(Stadium::class);
    }

    public function tipo(){

        return $this->belongsTo(EquipoTipo::class,'id_tipo');
    }
}
