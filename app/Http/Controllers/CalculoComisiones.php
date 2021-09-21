<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\ComisionVenta;
use App\Models\CallidusVenta;
use App\Models\Calculo;
use App\Models\Distribuidor;
use App\Models\Mediciones;
use App\Models\PagosDistribuidor;
use App\Models\AnticipoNoPago;
use App\Models\AnticipoExtraordinario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalculoComisiones extends Controller
{
    public function ejecutar_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $calculo=Calculo::find($calculo_id);
        $this->ejecutar_mediciones($calculo);
        $this->acreditar_ventas($calculo);
        $this->comision_ventas($calculo);
        $this->pagos($calculo);
        return(back()->withStatus('Calculo de comisiones ('.$calculo->descripcion.') ejecutado correctamente'));
    }
    private function ejecutar_mediciones($calculo)
    {
        DB::delete('delete from mediciones where calculo_id='.$calculo->id);

        $sql_nuevas="(select user_id, count(*) as nuevas, sum(renta) as renta_nuevas,0 as adiciones, 0 as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->fecha_inicio."' and '".$calculo->fecha_fin."' and `tipo` = 'NUEVA' and validado=1 group by `user_id`)";
        $sql_adiciones="(select user_id, 0 as nuevas, 0 as renta_nuevas,count(*) as adiciones, sum(renta) as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->fecha_inicio."' and '".$calculo->fecha_fin."' and `tipo` = 'ADICION' and validado=1  group by `user_id`)";
        $sql_renovaciones="(select user_id, 0 as nuevas, 0 as renta_nuevas,0 as adiciones, 0 as renta_adiciones,count(*) as renovaciones, sum(renta) as renta_renovaciones from `ventas` where `fecha` between '".$calculo->fecha_inicio."' and '".$calculo->fecha_fin."' and `tipo` = 'RENOVACION' and validado=1  group by `user_id`)";
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
            $registro->nuevas=$medicion->nuevas;
            $registro->renta_nuevas=$medicion->renta_nuevas;
            $registro->adiciones=$medicion->adiciones;
            $registro->renta_adiciones=$medicion->renta_adiciones;
            $registro->renovaciones=$medicion->renovaciones;
            $registro->renta_renovaciones=$medicion->renta_renovaciones;
            $registro->porcentaje_nuevas=100*($medicion->nuevas+$medicion->adiciones)/($medicion->renovaciones);
            $registro->save();
        }
        return($mediciones);
    }
    private function acreditar_ventas($calculo)
    {
        $ventas=Venta::whereBetween('fecha',[$calculo->fecha_inicio,$calculo->fecha_fin])
                        ->where('validado','1')
                        ->get();

        DB::delete('delete from comision_ventas where calculo_id='.$calculo->id);

        foreach($ventas as $venta)
        {
            $registro=new ComisionVenta;
            $registro->venta_id=$venta->id;
            $registro->calculo_id=$calculo->id;
            $registro->upfront=0;
            $registro->bono=0;
            $validacion=$this->validar_venta($venta);
            $a_pagar=$validacion['encontrada'];
            $estatus="NO PAGO";
            $calculo_proceso=0;
            if($a_pagar)
            {
                $estatus="PAGO";
                $calculo_proceso=$calculo->id;
                $registro->renta=$validacion['renta'];
                $registro->plazo=$validacion['plazo'];
                $registro->tipo=$validacion['tipo'];
                $registro->descuento_multirenta=$validacion['descuento_multirenta']*100;
                $registro->afectacion_comision=$validacion['afectacion_comision']*100;
            }
            $registro->calculo_id_proceso=$calculo_proceso;
            $registro->estatus=$estatus;
            $registro->save();
        }
    }
    private function validar_venta($venta)
    {
        $respuesta=array(
            'encontrada'=>false,
            'renta'=>0,
            'plazo'=>0,
            'tipo'=>'',
            'descuento_multirenta'=>0,
            'afectacion_comision'=>0,
        );
        //$registro=CallidusVenta::where('dn',$venta->dn)->where('cuenta','like',$venta->cuenta.'%')
        //                        ->get()
        //                        ->first();

        $registro=CallidusVenta::where(function($query) use ($venta){$query->where('dn',$venta->dn)->where('cuenta','like',$venta->cuenta.'%');})
                                ->orWhere('contrato',$venta->folio.'_DL')
                                ->get()
                                ->first();
        if(!is_null($registro))
        {
            $respuesta['encontrada']=true;
            $respuesta['renta']=$registro->renta;
            $respuesta['plazo']=$registro->plazo;
            $respuesta['tipo']=$registro->tipo;
            $respuesta['descuento_multirenta']=$registro->descuento_multirenta;
            $respuesta['afectacion_comision']=$registro->afectacion_comision;
        }
        return($respuesta);

    }
    private function comision_ventas($calculo)
    {
        $comisionables=ComisionVenta::where('calculo_id',$calculo->id)->get();
        foreach($comisionables as $credito)
        {
            $venta=Venta::find($credito->venta_id);
            $distribuidor=Distribuidor::where('user_id',$venta->user_id)->get()->first();
            $medicion=Mediciones::where('calculo_id',$calculo->id)
                                ->where('user_id',$venta->user_id)
                                ->get()
                                ->first();
                                
            $tipo=$venta->tipo;

            if($credito->estatus=="PAGO")
            {
            //LOS PARAMETROS DE CALCULO SON DE CALLIDUS
                $plazo=$credito->plazo;
                $renta=$credito->renta;
                $dmr=$credito->descuento_multirenta;
                $afectacion=$credito->afectacion_comision;
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
                if($plazo=='12'){ $comision=$renta_neta*$distribuidor->a_12;}
                if($plazo=='18'){ $comision=$renta_neta*$distribuidor->a_18;}
                if($plazo=='24'){ $comision=$renta_neta*$distribuidor->a_24;}
                if($medicion->porcentaje_nuevas>=30 && $renta_neta>=200){$bono=100;}
            }
            if($tipo=='RENOVACION')
            {
                if($plazo=='12'){ $comision=$renta_neta*$distribuidor->r_12;}
                if($plazo=='18'){ $comision=$renta_neta*$distribuidor->r_18;}
                if($plazo=='24'){ $comision=$renta_neta*$distribuidor->r_24;}
            }
            $credito->upfront=$comision;
            $credito->bono=$bono;
            $credito->save();
        }
    }
    public function pagos($calculo)
    {
        DB::delete('delete from pagos_distribuidors where calculo_id='.$calculo->id);
        
        $sql_pagos="    
        select user_id,sum(nuevas) as nuevas,sum(n_rentas) as n_rentas, sum(n_comision) as n_comision,sum(n_bono) as n_bono,sum(adiciones) as adiciones,sum(a_rentas) as a_rentas, sum(a_comision) as a_comision,sum(a_bono) as a_bono,sum(renovaciones) as renovaciones,sum(r_rentas) as r_rentas, sum(r_comision) as r_comision,sum(r_bono) as r_bono,sum(n_no_pago) as n_no_pago,sum(n_rentas_no_pago) as n_rentas_no_pago, sum(n_comision_no_pago) as n_comision_no_pago,sum(n_bono_no_pago) as n_bono_no_pago,sum(a_no_pago) as a_no_pago,sum(a_rentas_no_pago) as a_rentas_no_pago, sum(a_comision_no_pago) as a_comision_no_pago,sum(a_bono_no_pago) as a_bono_no_pago,sum(r_no_pago) as r_no_pago,sum(r_rentas_no_pago) as r_rentas_no_pago, sum(r_comision_no_pago) as r_comision_no_pago,sum(r_bono_no_pago) as r_bono_no_pago FROM (
            SELECT b.user_id,count(*) nuevas,sum(b.renta) as n_rentas,sum(a.upfront) as n_comision,sum(a.bono) as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and b.tipo='NUEVA' and a.estatus='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,count(*) as adiciones,sum(b.renta) as a_rentas,sum(a.upfront) as a_comision, sum(a.bono) as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and b.tipo='ADICION' and a.estatus='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,sum(b.renta) as r_rentas,sum(a.upfront) as r_comision, sum(a.bono) as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and b.tipo='RENOVACION' and a.estatus='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,count(*) as n_no_pago,sum(b.renta) as n_rentas_no_pago,sum(a.upfront) as n_comision_no_pago,sum(a.bono) as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and b.tipo='NUEVA' and a.estatus='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,count(*) as a_no_pago,sum(b.renta) as a_rentas_no_pago,sum(a.upfront) as a_comision_no_pago,sum(a.bono) as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and b.tipo='ADICION' and a.estatus='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,count(*) as r_no_pago,sum(b.renta) as r_rentas_no_pago,sum(a.upfront) as r_comision_no_pago,sum(a.bono) as r_bono_no_pago FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and b.tipo='RENOVACION' and a.estatus='NO PAGO' group by b.user_id
                ) as a group by a.user_id
        ";

        $pagos=DB::select(DB::raw(
            $sql_pagos
           ));
        $pagos=collect($pagos);
        foreach($pagos as $pago)
        {
            $registro=new PagosDistribuidor;
            $registro->calculo_id=$calculo->id;
            $registro->user_id=$pago->user_id;
            $registro->nuevas=$pago->nuevas;
            $registro->renta_nuevas=$pago->n_rentas;
            $registro->comision_nuevas=$pago->n_comision;
            $registro->bono_nuevas=$pago->n_bono;
            $registro->adiciones=$pago->adiciones;
            $registro->renta_adiciones=$pago->a_rentas;
            $registro->comision_adiciones=$pago->a_comision;
            $registro->bono_adiciones=$pago->a_bono;
            $registro->renovaciones=$pago->renovaciones;
            $registro->renta_renovaciones=$pago->r_rentas;
            $registro->comision_renovaciones=$pago->r_comision;
            $registro->bono_renovaciones=$pago->r_bono;


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

            $anticipo_no_pago=0;

            $anticipo=AnticipoNoPago::select(DB::raw('sum(anticipo) as anticipo'))
                                ->where('calculo_id',$calculo->id)
                                ->where('user_id',$pago->user_id)
                                ->get()
                                ->first();

            $anticipo_no_pago=is_null($anticipo->anticipo)?0:$anticipo->anticipo;

            $residual=0; //PENDIENTE DE CALCULO
            $charge_back=0; //PENDIENTE DE CALCULO
            $retroactivos_reproceso=0; //PENDIENTE DE CALCULO

            $anticipos_extraordinarios=0;

            //RESETEA LOS ANTICIPOS
            AnticipoExtraordinario::where('aplicado_calculo_id',$calculo->id)
                                        ->where('user_id',$pago->user_id)
                                        ->update([
                                                    'aplicado'=>false,
                                                    'aplicado_calculo_id'=>0,
                                        ]);
            //OBTIENE ANTICIPOS RESETEADOS y DE PERIODOS PREVIOS 
            $anticipos=AnticipoExtraordinario::where('fecha_relacionada','<=',$calculo->fecha_fin)
                                        ->where('user_id',$pago->user_id)
                                        ->where('aplicado',false)
                                        ->get();
                                        
            foreach($anticipos as $por_aplicar)
            {
                $anticipos_extraordinarios=$anticipos_extraordinarios+$por_aplicar->anticipo;
                $por_aplicar->aplicado_calculo_id=$calculo->id;
                $por_aplicar->aplicado=true;
                $por_aplicar->save();
            }

            $registro->anticipo_no_pago=$anticipo_no_pago;
            $registro->residual=$residual;
            $registro->charge_back=$charge_back;
            $registro->anticipos_extraordinarios=$anticipos_extraordinarios;
            $registro->retroactivos_reproceso=$retroactivos_reproceso;

            $total_comisiones=$pago->n_comision+$pago->n_bono+$pago->a_comision+$pago->a_bono+$pago->r_comision+$pago->r_bono;

            $registro->total_pago=$total_comisiones+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios;

            $registro->save();


        }
        return;
    }
}
