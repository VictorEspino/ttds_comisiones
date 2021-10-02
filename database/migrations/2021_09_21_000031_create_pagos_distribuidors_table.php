<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosDistribuidorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_distribuidors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculo_id');
            $table->foreignId('user_id');
            $table->integer('version');
            $table->integer('nuevas');
            $table->float('renta_nuevas');
            $table->float('comision_nuevas');
            $table->float('bono_nuevas');
            $table->integer('adiciones');
            $table->float('renta_adiciones');
            $table->float('comision_adiciones');
            $table->float('bono_adiciones');
            $table->integer('renovaciones');
            $table->float('renta_renovaciones');
            $table->float('comision_renovaciones');
            $table->float('bono_renovaciones');
            $table->integer('nuevas_no_pago');
            $table->float('nuevas_renta_no_pago');
            $table->float('nuevas_comision_no_pago');
            $table->float('nuevas_bono_no_pago');
            $table->integer('adiciones_no_pago');
            $table->float('adiciones_renta_no_pago');
            $table->float('adiciones_comision_no_pago');
            $table->float('adiciones_bono_no_pago');
            $table->integer('renovaciones_no_pago');
            $table->float('renovaciones_renta_no_pago');
            $table->float('renovaciones_comision_no_pago');
            $table->float('renovaciones_bono_no_pago');
            $table->float('anticipo_ordinario');
            $table->float('anticipo_no_pago');
            $table->float('residual');
            $table->float('charge_back');
            $table->float('anticipos_extraordinarios');
            $table->float('retroactivos_reproceso');
            $table->float('total_pago');
            $table->string('pdf',50)->nullable();
            $table->string('xml',50)->nullable();
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
        Schema::dropIfExists('pagos_distribuidors');
    }
}
