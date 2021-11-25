<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comision_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->default(0);
            $table->foreignId('calculo_id');
            $table->foreignId('callidus_id')->default(0);
            $table->integer('version');
            $table->float('comision')->default(0);
            $table->float('comision_supervisor')->default(0);
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
        Schema::dropIfExists('comision_addons');
    }
}
