<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentGroups extends Model
{
    use HasFactory;

    protected $table = 'torneos_grupos';

    protected $fillable = ['id_torneo','grupo'];

    protected $hidden = ['id_torneo'];

    public $timestamps = false;
}
