<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DesafioEstado;

class CreateRowDesafiosEstados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DesafioEstado::create(['nombre' => "Pendiente"]);
        DesafioEstado::create(['nombre' => "Aceptado"]);
        DesafioEstado::create(['nombre' => "Rechazado"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        #
    }
}
