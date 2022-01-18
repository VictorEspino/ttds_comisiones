<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnticipoExtraordinario;
use App\Models\Periodo;
use App\Models\Distribuidor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AnticiposExtraordinariosController extends Controller
{
    public function anticipos_extraordinarios_consulta(Request $request)
    {
        return(AnticipoExtraordinario::with('periodo')->where('user_id',$request->user_id)->where('calculo_id_aplicado',0)->get());
    }
    public function distribuidores_anticipos_extraordinarios_save(Request $request)
    {
        $request->validate([
            'mes'=> 'required',
            'año'=> 'required',
            'anticipo'=>'required|numeric',
            'descripcion'=>'required|max:255'
        ]);
        $periodo=Periodo::where('año',$request->año)->where('mes',$request->mes)->get()->first();
        $respuesta=$this->valida_viabilidad_anticipo($request);
        if($respuesta['viable'])
        {
            $registro=new AnticipoExtraordinario;
            $registro->user_id=$request->id_distribuidor;
            $registro->periodo_id=$periodo->id;
            $registro->anticipo=$request->anticipo;
            $registro->descripcion=$request->descripcion;
            $registro->save();
        }
        else
        {
            return(back()->withStatus($respuesta['estatus']));
        }
        return(back()->withStatus('OK - Anticipo guardado con exito! '.$respuesta['estatus']));
    }
    public function anticipos_extraordinarios_borrar(Request $request)
    {
        AnticipoExtraordinario::find($request->id)->delete();
        return('OK');
    }
    public function distribuidores_anticipos_extraordinarios(Request $request)
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
        return(view('distribuidores_anticipos_extraordinarios',['registros'=>$registros,'query'=>$query,'años'=>$años]));        
    }
    private function valida_viabilidad_anticipo($request)
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
        }
        else
        {
            if($periodo->calculo->terminado=="1")
                {
                    $respuesta['viable']=false;
                    $respuesta['estatus']='Periodo de medicion';
                    return($respuesta);
                }                
            if($periodo->calculo->cierre=="1")
                {
                    $respuesta['viable']=true;
                    $respuesta['estatus']='EJECUTE EL CIERRE NUEVAMENTE';
                }
        }    
        $distribuidor=User::with('detalles')->find($request->id_distribuidor);
        $porcentaje_previo=$distribuidor->detalles->porcentaje_adelanto-($periodo->calculo->adelanto=="0"?25:12);
        $ventas=Venta::select(DB::raw('count(*) as n')) 
                        ->where('user_id',$request->id_distribuidor)
                        ->whereBetween('fecha',[$periodo->fecha_inicio,$periodo->fecha_fin])
                        ->get()
                        ->first();
        if(is_null($ventas->n))
        {
            $respuesta['viable']=false;
            $respuesta['estatus']='El distribuidor no tiene ventas en el periodo indicado';
            return($respuesta);
        }
        else
        {
            if($ventas->n==0)
            {
                $respuesta['viable']=false;
                $respuesta['estatus']='El distribuidor no tiene ventas en el periodo indicado';
                return($respuesta);
            }
        }
        $anticipos_ext_previos=AnticipoExtraordinario::where('user_id',$request->id_distribuidor)
                            ->where('periodo_id',$periodo->id)
                            ->select(DB::raw('sum(anticipo) as total'))
                            ->get()
                            ->first();
        $estimado_comisiones=$ventas->n*$comision_unitaria;
        $cantidad_anticipos_ext_previos=is_null($anticipos_ext_previos->total)?0:$anticipos_ext_previos->total;
        $porcentaje_cubierto=$porcentaje_previo+(100*$cantidad_anticipos_ext_previos/$estimado_comisiones);
        $porcentaje_resultante=$porcentaje_cubierto+(100*$request->anticipo/$estimado_comisiones);
        if($porcentaje_resultante>$porcentaje_proteccion)
            {
                $porcentaje_maximo_a_aplicar=$porcentaje_proteccion-$porcentaje_cubierto;
                $cantidad_maxima_anticipo=($porcentaje_maximo_a_aplicar/100)*$estimado_comisiones;
                $respuesta['viable']=false;
                $respuesta['estatus']='La cantidad del anticipo excede el % de proteccion integrado al sistema, especifique un adelanto menor a $'.number_format($cantidad_maxima_anticipo,0);
                return($respuesta);
            }
        else
            {
                $respuesta['viable']=true;
            }
        return($respuesta);
    }
    public function anticipos_extraordinarios(Request $request)
    {
        $filtro=false;
        $distribuidores=Distribuidor::select('user_id','nombre')
                                ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                                ->orderBy('nombre','asc')->get();
        $solo_distribuidores=User::select('id')->where('perfil','distribuidor')->get();
        $periodos_con_anticipo=AnticipoExtraordinario::select(DB::raw('distinct periodo_id'))->get()->pluck('periodo_id');
        $periodos=Periodo::whereIn('id',$periodos_con_anticipo)->get();
        $solo_distribuidores->pluck('id');
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
        $pagos=AnticipoExtraordinario::with('user','user.detalles','periodo')
                    ->select('id','periodo_id','user_id','anticipo','descripcion','xml','pdf','aplicado','carga_facturas')
                    ->orderBy('created_at','desc')
                    ->when($filtro && $aplicado!="NULO",function ($query) use ($aplicado){$query->where('aplicado',$aplicado);})
                    ->when($filtro && $distribuidor!="NULO",function ($query) use ($distribuidor){$query->where('user_id',$distribuidor);})
                    ->when($filtro && $periodo_id!="NULO",function ($query) use ($periodo_id){$query->where('periodo_id',$periodo_id);})
                    ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                    ->when($nuevos_pagos,function($query){$query->where('created_at','>=',Auth::user()->anterior_login);})
                    ->when($nuevas_facturas,function($query){$query->where('carga_facturas','>=',Auth::user()->anterior_login);})
                    ->whereIn('user_id',$solo_distribuidores)
                    ->paginate(10);
                    //return($pagos);
        $pagos->appends($request->all());
        return(view('anticipos',['pagos'=>$pagos,
                             'distribuidores'=>$distribuidores,
                             'periodos'=>$periodos,
                             'aplicado'=>$aplicado=='NULO'?'':$aplicado,
                             'distribuidor_id'=>$distribuidor=='NULO'?'0':$distribuidor,
                             'periodo_id'=>$periodo_id=='NULO'?'0':$periodo_id,
                            ]));
    }
    public function cambiar_estatus_anticipo(Request $request)
    {
        AnticipoExtraordinario::find($request->id)->update(['aplicado'=>$request->nuevo_estatus]);
        return(back()->withStatus('Anticipo de '.$request->nombre.' por $'.number_format($request->monto,0).' actualizado con exito!'));
    }
    public function facturar_anticipo_form(Request $request)
    {
        $id_anticipo=$request->id;
        AnticipoExtraordinario::with('periodo')->find($id_anticipo);
        return(view('facturar_anticipo',['anticipo'=>AnticipoExtraordinario::find($id_anticipo)]));
    }
    public function facturar_anticipo_save(Request $request)
    {
        //return($request);
        $request->validate([
            'pdf_file' => 'required|mimes:pdf',
            'xml_file' => 'required|mimes:xml',
           ]);
        //return($request->all());
        $upload_path = public_path('facturas');
        $upload_path="/home/icubecom/ttds.icube.com.mx/facturas";

        $file_name = $request->file("pdf_file")->getClientOriginalName();
        $generated_new_name_pdf = 'anticipo'.$request->id.'_'.time() . '.' . $request->file("pdf_file")->getClientOriginalExtension();
        $request->file("pdf_file")->move($upload_path, $generated_new_name_pdf);

        $file_name = $request->file("xml_file")->getClientOriginalName();
        $generated_new_name_xml ='anticipo'.$request->id.'_'.time() . '.' . $request->file("xml_file")->getClientOriginalExtension();
        $request->file("xml_file")->move($upload_path, $generated_new_name_xml);

        AnticipoExtraordinario::where('id',$request->id)
                            ->update([
                                'pdf'=>$generated_new_name_pdf,
                                'xml'=>$generated_new_name_xml,
                                'carga_facturas'=>now()->toDateTimeString(),
                            ]);

        return(back()->withStatus('Datos de facturacion OK'));
    }
}
