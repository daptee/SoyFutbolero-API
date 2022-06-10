<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioGenero extends Model
{
    use HasFactory;

    protected $table = 'usuarios_generos';

    public function usuarios(){
        return $this->hasMany(User::class);
    }

}
