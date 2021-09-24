<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distribuidor;
use App\Models\PagosDistribuidor;
use App\Models\AnticipoExtraordinario;
use App\Models\Calculo;
use App\Models\Venta;
use App\Models\User;
use App\Models\ComisionVenta;
use App\Models\Mediciones;
use App\Models\CallidusVenta;
use App\Models\Reclamo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessViewController extends Controller
{

    public function distribuidores_admin(Request $request)
    {
        if(isset($_GET['query']))
        {
            $registros=Distribuidor::where('nombre','like','%'.$_GET["query"].'%')
                                    ->orderBy('nombre','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('distribuidores_admin',['registros'=>$registros,'query'=>$_GET['query']]));
        }
        else
        {
            $registros=Distribuidor::orderBy('nombre','asc')
                                    ->paginate(10);
            return(view('distribuidores_admin',['registros'=>$registros,'query'=>'']));
        }
    }
    public function distribuidores_consulta(Request $request)
    {
        return(Distribuidor::find($request->id));
    }
    public function distribuidores_nuevo(Request $request)
    {
        return(view('distribuidores_nuevo'));
    }
    public function seguimiento_calculos(Request $request)
    {
        return(view('seguimiento_calculos',['calculos'=>Calculo::all()]));
    }

    public function transacciones_calculo(Request $request)
    {
        $query=ComisionVenta::with('venta','venta.user','callidus')
            ->where('calculo_id',$request->id)
            ->where('estatus_inicial',$request->estatus)
            ->get();
        return(view('transacciones_calculo',['query'=>$query,'pago'=>$request->estatus]));
    }

    public function ventas_admin(Request $request)
    {
        if(isset($_GET['query']))
        {
            $registros=DB::table('ventas')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select('ventas.*','users.user','users.name')
                                ->where(function($query){
                                    $query->where('ventas.cliente','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.dn','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.folio','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.cuenta','like','%'.$_GET["query"].'%');
                                        })
                                ->where('ventas.validado',false)
                                ->orderBy('ventas.cliente','asc')
                                ->paginate(10);
            $registros->appends($request->all());
            $distribuidores=DB::table('ventas')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select(DB::raw('distinct users.id,users.name'))
                                ->where(function($query){
                                    $query->where('ventas.cliente','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.folio','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.dn','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.cuenta','like','%'.$_GET["query"].'%');
                                        })
                                ->where('ventas.validado',false)
                                ->orderBy('ventas.cliente','asc')
                                ->get();

            return(view('ventas_admin',['registros'=>$registros,'query'=>$_GET['query'],'distribuidores'=>$distribuidores]));
        }
        else
        {
            $registros=DB::table('ventas')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select('ventas.*','users.user','users.name')
                                ->where('ventas.validado',false)
                                ->orderBy('ventas.cliente','asc')
                                ->paginate(10);
            $distribuidores=DB::table('ventas')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select(DB::raw('distinct users.id,users.name'))
                                ->where('ventas.validado',false)
                                ->orderBy('ventas.cliente','asc')
                                ->get();
            return(view('ventas_admin',['registros'=>$registros,'query'=>'','distribuidores'=>$distribuidores]));
        }
    }
    public function ventas_consulta(Request $request)
    {
        return(Venta::find($request->id));
    }
    public function detalle_calculo(Request $request)
    {
        $calculo=Calculo::find($request->id);
        $validaciones=Venta::select(DB::raw('validado,count(*) as n'))
                    ->whereBetween('fecha', [$calculo->fecha_inicio,$calculo->fecha_fin ])
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

        $totales_comision=0;
        $pagados=0;
        $no_pagados=0;
        $porcentaje_comisionado=0;
        $procesados=ComisionVenta::select(DB::raw('estatus_inicial,count(*) as n'))
                    ->where('calculo_id',$request->id)
                    ->groupBy('estatus_inicial')
                    ->get();
        foreach($procesados as $procesado)
        {
            $totales_comision=$totales_comision+$procesado->n;
            if($procesado->estatus_inicial=="PAGO")
            {
                $pagados=$procesado->n;
            }
            else
            {
                $no_pagados=$procesado->n;
            }
        }
        $porcentaje_comisionado=0;
        try{
            $porcentaje_comisionado=intval(100*$pagados/$totales_comision);
        }
        catch(\Exception $e){
            $porcentaje_comisionado=0;
        }

        $n_pagos=PagosDistribuidor::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();

        $n_inconsistencias=ComisionVenta::select(DB::raw('count(*) as n'))
                        ->where('calculo_id_proceso',$request->id)
                        ->where('consistente',false)
                        ->get()
                        ->first();

        $n_reclamos=Reclamo::select(DB::raw('count(*) as n'))
                        ->where('calculo_id',$request->id)
                        ->get()
                        ->first();
        
        $n_callidus_usados=ComisionVenta::select(DB::raw('count(*) as n'))
                        ->where('calculo_id_proceso',$request->id)
                        ->get()
                        ->first();

        $n_callidus_sin_usar=$n_callidus->n-$n_callidus_usados->n;


        return(view('detalle_calculo',['id_calculo'=>$calculo->id,
                                       'callidus'=>$calculo->callidus,
                                       'n_callidus'=>$n_callidus->n,
                                       'porcentaje_validacion'=>$porcentaje_validacion,
                                       'fecha_inicio'=>$calculo->fecha_inicio,
                                       'fecha_fin'=>$calculo->fecha_fin,
                                       'descripcion'=>$calculo->descripcion,
                                       'tipo'=>$calculo->tipo,
                                       'validados'=>$validados,
                                       'no_validados'=>$no_validados,
                                       'totales'=>$totales,
                                       'totales_comision'=>$totales_comision,
                                       'pagados'=>$pagados,
                                       'no_pagados'=>$no_pagados,
                                       'porcentaje_comisionado'=>$porcentaje_comisionado,
                                       'n_pagos'=>$n_pagos->n,
                                       'n_inconsistencias'=>$n_inconsistencias->n,
                                       'n_reclamos'=>$n_reclamos->n,
                                       'n_callidus_sin_usar'=>$n_callidus_sin_usar,
                                    ]));
    }
    public function acciones_distribuidores_calculo(Request $request)
    {
        $id_calculo=$request->id;
        $calculo=Calculo::find($id_calculo);
        if(isset($_GET['query']))
        {
            $registros=Distribuidor::where('nombre','like','%'.$_GET["query"].'%')
                                    ->orderBy('nombre','asc')
                                    ->paginate(10);
            $registros=DB::table('pagos_distribuidors')
                                    ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                    ->select('users.id',DB::raw('users.user as numero_distribuidor'),DB::raw('users.name as nombre'),
                                                                'pagos_distribuidors.total_pago',
                                                                'pagos_distribuidors.anticipos_extraordinarios',
                                                                'pagos_distribuidors.anticipo_no_pago',
                                                                'pagos_distribuidors.nuevas_comision_no_pago',
                                                                'pagos_distribuidors.adiciones_comision_no_pago',
                                                                'pagos_distribuidors.renovaciones_comision_no_pago',
                                                                'pagos_distribuidors.nuevas_bono_no_pago',
                                                                'pagos_distribuidors.adiciones_bono_no_pago',
                                                                'pagos_distribuidors.renovaciones_bono_no_pago',
                                                                )
                                    ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                    ->where('users.name','like','%'.$_GET["query"].'%')
                                    ->orderBy('users.name','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('acciones_distribuidores_calculo',['calculo'=>$calculo,
                                                           'registros'=>$registros,'query'=>$_GET['query']
                                                          ]));
        }
        else
        {
            $registros=DB::table('pagos_distribuidors')
                                ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                ->select('users.id',DB::raw('users.user as numero_distribuidor'),DB::raw('users.name as nombre'),
                                                                'pagos_distribuidors.total_pago',
                                                                'pagos_distribuidors.anticipos_extraordinarios',
                                                                'pagos_distribuidors.anticipo_no_pago',
                                                                'pagos_distribuidors.nuevas_comision_no_pago',
                                                                'pagos_distribuidors.adiciones_comision_no_pago',
                                                                'pagos_distribuidors.renovaciones_comision_no_pago',
                                                                'pagos_distribuidors.nuevas_bono_no_pago',
                                                                'pagos_distribuidors.adiciones_bono_no_pago',
                                                                'pagos_distribuidors.renovaciones_bono_no_pago',
                                                                )
                                ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                ->orderBy('users.name','asc')
                                ->paginate(10);
            return(view('acciones_distribuidores_calculo',['calculo'=>$calculo,
                                'registros'=>$registros,'query'=>'',
                               ]));
        }
    }
   
    public function estado_cuenta_distribuidor(Request $request)
    {
        $id_calculo=$request->id;
        $id_user=$request->id_user;
        $calculo=Calculo::find($id_calculo);
        $user=User::find($id_user);
        $pago=PagosDistribuidor::where('calculo_id',$id_calculo)->where('user_id',$id_user)->get()->first();
        $anticipos_aplicados=AnticipoExtraordinario::where('aplicado_calculo_id',$id_calculo)->where('user_id',$id_user)->get();
        return(view('estado_cuenta_distribuidor',[  'calculo'=>$calculo,
                                                    'user'=>$user,
                                                    'pago'=>$pago,
                                                    'anticipos_aplicados'=>$anticipos_aplicados,
                                                ]));
    }
    public function transacciones_pago_distribuidor(Request $request)
    {

    $sql_consulta="SELECT a.upfront,a.bono,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_venta_id=c.id and a.calculo_id='".$request->id."' and b.user_id='".$request->id_user."' and a.estatus_inicial='PAGO'";
    $query=DB::select(DB::raw(
        $sql_consulta
       ));

    $sql_consulta_no_pago="SELECT a.upfront,a.bono,0 as c_renta,0 as c_plazo,0 as c_descuento_multirenta,0 as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id='".$request->id."' and b.user_id='".$request->id_user."' and a.estatus_inicial='NO PAGO'";
    $query_no_pago=DB::select(DB::raw(
        $sql_consulta_no_pago
       ));
    return(view('transacciones_pago_distribuidor',['query'=>$query,'query_no_pago'=>$query_no_pago]));
    }

    public function distribuidores_consulta_pago(Request $request)
    {
        return(PagosDistribuidor::where('calculo_id',$request->id)->where('user_id',$request->user_id)->get()->first());
    }

    public function distribuidores_anticipos_extraordinarios(Request $request)
    {
        if(isset($_GET['query']))
        {
            $registros=Distribuidor::where('nombre','like','%'.$_GET["query"].'%')
                                    ->orderBy('nombre','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('distribuidores_anticipos_extraordinarios',['registros'=>$registros,'query'=>$_GET['query']]));
        }
        else
        {
            $registros=Distribuidor::orderBy('nombre','asc')
                                    ->paginate(10);
            return(view('distribuidores_anticipos_extraordinarios',['registros'=>$registros,'query'=>'']));
        }
    }
    public function anticipos_extraordinarios_consulta(Request $request)
    {
        return(AnticipoExtraordinario::where('user_id',$request->user_id)->where('aplicado',false)->get());
    }
    public function ventas_review(Request $request)
    {
        if(isset($_GET['query']))
        {
            $registros=DB::table('ventas')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select('ventas.*','users.user','users.name')
                                ->where(function($query){
                                    $query->where('ventas.cliente','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.folio','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.dn','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.cuenta','like','%'.$_GET["query"].'%');
                                        })
                                ->where('ventas.validado',true)
                                ->orderBy('ventas.cliente','asc')
                                ->paginate(10);
            $registros->appends($request->all());
            return(view('ventas_review',['registros'=>$registros,'query'=>$_GET['query']]));
        }
        else
        {
            $registros=DB::table('ventas')
                                ->join('users', 'users.id', '=', 'ventas.user_id')
                                ->select('ventas.*','users.user','users.name')
                                ->where('ventas.validado',true)
                                ->orderBy('ventas.cliente','asc')
                                ->paginate(10);
            return(view('ventas_review',['registros'=>$registros,'query'=>'']));
        }

    }
    public function ventas_inconsistencias(Request $request)
    {
        $calculo=Calculo::find($request->id);
        if(isset($_GET['query']))
        {
            $registros=DB::table('comision_ventas')
            ->select('ventas.id','ventas.user','ventas.plan','ventas.name','ventas.cliente','ventas.dn','ventas.cuenta','ventas.folio','ventas.renta','ventas.plazo','ventas.descuento_multirenta','ventas.afectacion_comision','callidus_ventas.renta as c_renta','callidus_ventas.plazo as c_plazo','callidus_ventas.descuento_multirenta as c_descuento_multirenta','callidus_ventas.afectacion_comision as c_afectacion_comision')
                                ->join(DB::raw('(
                                        select ventas.*,users.user,users.name
                                        from ventas,users
                                        where ventas.user_id=users.id
                                        ) as ventas')
                                    , 'comision_ventas.venta_id', '=', 'ventas.id')
                                ->join('callidus_ventas', 'comision_ventas.callidus_venta_id', '=', 'callidus_ventas.id')
                                ->where('comision_ventas.calculo_id_proceso',$request->id)
                                ->where('comision_ventas.consistente',0)
                                ->where(function($query){
                                    $query->where('ventas.cliente','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.folio','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.dn','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.cuenta','like','%'.$_GET["query"].'%');
                                        })
                                ->paginate(10);
            $registros->appends($request->all());
            
            return(view('ventas_inconsistencias',['calculo'=>$calculo,'registros'=>$registros,'query'=>$_GET['query']]));
        }
        else
        {
            $registros=DB::table('comision_ventas')
                                ->select('ventas.id','ventas.user','ventas.plan','ventas.name','ventas.cliente','ventas.dn','ventas.cuenta','ventas.folio','ventas.renta','ventas.plazo','ventas.descuento_multirenta','ventas.afectacion_comision','callidus_ventas.renta as c_renta','callidus_ventas.plazo as c_plazo','callidus_ventas.descuento_multirenta as c_descuento_multirenta','callidus_ventas.afectacion_comision as c_afectacion_comision')
                                ->join(DB::raw('(
                                        select ventas.*,users.user,users.name
                                        from ventas,users
                                        where ventas.user_id=users.id
                                        ) as ventas')
                                    , 'comision_ventas.venta_id', '=', 'ventas.id')
                                ->join('callidus_ventas', 'comision_ventas.callidus_venta_id', '=', 'callidus_ventas.id')
                                ->where('comision_ventas.calculo_id_proceso',$request->id)
                                ->where('comision_ventas.consistente',0)
                                ->paginate(10);
            return(view('ventas_inconsistencias',['calculo'=>$calculo,'registros'=>$registros,'query'=>'']));
        }

    }
    public function export_validacion(Request $request)
    {
        $query=Venta::with('user')->where('validado',false)->get();
        return(view('transacciones_validacion',['query'=>$query]));
    }
    public function pagos_export(Request $request)
    {
        $query=PagosDistribuidor::with('user')->where('calculo_id',$request->id)->get();
        return(view('pagos_export',['query'=>$query]));
    }
    public function reclamos_export(Request $request)
    {
        $query=Reclamo::with('venta')->where('calculo_id',$request->id)->get();
        return(view('reclamos_export',['query'=>$query]));
    }
    public function callidus_no_usados(Request $request)
    {
        $query=CallidusVenta::doesnthave('pagada')->where('calculo_id',$request->id)->get();
        return(view('callidus_no_usados',['query'=>$query]));
    }
    
}
