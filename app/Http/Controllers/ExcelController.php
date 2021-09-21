<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\VentasImport;
use App\Models\Calculo;
use App\Imports\VentasImportAdmin;
use App\Imports\ImportCallidusVentas;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function ventas_import(Request $request) 
    {
        $file=$request->file('file');
        $import=new VentasImport;
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }    
        return back()->withStatus('Archivo cargado con exito!');
    }
    public function ventas_import_admin(Request $request) 
    {
        $file=$request->file('file');
        $import=new VentasImportAdmin;
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            
            return back()->withFailures($e->failures());
        }    
        return back()->withStatus('Archivo cargado con exito!');
    }
    public function callidus_import(Request $request) 
    {
        $file=$request->file('file');
        $import=new ImportCallidusVentas;
        session(['id_calculo' => $request->id_calculo]);
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }    
        Calculo::where('id',$request->id_calculo)
                ->update(['callidus'=>1]);
        return back()->withStatus('Archivo cargado con exito!');
    }
}