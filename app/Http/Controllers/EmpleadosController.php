<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\User;
use App\Models\Calculo;
use App\Models\Venta;
use App\Models\ComisionVenta;
use App\Models\AutorizacionEspecial;
use App\Models\PagosDistribuidor;
use App\Models\ChargeBackDistribuidor;
use App\Models\Mediciones;
use App\Models\AlertaCobranza;
use Illuminate\Support\Facades\DB;

class EmpleadosController extends Controller
{
    public function form_nuevo(Request $request)
    {
        $supervisores=Empleado::select('user_id','nombre')->where('puesto','GERENTE')->get();
        return(view('empleados_nuevo',['supervisores'=>$supervisores]));
    }
    public function empleados_nuevo(Request $request)
    {
        //return($request->all());
        $request->validate([
                            'nombre'=>'required',
                            'region'=>'required',
                            'puesto'=>'required',
                            'estatus'=>'required',
                            'cuota_unidades'=>'required|numeric',
                            'aduana_nuevas'=>'required|numeric',
                            'fecha_ingreso'=>'required',
                            'supervisor'=>'exclude_unless:puesto,EJECUTIVO|required',
                            ]);
        $numero_empleado=Empleado::select(DB::raw('max(numero_empleado) as ultimo'))->get()->first();
        if(is_null($numero_empleado->ultimo))
        {
            $numero_empleado->ultimo=200000;
        }
        
        $usuario=new User;
        $usuario->user=$numero_empleado->ultimo+1;
        $usuario->perfil=strtolower($request->puesto);
        $usuario->name=$request->nombre;
        $usuario->email=($numero_empleado->ultimo+1).'@ttdsolutions.com.mx';
        $usuario->password='$2y$10$0ATfpb55ADCKEJltfS8c/ONmlcaK6RW0dlbsqTQ51DASoHAf4RNZm';
        $usuario->ultimo_login=now()->toDateTimeString();
        $usuario->anterior_login=now()->toDateTimeString();
        $usuario->supervisor=$request->supervisor;
        $usuario->save();
        $registro=Empleado::create([
            'nombre'=>$request->nombre,
            'region'=>$request->region,
            'puesto'=>$request->puesto,
            'activo'=>$request->estatus,
            'user_id'=>$usuario->id,
            'numero_empleado'=>$numero_empleado->ultimo+1,
            'cuota_unidades'=>$request->cuota_unidades,
            'aduana_nuevas'=>$request->aduana_nuevas,
            'fecha_ingreso'=>$request->fecha_ingreso
        ]);
        return(back()->withStatus('Registro de '.$request->nombre.' creado con exito, numero empleado y usuario de sistema = '.$registro->numero_empleado.''));
    }
    public function empleados_admin(Request $request)
    {
        $consulta='';
        if(isset($_GET['query']))
        {
            $consulta=$_GET['query'];
        }
        $registros=Empleado::with('user')->when(isset($_GET['query']),function ($query) use ($consulta){
                                    $query->where('nombre','like','%'.$consulta.'%');
                                })
                                ->orderBy('nombre','asc')
                                ->paginate(10);
        $registros->appends($request->all());
        //return($registros);
        $supervisores=Empleado::select('user_id','nombre')->where('puesto','GERENTE')->get();
        return(view('empleados_admin',['registros'=>$registros,'supervisores'=>$supervisores,'query'=>$consulta]));
    }
    public function empleados_consulta(Request $request)
    {
        return(Empleado::with('user')->find($request->id));
    }
    public function empleados_actualiza(Request $request)
    {
        $request->validate(['nombre'=>'required',
                            'region'=>'required',
                            'puesto'=>'required',
                            'activo'=>'required',
                            'cuota_unidades'=>'required|numeric',
                            'aduana_nuevas'=>'required|numeric',
                            'fecha_ingreso'=>'required',
                            'supervisor'=>'exclude_unless:puesto,EJECUTIVO|required',
                            ]);
        Empleado::where('id', $request->id_empleado)
        ->update(['nombre' => $request->nombre,
                  'region' => $request->region,
                  'puesto' => $request->puesto,
                  'activo' => $request->activo,
                  'cuota_unidades' => $request->cuota_unidades,
                  'aduana_nuevas' => $request->aduana_nuevas,
                  'fecha_ingreso' => $request->fecha_ingreso,   
                ]);
        User::where('id',$request->id_user)
        ->update(['supervisor'=>$request->supervisor,
                  'name'=>$request->nombre
                ]);
        return(back()->withStatus('Registro de '.$request->nombre.' actualizado con exito'));
    }
    public function acciones_empleados_calculo(Request $request)
    {
        $id_calculo=$request->id;
        $calculo=Calculo::with('periodo')->find($id_calculo);
        $etapa_cierre=$calculo->cierre;
        $version=$request->version;
        $terminado=$calculo->terminado;
        $autorizaciones_especiales=AutorizacionEspecial::where('calculo_id',$id_calculo)->get()->pluck('porcentaje_autorizado','user_id');
        if(isset($_GET['query']))
        {
            $registros=DB::table('pagos_distribuidors')
                                    ->join('users', 'users.id', '=', 'pagos_distribuidors.user_id')
                                    ->leftJoin(DB::raw('(select user_id,anticipo from anticipo_no_pagos where calculo_id='.$id_calculo.') as anp'),
                                            'anp.user_id','=','pagos_distribuidors.user_id'
                                            )
                                    ->select('users.id',DB::raw('users.user as numero_empleado'),DB::raw('users.name as nombre'),
                                                                'pagos_distribuidors.total_pago',
                                                                DB::raw('pagos_distribuidors.comision_nuevas + pagos_distribuidors.bono_nuevas +  pagos_distribuidors.comision_adiciones + pagos_distribuidors.bono_adiciones +pagos_distribuidors.comision_renovaciones + pagos_distribuidors.bono_renovaciones as comisiones'),
                                                                DB::raw('pagos_distribuidors.nuevas_comision_no_pago + pagos_distribuidors.nuevas_bono_no_pago +  pagos_distribuidors.adiciones_comision_no_pago + pagos_distribuidors.adiciones_bono_no_pago + pagos_distribuidors.renovaciones_comision_no_pago + pagos_distribuidors.renovaciones_bono_no_pago as comisiones_pendientes'),
                                                                'pagos_distribuidors.anticipo_ordinario',
                                                                'pagos_distribuidors.anticipo_no_pago',
                                                                DB::raw('anp.anticipo as anticipo_no_pago'),
                                                                )
                                    ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                    ->where('pagos_distribuidors.version',$version)
                                    ->where('users.name','like','%'.$_GET["query"].'%')
                                    ->where('users.user','like','2%')
                                    ->orderBy('users.name','asc')
                                    ->paginate(10);
            $registros->appends($request->all());
            return(view('acciones_empleados_calculo',['calculo'=>$calculo,
                                                           'registros'=>$registros,'query'=>$_GET['query'],
                                                           'version'=>$version,
                                                           'etapa_cierre'=>$etapa_cierre,
                                                           'terminado'=>$terminado,
                                                           'autorizaciones_especiales'=>$autorizaciones_especiales
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
                                                                'pagos_distribuidors.anticipo_no_pago',
                                                                DB::raw('anp.anticipo as anticipo_no_pago'),
                                                                )
                                ->where('pagos_distribuidors.calculo_id',$id_calculo)
                                ->where('pagos_distribuidors.version',$version)
                                ->where('users.user','like','2%')
                                ->orderBy('users.name','asc')
                                ->paginate(10);
            return(view('acciones_empleados_calculo',['calculo'=>$calculo,
                                'registros'=>$registros,'query'=>'',
                                'version'=>$version,
                                'etapa_cierre'=>$etapa_cierre,
                                'terminado'=>$terminado,
                                'autorizaciones_especiales'=>$autorizaciones_especiales
                               ]));
        }
    }
    public function empleados_consulta_pago(Request $request)
    {
        return(PagosDistribuidor::where('calculo_id',$request->id)->where('user_id',$request->user_id)->where('version',$request->version)->get()->first());
    }
    public function empleados_autorizacion_especial(Request $request)
    {
        $id_calculo=$request->id_calculo_aut;
        $id_empleado=$request->id_empleado_aut;
        $user_id=$id_empleado;
        $actualizados=AutorizacionEspecial::where('calculo_id',$id_calculo)
                                            ->where('user_id',$id_empleado)
                                            ->update([
                                                'porcentaje_autorizado'=>$request->porcentaje_autorizacion,
                                            ]);
        if($actualizados==0)
        {
            $registro=new AutorizacionEspecial;
            $registro->calculo_id=$id_calculo;
            $registro->user_id=$id_empleado;
            $registro->porcentaje_autorizado=$request->porcentaje_autorizacion;
            $registro->save();
        }
        $campo='porcentaje_pago_vendedor';
        $empleado=User::find($user_id);
        $calculo=Calculo::find($id_calculo);
        if($empleado->perfil=='gerente')
        {$campo='porcentaje_pago_supervisor';}
        $supervisados=User::select('id')
                        ->where('supervisor',$user_id)
                        ->orWhere('id',$user_id)
                        ->get();
        $supervisados=$supervisados->pluck('id');
        $ventas=Venta::select('id')
                    ->whereBetween('fecha',[$calculo->periodo->fecha_inicio,$calculo->periodo->fecha_fin])
                    ->where('validado','1')
                    ->where(function($query) use ($supervisados)
                            {
                                $query->whereIn('user_id',$supervisados);
                                $query->orWhereIn('supervisor_id',$supervisados);
                            })
                    ->get()->pluck('id');
        ComisionVenta::whereIn('venta_id',$ventas)
                        ->where('calculo_id',$calculo->id)
                        ->update([
                                    $campo=>$request->porcentaje_autorizacion
                                ]);
        $registro=PagosDistribuidor::where('calculo_id',$calculo->id)
                                        ->where('user_id',$user_id)
                                        ->where('version',$request->version_aut)
                                        ->get()
                                        ->first();
        $total_comisiones=$total_comisiones=$registro->comision_nuevas+$registro->bono_nuevas+$registro->comision_adiciones+$registro->bono_adiciones+$registro->comision_renovaciones+$registro->bono_renovaciones;
        $registro->total_pago=$total_comisiones*($request->porcentaje_autorizacion/100)+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios-$registro->anticipo_ordinario;
        $registro->save();
        return(back()->withStatus('Autorizacion especial del '.$request->porcentaje_autorizacion.'% para '.$request->nombre_aut.' registrado con exito'));
    }
    public function estado_cuenta_empleado(Request $request)
    {
        //echo "inicio=".now();
        $id_calculo=$request->id;
        $id_user=$request->id_user;
        $version=$request->version;
        $medicion=Mediciones::where('calculo_id',$request->id)->where('user_id',$request->id_user)->where('version',$request->version)->get()->first();
        $calculo=Calculo::with('periodo')->find($id_calculo);
        $user=User::with('empleado')->find($id_user);
        $pago=PagosDistribuidor::where('calculo_id',$id_calculo)->where('user_id',$id_user)->where('version',$version)->get()->first();
        $alertas=0;
        if($version=="2")
        {
            $alertas_cobranza=AlertaCobranza::select(DB::raw('count(*) as n'))->where('calculo_id',$calculo->id)
                                            ->where('user_id',$id_user)
                                            ->get()
                                            ->first();
            $alertas=!is_null($alertas_cobranza->n)?$alertas_cobranza->n:0;
        }
        $autorizacion="NO";
        $porcentaje_autorizacion=0;
        $autorizacion_especial=AutorizacionEspecial::where('calculo_id',$id_calculo)
                                                    ->where('user_id',$id_user)
                                                    ->get();
        foreach($autorizacion_especial as $registro)
        {
            if($registro->porcentaje_autorizado!="0")
            {
                $autorizacion="SI";
                $porcentaje_autorizacion=$registro->porcentaje_autorizado;
            }
        }
        
        return(view('estado_cuenta_empleado',[  'calculo'=>$calculo,
                                                    'user'=>$user,
                                                    'pago'=>$pago,
                                                    'version'=>$version,
                                                    'alertas'=>$alertas,  
                                                    'medicion'=>$medicion,
                                                    'autorizacion_especial'=>$autorizacion,
                                                    'porcentaje_autorizacion'=>$porcentaje_autorizacion,          
                                                ]));
    }
    public function transacciones_pago_empleado(Request $request)
    {
        $pago=PagosDistribuidor::where('calculo_id',$request->id)
                                ->where('version',$request->version)
                                ->where('user_id',$request->id_user)
                                ->get()
                                ->first();

    $empleado=User::with('empleado')->find($request->id_user);
    $medicion=Mediciones::where('calculo_id',$request->id)->where('user_id',$request->id_user)->where('version',$request->version)->get()->first();
    $sql_consulta="SELECT b.user_id,b.supervisor_id,a.upfront as upfront,a.bono,a.upfront_supervisor,c.tipo as c_tipo,c.periodo as c_periodo,c.contrato as c_contrato,c.cuenta as c_cuenta,c.cliente as c_cliente,c.plan as c_plan,c.dn as c_dn,c.propiedad as c_propiedad,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_venta_id=c.id and a.calculo_id='".$request->id."' and (b.user_id='".$request->id_user."' or b.supervisor_id='".$request->id_user."') and a.estatus_inicial='PAGO' and a.version='".$request->version."'
                    UNION
                    SELECT b.user_id,b.supervisor_id,a.comision as upfront,0 as bono,a.comision_supervisor as upfront_supervisor,c.tipo as c_tipo,c.periodo as c_periodo,c.contrato as c_contrato,c.cuenta as c_cuenta,c.cliente as c_cliente,c.plan as c_plan,c.dn as c_dn,c.propiedad as c_propiedad,c.renta as c_renta,c.plazo as c_plazo,c.descuento_multirenta as c_descuento_multirenta,c.afectacion_comision as c_afectacion_comision,b.* FROM comision_addons as a,ventas as b,callidus_ventas as c WHERE a.venta_id=b.id and a.callidus_id=c.id and a.calculo_id='".$request->id."' and (b.user_id='".$request->id_user."' or b.supervisor_id='".$request->id_user."') and a.version='".$request->version."'
                ";
    //return($sql_consulta);
    $query=DB::select(DB::raw(
        $sql_consulta
       ));

    $sql_consulta_no_pago="SELECT b.user_id,b.supervisor_id,a.upfront,a.bono,a.upfront_supervisor,0 as c_renta,0 as c_plazo,0 as c_descuento_multirenta,0 as c_afectacion_comision,b.* FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id='".$request->id."' and (b.user_id='".$request->id_user."' or b.supervisor_id='".$request->id_user."') and a.estatus_inicial='NO PAGO' and a.version='".$request->version."'";
    $query_no_pago=DB::select(DB::raw(
        $sql_consulta_no_pago
       ));
    return(view('transacciones_pago_empleado',['empleado'=>$empleado,'query'=>$query,'query_no_pago'=>$query_no_pago,'pago'=>$pago]));
    }
    public function transacciones_charge_back_empleado(Request $request)
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
}
