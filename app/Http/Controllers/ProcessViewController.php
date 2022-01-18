<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distribuidor;
use App\Models\PagosDistribuidor;
use App\Models\AnticipoExtraordinario;
use App\Models\ChargeBackDistribuidor;
use App\Models\Calculo;
use App\Models\Empleado;
use App\Models\Venta;
use App\Models\User;
use App\Models\ComisionVenta;
use App\Models\ComisionResidual;
use App\Models\Mediciones;
use App\Models\CallidusVenta;
use App\Models\Reclamo;
use App\Models\Periodo;
use App\Models\AlertaCobranza;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessViewController extends Controller
{

    public function distribuidores_admin(Request $request)
    {
        $supervisores=Empleado::select('user_id','nombre')->where('puesto','GERENTE')->get();
        if(isset($_GET['query']))
        {
            $registros=Distribuidor::where('nombre','like','%'.$_GET["query"].'%')
                                    ->orderBy('nombre','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('distribuidores_admin',['registros'=>$registros,'supervisores'=>$supervisores,'query'=>$_GET['query']]));
        }
        else
        {
            $registros=Distribuidor::orderBy('nombre','asc')
                                    ->paginate(10);
            return(view('distribuidores_admin',['registros'=>$registros,'supervisores'=>$supervisores,'query'=>'']));
        }
    }
    public function distribuidores_consulta(Request $request)
    {
        return(Distribuidor::with('user')->find($request->id));
    }
    public function distribuidores_nuevo(Request $request)
    {
        return(view('distribuidores_nuevo'));
    }
    

    public function transacciones_calculo(Request $request)
    {
        $query=ComisionVenta::with('venta','venta.user','callidus')
            ->where('calculo_id',$request->id)
            ->where('estatus_inicial',$request->estatus)
            ->where('version',$request->version)
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
    
    public function acciones_distribuidores_calculo(Request $request)
    {
        $id_calculo=$request->id;
        $calculo=Calculo::with('periodo')->find($id_calculo);
        $etapa_cierre=$calculo->cierre;
        $version=$request->version;
        $terminado=$calculo->terminado;
        if(isset($_GET['query']))
        {
            $registros=DB::table('pagos_distribuidors')
                                    ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                    ->leftJoin(DB::raw('(select user_id,anticipo from anticipo_no_pagos where calculo_id='.$id_calculo.') as anp'),
                                            'anp.user_id','=','pagos_distribuidors.user_id'
                                            )
                                    ->select('users.id',DB::raw('users.user as numero_distribuidor'),DB::raw('users.name as nombre'),
                                                                'pagos_distribuidors.total_pago',
                                                                DB::raw('pagos_distribuidors.comision_nuevas + pagos_distribuidors.bono_nuevas +  pagos_distribuidors.comision_adiciones + pagos_distribuidors.bono_adiciones +pagos_distribuidors.comision_renovaciones + pagos_distribuidors.bono_renovaciones as comisiones'),
                                                                DB::raw('pagos_distribuidors.nuevas_comision_no_pago + pagos_distribuidors.nuevas_bono_no_pago +  pagos_distribuidors.adiciones_comision_no_pago + pagos_distribuidors.adiciones_bono_no_pago + pagos_distribuidors.renovaciones_comision_no_pago + pagos_distribuidors.renovaciones_bono_no_pago as comisiones_pendientes'),
                                                                'pagos_distribuidors.anticipo_ordinario',
                                                                'pagos_distribuidors.anticipos_extraordinarios',
                                                                'pagos_distribuidors.anticipo_no_pago',
                                                                DB::raw('anp.anticipo as anticipo_no_pago'),
                                                                'pagos_distribuidors.pdf',
                                                                'pagos_distribuidors.xml'
                                                                )
                                    ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                    ->where('pagos_distribuidors.version',$version)
                                    ->where('users.name','like','%'.$_GET["query"].'%')
                                    ->where('users.user','like','1%')
                                    ->orderBy('users.name','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('acciones_distribuidores_calculo',['calculo'=>$calculo,
                                                           'registros'=>$registros,'query'=>$_GET['query'],
                                                           'version'=>$version,
                                                           'etapa_cierre'=>$etapa_cierre,
                                                           'terminado'=>$terminado
                                                          ]));
        }
        else
        {
            $registros=DB::table('pagos_distribuidors')
                                ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                ->leftJoin(DB::raw('(select user_id,anticipo from anticipo_no_pagos where calculo_id='.$id_calculo.') as anp'),
                                            'anp.user_id','=','pagos_distribuidors.user_id'
                                            )
                                ->select('users.id',DB::raw('users.user as numero_distribuidor'),DB::raw('users.name as nombre'),
                                                                'pagos_distribuidors.total_pago',
                                                                DB::raw('pagos_distribuidors.comision_nuevas + pagos_distribuidors.bono_nuevas +  pagos_distribuidors.comision_adiciones + pagos_distribuidors.bono_adiciones +pagos_distribuidors.comision_renovaciones + pagos_distribuidors.bono_renovaciones as comisiones'),
                                                                DB::raw('pagos_distribuidors.nuevas_comision_no_pago + pagos_distribuidors.nuevas_bono_no_pago +  pagos_distribuidors.adiciones_comision_no_pago + pagos_distribuidors.adiciones_bono_no_pago + pagos_distribuidors.renovaciones_comision_no_pago + pagos_distribuidors.renovaciones_bono_no_pago as comisiones_pendientes'),
                                                                'pagos_distribuidors.anticipo_ordinario',
                                                                'pagos_distribuidors.anticipos_extraordinarios',
                                                                'pagos_distribuidors.anticipo_no_pago',
                                                                DB::raw('anp.anticipo as anticipo_no_pago'),
                                                                'pagos_distribuidors.pdf',
                                                                'pagos_distribuidors.xml'
                                                                )
                                ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                ->where('pagos_distribuidors.version',$version)
                                ->where('users.user','like','1%')
                                ->orderBy('users.name','asc')
                                ->paginate(10);
            return(view('acciones_distribuidores_calculo',['calculo'=>$calculo,
                                'registros'=>$registros,'query'=>'',
                                'version'=>$version,
                                'etapa_cierre'=>$etapa_cierre,
                                'terminado'=>$terminado
                               ]));
        }
    }
   
    
    public function transacciones_pago_distribuidor(Request $request)
    {
    $distribuidor=User::with('detalles')->find($request->id_user);
    $pago=PagosDistribuidor::where('calculo_id',$request->id)
                            ->where('version',$request->version)
                            ->where('user_id',$request->id_user)
                            ->get()
                            ->first();
    
    $sql_consulta="SELECT a.upfront as upfront,a.bono,c.tipo as c_tipo,c.periodo as c_periodo,c.contrato as c_contrato,c.cuenta as c_cuenta,c.cliente as c_cliente,c.plan as c_plan,c.dn as c_dn,c.propiedad as c_propiedad,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_venta_id=c.id and a.calculo_id='".$request->id."' and b.user_id='".$request->id_user."' and a.estatus_inicial='PAGO' and a.version='".$request->version."'
                    UNION
                    SELECT a.comision as upfront,0 as bono,c.tipo as c_tipo,c.periodo as c_periodo,c.contrato as c_contrato,c.cuenta as c_cuenta,c.cliente as c_cliente,c.plan as c_plan,c.dn as c_dn,c.propiedad as c_propiedad,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_addons as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_id=c.id and a.calculo_id='".$request->id."' and b.user_id='".$request->id_user."'and a.version='".$request->version."'
                ";
    $query=DB::select(DB::raw(
        $sql_consulta
       ));
    $sql_consulta_no_pago="SELECT a.upfront,a.bono,0 as c_renta,0 as c_plazo,0 as c_descuento_multirenta,0 as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id='".$request->id."' and b.user_id='".$request->id_user."' and a.estatus_inicial='NO PAGO' and a.version='".$request->version."'";
    $query_no_pago=DB::select(DB::raw(
        $sql_consulta_no_pago
       ));
    //return($distribuidor);
    if($request->id_user=='5000')
    {
        $pago=PagosDistribuidor::where('calculo_id',$request->id)
                            ->where('version',$request->version)
                            ->whereIn('user_id',[75,76,78,80,93])
                            ->get()
                            ->first();
                            $sql_consulta="SELECT a.upfront as upfront,a.bono,c.tipo as c_tipo,c.periodo as c_periodo,c.contrato as c_contrato,c.cuenta as c_cuenta,c.cliente as c_cliente,c.plan as c_plan,c.dn as c_dn,c.propiedad as c_propiedad,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_venta_id=c.id and a.calculo_id='".$request->id."' and b.user_id IN (75,76,78,80,93) and a.estatus_inicial='PAGO' and a.version='".$request->version."'
                            UNION
                            SELECT a.comision as upfront,0 as bono,c.tipo as c_tipo,c.periodo as c_periodo,c.contrato as c_contrato,c.cuenta as c_cuenta,c.cliente as c_cliente,c.plan as c_plan,c.dn as c_dn,c.propiedad as c_propiedad,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_addons as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_id=c.id and a.calculo_id='".$request->id."' and and b.user_id IN (75,76,78,80,93) and a.version='".$request->version."'
                        ";
            $query=DB::select(DB::raw(
                $sql_consulta
               ));
            $sql_consulta_no_pago="SELECT a.upfront,a.bono,0 as c_renta,0 as c_plazo,0 as c_descuento_multirenta,0 as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id='".$request->id."' and b.user_id in (75,76,78,80,93) and a.estatus_inicial='NO PAGO' and a.version='".$request->version."'";
            $query_no_pago=DB::select(DB::raw(
                $sql_consulta_no_pago
               ));
               return(view('transacciones_pago_distribuidor',['query'=>$query,'query_no_pago'=>$query_no_pago,'bono'=>0,'pago'=>$pago,'distribuidor'=>$distribuidor]));
    }
    return(view('transacciones_pago_distribuidor',['query'=>$query,'query_no_pago'=>$query_no_pago,'bono'=>$distribuidor->detalles->bono,'pago'=>$pago,'distribuidor'=>$distribuidor]));
    }
    public function transacciones_charge_back_distribuidor(Request $request)
    {
        $calculo_id=$request->id;
        $user_id=$request->id_user;
        $version=$request->version;
        $query=ChargeBackDistribuidor::select('ventas.*',
                                              'charge_back_distribuidors.charge_back',
                                              'charge_back_distribuidors.cargo_equipo',
                                              'callidus_ventas.fecha_baja',
                                              'callidus_ventas.tipo_baja',
                                              'comision_ventas.upfront',
                                              'comision_ventas.bono'
                                              )
                                    ->join('comision_ventas','charge_back_distribuidors.comision_venta_id','=','comision_ventas.id')
                                    ->join(DB::raw('(select ventas.*,users.name from ventas left join users on ventas.user_id=users.id) as ventas'),
                                    'comision_ventas.venta_id','=','ventas.id')
                                    ->join('callidus_ventas','charge_back_distribuidors.callidus_venta_id','=','callidus_ventas.id')
                                    ->where('charge_back_distribuidors.calculo_id',$calculo_id)->where('charge_back_distribuidors.comision_venta_id','!=',0)
                                    ->where('ventas.user_id',$user_id)
                                    ->get();

    return(view('transacciones_charge_back_distribuidor',['query'=>$query]));
    }
    public function charge_back_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $query=ChargeBackDistribuidor::select('callidus_ventas.*',
                                            'charge_back_distribuidors.charge_back',
                                            'charge_back_distribuidors.cargo_equipo',
                                            'callidus_ventas.fecha_baja',
                                            'callidus_ventas.tipo_baja',
                                            'comision_ventas.upfront',
                                            'comision_ventas.bono',
                                            'comision_ventas.name'
                                              )
                                    ->leftJoin(DB::raw(
                                        '(select a.*,b.name from comision_ventas as a left join 
                                        (select ventas.id,users.name from ventas left join users on ventas.user_id=users.id)
                                        as b on a.venta_id=b.id) as comision_ventas'
                                        ),
                                            'charge_back_distribuidors.comision_venta_id','=','comision_ventas.id')
                                    ->join('callidus_ventas','charge_back_distribuidors.callidus_venta_id','=','callidus_ventas.id')
                                    ->where('charge_back_distribuidors.calculo_id',$calculo_id)
                                    ->get();
    return(view('transacciones_charge_back_distribuidor',['query'=>$query]));
    }
    public function distribuidores_consulta_pago(Request $request)
    {
        return(PagosDistribuidor::where('calculo_id',$request->id)->where('user_id',$request->user_id)->where('version',$request->version)->get()->first());
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
                                ->when(Auth::user()->perfil=='distribuidor' || Auth::user()->perfil=='ejecutivo' || Auth::user()->perfil=='gerente',function($query){$query->where('ventas.user_id',Auth::user()->id);})
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
                                ->when(Auth::user()->perfil=='distribuidor' || Auth::user()->perfil=='ejecutivo' || Auth::user()->perfil=='gerente',function($query){$query->where('ventas.user_id',Auth::user()->id);})
                                ->orderBy('ventas.cliente','asc')
                                ->paginate(10);
            return(view('ventas_review',['registros'=>$registros,'query'=>'']));
        }

    }
    public function ventas_inconsistencias(Request $request)
    {
        $calculo=Calculo::find($request->id);
        $version=$request->version;
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
                                ->where('comision_ventas.version',$version)
                                ->where(function($query){
                                    $query->where('ventas.cliente','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.folio','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.dn','like','%'.$_GET["query"].'%')
                                          ->orWhere('ventas.cuenta','like','%'.$_GET["query"].'%');
                                        })
                                ->paginate(10);
            $registros->appends($request->all());
            
            return(view('ventas_inconsistencias',['calculo'=>$calculo,'registros'=>$registros,'query'=>$_GET['query'],'version'=>$version]));
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
                                ->where('comision_ventas.version',$version)
                                ->paginate(10);
            return(view('ventas_inconsistencias',['calculo'=>$calculo,'registros'=>$registros,'query'=>'','version'=>$version]));
        }

    }
    public function export_validacion(Request $request)
    {
        $query=Venta::with('user')->where('validado',false)->get();
        return(view('transacciones_validacion',['query'=>$query]));
    }
    public function pagos_export(Request $request)
    {
        $query=PagosDistribuidor::with('user')->where('calculo_id',$request->id)->where('version',$request->version)->get();
        return(view('pagos_export',['query'=>$query]));
    }
    public function reclamos_export(Request $request)
    {
        $query=Reclamo::with('venta','callidus')->where('calculo_id',$request->id)->get();
        return(view('reclamos_export',['query'=>$query]));
    }
    public function callidus_no_usados(Request $request)
    {
        $query=CallidusVenta::doesnthave('pagada')->where('calculo_id',$request->id)->get();
        return(view('callidus_no_usados',['query'=>$query]));
    }
    public function residuales(Request $request)
    {
        $query=ComisionResidual::with('callidus','user')
                                ->where('user_id','!=',1)
                                ->where('user_id','!=',1000)
                                ->where('calculo_id',$request->id)
                                ->get();
        return(view('residuales_pagados',['query'=>$query]));

    }
    public function residuales_distribuidor(Request $request)
    {
        $query=ComisionResidual::with('callidus','user')
                                ->where('user_id','=',$request->user_id)
                                ->where('calculo_id',$request->id)
                                ->get();
        $distribuidor=User::find($request->user_id);
        return(view('residuales_pagados',['query'=>$query,'distribuidor'=>$distribuidor]));

    }
    public function export_alertas(Request $request)
    {
        $user_id=$request->user_id;
        $query=AlertaCobranza::with('callidus','user')->where('calculo_id',$request->id)
                              ->when($user_id!="0",function($q) use ($user_id)
                                    {
                                        $q->where('user_id',$user_id);
                                    }
                                    )
                              ->get();
        return(view('alertas_export',['query'=>$query]));
    }    
}
