<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'titulo',
        'mensaje',
        'torneo_id'
    ];

    public function torneo(){
        return $this->belongsTo(Turnament::class,'torneo_id');
    }

    public function usuario_notificacion(){
        return $this->hasMany(UserNotification::class, 'notificacion_id', 'id');
    }

}
