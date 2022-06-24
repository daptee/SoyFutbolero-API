<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeStage extends Model
{
    use HasFactory;

    protected $table = 'fases_tipos';

    public $timestamps = false;

    public function fases(){
        return $this->hasMany(Stage::class);
    }

}
