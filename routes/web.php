<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcessFormsController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\ProcessViewController;
use App\Http\Controllers\CalculoComisiones;
use App\Http\Controllers\CalculoController;
use App\Http\Controllers\ShowPagosController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\AnticiposExtraordinariosController;
use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\PagoACuentaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

//RUTAS DE CARGA DE VENTAS

Route::get('/venta_nueva', function () {return view('venta_nueva');})->middleware('auth')->name('venta_nueva')->middleware('auth');
Route::post('/venta_nueva', [ProcessFormsController::class,'venta_nueva'])->middleware('auth')->name('venta_nueva')->middleware('auth');
Route::get('/venta_import', function () {return view('venta_import');})->middleware('auth')->name('venta_import')->middleware('auth');
Route::post('/ventas_import', [ExcelController::class,'ventas_import'])->middleware('auth')->name('ventas_import')->middleware('auth');
Route::post('/ventas_import_admin', [ExcelController::class,'ventas_import_admin'])->middleware('auth')->name('ventas_import_admin')->middleware('auth');

//RUTAS DE ADMINISTRACION DE DISTRIBUIDORES

Route::get('/distribuidores_admin',[ProcessViewController::class,'distribuidores_admin'])->name('distribuidores_admin')->middleware('auth');
Route::get('/distribuidores_consulta/{id}',[ProcessViewController::class,'distribuidores_consulta'])->middleware('auth');
Route::post('/distribuidores_actualiza',[ProcessFormsController::class,'distribuidores_actualiza'])->name('guarda_cambios_distribuidor')->middleware('auth');
Route::post('/distribuidores_nuevo',[ProcessFormsController::class,'distribuidores_nuevo'])->name('distribuidores_nuevo')->middleware('auth');
Route::get('/distribuidores_nuevo',[ProcessViewController::class,'distribuidores_nuevo'])->name('distribuidores_nuevo')->middleware('auth');
Route::get('/distribuidores_anticipos_extraordinarios',[AnticiposExtraordinariosController::class,'distribuidores_anticipos_extraordinarios'])->name('distribuidores_anticipos_extraordinarios')->middleware('auth');
Route::post('/distribuidores_anticipos_extraordinarios',[AnticiposExtraordinariosController::class,'distribuidores_anticipos_extraordinarios_save'])->name('distribuidores_anticipos_extraordinarios')->middleware('auth');
Route::get('/anticipos_extraordinarios_consulta/{user_id}',[AnticiposExtraordinariosController::class,'anticipos_extraordinarios_consulta'])->middleware('auth');
Route::get('/anticipos_extraordinarios_borrar/{id}',[AnticiposExtraordinariosController::class,'anticipos_extraordinarios_borrar'])->middleware('auth');

//RUTAS DE ADMINISTRACION DE EMPLEADOS

Route::post('/empleados_nuevo',[EmpleadosController::class,'empleados_nuevo'])->name('empleados_nuevo')->middleware('auth');
Route::get('/empleados_nuevo',[EmpleadosController::class,'form_nuevo'])->name('empleados_nuevo')->middleware('auth');
Route::get('/empleados_admin',[EmpleadosController::class,'empleados_admin'])->name('empleados_admin')->middleware('auth');
Route::get('/empleados_consulta/{id}',[EmpleadosController::class,'empleados_consulta'])->middleware('auth');
Route::post('/empleados_actualiza',[EmpleadosController::class,'empleados_actualiza'])->name('guarda_cambios_empleado')->middleware('auth');
Route::get('/acciones_empleados_calculo/{id}/{version}',[EmpleadosController::class,'acciones_empleados_calculo'])->name('acciones_empleados_calculo')->middleware('auth');
Route::get('/empleados_consulta_pago/{id}/{user_id}/{version}',[EmpleadosController::class,'empleados_consulta_pago'])->middleware('auth');
Route::post('/empleados_autorizacion_especial',[EmpleadosController::class,'empleados_autorizacion_especial'])->name('empleados_autorizacion_especial')->middleware('auth');
Route::get('/estado_cuenta_empleado/{id}/{id_user}/{version}',[EmpleadosController::class,'estado_cuenta_empleado'])->middleware('auth');
Route::get('/transacciones_pago_empleado/{id}/{id_user}/{version}',[EmpleadosController::class,'transacciones_pago_empleado'])->middleware('auth');
Route::get('/transacciones_charge_back_empleado/{id}/{id_user}/{version}',[EmpleadosController::class,'transacciones_charge_back_empleado'])->middleware('auth');


//RUTAS DE CALCULO DE COMISIONES

