<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesafioEstado extends Model
{
    use HasFactory;

    protected $table = 'desafio_estado';

    protected $fillable = [
        'nombre'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function desafios(){
        return $this->hasMany(Desafio::class);
    }
}
