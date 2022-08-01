<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medallero extends Model
{
    use HasFactory;

    protected $table = 'medallero';

    protected $fillable = [
        'torneo_id',
        'usuario_id',
        'puesto'
    ];

    protected $hidden = [
        'torneo_id',
        'usuario_id'
    ];

    public function torneo(){
        return $this->belongsTo(Turnament::class,'torneo_id');
    }

    public function usuarios(){
        return $this->belongsTo(User::class,'usuario_id');
    }
}
