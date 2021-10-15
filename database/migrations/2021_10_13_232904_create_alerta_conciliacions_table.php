<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertaConciliacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerta_conciliacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculo_id');
            $table->string('tipo');
            $table->foreignId('callidus_venta_id')->default(0);
            $table->foreignId('callidus_residual_id')->default(0);
            $table->string('contrato');
            $table->string('descripcion');
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
        Schema::dropIfExists('alerta_conciliacions');
    }
}
