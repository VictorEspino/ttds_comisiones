<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\ComisionVenta;
use App\Models\ComisionResidual;
use App\Models\Reclamo;
use App\Models\CallidusVenta;
use App\Models\CallidusResidual;
use App\Models\Calculo;
use App\Models\Distribuidor;
use App\Models\Mediciones;
use App\Models\PagosDistribuidor;
use App\Models\AnticipoNoPago;
use App\Models\AnticipoExtraordinario;
use App\Models\ChargeBackDistribuidor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalculoComisiones extends Controller
{
    public function terminar_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $calculo=Calculo::find($calculo_id);
        $calculo->terminado=1;
        $calculo->save();
        return(back()->withStatus('Calculo de comisiones ('.$calculo->descripcion.') terminado'));
    }
    public function ejecutar_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $version=$request->version;
        $calculo=Calculo::find($calculo_id);
        $this->acreditar_ventas($calculo,$version);
        $this->ejecutar_mediciones($calculo,$version);
        $this->comision_ventas($calculo,$version);
        $this->charge_back($calculo,$version);
        $this->residual($calculo,$version);
        $this->pagos($calculo,$version);
        if($version=="1")
        {
            $calculo->adelanto=true;
        }
        else
        {
            $calculo->cierre=true;
        }
        $calculo->save();
        return(back()->withStatus('Calculo de comisiones ('.$calculo->descripcion.') ejecutado correctamente'));
    }
    private function ejecutar_mediciones($calculo,$version)
    {
        DB::delete('delete from mediciones where calculo_id='.$calculo->id." and version=".$version);

        $sql_nuevas="(select user_id, count(*) as nuevas, sum(renta) as renta_nuevas,0 as adiciones, 0 as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'NUEVA' and validado=1 group by `user_id`)";
        $sql_adiciones="(select user_id, 0 as nuevas, 0 as renta_nuevas,count(*) as adiciones, sum(renta) as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'ADICION' and validado=1  group by `user_id`)";
        $sql_renovaciones="(select user_id, 0 as nuevas, 0 as renta_nuevas,0 as adiciones, 0 as renta_adiciones,count(*) as renovaciones, sum(renta) as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'RENOVACION' and validado=1  group by `user_id`)";
        $sql_medicion="select user_id,sum(nuevas) as nuevas,sum(renta_nuevas) as renta_nuevas,sum(adiciones) as adiciones,sum(renta_adiciones) as renta_adiciones,sum(renovaciones) as renovaciones,sum(renta_renovaciones) as renta_renovaciones";
        $sql_medicion=$sql_medicion." from (";
        $sql_medicion=$sql_medicion."".$sql_nuevas." UNION ".$sql_adiciones." UNION ".$sql_renovaciones;
        $sql_medicion=$sql_medicion." ) as a group by a.user_id";
        $mediciones=DB::select(DB::raw(
            $sql_medicion
           ));
        $mediciones=collect($mediciones);
        foreach($mediciones as $medicion)
        {
            $registro=new Mediciones;
            
            $registro->calculo_id=$calculo->id;
            $registro->user_id=$medicion->user_id;
            $registro->version=$version;
            $registro->nuevas=$medicion->nuevas;
            $registro->renta_nuevas=$medicion->renta_nuevas;
            $registro->adiciones=$medicion->adiciones;
            $registro->renta_adiciones=$medicion->renta_adiciones;
            $registro->renovaciones=$medicion->renovaciones;
            $registro->renta_renovaciones=$medicion->renta_renovaciones;
            $registro->porcentaje_nuevas=($medicion->renovaciones)>0?100*($medicion->nuevas+$medicion->adiciones)/($medicion->renovaciones):0;
            $registro->save();
        }
        return($mediciones);
    }
    private function acreditar_ventas($calculo,$version)
    {
        $ventas=Venta::whereBetween('fecha',[$calculo->periodo->fecha_inicio,$calculo->periodo->fecha_fin])
                        ->where('validado','1')
                        ->get();

        DB::delete('delete from comision_ventas where calculo_id='.$calculo->id.' and version='.$version);
        Reclamo::where('calculo_id',$calculo->id)->where('tipo','Faltante')->delete();

        foreach($ventas as $venta)
        {
            $registro=new ComisionVenta;
            $registro->venta_id=$venta->id;
            $registro->calculo_id=$calculo->id;
            $registro->version=$version;
            $registro->upfront=0;
            $registro->bono=0;
            $validacion=$this->validar_venta($venta,$calculo->id);
            $a_pagar=$validacion['encontrada'];
            $consistencia=$validacion['consistente'];

            $estatus_inicial="NO PAGO";
            $estatus_final="VENTA NO PAGADA";
            $calculo_proceso=0;

            if($a_pagar)
            {
                $estatus_inicial="PAGO";
                $estatus_final=$consistencia?"VENTA PAGADA":"PAGADA CON INCOSISTENCIA";
                $calculo_proceso=$calculo->id;
            }

            $registro->callidus_venta_id=$validacion['callidus_id'];
            $registro->consistente=$validacion['consistente'];
            $registro->estatus_final=$estatus_final;
            $registro->calculo_id_proceso=$calculo_proceso;
            $registro->estatus_inicial=$estatus_inicial;
            $registro->save();
            if(!$a_pagar) //NO SE ENCONTRO EN CALLIDUS
            {
                $reclamo=new Reclamo;
                $reclamo->venta_id=$venta->id;
                $reclamo->calculo_id=$calculo->id;
                $reclamo->monto=0;
                $reclamo->razon="Venta no pagada";
                $reclamo->tipo="Faltante";
                $reclamo->save();            
            }
        }
    }
    private function validar_venta($venta,$calculo_id)
    {
        $respuesta=array(
            'encontrada'=>false,
            'consistente'=>false,
            'callidus_id'=>0,
        );
        //$registro=CallidusVenta::where('dn',$venta->dn)->where('cuenta','like',$venta->cuenta.'%')
        //                        ->get()
        //                        ->first();

        $registro=CallidusVenta::where('contrato',$venta->folio.'_DL')
                                ->where('calculo_id',$calculo_id)
                                ->get()
                                ->first();
        
        if(is_null($registro))
        {
            $registro=CallidusVenta::where('dn',$venta->dn)->where('cuenta','like',$venta->cuenta.'%')
                                ->where('calculo_id',$calculo_id)
                                ->get()
                                ->first();
        }

        if(!is_null($registro))
        {
            $respuesta['encontrada']=true;
            $respuesta['consistente']=true;
            $respuesta['callidus_id']=$registro->id;
            $error_renta=$venta->renta-$registro->renta;
            if($error_renta<(-1) || $error_renta>(1)){$respuesta['consistente']=false;}
            if($venta->plazo!=$registro->plazo){$respuesta['consistente']=false;}
            if($venta->descuento_multirenta!=$registro->descuento_multirenta){$respuesta['consistente']=false;}
            if($venta->afectacion_comision!=$registro->afectacion_comision){$respuesta['consistente']=false;}
        }
        return($respuesta);

    }
    private function comision_ventas($calculo,$version)
    {
        $comisionables=ComisionVenta::where('calculo_id',$calculo->id)->where('version',$version)->get();
        foreach($comisionables as $credito)
        {
            $venta=Venta::find($credito->venta_id);
            $callidus=CallidusVenta::find($credito->callidus_venta_id);
            $distribuidor=Distribuidor::where('user_id',$venta->user_id)->get()->first();
            $medicion=Mediciones::where('calculo_id',$calculo->id)
                                ->where('user_id',$venta->user_id)
                                ->where('version',$version)
                                ->get()
                                ->first();
                                
            $tipo=$venta->tipo;

            if($credito->estatus_inicial=="PAGO")
            {
            //LOS PARAMETROS DE CALCULO SON DE CALLIDUS
                $plazo=$callidus->plazo;
                $renta=$callidus->renta;
                $dmr=$callidus->descuento_multirenta;
                $afectacion=$callidus->afectacion_comision;
            ///////////////////////
            }
            else
            {
                $plazo=$venta->plazo;
                $renta=$venta->renta;
                $dmr=$venta->descuento_multirenta;
                $afectacion=$venta->afectacion_comision;
            }

            $renta_neta=($renta/1.16/1.03)*(1-($dmr/100))*(1-($afectacion/100));
            $comision=0;
            $bono=0;
            if($tipo=='NUEVA' || $tipo=='ADICION')
            {
                if($plazo=='12' || $plazo=='6' || $plazo=='0'){ $comision=$renta_neta*$distribuidor->a_12;}
                if($plazo=='18'){ $comision=$renta_neta*$distribuidor->a_18;}
                if($plazo=='24' || $plazo='36'){ $comision=$renta_neta*$distribuidor->a_24;}
                if($medicion->porcentaje_nuevas>=30 && $renta_neta>=200){$bono=100;}
            }
            if($tipo=='RENOVACION')
            {
                if($plazo=='12' || $plazo=='6' || $plazo=='0'){ $comision=$renta_neta*$distribuidor->r_12;}
                if($plazo=='18'){ $comision=$renta_neta*$distribuidor->r_18;}
                if($plazo=='24' || $plazo='36'){ $comision=$renta_neta*$distribuidor->r_24;}
            }
            $credito->upfront=$comision;
            $credito->bono=$bono;
            $credito->save();
        }
    }
    public function charge_back($calculo,$version)
    {
        if($version=="1") {return;}
        
        ChargeBackDistribuidor::where('calculo_id',$calculo->id)->delete();

        $cancelaciones=CallidusVenta::select('id','contrato','tipo_baja','fecha_baja')
                    ->where('tipo','DESACTIVACION_DESACTIVACIONES')
                    ->where('calculo_id',$calculo->id)
                    ->get();
        foreach($cancelaciones as $cancelacion)
        {
            $registro_original=CallidusVenta::select('id')
                        ->where('contrato',$cancelacion->contrato)
                        ->where('tipo','!=','DESACTIVACION_DESACTIVACIONES')
                        ->get()
                        ->first();
            $venta_previa=false;
            if(!is_null($registro_original))
            {
                $venta_pagada=ComisionVenta::with('venta')
                            ->select('id','upfront','bono','venta_id')
                            ->where('callidus_venta_id',$registro_original->id)
                            ->where('version',2)
                            ->get()
                            ->first();
                $venta_previa=!is_null($venta_pagada)?true:false;
            }
            $venta_pagada_id=0;
            $venta_pagada_cb=0;
            $venta_pagada_eq=0;
            $estatus="NO APLICADO";

            if($venta_previa)
            {
                $venta_pagada_id=$venta_pagada->id;
                $venta_pagada_cb=$venta_pagada->upfront+$venta_pagada->bono;
                $venta_pagada_eq=$venta_pagada->venta->propiedad=='NUEVO'&&$cancelacion->tipo_baja=='INVOLUNTARIO'?2000:0;
                $estatus="APLICADO";
            }
            $charge_back=new ChargeBackDistribuidor;
            $charge_back->callidus_venta_id=$cancelacion->id;
            $charge_back->comision_venta_id=$venta_pagada_id;
            $charge_back->calculo_id=$calculo->id;
            $charge_back->charge_back=$venta_pagada_cb;
            $charge_back->cargo_equipo=$venta_pagada_eq;
            $charge_back->estatus=$estatus;
            $charge_back->save();

        }
        return;
    }
    public function residual($calculo,$version)
    {
        if($version=="1") {return;}

        ComisionResidual::where('calculo_id',$calculo->id)->delete();

        $calculo_anterior=$calculo->id-1;
        $calculo_anterior_2=$calculo->id-2;
        $sql_mes_anterior="
            select a.user_id as user_id_ant,a.venta_id as venta_id_ant,callidus_res.contrato_ant from comision_residuals as a left join 
            (select id,contrato as contrato_ant from callidus_residuals where calculo_id=".$calculo_anterior.") as callidus_res on callidus_res.id=a.callidus_residual_id
            where a.calculo_id=".$calculo_anterior."
            ";
        $pago_anterior=DB::select(DB::raw(
            $sql_mes_anterior
           ));
        $pago_anterior=collect($pago_anterior);
        $periodo_anterior=$pago_anterior->pluck('user_id_ant','contrato_ant');
        $venta_anterior=$pago_anterior->pluck('venta_id_ant','contrato_ant');
    
        $residuales_actuales=CallidusResidual::select('id','contrato','renta','estatus')->where('calculo_id',$calculo->id)->get();
        
        foreach($residuales_actuales as $actual)
        {
            $user_id_anterior=0;
            try{
                $user_id_anterior=$periodo_anterior[$actual->contrato];
            }
            catch(\Exception $e)
            {
                $user_id_anterior=-1;
            }
            if($user_id_anterior==-1) //SI NO LO ENCUENTRA VA A BUSCAR A LA BASE DE VENTAS DE 2 PERIODOS ANTERIORES
                {
                    $contrato_consulta=explode("_",$actual->contrato);

                    $sql_venta="
                                select ventas.id,ventas.user_id,ventas.contrato,comision_ventas.calculo_id 
                                from ventas,comision_ventas 
                                where comision_ventas.version=2 and 
                                    comision_ventas.venta_id=ventas.id and 
                                    comision_ventas.calculo_id in (".$calculo_anterior.",".$calculo_anterior_2.") 
                                    and ventas.contrato='".$contrato_consulta[0]."'
                            ";
                    $venta=DB::select(DB::raw(
                        $sql_venta
                        ));
                    $venta=collect($venta);
                    if(!$venta->isEmpty()) //ENCONTRADO EN REGISTRO DE VENTAS
                    { 
                        $venta=$venta->first();
                        $registro=new ComisionResidual;
                        $registro->user_id=$venta->user_id;
                        $registro->callidus_residual_id=$actual->id;
                        $registro->venta_id=$venta->id;
                        $registro->calculo_id=$calculo->id;
                        $registro->comision=$actual->estatus=="ACTIVO"?$actual->renta*0.02:0;
                        $registro->save();
                        
                    }
                }
            else
                {
                    $registro=new ComisionResidual;
                    $registro->user_id=$user_id_anterior;
                    $registro->callidus_residual_id=$actual->id;
                    $registro->venta_id=$venta_anterior[$actual->contrato];
                    $registro->calculo_id=$calculo->id;
                    $registro->comision=$actual->estatus=="ACTIVO"?$actual->renta*0.02:0;
                    $registro->save();
                }
        }
        return;
    }
    public function pagos($calculo,$version)
    {
        DB::delete('delete from pagos_distribuidors where calculo_id='.$calculo->id.' and version='.$version);
        
        $sql_pagos="    
        select user_id,sum(nuevas) as nuevas,sum(n_rentas) as n_rentas, sum(n_comision) as n_comision,sum(n_bono) as n_bono,sum(adiciones) as adiciones,sum(a_rentas) as a_rentas, sum(a_comision) as a_comision,sum(a_bono) as a_bono,sum(renovaciones) as renovaciones,sum(r_rentas) as r_rentas, sum(r_comision) as r_comision,sum(r_bono) as r_bono,sum(n_no_pago) as n_no_pago,sum(n_rentas_no_pago) as n_rentas_no_pago, sum(n_comision_no_pago) as n_comision_no_pago,sum(n_bono_no_pago) as n_bono_no_pago,sum(a_no_pago) as a_no_pago,sum(a_rentas_no_pago) as a_rentas_no_pago, sum(a_comision_no_pago) as a_comision_no_pago,sum(a_bono_no_pago) as a_bono_no_pago,sum(r_no_pago) as r_no_pago,sum(r_rentas_no_pago) as r_rentas_no_pago, sum(r_comision_no_pago) as r_comision_no_pago,sum(r_bono_no_pago) as r_bono_no_pago FROM (
            SELECT b.user_id,count(*) nuevas,sum(b.renta) as n_rentas,sum(a.upfront) as n_comision,sum(a.bono) as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='NUEVA' and a.estatus_inicial='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,count(*) as adiciones,sum(b.renta) as a_rentas,sum(a.upfront) as a_comision, sum(a.bono) as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='ADICION' and a.estatus_inicial='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,sum(b.renta) as r_rentas,sum(a.upfront) as r_comision, sum(a.bono) as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='RENOVACION' and a.estatus_inicial='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,count(*) as n_no_pago,sum(b.renta) as n_rentas_no_pago,sum(a.upfront) as n_comision_no_pago,sum(a.bono) as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='NUEVA' and a.estatus_inicial='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,count(*) as a_no_pago,sum(b.renta) as a_rentas_no_pago,sum(a.upfront) as a_comision_no_pago,sum(a.bono) as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='ADICION' and a.estatus_inicial='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,count(*) as r_no_pago,sum(b.renta) as r_rentas_no_pago,sum(a.upfront) as r_comision_no_pago,sum(a.bono) as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='RENOVACION' and a.estatus_inicial='NO PAGO' group by b.user_id
                ) as a group by a.user_id
        ";
        $pagos=DB::select(DB::raw(
            $sql_pagos
           ));
        $pagos=collect($pagos);

        $factor=$version=="1"?0.5:1;   

        foreach($pagos as $pago)
        {
            $registro=new PagosDistribuidor;
            $registro->calculo_id=$calculo->id;
            $registro->user_id=$pago->user_id;
            $registro->version=$version;
            $registro->nuevas=$pago->nuevas;
            $registro->renta_nuevas=$pago->n_rentas;
            $registro->comision_nuevas=$pago->n_comision*$factor;
            $registro->bono_nuevas=$pago->n_bono*$factor;
            $registro->adiciones=$pago->adiciones;
            $registro->renta_adiciones=$pago->a_rentas;
            $registro->comision_adiciones=$pago->a_comision*$factor;
            $registro->bono_adiciones=$pago->a_bono*$factor;
            $registro->renovaciones=$pago->renovaciones;
            $registro->renta_renovaciones=$pago->r_rentas;
            $registro->comision_renovaciones=$pago->r_comision*$factor;
            $registro->bono_renovaciones=$pago->r_bono*$factor;


            $registro->nuevas_no_pago=$pago->n_no_pago;
            $registro->nuevas_renta_no_pago=$pago->n_rentas_no_pago;
            $registro->nuevas_comision_no_pago=$pago->n_comision_no_pago;
            $registro->nuevas_bono_no_pago=$pago->n_bono_no_pago;

            $registro->adiciones_no_pago=$pago->a_no_pago;
            $registro->adiciones_renta_no_pago=$pago->a_rentas_no_pago;
            $registro->adiciones_comision_no_pago=$pago->a_comision_no_pago;
            $registro->adiciones_bono_no_pago=$pago->a_bono_no_pago;

            $registro->renovaciones_no_pago=$pago->r_no_pago;
            $registro->renovaciones_renta_no_pago=$pago->r_rentas_no_pago;
            $registro->renovaciones_comision_no_pago=$pago->r_comision_no_pago;
            $registro->renovaciones_bono_no_pago=$pago->r_bono_no_pago;

            $anticipo_ordinario=0;
            $anticipo_no_pago=0;
            $residual=0; 
            $charge_back=0; 
            $retroactivos_reproceso=0;

            if($version=="2")
            {
                $anticipo=AnticipoNoPago::select(DB::raw('sum(anticipo) as anticipo'))
                                    ->where('calculo_id',$calculo->id)
                                    ->where('user_id',$pago->user_id)
                                    ->get()
                                    ->first();

                $anticipo_no_pago=is_null($anticipo->anticipo)?0:$anticipo->anticipo;

                $anticipo_ordinario=PagosDistribuidor::where('calculo_id',$calculo->id)
                                                    ->where('user_id',$pago->user_id)
                                                    ->where('version',1)
                                                    ->get()
                                                    ->first()
                                                    ->total_pago;

                $charge_back=0; //solo se calcula en el cierre
                $descuentos=ChargeBackDistribuidor::select(DB::raw('sum(charge_back_distribuidors.charge_back+charge_back_distribuidors.cargo_equipo) as charge_back'))
                                                    ->join('comision_ventas','charge_back_distribuidors.comision_venta_id','=','comision_ventas.id')
                                                    ->join('ventas','comision_ventas.venta_id','=','ventas.id')
                                                    ->where('charge_back_distribuidors.calculo_id',$calculo->id)->where('charge_back_distribuidors.comision_venta_id','!=',0)
                                                    ->where('ventas.user_id',$pago->user_id)
                                                    ->get()
                                                    ->first();
                if(!is_null($descuentos->charge_back)){$charge_back=$descuentos->charge_back;}

                $residual=0; //solo se calcula en el cierre

                $residuales=ComisionResidual::select(DB::raw('sum(comision) as comision'))
                                            ->where('calculo_id',$calculo->id)
                                            ->where('user_id',$pago->user_id)
                                            ->get()
                                            ->first();

                if(!is_null($residuales->comision)){$residual=$residuales->comision;}
                
                $retroactivos_reproceso=0; //solo se calcula en el cierre
            }

            $anticipos_extraordinarios=$this->aplicar_anticipos($calculo->id,$pago->user_id,$calculo->periodo_id,$version);
            $registro->anticipos_extraordinarios=$anticipos_extraordinarios*$factor;

            $registro->anticipo_ordinario=$anticipo_ordinario; //Solo se calcula en el cierre
            $registro->anticipo_no_pago=$anticipo_no_pago; //Solo se calcula en el cierre
            $registro->residual=$residual; //Solo se calcula en el cierre
            $registro->charge_back=$charge_back;//Solo se calcula en el cierre
            $registro->retroactivos_reproceso=$retroactivos_reproceso;//Solo se calcula en el cierre
            
            

            $total_comisiones=($pago->n_comision+$pago->n_bono+$pago->a_comision+$pago->a_bono+$pago->r_comision+$pago->r_bono)*$factor;

            $registro->total_pago=$total_comisiones+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios-$registro->anticipo_ordinario;

            $registro->save();


        }
        return;
    }
    public function aplicar_anticipos($calculo_id,$user_id,$periodo_id,$version)
    {
        AnticipoExtraordinario::where('calculo_id_aplicado',$calculo_id)
                                        ->where('user_id',$user_id)
                                        ->update([
                                                    'calculo_id_aplicado'=>0,
                                        ]);

        //OBTIENE ANTICIPOS RESETEADOS y DE PERIODOS PREVIOS 
        $anticipos=AnticipoExtraordinario::where('periodo_id','<=',$periodo_id)
                                        ->where('user_id',$user_id)
                                        ->where('calculo_id_aplicado',0)
                                        ->get();
        $aplicados=0;
        foreach($anticipos as $por_aplicar)
        {
            $aplicados=$aplicados+$por_aplicar->anticipo;
            $por_aplicar->calculo_id_aplicado=$calculo_id;
            if($version=="1")
            {
                $por_aplicar->en_adelanto=true;
            }
            $por_aplicar->save();
        }
        return($aplicados);
    }

}
