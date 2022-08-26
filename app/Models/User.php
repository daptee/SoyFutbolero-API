<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
        *
        * @var array<int, string>
        */
        protected $fillable = [
            'usuario',
            'nombre',
            'apellido',
            'id_genero',
            'confirma_mail',
            'dni',
            'estado',
            'is_admin',
            'mail',
            'password',
            'foto'
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id_genero',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function genero(){
        return $this->belongsTo(UsuarioGenero::class,'id_genero');
    }

    public function usuarios_torneo(){
        return $this->hasMany(UserTournamet::class);
    }

    public function usuario_notificacion(){
        return $this->hasMany(UserNotification::class, 'usuario_id', 'id');
    }

    public function medallero(){
        return $this->hasMany(Medallero::class,'usuario_id','id');
    }

    public function prediccion(){
        return $this->hasOne(UserPrediction::class,'id_usuario', 'id');
    }

    public function usuarios_desafio(){
        return $this->hasMany(DesafioUsuario::class);
    }
}
