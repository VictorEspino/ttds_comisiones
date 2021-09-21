<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\Distribuidor;
use App\Models\Calculo;
use App\Models\AnticipoNoPago;
use App\Models\AnticipoExtraordinario;
use App\Models\PagosDistribuidor;
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
            'dn'=> 'required|digits:10|unique:ventas,dn',
            'plan'=> ' required',
            'plazo'=> ' required',
            'renta'=> ' required|numeric',
            'equipo'=> ' required',
            'descuento_multirenta'=> ' required|numeric',
            'afectacion_comision'=> ' required|numeric',
            'contrato'=> ' required',
            'fecha_movimiento' => 'required|date_format:Y-m-d'
        ]);
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
                                'mensaje'=> 'El registro de venta con DN y Contrato ('.$request->mdn.', '.$request->contrato.') se ha realizado con exito',
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
        $numero_distribuidor=Distribuidor::select(DB::raw('max(numero_distribuidor) as ultimo'))->get()->first();

        $usuario=new User;
        $usuario->user=$numero_distribuidor->ultimo+1;
        $usuario->perfil='distribuidor';
        $usuario->name=$request->nombre;
        $usuario->email=($numero_distribuidor->ultimo+1).'@ttdsolutions.com.mx';
        $usuario->password='$2y$10$0ATfpb55ADCKEJltfS8c/ONmlcaK6RW0dlbsqTQ51DASoHAf4RNZm';
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
        $registro->save();
        return(back()->withStatus('Registro de '.$request->nombre.' creado con exito, numero distribuidor y usuario de sistema = '.$registro->numero_distribuidor.''));
        
    }
    public function calculo_nuevo(Request $request)
    {
        $request->validate([
            'descripcion'=> 'required|max:255',
            'fecha_inicio'=> 'required|date_format:Y-m-d',
            'fecha_fin'=> 'required|date_format:Y-m-d',
            'tipo'=> 'required',
        ]);
        $registro=new Calculo;
        $registro->descripcion=$request->descripcion;
        $registro->fecha_inicio=$request->fecha_inicio;
        $registro->fecha_fin=$request->fecha_fin;
        $registro->user_id=Auth::user()->id;
        $registro->tipo=$request->tipo;
        $registro->save();
        return(back()->withStatus('Registro de calculo de comisiones'.$request->descripcion.' creado con exito'));
        
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

        $pago=PagosDistribuidor::where('user_id',$request->id_distribuidor)->where('calculo_id',$request->id_calculo)->get()->first();
        $pago->total_pago=$pago->total_pago-$pago->anticipo_no_pago+$request->anticipo_no_pago;
        $pago->anticipo_no_pago=$request->anticipo_no_pago;
        $pago->save();

        return(back()->withStatus('Anticipo aplicado con exito!'));

    }
    public function distribuidores_anticipos_extraordinarios_save(Request $request)
    {
        $request->validate([
            'fecha'=> 'required|date_format:Y-m-d',
            'anticipo'=>'required|numeric',
            'descripcion'=>'required|max:255'
        ]);
        $registro=new AnticipoExtraordinario;
        $registro->user_id=$request->id_distribuidor;
        $registro->fecha_relacionada=$request->fecha;
        $registro->anticipo=$request->anticipo;
        $registro->descripcion=$request->descripcion;
        $registro->aplicado=false;
        $registro->save();
        return(back()->withStatus('Anticipo guardado con exito!'));
    }
    public function anticipos_extraordinarios_borrar(Request $request)
    {
        AnticipoExtraordinario::find($request->id)->delete();
        return('OK');
    }
}
