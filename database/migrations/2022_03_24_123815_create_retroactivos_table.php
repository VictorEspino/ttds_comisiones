<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetroactivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retroactivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('supervisor_id')->nullable();
            $table->foreignId('user_origen_id');
            $table->foreignId('venta_id');
            $table->foreignId('callidus_id');
            $table->float('retroactivo');
            $table->float('retroactivo_supervisor')->default(0);
            $table->string('comentario');
            $table->string('calculo_id');
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
        Schema::dropIfExists('retroactivos');
    }
}
