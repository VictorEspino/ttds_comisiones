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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalculoController extends Controller
{
    public function vista_nuevo(Request $request)
    {
        $años=Periodo::select(DB::raw('distinct(año) as valor'))
                    ->whereRaw('fecha_fin>=(now()-60)')
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
        $calculos=Calculo::with('periodo')->orderBy('id','desc')->get()->take(10);
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

        $n_pagos=PagosDistribuidor::select(DB::raw('version,count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->groupBy('version')
                        ->get();

        foreach($n_pagos as $pagos)
        {
            if($pagos->version=="1"){$n_pagos_adelanto=$pagos->n;}
            else{$n_pagos_cierre=$pagos->n;}
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
                                       'n_inconsistencias'=>$n_inconsistencias->n,
                                       'n_reclamos'=>$n_reclamos->n,
                                       'n_callidus_sin_usar'=>$n_callidus_sin_usar,
                                       'cb_aplicados'=>$cb_aplicados,
                                       'cb_no_aplicados'=>$cb_no_aplicados,
                                    ]));
    }
    public function estado_cuenta_distribuidor(Request $request)
    {
        $id_calculo=$request->id;
        $id_user=$request->id_user;
        $version=$request->version;
        $calculo=Calculo::with('periodo')->find($id_calculo);
        $user=User::with('detalles')->find($id_user);
        $pago=PagosDistribuidor::where('calculo_id',$id_calculo)->where('user_id',$id_user)->where('version',$version)->get()->first();
        $anticipos_aplicados=AnticipoExtraordinario::with('periodo')->where('calculo_id_aplicado',$id_calculo)->where('user_id',$id_user)->where('en_adelanto',$version=='1'?'=':'<=',1)->get();
        return(view('estado_cuenta_distribuidor',[  'calculo'=>$calculo,
                                                    'user'=>$user,
                                                    'pago'=>$pago,
                                                    'anticipos_aplicados'=>$anticipos_aplicados,
                                                    'version'=>$version
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
        $file_name = $request->file("pdf_file")->getClientOriginalName();
        $generated_new_name_pdf = $request->user_id.'_'.$request->calculo_id.'_'.$request->version.'_'.time() . '.' . $request->file("pdf_file")->getClientOriginalExtension();
        $request->file("pdf_file")->move($upload_path, $generated_new_name_pdf);

        $upload_path = public_path('facturas');
        $file_name = $request->file("xml_file")->getClientOriginalName();
        $generated_new_name_xml = $request->user_id.'_'.$request->calculo_id.'_'.$request->version.'_'.time() . '.' . $request->file("xml_file")->getClientOriginalExtension();
        $request->file("xml_file")->move($upload_path, $generated_new_name_xml);

        PagosDistribuidor::where('calculo_id',$request->calculo_id)
                            ->where('user_id',$request->user_id)
                            ->where('version',$request->version)
                            ->update([
                                'pdf'=>$generated_new_name_pdf,
                                'xml'=>$generated_new_name_xml,
                            ]);

        return(back()->withStatus('Datos de facturacion OK'));
    }

}
