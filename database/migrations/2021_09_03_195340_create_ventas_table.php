<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('supervisor_id')->nullable();
            $table->date('fecha');
            $table->string('cliente');
            $table->string('dn');
            $table->string('cuenta');
            $table->string('tipo');
            $table->string('folio');
            $table->string('ciudad');
            $table->string('plan');
            $table->float('renta');
            $table->string('equipo');
            $table->integer('plazo');
            $table->float('descuento_multirenta');
            $table->float('afectacion_comision');
            $table->string('propiedad');
            $table->string('contrato');
            $table->boolean('validado');
            $table->foreignId('user_id_carga');
            $table->foreignId('user_id_validacion');
            $table->string('carga_id',20);
            $table->boolean('lead');
            $table->foreignId('padrino_lead');
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
        Schema::dropIfExists('ventas');
    }
}
