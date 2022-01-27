<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\ComisionVenta;
use App\Models\ComisionAddon;
use App\Models\ComisionResidual;
use App\Models\AutorizacionEspecial;
use App\Models\Reclamo;
use App\Models\CallidusVenta;
use App\Models\CallidusResidual;
use App\Models\Calculo;
use App\Models\Distribuidor;
use App\Models\Empleado;
use App\Models\Mediciones;
use App\Models\PagosDistribuidor;
use App\Models\AnticipoNoPago;
use App\Models\AnticipoExtraordinario;
use App\Models\ChargeBackDistribuidor;
use App\Models\AlertaCobranza;
use App\Models\AlertaConciliacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;

class CalculoComisiones extends Controller
{
    public function terminar_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $calculo=Calculo::find($calculo_id);
        PagosDistribuidor::where('version',2)->where('calculo_id',$calculo_id)->update(['activo'=>1,'created_at'=>now()->toDateTimeString()]);
        $calculo->terminado=1;
        $calculo->save();
        return(back()->withStatus('Calculo de comisiones ('.$calculo->descripcion.') terminado'));
    }
    public function reabrir_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $calculo=Calculo::find($calculo_id);
        PagosDistribuidor::where('version',2)->where('calculo_id',$calculo_id)->update(['activo'=>0,'created_at'=>now()->toDateTimeString()]);
        $calculo->terminado=0;
        $calculo->save();
        return(back()->withStatus('Calculo de comisiones ('.$calculo->descripcion.') reabierto'));
    }
    public function reset_calculo(Request $request)
    {
        $calculo_id=$request->id;
        $calculo=Calculo::find($calculo_id);
        $calculo->adelanto=0;
        $calculo->cierre=0;
        CallidusVenta::where('calculo_id',$calculo_id)->delete();
        CallidusResidual::where('calculo_id',$calculo_id)->delete();
        ComisionVenta::where('calculo_id',$calculo_id)->delete();
        ComisionAddon::where('calculo_id',$calculo_id)->delete();
        ComisionResidual::where('calculo_id',$calculo_id)->delete();
        Mediciones::where('calculo_id',$calculo_id)->delete();
        ChargeBackDistribuidor::where('calculo_id',$calculo_id)->delete();
        PagosDistribuidor::where('calculo_id',$calculo_id)->delete();
        Reclamo::where('calculo_id',$calculo_id)->where('tipo','Faltante')->delete();
        AlertaCobranza::where('calculo_id',$calculo_id)->delete();
        $calculo->save();
        return(back()->withStatus('Calculo de comisiones ('.$calculo->descripcion.') reseteado correctamente'));
    }
    public function ejecutar_calculo(Request $request)
    {
        set_time_limit(0);
        
        $calculo_id=$request->id;
        $version=$request->version;
        $calculo=Calculo::find($calculo_id);
        $distribuidores=Distribuidor::all();
        
        echo "Inicio acreditar=".now();
        $this->acreditar_ventas($calculo,$version);
        echo "<br>Inicio mediciones=".now();
        $this->ejecutar_mediciones($calculo,$version);
        echo "<br>Inicio comision=".now();
        $this->comision_ventas($calculo,$version,$distribuidores);
        echo "<br>Inicio comision addons=".now();
        $this->comision_addons($calculo,$version,$distribuidores);
        echo "<br>Inicio cb=".now();
        $this->charge_back($calculo,$version);
        echo "<br>Inicio residual=".now();
        $this->residual($calculo,$version,$distribuidores);
        echo "<br>Respalda Facturas".now();
        $facturas_precargadas=$this->respaldaFacturas($calculo,$version);
        echo "<br>Inicio pagos=".now();
        $this->pagos($calculo,$version,$distribuidores);
        echo "<br>Inicia reconstruccion de ligas a facturas=".now();
        $this->restoreFacturas($facturas_precargadas);
        echo "<br>Inicio alertas=".now();
        $this->alertas_cobranza($calculo,$version);
        echo "<br>Fin calculo=".now(); 
        
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
    private function respaldaFacturas($calculo,$version)
    {
        return(
        PagosDistribuidor::select('user_id','calculo_id','version','pdf','xml','carga_facturas','aplicado')
                          ->where('calculo_id',$calculo->id) 
                          ->where('version',$version)
                          ->get()
        );
    }
    private function restoreFacturas($facturas_precargadas)
    {
        foreach($facturas_precargadas as $factura)
        {
            PagosDistribuidor::where('calculo_id',$factura->calculo_id)
                            ->where('version',$factura->version)
                            ->where('user_id',$factura->user_id)
                            ->update([
                                'xml'=>$factura->xml,
                                'pdf'=>$factura->pdf,
                                'aplicado'=>$factura->aplicado,
                                'carga_facturas'=>$factura->carga_facturas
                            ]);
        }
    }
    private function ejecutar_mediciones($calculo,$version)
    {
        $this->ejecutar_mediciones_vendedor($calculo,$version);
        $this->ejecutar_mediciones_supervisor($calculo,$version);
    }
    private function ejecutar_mediciones_vendedor($calculo,$version)
    {
        DB::delete('delete from mediciones where calculo_id='.$calculo->id." and version=".$version);

        $sql_nuevas="(select user_origen_id, count(*) as nuevas, sum(renta) as renta_nuevas,0 as adiciones, 0 as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'NUEVA' and validado=1 group by `user_origen_id`)";
        $sql_adiciones="(select user_origen_id, 0 as nuevas, 0 as renta_nuevas,count(*) as adiciones, sum(renta) as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'ADICION' and validado=1  group by `user_origen_id`)";
        $sql_renovaciones="(select user_origen_id, 0 as nuevas, 0 as renta_nuevas,0 as adiciones, 0 as renta_adiciones,count(*) as renovaciones, sum(renta) as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'RENOVACION' and validado=1  group by `user_origen_id`)";
        $sql_medicion="select user_origen_id,sum(nuevas) as nuevas,sum(renta_nuevas) as renta_nuevas,sum(adiciones) as adiciones,sum(renta_adiciones) as renta_adiciones,sum(renovaciones) as renovaciones,sum(renta_renovaciones) as renta_renovaciones";
        $sql_medicion=$sql_medicion." from (";
        $sql_medicion=$sql_medicion."".$sql_nuevas." UNION ".$sql_adiciones." UNION ".$sql_renovaciones;
        $sql_medicion=$sql_medicion." ) as a group by a.user_origen_id";
        return($sql_medicion);
        $mediciones=DB::select(DB::raw(
            $sql_medicion
           ));
        $mediciones=collect($mediciones);
        foreach($mediciones as $medicion)
        {
            $registro=new Mediciones;
            $registro->calculo_id=$calculo->id;
            $registro->user_id=$medicion->user_origen_id;
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
        return;
    }
    private function ejecutar_mediciones_supervisor($calculo,$version)
    {
        $supervisores=User::select('id')->where('perfil','gerente')->get();
        foreach($supervisores as $supervisor)
        {
            Mediciones::where('calculo_id',$calculo->id)->where('version',$version)->where('user_id',$supervisor->id)->delete();
            $supervisados=User::select('id')
                            ->where('supervisor',$supervisor->id)
                            ->orWhere('id',$supervisor->id)
                            ->get();
            $x=0;
            $usuarios_supervisados="";
            foreach($supervisados as $supervisado)
            {
                $usuarios_supervisados=$usuarios_supervisados.($x>0?',':'').$supervisado->id;
                $x=$x+1;
            }
            $sql_nuevas="(select ".$supervisor->id." as user_id,count(*) as nuevas, sum(renta) as renta_nuevas,0 as adiciones, 0 as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'NUEVA' and validado=1 and user_id in (".$usuarios_supervisados."))";
            $sql_adiciones="(select ".$supervisor->id." as user_id,0 as nuevas, 0 as renta_nuevas,count(*) as adiciones, sum(renta) as renta_adiciones,0 as renovaciones, 0 as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'ADICION' and validado=1 and user_id in (".$usuarios_supervisados."))";
            $sql_renovaciones="(select ".$supervisor->id." as user_id,0 as nuevas, 0 as renta_nuevas,0 as adiciones, 0 as renta_adiciones,count(*) as renovaciones, sum(renta) as renta_renovaciones from `ventas` where `fecha` between '".$calculo->periodo->fecha_inicio."' and '".$calculo->periodo->fecha_fin."' and `tipo` = 'RENOVACION' and validado=1 and user_id in (".$usuarios_supervisados."))";
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
        }
    }
    private function acreditar_ventas($calculo,$version)
    {
        $ventas=Venta::whereBetween('fecha',[$calculo->periodo->fecha_inicio,$calculo->periodo->fecha_fin])
                        ->where('validado','1')
                        ->get();

        DB::delete('delete from comision_ventas where calculo_id='.$calculo->id.' and version='.$version);
        Reclamo::where('calculo_id',$calculo->id)->where('tipo','Faltante')->delete();
        $callidus=CallidusVenta::select('id','contrato','cuenta','dn','renta','plazo','descuento_multirenta','afectacion_comision')
                                ->where('tipo','<>','ADDON')
                                ->where('calculo_id',$calculo->id)
                                ->get();
        $registros=[];
        foreach($ventas as $venta)
        {
            $validacion=$this->validar_venta($venta,$calculo->id,$callidus);
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
            $registros[]=[
                        'venta_id'=>$venta->id,
                        'calculo_id'=>$calculo->id,
                        'calculo_id_proceso'=>$calculo_proceso,
                        'calculo_id_consistencia'=>0,
                        'callidus_venta_id'=>$validacion['callidus_id'],
                        'version'=>$version,
                        'estatus_inicial'=>$estatus_inicial,
                        'consistente'=>$validacion['consistente'],
                        'estatus_final'=>$estatus_final,
                        'upfront'=>0,
                        'bono'=>0,
                        'upfront_supervisor'=>0,
                        'upfront_final'=>0,
                        'bono_final'=>0,
                        'diferencia_inconsistencia'=>0,
                        'created_at'=>now()->toDateTimeString(),
                        'updated_at'=>now()->toDateTimeString(),
            ];

            if(!$a_pagar) //NO SE ENCONTRO EN CALLIDUS
            {
                $monto=0;
                if($venta->tipo=="RENOVACION")
                {$monto=4.5*$venta->renta/1.16/1.03;}
                else
                {
                    if($venta->plazo<18)
                    {$monto=4*$venta->renta/1.16/1.03;}
                    if($venta->plazo>=18 && $venta->plazo<24)
                    {$monto=5*$venta->renta/1.16/1.03;}
                    if($venta->plazo>=24)
                    {$monto=6*$venta->renta/1.16/1.03;}
                }

                $reclamo=new Reclamo;
                $reclamo->venta_id=$venta->id;
                $reclamo->calculo_id=$calculo->id;
                $reclamo->monto=$monto;
                $reclamo->razon="Venta no pagada";
                $reclamo->tipo="Faltante";
                $reclamo->save();            
            }
        }
        ComisionVenta::insert($registros);
    }
    private function comision_addons($calculo,$version,$distribuidores)
    {
        DB::delete('delete from comision_addons where calculo_id='.$calculo->id.' and version='.$version);
        DB::delete('delete from reclamos where calculo_id='.$calculo->id.' and tipo="ADDON"');
        
        $callidus_addons=CallidusVenta::select('id','contrato','renta','plazo','descuento_multirenta','afectacion_comision','comision')
                                ->where('tipo','ADDON')
                                ->where('calculo_id',$calculo->id)
                                ->get();
        $registros_addon=[];
        foreach($callidus_addons as $addon)
        {
            $venta_padre=CallidusVenta::select('id','descuento_multirenta','afectacion_comision','renta','comision')
                                    ->where('calculo_id',$calculo->id)
                                    ->where('tipo','<>','ADDON')
                                    ->where('contrato',$addon->contrato)
                                    ->get()
                                    ->first();  

            try{
            $factor_comision_venta_padre=($venta_padre->comision)/(($venta_padre->renta/1.16/1.03)*(1-($venta_padre->descuento_multirenta/100))*(1-($venta_padre->afectacion_comision/100)));
            }
            catch(\Exception $e){
                $factor_comision_venta_padre=0;
            }
            try{
            $factor_comision_add_on=($addon->comision)/(($addon->renta/1.16/1.03)*(1-($addon->descuento_multirenta/100))*(1-($addon->afectacion_comision/100)));
            }
            catch(\Exception $e){
                $factor_comision_add_on=0;
            }

            if(round($factor_comision_venta_padre,1)!=round($factor_comision_add_on,1))
            {
                $reclamo=new Reclamo;
                $reclamo->venta_id=0;
                $reclamo->callidus_id=$addon->id;
                $reclamo->calculo_id=$calculo->id;
                $reclamo->monto=(round($factor_comision_venta_padre,1)*($addon->renta/1.16/1.03))-$addon->comision;
                $reclamo->razon="ADDON Pagado a factor distinto que el de la venta factor venta=".round($factor_comision_venta_padre,1)."x, addon=".round($factor_comision_add_on,1)."x";
                $reclamo->tipo="ADDON";
                $reclamo->save();
            }

            $venta_pagada=ComisionVenta::with('venta')
                                    ->select('venta_id')
                                    ->where('calculo_id',$calculo->id)
                                    ->where('version',$version)
                                    ->where('callidus_venta_id',$venta_padre->id)
                                    ->get()
                                    ->first();
            if(!is_null($venta_pagada))
            {
                $registros_addon[]=[
                    'venta_id'=>$venta_pagada->venta_id,
                    'calculo_id'=>$calculo->id,
                    'callidus_id'=>$addon->id,
                    'version'=>$version,
                    'comision'=>$addon->renta/1.16/1.03,
                    'comision_supervisor'=>!is_null($venta_pagada->venta->supervisor_id)?$addon->renta/1.16/1.03*0.5:0,
                    'created_at'=>now()->toDateTimeString(),
                    'updated_at'=>now()->toDateTimeString(),
                ];

            }          
        }
        ComisionAddon::insert($registros_addon);
    }
    private function validar_venta($venta,$calculo_id,$callidus)
    {
        $respuesta=array(
            'encontrada'=>false,
            'consistente'=>false,
            'callidus_id'=>0,
        );

        $registro=$callidus->where('contrato',$venta->folio.'_DL')->first();

        if(is_null($registro))
        {
            $registro=$callidus->where('dn',$venta->dn)->where('cuenta','like',$venta->cuenta.'%')->first();
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
    private function comision_ventas($calculo,$version,$distribuidores)
    {
        $comisionables=ComisionVenta::with('venta','callidus','venta.user','venta.user_origen')
                        ->where('calculo_id',$calculo->id)
                        ->where('version',$version)
                        ->get();
        $mediciones=Mediciones::where('calculo_id',$calculo->id)
                                ->where('version',$version)
                                ->get();
        foreach($comisionables as $credito)
        {
            if($credito->venta->user_origen->perfil=="distribuidor")
            {
                $distribuidor=$distribuidores->where('user_id',$credito->venta->user_origen->id)->first();  
                $a_12=$distribuidor->a_12;
                $a_18=$distribuidor->a_18;
                $a_24=$distribuidor->a_24;
                $r_12=$distribuidor->r_12;
                $r_18=$distribuidor->r_18;
                $r_24=$distribuidor->r_24;
                $paga_bono=$distribuidor->bono;
            }
            if($credito->venta->user_origen->perfil=="ejecutivo" || $credito->venta->user_origen->perfil=="gerente")
            {
                $a_12=1.5;
                $a_18=1.5;
                $a_24=2.0;
                $r_12=1.0;
                $r_18=1.0;
                $r_24=1.5;
                $paga_bono='0';
            }
                $medicion=$mediciones->where('user_id',$credito->venta->user_origen->id)->first();                                
                $tipo=$credito->venta->tipo;
                if($credito->estatus_inicial=="PAGO")
                {
                //LOS PARAMETROS DE CALCULO SON DE CALLIDUS
                    $plazo=$credito->callidus->plazo;
                    $renta=$credito->callidus->renta;
                    $dmr=$credito->callidus->descuento_multirenta;
                    $afectacion=$credito->callidus->afectacion_comision;
                    $propiedad=$credito->callidus->propiedad;
                ///////////////////////
                }
                else
                {
                    $plazo=$credito->venta->plazo;
                    $renta=$credito->venta->renta;
                    $dmr=$credito->venta->descuento_multirenta;
                    $afectacion=$credito->venta->afectacion_comision;
                    $propiedad=$credito->venta->propiedad;
                }

                $renta_neta=($renta/1.16/1.03)*(1-($dmr/100))*(1-($afectacion/100));
                $comision_supervisor=0;
                $comision=0;
                $bono=0;
                $factor_correccion=1;
                if($tipo=='NUEVA' || $tipo=='ADICION')
                {
                    if($plazo=='12' || $plazo=='6' || $plazo=='0'){ $comision=$renta_neta*$a_12;}
                    if($plazo=='18'){ $comision=$renta_neta*$a_18;}
                    if($plazo=='24' || $plazo=='36'){ $comision=$renta_neta*$a_24;}
                    //if($medicion->porcentaje_nuevas>=30 && $renta_neta>=200 && $paga_bono=="1"){$bono=0;}//{$bono=100;}
                }
                if($tipo=='RENOVACION')
                {
                    if($plazo=='12' || $plazo=='6' || $plazo=='0'){ $comision=$renta_neta*$r_12;}
                    if($plazo=='18'){ $comision=$renta_neta*$r_18;}
                    if($plazo=='24' || $plazo=='36'){ $comision=$renta_neta*$r_24;}

                    if($propiedad=='PROPIO')
                    {
                        $factor_correccion=0.5;
                    }
                }
                if(!is_null($credito->venta->user_origen->supervisor))
                {
                    $comision_supervisor=$renta_neta*0.5;
                }
                $credito->upfront_supervisor=$comision_supervisor*$factor_correccion;
                $credito->upfront=$comision*$factor_correccion;
                $credito->bono=$bono;
                $credito->save();
            
        }
    }
    public function charge_back($calculo,$version)
    {
        if($version=="1") {return;}
        
        ChargeBackDistribuidor::where('calculo_id',$calculo->id)->delete();

        $cancelaciones=CallidusVenta::select('id','contrato','tipo_baja','fecha_baja','comision')
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
                //$venta_pagada_eq=$venta_pagada->venta->propiedad=='NUEVO'&&$cancelacion->tipo_baja=='INVOLUNTARIO'?2000:0;
                $estatus="APLICADO";
            }
            $charge_back=new ChargeBackDistribuidor;
            $charge_back->callidus_venta_id=$cancelacion->id;
            $charge_back->comision_venta_id=$venta_pagada_id;
            $charge_back->calculo_id=$calculo->id;
            $charge_back->charge_back=$venta_pagada_cb;
            $charge_back->cargo_equipo=$venta_pagada_eq;
            //$charge_back->charge_back=is_null($cancelacion->comision)?0:-1*$cancelacion->comision;
            $charge_back->cargo_equipo=0;
            $charge_back->estatus=$estatus;
            $charge_back->save();

        }
        return;
    }
    public function periodos_anteriores($calculo)
    {
        $periodos_anteriores=array(
                                'menos_1'=>0,
                                'menos_2'=>0,
                                'menos_3'=>0,
                                'menos_4'=>0,
                                );
        $periodo_actual=Calculo::find($calculo->id)->periodo_id;
        $periodo_menos1=Calculo::where('periodo_id',$periodo_actual-1)->get()->first();
        $periodo_menos2=Calculo::where('periodo_id',$periodo_actual-2)->get()->first();
        $periodo_menos3=Calculo::where('periodo_id',$periodo_actual-3)->get()->first();
        $periodo_menos4=Calculo::where('periodo_id',$periodo_actual-4)->get()->first();
        
        $ultimo_conocido=$calculo->id;
        $periodos_anteriores['menos_1']=is_null($periodo_menos1)?$ultimo_conocido:$periodo_menos1->id;
        $ultimo_conocido=$periodos_anteriores['menos_1'];
        $periodos_anteriores['menos_2']=is_null($periodo_menos2)?$ultimo_conocido:$periodo_menos2->id;
        $ultimo_conocido=$periodos_anteriores['menos_2'];
        $periodos_anteriores['menos_3']=is_null($periodo_menos3)?$ultimo_conocido:$periodo_menos3->id;
        $ultimo_conocido=$periodos_anteriores['menos_3'];
        $periodos_anteriores['menos_4']=is_null($periodo_menos4)?$ultimo_conocido:$periodo_menos4->id;
        return($periodos_anteriores);        
    }
    public function residual($calculo,$version,$distribuidores)
    {
        if($version=="1") {return;}

        $usuarios=User::select('id','perfil')->get();
        $perfiles=$usuarios->pluck('perfil','id');

        ComisionResidual::where('calculo_id',$calculo->id)->delete();

        $periodos_anteriores=$this->periodos_anteriores($calculo);

        $calculo_anterior=$periodos_anteriores['menos_1'];
        $calculo_anterior_2=$periodos_anteriores['menos_2'];

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
    
        $residuales_actuales=CallidusResidual::select('id','contrato','renta','estatus','plazo')->where('calculo_id',$calculo->id)->get();

        $sql_ventas_anteriores="
                                select ventas.id,ventas.user_id,ventas.contrato,comision_ventas.calculo_id 
                                from ventas,comision_ventas 
                                where comision_ventas.version=2 and 
                                    comision_ventas.venta_id=ventas.id and 
                                    comision_ventas.calculo_id in (".$calculo_anterior.",".$calculo_anterior_2.") 
                            ";
                    //and ventas.contrato='".$contrato_consulta[0]."'
        $ventas_anteriores=DB::select(DB::raw(
            $sql_ventas_anteriores
            ));
        $ventas_anteriores=collect($ventas_anteriores);

        //return($ventas_anteriores);

        $registros=[];

        foreach($residuales_actuales as $actual)
        {
            $user_id_anterior=0;

            $user_id=0;
            $callidus_residual_id=0;
            $venta_id=0;
            $calculo_id=0;
            $comision=0;

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

                    $venta=$ventas_anteriores->where('contrato',$contrato_consulta[0]);                  

                    if(!$venta->isEmpty()) //ENCONTRADO EN REGISTRO DE VENTAS
                    { 
                        $venta=$venta->first();
                        $factor_residual=0;
                        if($perfiles[$venta->user_id]=="distribuidor" && $actual->plazo>=24)
                        {
                            $factor_residual=$distribuidores->where('user_id',$venta->user_id)->first()->porcentaje_residual;
                        }

                        $user_id=$venta->user_id;
                        $callidus_residual_id=$actual->id;
                        $venta_id=$venta->id;
                        $calculo_id=$calculo->id;
                        $comision=$actual->estatus=="ACTIVO"?$actual->renta*$factor_residual/100:0;
                    }
                    
                }
            else
                { 
                    $factor_residual=0;
                    if($perfiles[$user_id_anterior]=="distribuidor" && $actual->plazo>=24)
                    {
                        $factor_residual=$user_id_anterior=="1"?0:$distribuidores->where('user_id',$user_id_anterior)->first()->porcentaje_residual;                    
                    }
                    $user_id=$user_id_anterior;
                    $callidus_residual_id=$actual->id;
                    $venta_id=$venta_anterior[$actual->contrato];
                    $calculo_id=$calculo->id;
                    $comision=$actual->estatus=="ACTIVO"?$actual->renta*$factor_residual/100:0;
                }
            if($calculo_id!="0") //SIGNIFICA QUE PASO POR CUALQUIER ESCENARIO Y PUDO MAPEAR LA LINEA
            {
                $registros[]=[
                            'user_id'=>$user_id,
                            'callidus_residual_id'=>$callidus_residual_id,
                            'venta_id'=>$venta_id,
                            'calculo_id'=>$calculo_id,
                            'comision'=>$comision,
                            'created_at'=>now()->toDateTimeString(),
                            'updated_at'=>now()->toDateTimeString(),
                        ];
            }
        }
        
        $chunks=array_chunk($registros,1000);
        foreach($chunks as $chunk)
        {
            ComisionResidual::insert($chunk);
        }
        
        return;
    }
    private function pagos($calculo,$version,$distribuidores)
    {
        $this->pagos_vendedor($calculo,$version,$distribuidores);
        $this->pagos_supervisor($calculo,$version);
    }
    private function pagos_vendedor($calculo,$version,$distribuidores)
    {
        DB::delete('delete from pagos_distribuidors where calculo_id='.$calculo->id.' and version='.$version);
        
        $sql_pagos="    
        select user_id,sum(nuevas) as nuevas,sum(n_rentas) as n_rentas, sum(n_comision) as n_comision,sum(n_bono) as n_bono,sum(adiciones) as adiciones,sum(a_rentas) as a_rentas, sum(a_comision) as a_comision,sum(a_bono) as a_bono,sum(renovaciones) as renovaciones,sum(r_rentas) as r_rentas, sum(r_comision) as r_comision,sum(r_bono) as r_bono,sum(n_no_pago) as n_no_pago,sum(n_rentas_no_pago) as n_rentas_no_pago, sum(n_comision_no_pago) as n_comision_no_pago,sum(n_bono_no_pago) as n_bono_no_pago,sum(a_no_pago) as a_no_pago,sum(a_rentas_no_pago) as a_rentas_no_pago, sum(a_comision_no_pago) as a_comision_no_pago,sum(a_bono_no_pago) as a_bono_no_pago,sum(r_no_pago) as r_no_pago,sum(r_rentas_no_pago) as r_rentas_no_pago, sum(r_comision_no_pago) as r_comision_no_pago,sum(r_bono_no_pago) as r_bono_no_pago,sum(addons) as addons,sum(c_addons) as c_addons FROM (
            SELECT b.user_id,count(*) nuevas,sum(b.renta) as n_rentas,sum(a.upfront) as n_comision,sum(a.bono) as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='NUEVA' and a.estatus_inicial='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,count(*) as adiciones,sum(b.renta) as a_rentas,sum(a.upfront) as a_comision, sum(a.bono) as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='ADICION' and a.estatus_inicial='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,sum(b.renta) as r_rentas,sum(a.upfront) as r_comision, sum(a.bono) as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='RENOVACION' and a.estatus_inicial='PAGO' group by b.user_id
            UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,count(*) as n_no_pago,sum(b.renta) as n_rentas_no_pago,sum(a.upfront) as n_comision_no_pago,sum(a.bono) as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='NUEVA' and a.estatus_inicial='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,count(*) as a_no_pago,sum(b.renta) as a_rentas_no_pago,sum(a.upfront) as a_comision_no_pago,sum(a.bono) as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='ADICION' and a.estatus_inicial='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,count(*) as r_no_pago,sum(b.renta) as r_rentas_no_pago,sum(a.upfront) as r_comision_no_pago,sum(a.bono) as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='RENOVACION' and a.estatus_inicial='NO PAGO' group by b.user_id
                UNION
            SELECT b.user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,count(*) as addons,sum(a.comision) as c_addons FROM comision_addons as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." group by b.user_id
                ) as a group by a.user_id
        ";
        $pagos=DB::select(DB::raw(
            $sql_pagos
           ));
        $pagos=collect($pagos);
        $usuarios=User::select('id','perfil')->get();
        $perfiles=$usuarios->pluck('perfil','id');

        foreach($pagos as $pago)
        {
            $adelanto="1";
            $porcentaje_adelanto=50;
            $paga_residual="0";
            if($perfiles[$pago->user_id]=="distribuidor")
            {
                $distribuidor=$distribuidores->where('user_id',$pago->user_id)->first();
                $adelanto=$distribuidor->adelanto;
                $porcentaje_adelanto=$distribuidor->porcentaje_adelanto;
                $paga_residual=$distribuidor->residual;
            }
            
            if(($adelanto=="1" && $version=="1") || $version=="2")
            {
                $factor=$version=="1"?($porcentaje_adelanto/100):1;
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

                $registro->addons=$pago->addons;
                $registro->c_addons=$pago->c_addons*$factor;

                $anticipo_ordinario=0;
                $anticipo_no_pago=0;
                $residual=0; 
                $charge_back=0; 
                $retroactivos_reproceso=0;

                $activo=1;

                if($version=="2")
                {
                    $activo=0;
                    $anticipo=AnticipoNoPago::select(DB::raw('sum(anticipo) as anticipo'))
                                        ->where('calculo_id',$calculo->id)
                                        ->where('user_id',$pago->user_id)
                                        ->get()
                                        ->first();

                    $anticipo_no_pago=is_null($anticipo->anticipo)?0:$anticipo->anticipo;

                    $anticipo_ordinario_previo=PagosDistribuidor::where('calculo_id',$calculo->id)
                                                        ->where('user_id',$pago->user_id)
                                                        ->where('version',1)
                                                        ->get()
                                                        ->first();

                    $anticipo_ordinario=is_null($anticipo_ordinario_previo)?0:$anticipo_ordinario_previo->total_pago;

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

                    if(!is_null($residuales->comision) && $paga_residual=="1"){$residual=$residuales->comision;}
                    
                    $retroactivos_reproceso=0; //solo se calcula en el cierre
                }

                $anticipos_extraordinarios=$this->aplicar_anticipos($calculo->id,$pago->user_id,$calculo->periodo_id,$version);
                $registro->anticipos_extraordinarios=$anticipos_extraordinarios*$factor;
                $registro->activo=$activo;
                $registro->anticipo_ordinario=$anticipo_ordinario; //Solo se calcula en el cierre
                $registro->anticipo_no_pago=$anticipo_no_pago; //Solo se calcula en el cierre
                $registro->residual=$residual; //Solo se calcula en el cierre
                $registro->charge_back=$charge_back;//Solo se calcula en el cierre
                $registro->retroactivos_reproceso=$retroactivos_reproceso;//Solo se calcula en el cierre
                
                
                $registro->carga_facturas='2021-01-01 00:00:01';
                $total_comisiones=($pago->n_comision+$pago->n_bono+$pago->a_comision+$pago->a_bono+$pago->r_comision+$pago->r_bono+$pago->c_addons)*$factor;
                $registro->total_pago=$total_comisiones+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios-$registro->anticipo_ordinario;
                if($perfiles[$pago->user_id]!="distribuidor")
                {
                   if(!$this->verifica_condiciones_pago_vendedor($calculo,$version,$pago->user_id))
                   {
                        $registro->total_pago=0;

                        $especial=$this->verifica_autorizacion_especial($calculo,$pago->user_id);
                        if($especial['tiene_autorizacion']=="SI")
                        {
                            $registro->total_pago=$total_comisiones*($especial['porcentaje_autorizado']/100)+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios-$registro->anticipo_ordinario;
                        }
                        else
                        {
                            $this->abandera_pago_cero($calculo,$pago->user_id);
                        }
                   }
                   
                }
                

                $registro->save();
            }

        }
        return;
    }
    private function pagos_supervisor($calculo,$version)
    {
        $supervisores=User::select('id')->where('perfil','gerente')->get();
        foreach($supervisores as $supervisor)
        {
            $supervisados=User::select('id')
                            ->where('supervisor',$supervisor->id)
                            ->orWhere('id',$supervisor->id)
                            ->get();
            $x=0;
            $usuarios_supervisados="";
            foreach($supervisados as $supervisado)
            {
                $usuarios_supervisados=$usuarios_supervisados.($x>0?',':'').$supervisado->id;
                $x=$x+1;
            }
            $sql_pagos="    
            select user_id,sum(nuevas) as nuevas,sum(n_rentas) as n_rentas, sum(n_comision) as n_comision,sum(n_bono) as n_bono,sum(adiciones) as adiciones,sum(a_rentas) as a_rentas, sum(a_comision) as a_comision,sum(a_bono) as a_bono,sum(renovaciones) as renovaciones,sum(r_rentas) as r_rentas, sum(r_comision) as r_comision,sum(r_bono) as r_bono,sum(n_no_pago) as n_no_pago,sum(n_rentas_no_pago) as n_rentas_no_pago, sum(n_comision_no_pago) as n_comision_no_pago,sum(n_bono_no_pago) as n_bono_no_pago,sum(a_no_pago) as a_no_pago,sum(a_rentas_no_pago) as a_rentas_no_pago, sum(a_comision_no_pago) as a_comision_no_pago,sum(a_bono_no_pago) as a_bono_no_pago,sum(r_no_pago) as r_no_pago,sum(r_rentas_no_pago) as r_rentas_no_pago, sum(r_comision_no_pago) as r_comision_no_pago,sum(r_bono_no_pago) as r_bono_no_pago,sum(addons) as addons,sum(c_addons) as c_addons FROM (
                SELECT ".$supervisor->id." as user_id,count(*) nuevas,sum(b.renta) as n_rentas,sum(a.upfront_supervisor) as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='NUEVA' and a.estatus_inicial='PAGO' and b.user_origen_id in (".$usuarios_supervisados.")
                UNION
                SELECT ".$supervisor->id." as user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,count(*) as adiciones,sum(b.renta) as a_rentas,sum(a.upfront_supervisor) as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='ADICION' and a.estatus_inicial='PAGO' and b.user_origen_id in (".$usuarios_supervisados.")
                UNION
                SELECT ".$supervisor->id." as user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,count(*) as renovaciones,sum(b.renta) as r_rentas,sum(a.upfront_supervisor) as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='RENOVACION' and a.estatus_inicial='PAGO' and b.user_origen_id in (".$usuarios_supervisados.")
                UNION
                SELECT ".$supervisor->id." as user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,count(*) as n_no_pago,sum(b.renta) as n_rentas_no_pago,sum(a.upfront_supervisor) as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='NUEVA' and a.estatus_inicial='NO PAGO' and b.user_origen_id in (".$usuarios_supervisados.")
                    UNION
                SELECT ".$supervisor->id." as user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,count(*) as a_no_pago,sum(b.renta) as a_rentas_no_pago,sum(a.upfront_supervisor) as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='ADICION' and a.estatus_inicial='NO PAGO' and b.user_origen_id in (".$usuarios_supervisados.")
                    UNION
                SELECT ".$supervisor->id." as user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,count(*) as r_no_pago,sum(b.renta) as r_rentas_no_pago,sum(a.upfront_supervisor) as r_comision_no_pago,0 as r_bono_no_pago,0 as addons,0 as c_addons FROM comision_ventas as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.tipo='RENOVACION' and a.estatus_inicial='NO PAGO' and b.user_origen_id in (".$usuarios_supervisados.")
                UNION
                SELECT ".$supervisor->id." as user_id,0 nuevas,0 as n_rentas,0 as n_comision,0 as n_bono,0 as adiciones,0 as a_rentas,0 as a_comision, 0 as a_bono,0 as renovaciones,0 as r_rentas,0 as r_comision, 0 as r_bono,0 as n_no_pago,0 as n_rentas_no_pago,0 as n_comision_no_pago,0 as n_bono_no_pago,0 as a_no_pago,0 as a_rentas_no_pago,0 as a_comision_no_pago,0 as a_bono_no_pago,0 as r_no_pago,0 as r_rentas_no_pago,0 as r_comision_no_pago,0 as r_bono_no_pago,count(*) as addons,sum(comision_supervisor) as c_addons FROM comision_addons as a,ventas as b WHERE a.venta_id=b.id and a.calculo_id=".$calculo->id." and a.version=".$version." and b.user_origen_id in (".$usuarios_supervisados.")
                    ) as a group by a.user_id
            ";
            $pagos=DB::select(DB::raw($sql_pagos));
            foreach($pagos as $pago)
            {
                //obtiene las comisiones que fueron calculadas como vendedor
                $pagos_vendedor=PagosDistribuidor::where('calculo_id',$calculo->id)->where('version',$version)->where('user_id',$supervisor->id)->get();
                $comision_nuevas_vendedor=0;
                $comision_adiciones_vendedor=0;
                $comision_renovaciones_vendedor=0;
                $comision_addons_vendedor=0;
                $comision_nuevas_vendedor_no_pago=0;
                $comision_adiciones_vendedor_no_pago=0;
                $comision_renovaciones_vendedor_no_pago=0;             
                $charge_back_vendedor=0;
                foreach($pagos_vendedor as $pago_vendedor)
                {
                    $comision_nuevas_vendedor=$comision_nuevas_vendedor+$pago_vendedor->comision_nuevas;
                    $comision_adiciones_vendedor=$comision_adiciones_vendedor+$pago_vendedor->comision_adiciones;
                    $comision_renovaciones_vendedor=$comision_renovaciones_vendedor+$pago_vendedor->comision_renovaciones;
                    $comision_addons_vendedor=$comision_addons_vendedor+$pago_vendedor->c_addons;
                    $comision_nuevas_vendedor_no_pago=$comision_nuevas_vendedor_no_pago+$pago_vendedor->nuevas_comision_no_pago;
                    $comision_adiciones_vendedor_no_pago=$comision_adiciones_vendedor_no_pago+$pago_vendedor->adiciones_comision_no_pago;
                    $comision_renovaciones_vendedor_no_pago=$comision_renovaciones_vendedor_no_pago+$pago_vendedor->renovaciones_comision_no_pago;
                    $charge_back_vendedor=$charge_back_vendedor+$pago_vendedor->charge_back;
                }
                PagosDistribuidor::where('calculo_id',$calculo->id)->where('version',$version)->where('user_id',$supervisor->id)->delete();
                $adelanto="1";
                $porcentaje_adelanto=50;
                $paga_residual="0";                
                if(($adelanto=="1" && $version=="1") || $version=="2")
                {
                    $factor=$version=="1"?($porcentaje_adelanto/100):1;
                    $registro=new PagosDistribuidor;
                    $registro->calculo_id=$calculo->id;
                    $registro->user_id=$pago->user_id;
                    $registro->version=$version;
                    $registro->nuevas=$pago->nuevas;
                    $registro->renta_nuevas=$pago->n_rentas;
                    $registro->comision_nuevas=$pago->n_comision*$factor+$comision_nuevas_vendedor;
                    $registro->bono_nuevas=$pago->n_bono*$factor;
                    $registro->adiciones=$pago->adiciones;
                    $registro->renta_adiciones=$pago->a_rentas;
                    $registro->comision_adiciones=$pago->a_comision*$factor+$comision_adiciones_vendedor;
                    $registro->bono_adiciones=$pago->a_bono*$factor;
                    $registro->renovaciones=$pago->renovaciones;
                    $registro->renta_renovaciones=$pago->r_rentas;
                    $registro->comision_renovaciones=$pago->r_comision*$factor+$comision_renovaciones_vendedor;
                    $registro->bono_renovaciones=$pago->r_bono*$factor;

                    $registro->addons=$pago->addons;
                    $registro->c_addons=$pago->c_addons*$factor+$comision_addons_vendedor;

                    $registro->nuevas_no_pago=$pago->n_no_pago;
                    $registro->nuevas_renta_no_pago=$pago->n_rentas_no_pago;
                    $registro->nuevas_comision_no_pago=$pago->n_comision_no_pago+$comision_nuevas_vendedor_no_pago;
                    $registro->nuevas_bono_no_pago=$pago->n_bono_no_pago;

                    $registro->adiciones_no_pago=$pago->a_no_pago;
                    $registro->adiciones_renta_no_pago=$pago->a_rentas_no_pago;
                    $registro->adiciones_comision_no_pago=$pago->a_comision_no_pago+$comision_adiciones_vendedor_no_pago;
                    $registro->adiciones_bono_no_pago=$pago->a_bono_no_pago;

                    $registro->renovaciones_no_pago=$pago->r_no_pago;
                    $registro->renovaciones_renta_no_pago=$pago->r_rentas_no_pago;
                    $registro->renovaciones_comision_no_pago=$pago->r_comision_no_pago+$comision_renovaciones_vendedor_no_pago;
                    $registro->renovaciones_bono_no_pago=$pago->r_bono_no_pago;

                    $anticipo_ordinario=0;
                    $anticipo_no_pago=0;
                    $residual=0; 
                    $charge_back=0; 
                    $retroactivos_reproceso=0;

                    $activo=1;

                    if($version=="2")
                    {
                        $activo=0;
                        $anticipo=AnticipoNoPago::select(DB::raw('sum(anticipo) as anticipo'))
                                            ->where('calculo_id',$calculo->id)
                                            ->where('user_id',$pago->user_id)
                                            ->get()
                                            ->first();

                        $anticipo_no_pago=is_null($anticipo->anticipo)?0:$anticipo->anticipo;

                        $anticipo_ordinario_previo=PagosDistribuidor::where('calculo_id',$calculo->id)
                                                            ->where('user_id',$pago->user_id)
                                                            ->where('version',1)
                                                            ->get()
                                                            ->first();

                        $anticipo_ordinario=is_null($anticipo_ordinario_previo)?0:$anticipo_ordinario_previo->total_pago;

                        $charge_back=$comision_nuevas_vendedor_no_pago; //solo se calcula en el cierre
                        $descuentos=ChargeBackDistribuidor::select(DB::raw('sum(charge_back_distribuidors.charge_back+charge_back_distribuidors.cargo_equipo) as charge_back'))
                                                            ->join('comision_ventas','charge_back_distribuidors.comision_venta_id','=','comision_ventas.id')
                                                            ->join('ventas','comision_ventas.venta_id','=','ventas.id')
                                                            ->where('charge_back_distribuidors.calculo_id',$calculo->id)
                                                            ->where('charge_back_distribuidors.comision_venta_id','!=',0)
                                                            ->where('ventas.user_id',$pago->user_id)
                                                            ->get()
                                                            ->first();
                        if(!is_null($descuentos->charge_back)){$charge_back=$charge_back+$descuentos->charge_back;}

                        $residual=0; //solo se calcula en el cierre

                        $residuales=ComisionResidual::select(DB::raw('sum(comision) as comision'))
                                                    ->where('calculo_id',$calculo->id)
                                                    ->where('user_id',$pago->user_id)
                                                    ->get()
                                                    ->first();

                        if(!is_null($residuales->comision) && $paga_residual=="1"){$residual=$residuales->comision;}
                        
                        $retroactivos_reproceso=0; //solo se calcula en el cierre
                    }

                    $anticipos_extraordinarios=$this->aplicar_anticipos($calculo->id,$pago->user_id,$calculo->periodo_id,$version);
                    $registro->anticipos_extraordinarios=$anticipos_extraordinarios*$factor;
                    $registro->activo=$activo;
                    $registro->anticipo_ordinario=$anticipo_ordinario; //Solo se calcula en el cierre
                    $registro->anticipo_no_pago=$anticipo_no_pago; //Solo se calcula en el cierre
                    $registro->residual=$residual; //Solo se calcula en el cierre
                    $registro->charge_back=$charge_back;//Solo se calcula en el cierre
                    $registro->retroactivos_reproceso=$retroactivos_reproceso;//Solo se calcula en el cierre
                    
                    
                    $registro->carga_facturas='2021-01-01 00:00:01';
                    $total_comisiones=($pago->n_comision+$pago->n_bono+$pago->a_comision+$pago->a_bono+$pago->r_comision+$pago->r_bono)*$factor
                                                                        +$comision_nuevas_vendedor
                                                                        +$comision_adiciones_vendedor
                                                                        +$comision_renovaciones_vendedor
                                                                        +$pago->c_addons*$factor+$comision_addons_vendedor
                                                                        ;

                    $registro->total_pago=$total_comisiones+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios-$registro->anticipo_ordinario;
                    if(!$this->verifica_condiciones_pago_supervisor($calculo,$version,$pago->user_id))
                    {
                        $registro->total_pago=0;

                        $especial=$this->verifica_autorizacion_especial($calculo,$pago->user_id);
                        if($especial['tiene_autorizacion']=="SI")
                        {
                            $registro->total_pago=$total_comisiones*($especial['porcentaje_autorizado']/100)+$registro->anticipo_no_pago+$registro->residual+$registro->retroactivos_reproceso-$registro->charge_back-$registro->anticipos_extraordinarios-$registro->anticipo_ordinario;
                        }
                        else
                        {
                            $this->abandera_pago_cero($calculo,$pago->user_id);
                        }
                    }
                    $registro->save();
                }

            }
        }
    }
    private function abandera_pago_cero($calculo,$user_id)
    {
        $campo='porcentaje_pago_vendedor';
        $empleado=User::where('id',$user_id)->get()->first();
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
                                    $campo=>0
                                ]);
    }
    private function verifica_autorizacion_especial($calculo,$user_id)
    {
        $respuesta=[
                    'tiene_autorizacion'=>"NO",
                    'porcentaje_autorizado'=>0,
                    ];
        $autorizaciones=AutorizacionEspecial::where('calculo_id',$calculo->id)->where('user_id',$user_id)->get();
        foreach($autorizaciones as $autorizacion)
        {
            echo "<br>----AUTORIZACION ESPECIAL ENCONTRADA para=".$autorizacion->user_id;
            $respuesta['tiene_autorizacion']="SI";
            $respuesta['porcentaje_autorizado']=$autorizacion->porcentaje_autorizado;
            $campo='porcentaje_pago_vendedor';
            $empleado=User::where('id',$user_id)->get()->first();
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
                                        $campo=>$autorizacion->porcentaje_autorizado
                                    ]);
        }
        return($respuesta);
    }
    private function verifica_condiciones_pago_vendedor($calculo,$version,$user_id)
    {
        $empleado=Empleado::with('user')->where('user_id',$user_id)->get()->first();
        if($empleado->user->perfil=="ejecutivo")
        {   
            echo "<br>-------VENDEDOR";
            $fecha_medicion=$calculo->periodo->fecha_inicio;
            echo "<br>-------fecha_medicion=".$fecha_medicion;
            echo "<br>-------fecha_ingreso=".$empleado->fecha_ingreso;
            $inicio_medicion  = new DateTime($fecha_medicion);
            $ingreso_empleado = new DateTime($empleado->fecha_ingreso);
            echo "<br>-------diferencia=".$inicio_medicion->diff($ingreso_empleado)->days;
            if($inicio_medicion<=$ingreso_empleado) {echo "<br>----ENTRADA DESPUES DE INICIAR A MEDIR";return(true);}
            if($inicio_medicion>$ingreso_empleado && $inicio_medicion->diff($ingreso_empleado)->days<90) {echo "<br>----TODAVIA NO CUMPLE 90 DIAS AL INICIAR";return(true);}
            if($inicio_medicion>$ingreso_empleado && $inicio_medicion->diff($ingreso_empleado)->days>=90) 
            {
                echo "<br>----YA CUMPLIO 90 DIAS AL INICIAR";
                $mediciones=Mediciones::where('calculo_id',$calculo->id)
                                        ->where('version',$version)
                                        ->where('user_id',$user_id)
                                        ->get()
                                        ->first();
                $nuevas=$mediciones->nuevas;
                $adiciones=$mediciones->adiciones;
                $renovaciones=$mediciones->renovaciones;
                $unidades=$nuevas+$adiciones+$renovaciones;
                $lineas_nuevas=$nuevas+$adiciones;
                if($unidades>=$empleado->cuota_unidades && $lineas_nuevas>=$empleado->aduana_nuevas)
                {
                    echo "<br>------EMPLEADO CUMPLE CONDICIONES";
                    return true;
                }
                else 
                {
                    echo "<br>------EMPLEADO NO CUMPLE CONDICIONES";
                    return false;
                }
            }
            echo "<br>-------FIN VENDEDOR";
        }
        return true;
    }
    private function verifica_condiciones_pago_supervisor($calculo,$version,$user_id)
    {
        $empleado=Empleado::with('user')->where('user_id',$user_id)->get()->first();
        if($empleado->user->perfil=="gerente")
        {   
            echo "<br>-------SUPERVISOR";
            $fecha_medicion=$calculo->periodo->fecha_inicio;
            echo "<br>-------fecha_medicion=".$fecha_medicion;
            echo "<br>-------fecha_ingreso=".$empleado->fecha_ingreso;
            $inicio_medicion  = new DateTime($fecha_medicion);
            $ingreso_empleado = new DateTime($empleado->fecha_ingreso);
            echo "<br>-------diferencia=".$inicio_medicion->diff($ingreso_empleado)->days;
            if($inicio_medicion<=$ingreso_empleado) {echo "<br>----ENTRADA DESPUES DE INICIAR A MEDIR";return(true);}
            if($inicio_medicion>$ingreso_empleado && $inicio_medicion->diff($ingreso_empleado)->days<90) {echo "<br>----TODAVIA NO CUMPLE 90 DIAS AL INICIAR";return(true);}
            if($inicio_medicion>$ingreso_empleado && $inicio_medicion->diff($ingreso_empleado)->days>=90) 
            {
                echo "<br>----YA CUMPLIO 90 DIAS AL INICIAR";
                $mediciones=Mediciones::where('calculo_id',$calculo->id)
                                        ->where('version',$version)
                                        ->where('user_id',$user_id)
                                        ->get()
                                        ->first();
                $nuevas=$mediciones->nuevas;
                $adiciones=$mediciones->adiciones;
                $renovaciones=$mediciones->renovaciones;
                $unidades=$nuevas+$adiciones+$renovaciones;
                $lineas_nuevas=$nuevas+$adiciones;
                if($unidades>=$empleado->cuota_unidades && $lineas_nuevas>=$empleado->aduana_nuevas)
                {
                    echo "<br>------EMPLEADO CUMPLE CONDICIONES";
                    return true;
                }
                else 
                {
                    echo "<br>------EMPLEADO NO CUMPLE CONDICIONES";
                    return false;
                }
            }
            echo "<br>-------FIN VENDEDOR";

        }
        return true;
    }
    private function aplicar_anticipos($calculo_id,$user_id,$periodo_id,$version)
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
    private function alertas_cobranza($calculo,$version)
    {
        if($version=="1") {return;}

        $periodos_anteriores=$this->periodos_anteriores($calculo);

        $calculo_m1=$periodos_anteriores['menos_1']; //11
        $calculo_m2=$periodos_anteriores['menos_2']; //10
        $calculo_m3=$periodos_anteriores['menos_3']; //9
        $calculo_m4=$periodos_anteriores['menos_4']; //8

        AlertaCobranza::where('calculo_id',$calculo->id)->delete();

        $registros_alerta=[];

        //3 PERIODO DE MEDICION ATRAS

        $mes_medido=Calculo::with('periodo')->find($calculo_m4);
        $f_inicio=$mes_medido->periodo->fecha_inicio;
        $f_fin=$mes_medido->periodo->fecha_fin;

        $sql_3_atras="
            select contrato,(medicion1+medicion2+medicion3+medicion4) as n from (
                select contrato,sum(medicion1) as medicion1, sum(medicion2) as medicion2,sum(medicion3) as medicion3,sum(medicion4) as medicion4 from 
                (
                select contrato,
                    case 
                    when estatus='SUSPENDIDO' then 1 
                    else 0 
                    end as medicion1,
                    0 as medicion2,
                    0 as medicion3,
                    0 as medicion4 
                from callidus_residuals where calculo_id=".$calculo_m3." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
                UNION
                select contrato,
                    0 as medicion1,
                    case 
                    when estatus='SUSPENDIDO' then 1 
                    else 0 
                    end as medicion2,
                    0 as medicion3,
                    0 as medicion4
                from callidus_residuals where calculo_id=".$calculo_m2." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
                UNION
                select contrato,
                    0 as medicion1,
                    0 as medicion2,
                    case 
                    when estatus='SUSPENDIDO' then 1 
                    else 0 
                    end as medicion3,
                    0 as medicion4 
                from callidus_residuals where calculo_id=".$calculo_m1." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
                UNION
                select contrato,
                    0 as medicion1,
                    0 as medicion2,
                    0 as medicion3,
                    case 
                    when estatus='SUSPENDIDO' then 1 
                    else 0 
                    end as medicion4 
                from callidus_residuals where calculo_id=".$calculo->id." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
                ) as a group by a.contrato
            )as final
            where (final.medicion1=1 and final.medicion2=1 and final.medicion3=1 and final.medicion4=1) or (final.medicion2=1 and final.medicion3=1 and final.medicion4=1)
            ";
        $tres_atras=DB::select(DB::raw(
            $sql_3_atras
            ));

        foreach($tres_atras as $alerta)
        {
            $sql_venta="select id,user_id from ventas where id in (SELECT b.venta_id FROM callidus_ventas as a,comision_ventas as b where a.contrato='".$alerta->contrato."' and a.calculo_id=".$mes_medido->id." and b.callidus_venta_id=a.id and b.version=2)";
            $venta=DB::select(DB::raw($sql_venta));
            $venta=collect($venta)->first();
            $user_id=!is_null($venta)?$venta->user_id:1;
            $venta_id=!is_null($venta)?$venta->id:0;

            $sql_callidus="select id FROM callidus_ventas where contrato='".$alerta->contrato."' and calculo_id=".$mes_medido->id;
            $callidus=DB::select(DB::raw($sql_callidus));
            $callidus=collect($callidus)->first();
            $callidus_id=!is_null($callidus)?$callidus->id:0;
            
            $registros_alerta[]=[
                'user_id'=>$user_id,
                'venta_id'=>$venta_id,
                'callidus_venta_id'=>$callidus_id,
                'calculo_id'=>$calculo->id,
                'medidos'=>4,
                'contrato'=>$alerta->contrato,
                'alerta'=>$alerta->n=="4"?'4 periodos suspendido':'3 ultimos periodos suspendido',
                'created_at'=>now()->toDateTimeString(),
                'updated_at'=>now()->toDateTimeString(),
            ];
        }

        //2 PERIODO DE MEDICION ATRAS

        $mes_medido=Calculo::with('periodo')->find($calculo_m3);
        $f_inicio=$mes_medido->periodo->fecha_inicio;
        $f_fin=$mes_medido->periodo->fecha_fin;

        $sql_2_atras="
        select contrato,(medicion1+medicion2+medicion3) as n from (
            select contrato,sum(medicion1) as medicion1, sum(medicion2) as medicion2,sum(medicion3) as medicion3 from 
            (
            select contrato,
                   case 
                when estatus='SUSPENDIDO' then 1 
                else 0 
                end as medicion1,
                0 as medicion2,
                0 as medicion3
            from callidus_residuals where calculo_id=".$calculo_m2." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
            UNION
            select contrato,
                0 as medicion1,
                case 
                when estatus='SUSPENDIDO' then 1 
                else 0 
                end as medicion2,
                0 as medicion3
            from callidus_residuals where calculo_id=".$calculo_m1." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
            UNION
            select contrato,
                0 as medicion1,
                0 as medicion2,
                case 
                when estatus='SUSPENDIDO' then 1 
                else 0 
                end as medicion3
            from callidus_residuals where calculo_id=".$calculo->id." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
            ) as a group by a.contrato
            ) as final
            where (final.medicion1=1 and final.medicion2=1 and final.medicion3=1) or (final.medicion2=1 and final.medicion3=1)
        ";
        $dos_atras=DB::select(DB::raw(
            $sql_2_atras
            ));
        foreach($dos_atras as $alerta)
        {
            $sql_venta="select id,user_id from ventas where id in (SELECT b.venta_id FROM callidus_ventas as a,comision_ventas as b where a.contrato='".$alerta->contrato."' and a.calculo_id=".$mes_medido->id." and b.callidus_venta_id=a.id and b.version=2)";
            $venta=DB::select(DB::raw($sql_venta));
            $venta=collect($venta)->first();
            $user_id=!is_null($venta)?$venta->user_id:1;
            $venta_id=!is_null($venta)?$venta->id:0;

            $sql_callidus="select id FROM callidus_ventas where contrato='".$alerta->contrato."' and calculo_id=".$mes_medido->id;
            $callidus=DB::select(DB::raw($sql_callidus));
            $callidus=collect($callidus)->first();
            $callidus_id=!is_null($callidus)?$callidus->id:0;

            $registros_alerta[]=[
                'user_id'=>$user_id,
                'venta_id'=>$venta_id,
                'callidus_venta_id'=>$callidus_id,
                'calculo_id'=>$calculo->id,
                'medidos'=>3,
                'contrato'=>$alerta->contrato,
                'alerta'=>$alerta->n=="3"?'3 periodos suspendido':'2 ultimos periodos suspendido',
                'created_at'=>now()->toDateTimeString(),
                'updated_at'=>now()->toDateTimeString(),
            ];
        }
        //1 PERIODO DE MEDICION ATRAS

        $mes_medido=Calculo::with('periodo')->find($calculo_m2);
        $f_inicio=$mes_medido->periodo->fecha_inicio;
        $f_fin=$mes_medido->periodo->fecha_fin;

        $sql_1_atras="
        Select contrato,(medicion1+medicion2) as n from (
            select contrato,sum(medicion1) as medicion1, sum(medicion2) as medicion2 from 
            (
            select contrato,
                   case 
                when estatus='SUSPENDIDO' then 1 
                else 0 
                end as medicion1,
                0 as medicion2
            from callidus_residuals where calculo_id=".$calculo_m1." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
            UNION
            select contrato,
                0 as medicion1,
                case 
                when estatus='SUSPENDIDO' then 1 
                else 0 
                end as medicion2
            from callidus_residuals where calculo_id=".$calculo->id." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
            ) as a group by a.contrato
            ) as final
            where (final.medicion1=1 and final.medicion2=1) or (final.medicion2=1)
        ";

        $uno_atras=DB::select(DB::raw(
            $sql_1_atras
            ));
        foreach($uno_atras as $alerta)
        {
            $sql_venta="select id,user_id from ventas where id in (SELECT b.venta_id FROM callidus_ventas as a,comision_ventas as b where a.contrato='".$alerta->contrato."' and a.calculo_id=".$mes_medido->id." and b.callidus_venta_id=a.id and b.version=2)";
            $venta=DB::select(DB::raw($sql_venta));
            $venta=collect($venta)->first();
            $user_id=!is_null($venta)?$venta->user_id:1;
            $venta_id=!is_null($venta)?$venta->id:0;

            $sql_callidus="select id FROM callidus_ventas where contrato='".$alerta->contrato."' and calculo_id=".$mes_medido->id;
            $callidus=DB::select(DB::raw($sql_callidus));
            $callidus=collect($callidus)->first();
            $callidus_id=!is_null($callidus)?$callidus->id:0;

            $registros_alerta[]=[
                'user_id'=>$user_id,
                'venta_id'=>$venta_id,
                'callidus_venta_id'=>$callidus_id,
                'calculo_id'=>$calculo->id,
                'medidos'=>2,
                'contrato'=>$alerta->contrato,
                'alerta'=>$alerta->n=="2"?'2 periodos suspendido':'Ultimo periodo suspendido',
                'created_at'=>now()->toDateTimeString(),
                'updated_at'=>now()->toDateTimeString(),
            ];
        }
        //0 PERIODO DE MEDICION ATRAS

        $mes_medido=Calculo::with('periodo')->find($calculo_m1);
        $f_inicio=$mes_medido->periodo->fecha_inicio;
        $f_fin=$mes_medido->periodo->fecha_fin;

        $sql_actual="
        select contrato,
            case 
	        when estatus='SUSPENDIDO' then 1 
	        else 0 
	        end as medicion1
            from callidus_residuals where calculo_id=".$calculo->id." and fecha BETWEEN '".$f_inicio."' and '".$f_fin."' and estatus='SUSPENDIDO'
        ";


        $actual=DB::select(DB::raw(
            $sql_actual
            ));
        foreach($actual as $alerta)
        {
            $sql_venta="select id,user_id from ventas where id in (SELECT b.venta_id FROM callidus_ventas as a,comision_ventas as b where a.contrato='".$alerta->contrato."' and a.calculo_id=".$mes_medido->id." and b.callidus_venta_id=a.id and b.version=2)";
            $venta=DB::select(DB::raw($sql_venta));
            $venta=collect($venta)->first();
            $user_id=!is_null($venta)?$venta->user_id:1;
            $venta_id=!is_null($venta)?$venta->id:0;

            $sql_callidus="select id FROM callidus_ventas where contrato='".$alerta->contrato."' and calculo_id=".$mes_medido->id;
            $callidus=DB::select(DB::raw($sql_callidus));
            $callidus=collect($callidus)->first();
            $callidus_id=!is_null($callidus)?$callidus->id:0;

            $registros_alerta[]=[
                'user_id'=>$user_id,
                'venta_id'=>$venta_id,
                'callidus_venta_id'=>$callidus_id,
                'calculo_id'=>$calculo->id,
                'medidos'=>1,
                'contrato'=>$alerta->contrato,
                'alerta'=>'Primer periodo sin pago',
                'created_at'=>now()->toDateTimeString(),
                'updated_at'=>now()->toDateTimeString(),
            ];
        }
        AlertaCobranza::insert($registros_alerta);
        return;

    }
    public function ejecutar_conciliacion(Request $request)
    {
        $calculo_id=$request->id;
        $calculo=Calculo::find($calculo_id);
        echo "Inicio de validacion comisiones = ".now();
        $this->validar_comisiones_att($calculo);
        echo "<br>Inicio de validacion residuales = ".now();
        $this->validar_residual_att($calculo);
        return(back()->withStatus('Conciliacion con AT&T del periodo de control ('.$calculo->descripcion.') ejecutado correctamente'));
    }
    public function validar_comisiones_att($calculo)
    {
        return;
    }
    public function validar_residual_att($calculo)
    {

        AlertaConciliacion::where('calculo_id',$calculo->id)->where('tipo','Residual_45D')->delete();

        $periodos_anteriores=$this->periodos_anteriores($calculo);
        $registro_sin_pago=[];

        $ultima_periodo_actual=CallidusResidual::select(DB::raw('max(fecha) as ultimo'))
                                                ->where('calculo_id',$calculo->id)
                                                ->get()
                                                ->first()
                                                ->ultimo;

        $ultima_periodo_anterior=CallidusResidual::select(DB::raw('max(fecha) as ultimo'))
                                                ->where('calculo_id',$periodos_anteriores['menos_1'])
                                                ->get()
                                                ->first()
                                                ->ultimo;
        $sql_45_dias="
            select * from (
                select * from
                    (select callidus_ventas.contrato from callidus_ventas where calculo_id in (".$periodos_anteriores['menos_1'].",".$periodos_anteriores['menos_2'].") and contrato in (select CONCAT(contrato,'_DL') as contrato from ventas where fecha>'".$ultima_periodo_anterior."' and fecha<='".$ultima_periodo_actual."')) as evaluadas
                    left join
                    (select contrato as contrato_residual from callidus_residuals where fecha>'".$ultima_periodo_anterior."' and fecha<='".$ultima_periodo_actual."' and calculo_id=".$calculo->id.") as residuales on evaluadas.contrato=residuales.contrato_residual
                ) as cruce where contrato_residual is null          
                    ";
        $regla_45_dias=DB::select(DB::raw($sql_45_dias));
        $x=0;
        $y=0;

        foreach($regla_45_dias as $registro)
        {
            $detalles_contrato=CallidusVenta::where('contrato',$registro->contrato)
                                            ->where(function($query) use ($periodos_anteriores)
                                                        {
                                                            $query->where('calculo_id', $periodos_anteriores['menos_1']);
                                                            $query->orWhere('calculo_id', $periodos_anteriores['menos_2']);
                                                        }
                                                    )
                                            ->get()
                                            ->first();
            $registro_sin_pago[]=['calculo_id'=>$calculo->id,
                                  'tipo'=>'RESIDUAL_45D',
                                  'callidus_venta_id'=>$detalles_contrato->id,
                                  'contrato'=>$detalles_contrato->contrato,
                                  'descripcion'=>'La linea paso 45 dias desde su activacion/renovacion y no registra pago de residual',
                                  'created_at'=>now()->toDateTimeString(),
                                  'updated_at'=>now()->toDateTimeString(),
                                ];
        }
        AlertaConciliacion::insert($registro_sin_pago);
        return;
    }

}
