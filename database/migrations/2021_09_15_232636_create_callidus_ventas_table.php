<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallidusVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callidus_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreingId('calculo_id');
            $table->string('tipo');
            $table->string('periodo');
            $table->string('cuenta');
            $table->string('contrato');
            $table->string('cliente')->nullable();
            $table->string('plan');
            $table->string('dn');
            $table->string('propiedad');
            $table->string('modelo')->nullable();
            $table->date('fecha');
            $table->date('fecha_baja')->nullable();
            $table->integer('plazo');
            $table->float('descuento_multirenta');
            $table->float('afectacion_comision')->nullable();
            $table->float('comision');
            $table->float('renta');
            $table->integer('tipo_baja')->nullable();
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
        Schema::dropIfExists('callidus_ventas');
    }
}
