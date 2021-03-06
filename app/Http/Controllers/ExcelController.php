<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\VentasImport;
use App\Models\Calculo;
use App\Models\Venta;
use App\Models\CargaLogs;
use App\Models\CallidusVenta;
use App\Models\ComisionVenta;
use App\Models\ComisionResidual;
use App\Models\CallidusResidual;
use App\Models\Mediciones;
use App\Models\Reclamo;
use App\Models\PagosDistribuidor;
use App\Imports\VentasImportAdmin;
use App\Imports\ImportCallidusVentas;
use App\Imports\ImportCallidusResidual;
use App\Models\ChargeBackDistribuidor;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ExcelController extends Controller
{
    public function ventas_import(Request $request) 
    {
        $request->validate([
            'file'=> 'required',
            ]);
        $file=$request->file('file');

        $bytes = random_bytes(5);
        $carga_id=bin2hex($bytes);
        session(['id_carga' => $carga_id]);

        $import=new VentasImport;
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }  

        $errores=$this->validar_carga($carga_id);
        if(!empty($errores))
        {
            $this->borrar_carga_ventas($carga_id);
            return(back()->with('error_validacion',$errores));
        }
        return back()->withStatus('Archivo cargado con exito!');
    }
    public function ventas_import_admin(Request $request)  //CON LOG DE CARGA
    {
        $request->validate([
            'file'=> 'required',
            ]);
        $file=$request->file('file');

        $bytes = random_bytes(5);
        $carga_id=bin2hex($bytes);

        session(['id_carga' => $carga_id]);

        $import=new VentasImportAdmin;
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            
            return back()->withFailures($e->failures());
        }
        $errores=$this->validar_carga($carga_id);
        if(!empty($errores))
        {
            $this->borrar_carga_ventas($carga_id);
            return(back()->with('error_validacion',$errores));
        }
        return back()->withStatus('Archivo cargado con exito!');
    }
    public function callidus_import(Request $request) 
    {
        $request->validate([
            'file_v'=> 'required',
            ]);
        $file=$request->file('file_v');
        CallidusVenta::where('calculo_id',$request->id_calculo)->delete();
        ComisionVenta::where('calculo_id',$request->id_calculo)->delete();
        Mediciones::where('calculo_id',$request->id_calculo)->delete();
        ChargeBackDistribuidor::where('calculo_id',$request->id_calculo)->delete();
        PagosDistribuidor::where('calculo_id',$request->id_calculo)->delete();
        Reclamo::where('calculo_id',$request->id_calculo)->where('tipo','Faltante')->delete();
        $calculo=Calculo::find($request->id_calculo);
        $calculo->adelanto=0;
        $calculo->cierre=0;
        $calculo->save();
        
        $import=new ImportCallidusVentas;
        session(['id_calculo' => $request->id_calculo]);
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }    
        return back()->withStatus('Archivo Callidus Ventas cargado con exito!');
    }
    public function callidus_residual_import(Request $request) 
    {
        $request->validate([
            'file_r'=> 'required',
            ]);
        $file=$request->file('file_r');
        CallidusResidual::where('calculo_id',$request->id_calculo)->delete();
        ComisionResidual::where('calculo_id',$request->id_calculo)->delete();
        $calculo=Calculo::find($request->id_calculo);
        $calculo->cierre=0;
        $calculo->save();
        $import=new ImportCallidusResidual;
        session(['id_calculo' => $request->id_calculo]);
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }    
        return back()->withStatus('Archivo Callidus Residual cargado con exito!');
    }
    public function validar_carga($id_carga)
    {
        $registros=Venta::where('carga_id',$id_carga)->orderBy('id','asc')->get();
        $renglon=2;
        $n_errores=0;
        $errores=[];
        foreach($registros as $venta)
        {
            $respuesta=$this->valida_dn_periodo($venta,$renglon);
            if($respuesta['valido']=="0")
            {
                $errores[]=['row'=>$respuesta['renglon'],'campo'=>$respuesta['campo'],'mensaje'=>$respuesta['mensaje'],'valor'=>$respuesta['valor']];
                $n_errores=$n_errores+1;
            }
            $respuesta=$this->valida_contrato_periodo($venta,$renglon);
            if($respuesta['valido']=="0")
            {
                $errores[]=['row'=>$respuesta['renglon'],'campo'=>$respuesta['campo'],'mensaje'=>$respuesta['mensaje'],'valor'=>$respuesta['valor']];
                $n_errores=$n_errores+1;
            }
            $renglon=$renglon+1;
            if($n_errores>=50){return($errores);}
        }
        return($errores);
    }
    public function valida_dn_periodo($venta,$renglon)
    {
        $respuesta=[    'valido'=>"0",
                        'renglon'=>"".$renglon,
                        'campo'=>"dn",
                        'mensaje'=>'',
                        'valor'=>'',
                    ];
        $ocurrencias=Venta::select(DB::raw('dn,count(*) as ocurrencias'))
                            ->whereRaw('lpad(fecha,7,0)=?',[substr($venta->fecha,0,7)])
                            ->where('dn',$venta->dn)
                            ->groupBy('dn')
                            ->get()
                            ->first();
        if($ocurrencias->ocurrencias=="1")
        {
            $respuesta['valido']="1";
        }
        else
        {
            $respuesta['mensaje']='Debe ser unico en el periodo mensual';
            $respuesta['valor']=$venta->dn;
        }
        return($respuesta);
    }
    public function valida_contrato_periodo($venta,$renglon)
    {
        $respuesta=[    'valido'=>"0",
                        'renglon'=>"".$renglon,
                        'campo'=>"folio",
                        'mensaje'=>'',
                        'valor'=>'',
                    ];
        $ocurrencias=Venta::select(DB::raw('folio,count(*) as ocurrencias'))
                            ->whereRaw('lpad(fecha,7,0)=?',[substr($venta->fecha,0,7)])
                            ->where('folio',$venta->folio)
                            ->groupBy('folio')
                            ->get()
                            ->first();
        if($ocurrencias->ocurrencias=="1")
        {
            $respuesta['valido']="1";
        }
        else
        {
            $respuesta['mensaje']='Debe tener un valor unico en el periodo mensual';
            $respuesta['valor']=$venta->folio;
        }
        return($respuesta);
    }
    public function borrar_carga_ventas($id_carga)
    {
        Venta::where('carga_id',$id_carga)->delete();
    }
}