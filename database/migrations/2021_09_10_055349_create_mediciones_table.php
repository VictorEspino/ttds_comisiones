<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mediciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculo_id');
            $table->foreignId('user_id');
            $table->integer('version'); //Se refiere a si es una medicion de adelanto o cierre
            $table->integer('nuevas');
            $table->float('renta_nuevas');
            $table->integer('adiciones');
            $table->float('renta_adiciones');
            $table->integer('renovaciones');
            $table->float('renta_renovaciones');
            $table->float('porcentaje_nuevas');
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
        Schema::dropIfExists('mediciones');
    }
}
