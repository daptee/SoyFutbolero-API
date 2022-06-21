<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;
    protected $table = 'fases';

    protected $hidden = ['id_tipo_fase','id_tipo_partido'];

    public $timestamps = false;

    public function tipoFase(){
        return $this->belongsTo(TypeStage::class ,'id_tipo_fase');
    }

    public function tipoPartido(){
        return $this->belongsTo(TypeMatch::class ,'id_tipo_partido');
    }
}
