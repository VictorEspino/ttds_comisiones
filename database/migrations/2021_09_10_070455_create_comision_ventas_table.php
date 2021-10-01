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
            $table->foreignId('calculo_id_proceso')->default(0);
            $table->foreignId('calculo_id_consistencia')->default(0); //De manera original debe ser 0
            $table->foreignId('callidus_venta_id')->default(0);
            $table->integer('version');
            $table->string('estatus_inicial',30);
            $table->boolean('consistente'); //se llena en el proceso que lo paga
            $table->string('estatus_final',50);
            $table->float('upfront')->default(0);
            $table->float('bono')->default(0);
            $table->float('upfront_final')->default(0); //se van a modificar cuando caiga el recalculo por inconsistencia
            $table->float('bono_final')->default(0); //se van a modificar cuando caiga el recalculo por inconsistencia
            $table->float('diferencia_inconsistencia')->default(0); ////se van a modificar cuando caiga el recalculo por inconsistencia
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
