<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calculo;
use App\Models\Periodo;
use App\Models\Venta;
use App\Models\CallidusVenta;
use App\Models\CallidusResidual;
use App\Models\ComisionVenta;
use App\Models\PagosDistribuidor;
use App\Models\ChargeBackDistribuidor;
use App\Models\Reclamo;
use App\Models\User;
use App\Models\AnticipoExtraordinario;
use App\Models\PagoACuenta;
use App\Models\AlertaCobranza;
use App\Models\AlertaConciliacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalculoController extends Controller
{
    public function vista_nuevo(Request $request)
    {
        $años=Periodo::select(DB::raw('distinct(año) as valor'))
                    ->whereRaw('DATEDIFF( now(),fecha_fin)<60')
                    ->get()
                    ->take(2);
        return(view('calculo_nuevo',['años'=>$años,
                                     'nombre'=>Auth::user()->name,
                                    ]));
    }
    public function calculo_nuevo(Request $request)
    {
        $request->validate([
            'descripcion_calculo'=> 'required|max:255',
            'año'=>'required',
            'mes'=>'required',
        ]);
        $periodo=Periodo::where('año',$request->año)->where('mes',$request->mes)->get()->first();
        $calculo_valida=Calculo::where('periodo_id',$periodo->id)->get();
        if(!$calculo_valida->isEmpty())
        {
            return(back()->with('error_validacion','El periodo de medicion ya se encuentra registrado'));
        }
        $registro=new Calculo;
        $registro->descripcion=$request->descripcion_calculo;
        $registro->periodo_id=$periodo->id;
        $registro->user_id=Auth::user()->id;
        $registro->save();
        return(back()->withStatus('Registro de calculo de comisiones'.$request->descripcion.' creado con exito'));       
    }
    public function seguimiento_calculos(Request $request)
    {
        $calculos=Calculo::with('periodo')->where('visible',1)->orderBy('id','desc')->get()->take(10);
        $meses=array('Ene',
                  'Feb',
                  'Mar',
                  'Abr',
                  'May',
                  'Jun',
                  'Jul',
                  'Ago',
                  'Sep',
                  'Oct',
                  'Nov',
                  'Dic',
                );
        return(view('seguimiento_calculos',['calculos'=>$calculos,'meses'=>$meses]));
    }
    public function detalle_calculo(Request $request)
    {
        if(Auth::user()->perfil=='distribuidor' || Auth::user()->perfil=='ejecutivo' || Auth::user()->perfil=='gerente')
        {
            return($this->detalle_calculo_actor($request->id,Auth::user()->id));
        }
        $calculo=Calculo::with('periodo')->find($request->id);
        $validaciones=Venta::select(DB::raw('validado,count(*) as n'))
                    ->whereBetween('fecha', [$calculo->periodo->fecha_inicio,$calculo->periodo->fecha_fin ])
                    ->groupBy('validado')
                    ->get();
        $totales=0;
        $validados=0;
        $no_validados=0;

        foreach($validaciones as $validacion)
        {
            $totales=$totales+$validacion->n;
            if($validacion->validado=="1")
            {
                $validados=$validacion->n;
            }
            else
            {
                $no_validados=$validacion->n;
            }
        }
        $porcentaje_validacion=0;
        try{
            $porcentaje_validacion=intval(100*$validados/$totales);
        }
        catch(\Exception $e){
            $porcentaje_validacion=0;
        }

        $n_callidus=CallidusVenta::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();

        $n_callidus_residual=CallidusResidual::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();

        $totales_comision_adelanto=0;
        $pagados_adelanto=0;
        $no_pagados_adelanto=0;
        $porcentaje_comisionado_adelanto=0;
        if($calculo->adelanto=="1")
        {
            $procesados=ComisionVenta::select(DB::raw('estatus_inicial,count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->where('version',"1")
                        ->groupBy('estatus_inicial')
                        ->get();
            foreach($procesados as $procesado)
            {
                $totales_comision_adelanto=$totales_comision_adelanto+$procesado->n;
                if($procesado->estatus_inicial=="PAGO")
                {
                    $pagados_adelanto=$procesado->n;
                }
                else
                {
                    $no_pagados_adelanto=$procesado->n;
                }
            }
            try{
                $porcentaje_comisionado_adelanto=intval(100*$pagados_adelanto/$totales_comision_adelanto);
            }
            catch(\Exception $e){
                $porcentaje_comisionado_adelanto=0;
            }
        }
        $totales_comision_cierre=0;
        $pagados_cierre=0;
        $no_pagados_cierre=0;
        $porcentaje_comisionado_cierre=0;
        if($calculo->cierre=="1")
        {
            $procesados=ComisionVenta::select(DB::raw('estatus_inicial,count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->where('version',"2")
                        ->groupBy('estatus_inicial')
                        ->get();
            foreach($procesados as $procesado)
            {
                $totales_comision_cierre=$totales_comision_cierre+$procesado->n;
                if($procesado->estatus_inicial=="PAGO")
                {
                    $pagados_cierre=$procesado->n;
                }
                else
                {
                    $no_pagados_cierre=$procesado->n;
                }
            }
            try{
                $porcentaje_comisionado_cierre=intval(100*$pagados_cierre/$totales_comision_cierre);
            }
            catch(\Exception $e){
                $porcentaje_comisionado_cierre=0;
            }
        }

        $n_pagos_adelanto=0;
        $n_pagos_cierre=0;
        $n_pagos_interno_adelanto=0;
        $n_pagos_interno_cierre=0;

        $n_pagos=PagosDistribuidor::select(DB::raw('version,count(*) as n'))
                        ->join('users','users.id','=','pagos_distribuidors.user_id')
                        ->where('calculo_id',$request->id)
                        ->where('users.user','like','1%')
                        ->groupBy('version')
                        ->get();
        foreach($n_pagos as $pagos)
        {
            if($pagos->version=="1" ){$n_pagos_adelanto=$pagos->n;}
            else{$n_pagos_cierre=$pagos->n;}
        }
        $n_pagos_interno=PagosDistribuidor::select(DB::raw('version,count(*) as n'))
                        ->join('users','users.id','=','pagos_distribuidors.user_id')
                        ->where('calculo_id',$request->id)
                        ->where('users.user','like','2%')
                        ->groupBy('version')
                        ->get();
        foreach($n_pagos_interno as $pagos)
        {
            if($pagos->version=="1" ){$n_pagos_interno_adelanto=$pagos->n;}
            else{$n_pagos_interno_cierre=$pagos->n;}
        }

        $n_inconsistencias=ComisionVenta::select(DB::raw('count(*) as n'))
                        ->where('calculo_id_proceso',$request->id)
                        ->where('consistente',false)
                        ->where('version',$calculo->cierre=="1"?2:1)
                        ->get()
                        ->first();

        $n_reclamos=Reclamo::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();
        
        $n_callidus_usados=ComisionVenta::select(DB::raw('count(*) as n'))
                        ->where('calculo_id_proceso',$request->id)
                        ->where('version',$calculo->cierre=="1"?2:1)
                        ->get()
                        ->first();

        $n_callidus_sin_usar=$n_callidus->n-$n_callidus_usados->n;

        $cb_aplicados=0;
        $cb_no_aplicados=0;
        $cb=ChargeBackDistribuidor::select(DB::raw('count(*) as n'))
                                    ->where('calculo_id',$calculo->id)
                                    ->where('estatus','APLICADO')
                                    ->get()
                                    ->first();
        $cb_aplicados=$cb->n;

        $cb=ChargeBackDistribuidor::select(DB::raw('count(*) as n'))
                                    ->where('calculo_id',$calculo->id)
                                    ->where('estatus','NO APLICADO')
                                    ->get()
                                    ->first();
        $cb_no_aplicados=$cb->n;

        $alertas=0;
        if($calculo->cierre=="1")
        {
            $alertas_cobranza=AlertaCobranza::select(DB::raw('count(*) as n'))->where('calculo_id',$calculo->id)->get()->first();
            $alertas=!is_null($alertas_cobranza->n)?$alertas_cobranza->n:0;
        }
        return(view('detalle_calculo',['id_calculo'=>$calculo->id,
                                       'callidus'=>$calculo->callidus,
                                       'n_callidus'=>$n_callidus->n,
                                       'n_callidus_residual'=>$n_callidus_residual->n,
                                       'porcentaje_validacion'=>$porcentaje_validacion,
                                       'fecha_inicio'=>$calculo->periodo->fecha_inicio,
                                       'fecha_fin'=>$calculo->periodo->fecha_fin,
                                       'adelanto'=>$calculo->adelanto,
                                       'cierre'=>$calculo->cierre,
                                       'terminado'=>$calculo->terminado,
                                       'descripcion'=>$calculo->descripcion,
                                       'validados'=>$validados,
                                       'no_validados'=>$no_validados,
                                       'totales'=>$totales,
                                       'totales_comision_adelanto'=>$totales_comision_adelanto,
                                       'pagados_adelanto'=>$pagados_adelanto,
                                       'no_pagados_adelanto'=>$no_pagados_adelanto,
                                       'porcentaje_comisionado_adelanto'=>$porcentaje_comisionado_adelanto,
                                       'totales_comision_cierre'=>$totales_comision_cierre,
                                       'pagados_cierre'=>$pagados_cierre,
                                       'no_pagados_cierre'=>$no_pagados_cierre,
                                       'porcentaje_comisionado_cierre'=>$porcentaje_comisionado_cierre,
                                       'n_pagos_adelanto'=>$n_pagos_adelanto,
                                       'n_pagos_cierre'=>$n_pagos_cierre,
                                       'n_pagos_interno_adelanto'=>$n_pagos_interno_adelanto,
                                       'n_pagos_interno_cierre'=>$n_pagos_interno_cierre,
                                       'n_inconsistencias'=>$n_inconsistencias->n,
                                       'n_reclamos'=>$n_reclamos->n,
                                       'n_callidus_sin_usar'=>$n_callidus_sin_usar,
                                       'cb_aplicados'=>$cb_aplicados,
                                       'cb_no_aplicados'=>$cb_no_aplicados,
                                       'alertas'=>$alertas,
                                    ]));
    }
    private function detalle_calculo_actor($id,$user_id)
    {
        $actor=User::with('detalles','empleado')->find($user_id);
        $calculo=Calculo::find($id);
        $pagos=PagosDistribuidor::with('calculo')->where('calculo_id',$id)->where('user_id',$user_id)->get();
        return(view('detalle_calculo_actor',[
                                                    'actor'=>$actor,
                                                    'calculo'=>$calculo,
                                                    'pagos'=>$pagos
                                                    ]));
    }    
    public function detalle_conciliacion(Request $request)
    {
        $calculo=Calculo::with('periodo')->find($request->id);

        $n_callidus=CallidusVenta::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();

        $n_callidus_residual=CallidusResidual::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();

        $n_comisiones=AlertaConciliacion::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->where('tipo','UPFRONT')
                        ->get()
                        ->first();
        
        $n_residual=AlertaConciliacion::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->where('tipo','RESIDUAL_45D')
                        ->get()
                        ->first();

        return(view('detalle_conciliacion',['id_calculo'=>$calculo->id,
                                       'n_callidus'=>$n_callidus->n,
                                       'n_callidus_residual'=>$n_callidus_residual->n,
                                       'fecha_inicio'=>$calculo->periodo->fecha_inicio,
                                       'fecha_fin'=>$calculo->periodo->fecha_fin,
                                       'descripcion'=>$calculo->descripcion,
                                       'n_comisiones'=>$n_comisiones->n,
                                       'n_residual'=>$n_residual->n,
                                    ]));
    }
    public function estado_cuenta_distribuidor(Request $request)
    {
        //echo "inicio=".now();
        $id_calculo=$request->id;
        $id_user=$request->id_user;
        $version=$request->version;
        $calculo=Calculo::with('periodo')->find($id_calculo);
        $user=User::with('detalles')->find($id_user);
        $pago=PagosDistribuidor::where('calculo_id',$id_calculo)->where('user_id',$id_user)->where('version',$version)->get()->first();
        $anticipos_aplicados=AnticipoExtraordinario::with('periodo')->where('calculo_id_aplicado',$id_calculo)->where('user_id',$id_user)->where('en_adelanto',$version=='1'?'=':'<=',1)->get();
        $pagos_a_cuenta_aplicados=PagoACuenta::with('periodo')->where('calculo_id_aplicado',$id_calculo)->where('user_id',$id_user)->where('en_adelanto',$version=='1'?'=':'<=',1)->get();
        $alertas=0;
        $analisis_residual=[];
        if($version=="2")
        {
            $alertas_cobranza=AlertaCobranza::select(DB::raw('count(*) as n'))->where('calculo_id',$calculo->id)
                                            ->where('user_id',$id_user)
                                            ->get()
                                            ->first();
            $alertas=!is_null($alertas_cobranza->n)?$alertas_cobranza->n:0;
        }
        if($user->detalles->residual=="1" && $version=="2")
        {
            $analisis_residual=$this->analizarResidual($calculo,$user);
            //return($analisis_residual);
        }
        //echo "<br>fin=".now();
        //return;
        return(view('estado_cuenta_distribuidor',[  'calculo'=>$calculo,
                                                    'user'=>$user,
                                                    'pago'=>$pago,
                                                    'anticipos_aplicados'=>$anticipos_aplicados,
                                                    'pagos_a_cuenta_aplicados'=>$pagos_a_cuenta_aplicados,
                                                    'version'=>$version,
                                                    'alertas'=>$alertas,
                                                    'diferencial_residual'=>$analisis_residual,
                                                ]));
    }
    public function cargar_factura_distribuidor(Request $request)
    {
        //return($request);
        $request->validate([
            'pdf_file' => 'required|mimes:pdf',
            'xml_file' => 'required|mimes:xml',
           ]);
        
        $upload_path = public_path('facturas');
        // $upload_path="/home/icubeitc/ttds.icube-it.com/facturas";
        $upload_path="/var/www/ttds.icube.com.mx/facturas";

        $file_name = $request->file("pdf_file")->getClientOriginalName();
        $generated_new_name_pdf = $request->user_id.'_'.$request->calculo_id.'_'.$request->version.'_'.time() . '.' . $request->file("pdf_file")->getClientOriginalExtension();
        $request->file("pdf_file")->move($upload_path, $generated_new_name_pdf);
        $file_name = $request->file("xml_file")->getClientOriginalName();
        $generated_new_name_xml = $request->user_id.'_'.$request->calculo_id.'_'.$request->version.'_'.time() . '.' . $request->file("xml_file")->getClientOriginalExtension();
        $request->file("xml_file")->move($upload_path, $generated_new_name_xml);

        PagosDistribuidor::where('calculo_id',$request->calculo_id)
                            ->where('user_id',$request->user_id)
                            ->where('version',$request->version)
                            ->update([
                                'pdf'=>$generated_new_name_pdf,
                                'xml'=>$generated_new_name_xml,
                                'carga_facturas'=>now()->toDateTimeString(),
                            ]);

        return(back()->withStatus('Datos de facturacion OK'));
    }
    private function analizarResidual($calculo,$user)
    {
        $respuesta=array(
            'salientes'=>[],
            'persistentes'=>[],
            'entrantes'=>[],            
        );
        $periodos_anteriores=$this->periodos_anteriores($calculo);
        $anterior=$periodos_anteriores['menos_1'];
        
        $sql_base="select contrato,sum(anterior) as anterior, sum(actual) as actual from (
            select callidus_residuals.contrato,1 as anterior,0 as actual from comision_residuals,callidus_residuals where comision_residuals.callidus_residual_id=callidus_residuals.id and comision_residuals.calculo_id=".$periodos_anteriores['menos_1']." and comision_residuals.user_id=".$user->id."
            UNION
            select callidus_residuals.contrato,0 as anterior,1 as actual from comision_residuals,callidus_residuals where comision_residuals.callidus_residual_id=callidus_residuals.id and comision_residuals.calculo_id=".$calculo->id." and comision_residuals.user_id=".$user->id."
                ) as a group by a.contrato
            ";
        $sql_no_estan="select contrato from (".$sql_base.") as a where a.actual=0";
        $no_estan=DB::select(DB::raw($sql_no_estan));
        $sql_no_estaban="select contrato from (".$sql_base.") as a where a.actual=1 and a.anterior=0";
        $no_estaban=DB::select(DB::raw($sql_no_estaban));
        $efecto_salientes=CallidusResidual::select(DB::raw('estatus,count(*) as n,sum(renta) as rentas'))
                                ->where('calculo_id',$periodos_anteriores['menos_1'])
                                ->whereIn('contrato',collect($no_estan)->pluck('contrato'))
                                ->groupBy('estatus')
                                ->get()->toArray();
        $efecto_entrantes=CallidusResidual::select(DB::raw('estatus,count(*) as n,sum(renta) as rentas'))
                                ->where('calculo_id',$calculo->id)
                                ->whereIn('contrato',collect($no_estaban)->pluck('contrato'))
                                ->groupBy('estatus')
                                ->get()->toArray();

        $sql_persistentes="select contrato from (".$sql_base.") as a where a.actual=1 and a.anterior=1";
        $persistentes=DB::select(DB::raw($sql_persistentes));
        $contratos_persistentes=collect($persistentes)->pluck('contrato');

        $persistentes_a=CallidusResidual::select('contrato',DB::raw("
                                            CASE 
                                            WHEN estatus='ACTIVO' THEN 1
                                            WHEN estatus='FIN_PLAZO' THEN 2
                                            WHEN estatus='SUSPENDIDO' THEN 3
                                            WHEN estatus='DESACTIVADO' THEN 4
                                            WHEN estatus='DESACTIVO' THEN 5
                                            ELSE 0
                                            END as estatus_anterior,0 as estatus_actual,renta
                                        "))
                                        ->where('calculo_id',$periodos_anteriores['menos_1'])
                                        ->whereIn('contrato',$contratos_persistentes);
        $persistentes_b=CallidusResidual::select('contrato',DB::raw("0 as estatus_anterior,
                                            CASE 
                                            WHEN estatus='ACTIVO' THEN 1
                                            WHEN estatus='FIN_PLAZO' THEN 2
                                            WHEN estatus='SUSPENDIDO' THEN 3
                                            WHEN estatus='DESACTIVADO' THEN 4
                                            WHEN estatus='DESACTIVO' THEN 5
                                            ELSE 0
                                            END as estatus_actual,0 as renta
                                        "))
                                        ->where('calculo_id',$calculo->id)
                                        ->whereIn('contrato',$contratos_persistentes);


        $query_persistentes=$persistentes_a->union($persistentes_b);
        $persistentes_union=DB::query()->fromSub($query_persistentes, 'c_persist')
        ->select('contrato', DB::raw('sum(estatus_anterior) as estatus_anterior, sum(estatus_actual) as estatus_actual,sum(renta) as renta'))
        ->groupBy('contrato');

        $persistente_final=DB::query()->fromSub($persistentes_union,'c_union')
                        ->select(
                            DB::raw(
                                'CASE 
                                WHEN estatus_anterior=1 THEN "ACTIVO"
                                WHEN estatus_anterior=2 THEN "FIN_PLAZO"
                                WHEN estatus_anterior=3 THEN "SUSPENDIDO"
                                WHEN estatus_anterior=4 THEN "DESACTIVADO"
                                WHEN estatus_anterior=5 THEN "DESACTIVO"
                                ELSE "OTRO" 
                                END
                                as estatus_anterior
                            '),
                            DB::raw(
                                'CASE 
                                WHEN estatus_actual=1 THEN "ACTIVO"
                                WHEN estatus_actual=2 THEN "FIN_PLAZO"
                                WHEN estatus_actual=3 THEN "SUSPENDIDO"
                                WHEN estatus_actual=4 THEN "DESACTIVADO"
                                WHEN estatus_actual=5 THEN "DESACTIVO"
                                ELSE "OTRO"
                                END
                                as estatus_actual
                                '),
                            DB::raw('count(*) as n,sum(c_union.renta) as rentas'))
                        ->groupBy('c_union.estatus_anterior','c_union.estatus_actual')
                        ->get();

        $respuesta['salientes']=$efecto_salientes;
        $respuesta['persistentes']=$persistente_final;
        $respuesta['entrantes']=$efecto_entrantes;
        return($respuesta);


    }
    public function periodos_anteriores($calculo)
    {
        $periodos_anteriores=array(
                                'menos_1'=>0,
                                'menos_2'=>0,
                                'menos_3'=>0,
                                'menos_4'=>0,
                                );
        $periodo_actual=Calculo::find($calculo->id)->periodo_id;
        $periodo_menos1=Calculo::where('periodo_id',$periodo_actual-1)->get()->first();
        $periodo_menos2=Calculo::where('periodo_id',$periodo_actual-2)->get()->first();
        $periodo_menos3=Calculo::where('periodo_id',$periodo_actual-3)->get()->first();
        $periodo_menos4=Calculo::where('periodo_id',$periodo_actual-4)->get()->first();
        
        $ultimo_conocido=$calculo->id;
        $periodos_anteriores['menos_1']=is_null($periodo_menos1)?$ultimo_conocido:$periodo_menos1->id;
        $ultimo_conocido=$periodos_anteriores['menos_1'];
        $periodos_anteriores['menos_2']=is_null($periodo_menos2)?$ultimo_conocido:$periodo_menos2->id;
        $ultimo_conocido=$periodos_anteriores['menos_2'];
        $periodos_anteriores['menos_3']=is_null($periodo_menos3)?$ultimo_conocido:$periodo_menos3->id;
        $ultimo_conocido=$periodos_anteriores['menos_3'];
        $periodos_anteriores['menos_4']=is_null($periodo_menos4)?$ultimo_conocido:$periodo_menos4->id;
        return($periodos_anteriores);        
    }
}
