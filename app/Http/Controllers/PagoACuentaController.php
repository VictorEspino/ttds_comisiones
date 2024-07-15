<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PagoACuenta;
use App\Models\Periodo;
use App\Models\Distribuidor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagoACuentaController extends Controller
{
    public function pagos_a_cuenta_consulta(Request $request)
    {
        return(PagoACuenta::with('periodo')->where('user_id',$request->user_id)->where('calculo_id_aplicado',0)->get());
    }
    public function distribuidores_pagos_save(Request $request)
    {
        $request->validate([
            'mes'=> 'required',
            'año'=> 'required',
            'cantidad'=>'required|numeric',
            'descripcion'=>'required|max:255'
        ]);
        $periodo=Periodo::where('año',$request->año)->where('mes',$request->mes)->get()->first();
        $respuesta=$this->valida_viabilidad_pago($request);
        if($respuesta['viable'])
        {
            $registro=new PagoACuenta;
            $registro->user_id=$request->id_distribuidor;
            $registro->periodo_id=$periodo->id;
            $registro->cantidad=$request->cantidad;
            $registro->descripcion=$request->descripcion;
            $registro->save();
        }
        else
        {
            return(back()->withStatus($respuesta['estatus']));
        }
        return(back()->withStatus('OK - Pago a cuenta de comisiones guardado con exito! '.$respuesta['estatus']));
    }
    public function pagos_a_cuenta_borrar(Request $request)
    {
        PagoACuenta::find($request->id)->delete();
        return('OK');
    }
    public function distribuidores_pagos(Request $request)
    {
        $años=Periodo::select(DB::raw('distinct(año) as valor'))
                    ->whereRaw('DATEDIFF( now(),fecha_fin)<60')
                    ->get()
                    ->take(2);
        $filtro=false;
        $query='';
        if(isset($_GET['query']))
        {   $filtro=true;
            $query=$_GET['query'];
        }
        $registros=Distribuidor::when($filtro,function($consulta) use ($query){$consulta->where('nombre','like','%'.$query.'%');})
                                ->orderBy('nombre','asc')
                                ->paginate(10);
        $registros->appends($request->all());
        return(view('distribuidores_pagos_a_cuenta',['registros'=>$registros,'query'=>$query,'años'=>$años]));        
    }
    private function valida_viabilidad_pago($request)
    {
        $respuesta=array('viable'=>false,'estatus'=>'');
        $comision_unitaria=1400;
        $porcentaje_proteccion=75;
        //verifica si existe calculo que ya se haya corrido, y en caso de que exista que no este terminado
        $año=$request->año;
        $mes=$request->mes;
        $periodo=Periodo::with('calculo')->where('año',$año)->where('mes',$mes)->get()->first();
        if(is_null($periodo->calculo))
        {
            $respuesta['viable']=true;
            $respuesta['estatus']='';
            return($respuesta);
        }
        else
        {
            if($periodo->calculo->terminado=="1")
                {
                    $respuesta['viable']=false;
                    $respuesta['estatus']='Periodo de medicion terminado ya no es posible registrar una cantidad a cuenta de este periodo';
                    return($respuesta);
                }                
            if($periodo->calculo->cierre=="1")
                {
                    $respuesta['viable']=true;
                    $respuesta['estatus']='EJECUTE EL CIERRE NUEVAMENTE';
                }
            if($periodo->calculo->cierre=="0")
                {
                    $respuesta['viable']=true;
                    $respuesta['estatus']='Pago a cuenta OK';
                }
        }       
        return($respuesta);
    }
    public function base_pagos_a_cuenta(Request $request)
    {
        $filtro=false;
        $distribuidores=Distribuidor::select('user_id','nombre')
                                ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                                ->orderBy('nombre','asc')->get();
        $solo_distribuidores=User::select('id')->where('perfil','distribuidor')->get();
        $periodos_con_anticipo=PagoACuenta::select(DB::raw('distinct periodo_id'))->get()->pluck('periodo_id');
        $periodos=Periodo::whereIn('id',$periodos_con_anticipo)->get();
        $solo_distribuidores=$solo_distribuidores->pluck('id');
        $aplicado="NULO";
        $distribuidor="NULO";
        $periodo_id="NULO";
        $nuevas_facturas=false;
        $nuevos_pagos=false;
        if(isset($_GET['f']))
        {
            $filtro=true;
            if(isset($_GET['aplicado'])){$aplicado=$_GET["aplicado"]==""?"NULO":$_GET["aplicado"];}            
            if(isset($_GET['distribuidor'])){$distribuidor=$_GET["distribuidor"]==""?"NULO":$_GET["distribuidor"];}
            if(isset($_GET['periodo'])){$periodo_id=$_GET["periodo"]==""?"NULO":$_GET["periodo"];}
            //return($tipo);
        }
        if(isset($_GET['np']))
        {
            $nuevos_pagos=true;
            //return('NUEVOS_PAGOS');
        }
        if(isset($_GET['nf']))
        {
            $nuevas_facturas=true;
            //return('NUEVAS_FACTURAS');
        }
        $pagos=[];
        $pagos=PagoACuenta::with('user','user.detalles','periodo')
                    ->select('id','periodo_id','user_id','cantidad','descripcion','xml','pdf','aplicado','carga_facturas')
                    ->orderBy('created_at','desc')
                    ->when($filtro && $aplicado!="NULO",function ($query) use ($aplicado){$query->where('aplicado',$aplicado);})
                    ->when($filtro && $distribuidor!="NULO",function ($query) use ($distribuidor){$query->where('user_id',$distribuidor);})
                    ->when($filtro && $periodo_id!="NULO",function ($query) use ($periodo_id){$query->where('periodo_id',$periodo_id);})
                    ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                    ->when($nuevos_pagos,function($query){$query->where('created_at','>=',Auth::user()->anterior_login);})
                    ->when($nuevas_facturas,function($query){$query->where('carga_facturas','>=',Auth::user()->anterior_login);})
                    ->whereIn('user_id',$solo_distribuidores)
                    ->paginate(10);
                    //return($pagos->get());
        $pagos->appends($request->all());
        return(view('base_pagos_a_cuenta',['pagos'=>$pagos,
                             'distribuidores'=>$distribuidores,
                             'periodos'=>$periodos,
                             'aplicado'=>$aplicado=='NULO'?'':$aplicado,
                             'distribuidor_id'=>$distribuidor=='NULO'?'0':$distribuidor,
                             'periodo_id'=>$periodo_id=='NULO'?'0':$periodo_id,
                            ]));
    }
    public function cambiar_estatus_pago_a_cuenta(Request $request)
    {
        PagoACuenta::find($request->id)->update(['aplicado'=>$request->nuevo_estatus]);
        return(back()->withStatus('Pago a cuenta para '.$request->nombre.' por $'.number_format($request->monto,0).' actualizado con exito!'));
    }
    public function facturar_pago_a_cuenta_form(Request $request)
    {
        $id_pago_a_cuenta=$request->id;
        PagoACuenta::with('periodo')->find($id_pago_a_cuenta);
        return(view('facturar_pago_a_cuenta',['pago_a_cuenta'=>PagoACuenta::find($id_pago_a_cuenta)]));
    }
    public function facturar_pago_a_cuenta_save(Request $request)
    {
        //return($request);
        $request->validate([
            'pdf_file' => 'required|mimes:pdf',
            'xml_file' => 'required|mimes:xml',
           ]);
        //return($request->all());
        $upload_path = public_path('facturas');

        $upload_path="/var/www/ttds.icube.com.mx/facturas";

        $file_name = $request->file("pdf_file")->getClientOriginalName();
        $generated_new_name_pdf = 'pago_a_cuenta_'.$request->id.'_'.time() . '.' . $request->file("pdf_file")->getClientOriginalExtension();
        $request->file("pdf_file")->move($upload_path, $generated_new_name_pdf);

        $file_name = $request->file("xml_file")->getClientOriginalName();
        $generated_new_name_xml ='pago_a_cuenta'.$request->id.'_'.time() . '.' . $request->file("xml_file")->getClientOriginalExtension();
        $request->file("xml_file")->move($upload_path, $generated_new_name_xml);

        PagoACuenta::where('id',$request->id)
                            ->update([
                                'pdf'=>$generated_new_name_pdf,
                                'xml'=>$generated_new_name_xml,
                                'carga_facturas'=>now()->toDateTimeString(),
                            ]);

        return(back()->withStatus('Datos de facturacion OK'));
    }
}
