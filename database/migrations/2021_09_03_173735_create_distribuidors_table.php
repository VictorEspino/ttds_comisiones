<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistribuidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribuidors', function (Blueprint $table) {
            $table->id();
            $table->foreingId('user_id');
            $table->integer('numero_distribuidor');
            $table->string('nombre');
            $table->string('region');
            $table->float('a_24');
            $table->float('a_18');
            $table->float('a_12');
            $table->float('r_24');
            $table->float('r_18');
            $table->float('r_12');
            $table->boolean('bono')->default('0');
            $table->boolean('residual')->default('0');
            $table->integer('porcentaje_residual')->default('0');
            $table->boolean('adelanto')->default('0');
            $table->integer('porcentaje_adelanto')->default('0');
            $table->boolean('emite_factura')->default(0);
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
        Schema::dropIfExists('distribuidors');
    }
}
