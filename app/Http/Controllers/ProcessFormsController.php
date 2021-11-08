<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\ComisionVenta;
use App\Models\User;
use App\Models\Distribuidor;
use App\Models\Calculo;
use App\Models\AnticipoNoPago;
use App\Models\AnticipoExtraordinario;
use App\Models\PagosDistribuidor;
use App\Models\Reclamo;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessFormsController extends Controller
{
    public function venta_nueva(Request $request)
    {
        $request->validate([
            'cuenta'=> ' required',
            'nombre_cliente'=> ' required|max:255',
            'tipo'=> 'required',
            'propiedad'=> 'required',
            'dn'=> 'required|digits:10',
            'plan'=> ' required',
            'plazo'=> ' required',
            'renta'=> ' required|numeric',
            'equipo'=> ' required',
            'descuento_multirenta'=> ' required|numeric',
            'afectacion_comision'=> ' required|numeric',
            'contrato'=> ' required',
            'fecha_movimiento' => 'required|date_format:Y-m-d'
        ]);

        $ocurrencias=Venta::select(DB::raw('dn,count(*) as ocurrencias'))
                            ->whereRaw('lpad(fecha,7,0)=?',[substr($request->fecha_movimiento,0,7)])
                            ->where('dn',$request->dn)
                            ->groupBy('dn')
                            ->get()
                            ->first();
        if(!is_null($ocurrencias))
        {
            return(view('mensaje_venta_nueva',['estatus'=>'FAIL',
                                'mensaje'=> 'El DN ('.$request->dn.') ya se encuentra en los registros del periodo',
                                'liga'=>$request->liga,
                                'parametros'=> '']));
        }
        $ocurrencias=Venta::select(DB::raw('folio,count(*) as ocurrencias'))
                            ->whereRaw('lpad(fecha,7,0)=?',[substr($request->fecha_movimiento,0,7)])
                            ->where('folio',$request->folio)
                            ->groupBy('folio')
                            ->get()
                            ->first();
        if(!is_null($ocurrencias))
        {
            return(view('mensaje_venta_nueva',['estatus'=>'FAIL',
                                'mensaje'=> 'El Folio ('.$request->folio.') ya se encuentra en los registros del periodo',
                                'liga'=>$request->liga,
                                'parametros'=> '']));
        }

            $registro=new Venta;
            $registro->user_id=Auth::user()->id;
            $registro->cuenta=$request->cuenta;
            $registro->cliente=$request->nombre_cliente;
            $registro->tipo=$request->tipo;
            $registro->fecha=$request->fecha_movimiento;
            $registro->propiedad=$request->propiedad;
            $registro->dn=$request->dn;
            $registro->plan=$request->plan;
            $registro->folio=$request->folio;
            $registro->ciudad=$request->ciudad;
            $registro->plazo=$request->plazo;
            $registro->renta=$request->renta;
            $registro->equipo=$request->cuenta;
            $registro->descuento_multirenta=$request->descuento_multirenta;
            $registro->afectacion_comision=$request->afectacion_comision;
            $registro->contrato=$request->contrato;
            $registro->validado=!(Auth::user()->perfil=='distribuidor');
            $registro->user_id_carga=Auth::user()->id;
            $registro->user_id_validacion=0;
            $registro->carga_id=0;
            $registro->save();

        $parametros="?complete=OK";
        $parametros=$parametros."&cuenta=".$request->cuenta;
        $parametros=$parametros."&nombre_cliente=".$request->nombre_cliente;
        $parametros=$parametros."&tipo=".$request->tipo;
        $parametros=$parametros."&propiedad=".$request->propiedad;
        $parametros=$parametros."&descuento_multirenta=".$request->descuento_multirenta;
        $parametros=$parametros."&afectacion_comision=".$request->afectacion_comision;
        $parametros=$parametros."&fecha_movimiento=".$request->fecha_movimiento;
        
        return(view('mensaje_venta_nueva',['estatus'=>'OK',
                                'mensaje'=> 'El registro de venta con DN y Folio ('.$request->dn.', '.$request->folio.') se ha realizado con exito',
                                'liga'=>$request->liga,
                                'parametros'=> $parametros
        ]));
    }
    public function distribuidores_actualiza(Request $request)
    {
        $request->validate([
            'id_distribuidor'=>'required',
            'nombre'=> 'required',
            'region'=> 'required',
            'a_24'=> 'required|numeric|between:0,5.99',
            'a_18'=> 'required|numeric|between:0,5.99',
            'a_12'=> 'required|numeric|between:0,5.99',
            'r_24'=> 'required|numeric|between:0,5.99',
            'r_18'=> 'required|numeric|between:0,5.99',
            'r_12'=> 'required|numeric|between:0,5.99',
            'porcentaje_residual'=>'exclude_unless:residual,on|required|numeric|between:1,6.0',
            'porcentaje_adelanto'=>'exclude_unless:adelanto,on|required|numeric|between:1,50.0'
        ]);
        $pr=0;
        $pa=0;
        if($request->boolean('residual'))
        {
            $pr=$request->porcentaje_residual;
        }
        if($request->boolean('adelanto'))
        {
            $pa=$request->porcentaje_adelanto;
        }
        Distribuidor::where('id', $request->id_distribuidor)
        ->update(['nombre' => $request->nombre,
                  'region' => $request->region,
                  'a_24' => $request->a_24,
                  'a_18' => $request->a_18,
                  'a_12' => $request->a_12,
                  'r_24' => $request->r_24,
                  'r_18' => $request->r_18,
                  'r_12' => $request->r_12,
                  'bono' => $request->boolean('bono'),
                  'residual' => $request->boolean('residual'),
                  'porcentaje_residual' => $pr,
                  'adelanto' => $request->boolean('adelanto'),
                  'porcentaje_adelanto' => $pa,
                  'emite_factura'=> $request->boolean('factura'),
                ]);
        User::where('id',$request->id_user)
        ->update(['supervisor'=>$request->supervisor,
                  'name'=>$request->nombre
                ]);

        return(back()->withStatus('Registro de '.$request->nombre.' actualizado con exito'));
        
    }
    public function distribuidores_nuevo(Request $request)
    {
        $request->validate([
            'nombre'=> 'required',
            'region'=> 'required',
            'a_24'=> 'required|numeric|between:0,5.99',
            'a_18'=> 'required|numeric|between:0,5.99',
            'a_12'=> 'required|numeric|between:0,5.99',
            'r_24'=> 'required|numeric|between:0,5.99',
            'r_18'=> 'required|numeric|between:0,5.99',
            'r_12'=> 'required|numeric|between:0,5.99',
            'porcentaje_residual'=>'exclude_unless:residual,on|required|numeric|between:1,6.0',
            'porcentaje_adelanto'=>'exclude_unless:adelanto,on|required|numeric|between:1,50.0',
        ]);
        $pr=0;
        $pa=0;
        if($request->boolean('residual'))
        {
            $pr=$request->porcentaje_residual;
        }
        if($request->boolean('adelanto'))
        {
            $pa=$request->porcentaje_adelanto;
        }
        $numero_distribuidor=Distribuidor::select(DB::raw('max(numero_distribuidor) as ultimo'))->get()->first();

        $usuario=new User;
        $usuario->user=$numero_distribuidor->ultimo+1;
        $usuario->perfil='distribuidor';
        $usuario->name=$request->nombre;
        $usuario->email=($numero_distribuidor->ultimo+1).'@ttdsolutions.com.mx';
        $usuario->password='$2y$10$0ATfpb55ADCKEJltfS8c/ONmlcaK6RW0dlbsqTQ51DASoHAf4RNZm';
        $usuario->ultimo_login=now()->toDateTimeString();
        $usuario->anterior_login=now()->toDateTimeString();
        $usuario->save();

        $registro=new Distribuidor;
        $registro->user_id=$usuario->id;
        $registro->numero_distribuidor=$numero_distribuidor->ultimo+1;
        $registro->nombre=$request->nombre;
        $registro->region=$request->region;
        $registro->a_24=$request->a_24;
        $registro->a_18=$request->a_18;
        $registro->a_12=$request->a_12;
        $registro->r_24=$request->r_24;
        $registro->r_18=$request->r_18;
        $registro->r_12=$request->r_12;
        $registro->bono=$request->boolean('bono');
        $registro->residual=$request->boolean('residual');
        $registro->porcentaje_residual=$pr;
        $registro->adelanto=$request->boolean('adelanto');
        $registro->porcentaje_adelanto=$pa;
        $registro->emite_factura=$request->boolean('bono');
        $registro->save();
        return(back()->withStatus('Registro de '.$request->nombre.' creado con exito, numero distribuidor y usuario de sistema = '.$registro->numero_distribuidor.''));
        
    }
    public function ventas_actualiza(Request $request)
    {
        $request->validate([
            'cuenta'=> ' required',
            'cliente'=> ' required|max:255',
            'tipo'=> 'required',
            'propiedad'=> 'required',
            'dn'=> 'required|digits:10',
            'plan'=> ' required',
            'folio'=>'required|numeric',
            'plazo'=> ' required',
            'renta'=> ' required|numeric',
            'descuento_multirenta'=> ' required|numeric',
            'afectacion_comision'=> ' required|numeric',
            //'contrato'=> ' required',
            'fecha_movimiento' => 'required|date_format:Y-m-d'
        ]);

        Venta::where('id', $request->id_venta)
        ->update(['cliente' => $request->cliente,
                  'cuenta' => $request->cuenta,
                  'tipo' => $request->tipo,
                  'propiedad' => $request->propiedad,
                  'folio'=>$request->folio,
                  'ciudad'=>$request->ciudad,
                  'dn' => $request->dn,
                  'plan' => $request->plan,
                  'plazo' => $request->plazo,
                  'renta' => $request->renta,
                  'equipo' => $request->equipo,
                  'descuento_multirenta'=>$request->descuento_multirenta,
                  'afectacion_comision'=>$request->afectacion_comision,
                  'fecha'=>$request->fecha_movimiento,
                  'validado'=>$request->validado,
                  'user_id_validacion'=>($request->validado=="1"?Auth::user()->id:0),

                ]);
                return(back()->withStatus('Registro de venta actualizado con exito'));
    }
    public function ventas_valida_distribuidor(Request $request)
    {
        Venta::where('user_id', $request->id_distribuidor)
            ->update(['validado'=>1,
                      'user_id_validacion'=>Auth::user()->id,
                    ]);
        return(back()->withStatus('Registro de venta del distribuidor validados con exito'));
    }
    public function distribuidores_anticipo_no_pago(Request $request)
    {
        $request->validate([
            'anticipo_no_pago'=> ' required|numeric',
        ]);
        $updates=AnticipoNoPago::where('user_id',$request->id_distribuidor)->where('calculo_id',$request->id_calculo)
                            ->update([
                                'anticipo'=>$request->anticipo_no_pago,
                            ]);
        if($updates==0)
        {
            $registro=new AnticipoNoPago;
            $registro->calculo_id=$request->id_calculo;
            $registro->user_id=$request->id_distribuidor;
            $registro->anticipo=$request->anticipo_no_pago;
            $registro->save();
        }

        if($request->version=="2")
        {
            $pago=PagosDistribuidor::where('user_id',$request->id_distribuidor)
                                    ->where('calculo_id',$request->id_calculo)
                                    ->where('version',2)
                                    ->get()
                                    ->first();
            $pago->total_pago=$pago->total_pago-$pago->anticipo_no_pago+$request->anticipo_no_pago;
            $pago->anticipo_no_pago=$request->anticipo_no_pago;
            $pago->save();

            return(back()->withStatus('Anticipo APLICADO con exito!'));
        }

        return(back()->withStatus('Anticipo PROGRAMADO PARA CIERRE con exito!'));

    }
    public function accion_inconsistencia(Request $request)
    {
        
        if($request->accion=="aclara")
        {
            $razon='Diferencia en ';
            $dif_renta=floatval($request->renta)-floatval($request->c_renta);
            if($dif_renta<(-1) || $dif_renta>(1))
            {$razon=$razon.', renta (debe ser :'.$request->renta.')';}
            if($request->plazo!=$request->c_plazo)
            {$razon=$razon.', plazo (debe ser :'.$request->plazo.')';}
            if($request->descuento_multirenta!=$request->c_descuento_multirenta)
            {$razon=$razon.', descuento multirenta (debe ser :'.$request->descuento_multirenta.'%)';}
            if($request->afectacion_comision!=$request->c_afectacion_comision)
            {$razon=$razon.', afectacion comision (debe ser :'.$request->afectacion_comision.'%)';}

            $actualizados=Reclamo::where('venta_id',$request->id_venta)
                        ->where('calculo_id',$request->id_calculo)
                        ->where('tipo','Inconsistencia')
                        ->update(['razon'=>$razon]);

            if($actualizados==0)
            {
                $reclamo=new Reclamo;
                $reclamo->venta_id=$request->id_venta;
                $reclamo->calculo_id=$request->id_calculo;
                $reclamo->monto=0;
                $reclamo->razon=$razon;
                $reclamo->tipo="Inconsistencia";
                $reclamo->save();
            }
            return(back()->withStatus('Registro de aclaracion generado con exito'));
        }
        if($request->accion=="corrige")
        {
            $venta=Venta::find($request->id_venta);
            $venta->renta=$request->c_renta;
            $venta->plazo=$request->c_plazo;
            $venta->afectacion_comision=$request->c_afectacion_comision;
            $venta->descuento_multirenta=$request->c_descuento_multirenta;
            $venta->save();

            ComisionVenta::where('venta_id',$request->id_venta)
                        ->where('calculo_id_proceso',$request->id_calculo)
                        ->update(['consistente'=>1,'estatus_final'=>'VENTA PAGADA']);

            Reclamo::where('venta_id',$request->id_venta)
                        ->where('calculo_id',$request->id_calculo)
                        ->where('tipo','Inconsistencia')
                        ->delete();

            return(back()->withStatus('Correccion de registros internos realizada con exito'));
        }
    }
}
