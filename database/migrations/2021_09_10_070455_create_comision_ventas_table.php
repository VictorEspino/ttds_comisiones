<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comision_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id');
            $table->foreignId('calculo_id');
            $table->foreignId('calculo_id_proceso');
            $table->float('upfront');
            $table->float('bono');
            $table->string('estatus');
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
        Schema::dropIfExists('comision_ventas');
    }
}
