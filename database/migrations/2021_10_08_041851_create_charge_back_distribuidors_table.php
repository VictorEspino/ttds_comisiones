<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeBackDistribuidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_back_distribuidors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculo_id');
            $table->foreignId('callidus_venta_id');
            $table->foreignId('comision_venta_id');
            $table->float('charge_back');
            $table->float('cargo_equipo');
            $table->string('estatus',20);
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
        Schema::dropIfExists('charge_back_distribuidors');
    }
}
