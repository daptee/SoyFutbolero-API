<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesafioUsuario extends Model
{
    use HasFactory;

    protected $table = 'desafio_usuarios';

    protected $fillable = [
        'usuario_id',
        'desafio_id',
        'estado',
        'usuario_mail'
    ];

    protected $hidden = [
        'usuario_id',
        'desafio_id',
        'created_at',
        'updated_at'
    ];

    public function desafio(){
        return $this->belongsTo(Desafio::class,'desafio_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class,'usuario_id');
    }
}
