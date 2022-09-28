<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDesafio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desafio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->bigInteger('desafio_estado_id')->unsigned();
            $table->foreign('desafio_estado_id')->references('id')->on('desafio_estado');
            $table->bigInteger('torneo_id')->unsigned();
            $table->foreign('torneo_id')->references('id')->on('torneos');
            $table->bigInteger('usuario_creacion_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desafio');
    }
}
