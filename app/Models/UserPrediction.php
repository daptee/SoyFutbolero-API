<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrediction extends Model
{
    use HasFactory;

    protected $table = 'usuarios_predicciones';

    protected $fillable = [
        'id_usuario',
        'id_partido',
        'goles_1',
        'goles_2'
    ];

    protected $hidden = [
        'id_usuario',
        'id_partido'
    ];

    public $timestamps = false;

    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function partido(){
        return $this->belongsTo(Match::class, 'id_partido');
    }
}