Route::post('/calculo_nuevo',[CalculoController::class,'calculo_nuevo'])->name('calculo_nuevo')->middleware('auth');
Route::get('/calculo_nuevo',[CalculoController::class,'vista_nuevo'])->name('calculo_nuevo')->middleware('auth');
Route::get('/seguimiento_calculos',[CalculoController::class,'seguimiento_calculos'])->name('seguimiento_calculos')->middleware('auth');
Route::get('/detalle_calculo/{id}',[CalculoController::class,'detalle_calculo'])->name('detalle_calculo')->middleware('auth');
Route::get('/detalle_conciliacion/{id}',[CalculoController::class,'detalle_conciliacion'])->name('detalle_conciliacion')->middleware('auth');
Route::post('/calculo_ejecutar',[CalculoComisiones::class,'ejecutar_calculo'])->name('calculo_ejecutar')->middleware('auth');
Route::post('/calculo_terminar',[CalculoComisiones::class,'terminar_calculo'])->name('calculo_terminar')->middleware('auth');
Route::post('/calculo_reabrir',[CalculoComisiones::class,'reabrir_calculo'])->name('calculo_reabrir')->middleware('auth');
Route::post('/calculo_reset',[CalculoComisiones::class,'reset_calculo'])->name('calculo_reset')->middleware('auth');
Route::post('/conciliacion_reset',[CalculoComisiones::class,'reset_conciliacion'])->name('conciliacion_reset')->middleware('auth');
Route::post('/conciliacion_ejecutar',[CalculoComisiones::class,'ejecutar_conciliacion'])->name('conciliacion_ejecutar')->middleware('auth');
Route::get('/diferencias_comisiones/{id}',[ProcessViewController::class,'diferencias_comisiones_export'])->name('diferencias_comisiones')->middleware('auth');
Route::get('/diferencias_residual/{id}',[ProcessViewController::class,'diferencias_residual_export'])->name('diferencias_residual')->middleware('auth');

Route::get('/transacciones_resumen_calculo/{id}/{estatus}/{version}',[ProcessViewController::class,'transacciones_calculo'])->middleware('auth');
Route::post('/callidus_import', [ExcelController::class,'callidus_import'])->middleware('auth')->name('callidus_import')->middleware('auth');
Route::post('/callidus_residual_import', [ExcelController::class,'callidus_residual_import'])->middleware('auth')->name('callidus_residual_import')->middleware('auth');
Route::get('/pagos_export/{id}/{version}',[ProcessViewController::class,'pagos_export'])->name('pagos_export')->middleware('auth');
Route::get('/reclamos_export/{id}',[ProcessViewController::class,'reclamos_export'])->name('reclamos_export')->middleware('auth');
Route::get('/callidus_no_usados/{id}',[ProcessViewController::class,'callidus_no_usados'])->name('callidus_no_usados')->middleware('auth');
Route::get('/export_alertas/{id}/{user_id}',[ProcessViewController::class,'export_alertas'])->name('export_alertas')->middleware('auth');
Route::get('/residuales/{id}',[ProcessViewController::class,'residuales'])->name('residuales')->middleware('auth');
Route::get('/residuales_distribuidor/{id}/{user_id}',[ProcessViewController::class,'residuales_distribuidor'])->name('residuales_distribuidor')->middleware('auth');

//RUTAS DE ADMINISTRACION DE VENTAS

Route::get('/ventas_admin',[ProcessViewController::class,'ventas_admin'])->name('ventas_admin')->middleware('auth');
Route::get('/ventas_consulta/{id}',[ProcessViewController::class,'ventas_consulta'])->middleware('auth');
Route::post('/ventas_actualiza',[ProcessFormsController::class,'ventas_actualiza'])->name('guarda_cambios_venta')->middleware('auth');
Route::post('/ventas_valida_distribuidor',[ProcessFormsController::class,'ventas_valida_distribuidor'])->name('ventas_valida_distribuidor')->middleware('auth');
Route::get('/ventas_review',[ProcessViewController::class,'ventas_review'])->name('ventas_review')->middleware('auth');
Route::get('/export_validacion',[ProcessViewController::class,'export_validacion'])->name('export_validacion')->middleware('auth');


//RUTAS DE ESTADO DE CUENTA

