<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTournametState extends Model
{
    use HasFactory;

    protected $table = 'usuarios_torneos_estados';

    public $timestamps = false;

    public function usuarios_torneo(){
        return $this->hasMany(UserTournamet::class);
    }

}
