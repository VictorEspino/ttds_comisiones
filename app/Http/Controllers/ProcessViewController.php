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
    $sql_consulta="SELECT a.upfront,a.bono,a.tipo as c_tipo,a.renta as c_renta,a.plazo as c_plazo,a.descuento_multirenta as c_descuento_multirenta,a.afectacion_comision as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b WHERE a.calculo_id='".$request->id."' and a.venta_id=b.id and a.estatus='".$request->estatus."'";
    $query=DB::select(DB::raw(
        $sql_consulta
       ));
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
                                    $query->where('users.name','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.dn','like','%'.$_GET["query"].'%')
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
                                    $query->where('users.name','like','%'.$_GET["query"].'%')
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
        $procesados=ComisionVenta::select(DB::raw('estatus,count(*) as n'))
                    ->where('calculo_id',$request->id)
                    ->groupBy('estatus')
                    ->get();
        foreach($procesados as $procesado)
        {
            $totales_comision=$totales_comision+$procesado->n;
            if($procesado->estatus=="PAGO")
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
                                       'porcentaje_comisionado'=>$porcentaje_comisionado

                                    ]));
    }
    public function acciones_distribuidores_calculo(Request $request)
    {
        $id_calculo=$request->id;
        if(isset($_GET['query']))
        {
            $registros=Distribuidor::where('nombre','like','%'.$_GET["query"].'%')
                                    ->orderBy('nombre','asc')
                                    ->paginate(10);
            $registros=DB::table('pagos_distribuidors')
                                    ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                    ->select('users.id',DB::raw('users.user as numero_distribuidor'),DB::raw('users.name as nombre'),'pagos_distribuidors.total_pago','pagos_distribuidors.anticipos_extraordinarios','pagos_distribuidors.anticipo_no_pago')
                                    ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                    ->where('users.name','like','%'.$_GET["query"].'%')
                                    ->orderBy('users.name','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('acciones_distribuidores_calculo',['id'=>$id_calculo,
                                                           'registros'=>$registros,'query'=>$_GET['query']
                                                          ]));
        }
        else
        {
            $registros=DB::table('pagos_distribuidors')
                                ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                ->select('users.id',DB::raw('users.user as numero_distribuidor'),DB::raw('users.name as nombre'),'pagos_distribuidors.total_pago','pagos_distribuidors.anticipos_extraordinarios','pagos_distribuidors.anticipo_no_pago')
                                ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                ->orderBy('users.name','asc')
                                ->paginate(10);
            return(view('acciones_distribuidores_calculo',['id'=>$id_calculo,
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
        
        return(view('estado_cuenta_distribuidor',[  'calculo'=>$calculo,
                                                    'user'=>$user,
                                                    'pago'=>$pago,
                                                ]));
    }
    public function transacciones_pago_distribuidor(Request $request)
    {
    
    $sql_consulta="SELECT a.upfront,a.bono,a.tipo as c_tipo,a.renta as c_renta,a.plazo as c_plazo,a.descuento_multirenta as c_descuento_multirenta,a.afectacion_comision as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b WHERE a.calculo_id='".$request->id."' and b.user_id='".$request->id_user."' and a.venta_id=b.id and a.estatus='PAGO'";
    //return($sql_consulta);
    $query=DB::select(DB::raw(
        $sql_consulta
       ));
    return(view('transacciones_pago_distribuidor',['query'=>$query]));
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
    
}
