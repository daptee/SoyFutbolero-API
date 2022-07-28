<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'usuario_notificaciones';

    protected $fillable = [
        'notificacion_id',
        'usuario_id'
    ];

    public function usuario_notificaciones(){
        return $this->belongsTo(Notification::class,'notificacion_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class,'usuario_id');
    }
}
