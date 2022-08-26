<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDesafioUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desafio_usuarios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usuario_id');
            $table->bigInteger('desafio_id')->unsigned();
            $table->foreign('desafio_id')->references('id')->on('desafio');
            $table->enum('estado', [0, 1])->default(0);
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
        Schema::dropIfExists('desafio_usuarios');
    }
}