Route::get('/estado_cuenta_distribuidor/{id}/{id_user}/{version}',[CalculoController::class,'estado_cuenta_distribuidor'])->middleware('auth');
Route::get('/acciones_distribuidores_calculo/{id}/{version}',[ProcessViewController::class,'acciones_distribuidores_calculo'])->name('acciones_distribuidores_calculo')->middleware('auth');
Route::get('/transacciones_pago_distribuidor/{id}/{id_user}/{version}',[ProcessViewController::class,'transacciones_pago_distribuidor'])->middleware('auth');
Route::get('/transacciones_charge_back_distribuidor/{id}/{id_user}/{version}',[ProcessViewController::class,'transacciones_charge_back_distribuidor'])->middleware('auth');
Route::get('/distribuidores_consulta_pago/{id}/{user_id}/{version}',[ProcessViewController::class,'distribuidores_consulta_pago'])->middleware('auth');
Route::post('/distribuidores_anticipo_no_pago',[ProcessFormsController::class,'distribuidores_anticipo_no_pago'])->name('distribuidores_anticipo_no_pago')->middleware('auth');
Route::get('/ventas_inconsistencias/{id}/{version}',[ProcessViewController::class,'ventas_inconsistencias'])->name('ventas_inconsistencias')->middleware('auth');
Route::get('/charge_back_calculo/{id}',[ProcessViewController::class,'charge_back_calculo'])->name('charge_back_calculo')->middleware('auth');
Route::post('/accion_inconsistencia',[ProcessFormsController::class,'accion_inconsistencia'])->name('accion_inconsistencia')->middleware('auth');
Route::post('/cargar_factura_distribuidor',[CalculoController::class,'cargar_factura_distribuidor'])->name('cargar_factura_distribuidor')->middleware('auth');

//PAGOS

Route::get('/pagos',[ShowPagosController::class,'pagos'])->name('pagos')->middleware('auth');
Route::post('/cambiar_estatus_pago',[ShowPagosController::class,'cambiar_estatus_pago'])->name('cambiar_estatus_pago')->middleware('auth');

//ANTICIPOS EXTRAORDINARIOS

Route::get('/anticipos_extraordinarios',[AnticiposExtraordinariosController::class,'anticipos_extraordinarios'])->name('anticipos_extraordinarios')->middleware('auth');
Route::post('/cambiar_estatus_anticipo',[AnticiposExtraordinariosController::class,'cambiar_estatus_anticipo'])->name('cambiar_estatus_anticipo')->middleware('auth');
Route::get('/facturar_anticipo/{id}',[AnticiposExtraordinariosController::class,'facturar_anticipo_form'])->name('facturar_anticipo_form')->middleware('auth');
Route::post('/facturar_anticipo',[AnticiposExtraordinariosController::class,'facturar_anticipo_save'])->name('facturar_anticipo_save')->middleware('auth');

//BASE USADA
Route::get('/export_base_usada/{id}',[ProcessViewController::class,'export_base_usada'])->name('export_base_usada')->middleware('auth');

//ETIQUETAS

Route::get('/etiquetas_import', function () {return view('etiquetas_import');})->middleware('auth')->name('etiquetas_import');
Route::post('/etiquetas_import', [EtiquetaController::class,'etiquetas_import'])->middleware('auth')->name('etiquetas_import');
Route::get('/etiquetas_show/{marca}', [EtiquetaController::class,'etiquetas_show'])->middleware('auth')->name('etiquetas_show');

//PAGOS A CUENTA

Route::get('/pagos_a_cuenta',[PagoACuentaController::class,'distribuidores_pagos'])->name('pagos_a_cuenta')->middleware('auth');
Route::post('/pagos_a_cuenta',[PagoACuentaController::class,'distribuidores_pagos_save'])->name('pagos_a_cuenta')->middleware('auth');
Route::post('/cambiar_estatus_pago_a_cuenta',[PagoACuentaController::class,'cambiar_estatus_pago_a_cuenta'])->name('cambiar_estatus_pago_a_cuenta')->middleware('auth');
Route::get('/pagos_a_cuenta_consulta/{user_id}',[PagoACuentaController::class,'pagos_a_cuenta_consulta'])->name('pagos_a_cuenta_consulta')->middleware('auth');
Route::get('/pagos_a_cuenta_borrar/{id}',[PagoACuentaController::class,'pagos_a_cuenta_borrar'])->middleware('auth');
Route::get('/base_pagos_a_cuenta',[PagoACuentaController::class,'base_pagos_a_cuenta'])->name('base_pagos_a_cuenta')->middleware('auth');
Route::post('/cambiar_estatus_pago_a_cuenta',[PagoACuentaController::class,'cambiar_estatus_pago_a_cuenta'])->name('cambiar_estatus_pago_a_cuenta')->middleware('auth');
Route::get('/facturar_pago_a_cuenta/{id}',[PagoACuentaController::class,'facturar_pago_a_cuenta_form'])->name('facturar_pago_a_cuenta_form')->middleware('auth');
Route::post('/facturar_pago_a_cuenta',[PagoACuentaController::class,'facturar_pago_a_cuenta_save'])->name('facturar_pago_a_cuenta_save')->middleware('auth');