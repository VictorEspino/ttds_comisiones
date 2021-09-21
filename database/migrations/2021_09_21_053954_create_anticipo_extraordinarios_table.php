<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnticipoExtraordinariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anticipo_extraordinarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->date('fecha_relacionada');
            $table->float('anticipo');
            $table->string('descripcion');
            $table->boolean('aplicado');
            $table->foreignId('aplicado_calculo_id')->default('0');
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
        Schema::dropIfExists('anticipo_extraordinarios');
    }
}
