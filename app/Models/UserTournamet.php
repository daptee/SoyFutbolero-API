<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTournamet extends Model
{
    use HasFactory;

    protected $table = 'usuarios_torneos';

    protected $hidden = ['id_usuario','usuario_id'];

    protected $attributes = [
        'id_estado' => 1,
    ];

    protected $fillable = ['id_usuario','id_torneo','id_estado','usuario_id'];

    public $timestamps = false;

    public function estado(){
        return $this->belongsTo(UserTournametState::class,'id_estado');
    }

    public function usuario(){
        return $this->belongsTo(User::class,'id_usuario');
    }

    public function torneo(){
        return $this->belongsTo(Turnament::class,'id_torneo');
    }

}
