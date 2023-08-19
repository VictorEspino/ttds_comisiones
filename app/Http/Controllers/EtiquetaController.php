<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EtiquetasImport;
use App\Models\Etiqueta;
use Illuminate\Support\Facades\DB;



class EtiquetaController extends Controller
{
    public function etiquetas_import(Request $request) 
    {
        Etiqueta::where('id','>',0)->delete();
        $request->validate([
            'file'=> 'required',
            ]);
        $file=$request->file('file');

        $import=new EtiquetasImport;
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }  
        return back()->withStatus('<div class="flex justify-center">Archivo cargado con exito!</div><div class="flex justify-center text-blue-600"><a target="_blank" href="'.route('etiquetas_show',['marca'=>$request->marca]).'">VER ETIQUETAS</a></div>');
    }
    public function etiquetas_show(Request $request)
    {
        $etiquetas=Etiqueta::select(DB::raw('telefono,lpad(created_at,10,0) as fecha'))->orderBy('telefono','asc')->get();
        return(view('etiquetas_show',['registros'=>$etiquetas,'marca'=>$request->marca]));
    }
}
