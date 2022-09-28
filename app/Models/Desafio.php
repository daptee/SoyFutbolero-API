<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desafio extends Model
{
    use HasFactory;

    protected $table = 'desafio';

    protected $fillable = [
        'nombre',
        'desafio_estado_id',
        'usuario_creacion_id',
        'torneo_id'
    ];

    protected $hidden = [
        'desafio_estado_id',
        'usuario_creacion_id',
        'created_at',
        'updated_at'
    ];

    protected $attributes = [
        'desafio_estado_id' => 1,
    ];

    public function usuarios_desafio(){
        return $this->hasMany(DesafioUsuario::class)->with('usuario');
    }

    public function estado(){
        return $this->belongsTo(DesafioEstado::class,'desafio_estado_id');
    }

    public function torneo(){
        return $this->belongsTo(Turnament::class,'torneo_id');
    }
}
