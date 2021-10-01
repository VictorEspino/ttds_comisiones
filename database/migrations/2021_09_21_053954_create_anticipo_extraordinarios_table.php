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
            $table->foreignId('periodo_id');
            $table->foreignId('calculo_id_aplicado')->default(0);
            $table->string('descripcion');
            $table->float('anticipo');
            $table->boolean('en_adelanto')->default(0);
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
