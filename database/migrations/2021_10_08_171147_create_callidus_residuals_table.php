<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallidusResidualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callidus_residuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculo_id');
            $table->string('periodo');
            $table->string('cuenta');
            $table->string('contrato');
            $table->string('contrato_anterior');
            $table->string('cliente')->nullable();
            $table->string('plan');
            $table->string('dn');
            $table->string('propiedad');
            $table->string('modelo')->nullable();
            $table->date('fecha');
            $table->integer('plazo');
            $table->float('descuento_multirenta');
            $table->float('afectacion_comision')->nullable();
            $table->float('comision')->nullable();
            $table->float('factor_comision')->nullable();
            $table->float('renta');
            $table->string('estatus');
            $table->string('marca');
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
        Schema::dropIfExists('callidus_residuals');
    }
}
