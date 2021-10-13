<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertaCobranzasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerta_cobranzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('venta_id');
            $table->foreignId('callidus_venta_id');
            $table->foreignId('calculo_id');
            $table->integer('medidos');
            $table->string('contrato');
            $table->string('alerta');
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
        Schema::dropIfExists('alerta_cobranzas');
    }
}
