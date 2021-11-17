<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PagosDistribuidor;
use App\Models\Distribuidor;
use App\Models\Calculo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShowPagosController extends Controller
{
    public function pagos(Request $request)
    {
        $filtro=false;
        $distribuidores=Distribuidor::select('user_id','nombre')
                                ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                                ->orderBy('nombre','asc')->get();
        $calculos=Calculo::select('id','descripcion')->orderBy('id','desc')->get();
        $solo_distribuidores=User::select('id')->where('perfil','distribuidor')->get();
        $solo_distribuidores=$solo_distribuidores->pluck('id');
        //return($solo_distribuidores);
        $aplicado="NULO";
        $distribuidor="NULO";
        $calculo_id="NULO";
        $nuevas_facturas=false;
        $nuevos_pagos=false;
        if(isset($_GET['f']))
        {
            $filtro=true;
            if(isset($_GET['aplicado'])){$aplicado=$_GET["aplicado"]==""?"NULO":$_GET["aplicado"];}            
            if(isset($_GET['distribuidor'])){$distribuidor=$_GET["distribuidor"]==""?"NULO":$_GET["distribuidor"];}
            if(isset($_GET['calculo'])){$calculo_id=$_GET["calculo"]==""?"NULO":$_GET["calculo"];}
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
        $pagos=PagosDistribuidor::with('user','user.detalles','calculo')
                    ->select('id','user_id','calculo_id','total_pago','xml','pdf','aplicado','carga_facturas','version')
                    ->orderBy('created_at','desc')
                    ->when($filtro && $aplicado!="NULO",function ($query) use ($aplicado){$query->where('aplicado',$aplicado);})
                    ->when($filtro && $distribuidor!="NULO",function ($query) use ($distribuidor){$query->where('user_id',$distribuidor);})
                    ->when($filtro && $calculo_id!="NULO",function ($query) use ($calculo_id){$query->where('calculo_id',$calculo_id);})
                    ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                    ->when($nuevos_pagos,function($query){$query->where('created_at','>=',Auth::user()->anterior_login);})
                    ->when($nuevas_facturas,function($query){$query->where('carga_facturas','>=',Auth::user()->anterior_login);})
                    ->whereIn('user_id',$solo_distribuidores)
                    ->paginate(10);
        $pagos->appends($request->all());
        return(view('pagos',['pagos'=>$pagos,
                             'distribuidores'=>$distribuidores,
                             'calculos'=>$calculos,
                             'aplicado'=>$aplicado=='NULO'?'':$aplicado,
                             'distribuidor_id'=>$distribuidor=='NULO'?'0':$distribuidor,
                             'calculo_id'=>$calculo_id=='NULO'?'0':$calculo_id,
                            ]));
    }
    public function cambiar_estatus_pago(Request $request)
    {
        PagosDistribuidor::find($request->id)->update(['aplicado'=>$request->nuevo_estatus]);
        return(back()->withStatus('Pago de '.$request->nombre.' por $'.number_format($request->monto,0).' actualizado con exito!'));
    }
}
