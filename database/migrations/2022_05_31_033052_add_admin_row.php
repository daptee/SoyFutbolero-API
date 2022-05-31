<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;


class AddAdminRow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::create([
            'usuario' => 'admin_soy_futbolero',
            'password' => bcrypt('123456'),
            'nombre' => 'Administrador',
            'apellido' => '',
            'mail' => 'admin@soyfutbolero.com',
            'id_genero' => 1,
            'confirma_mail' => 1,
            'dni' => 0,
            'estado' => 1,
            'is_admin' => 1,
            'foto' => 'test'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
