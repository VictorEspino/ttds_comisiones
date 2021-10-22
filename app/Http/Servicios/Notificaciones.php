<?php
namespace App\Http\Servicios;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PagosDistribuidor;

Class Notificaciones{

    public static function nuevos_pagos()
    {
        $anterior_login=Auth::user()->anterior_login;
        $nuevos=PagosDistribuidor::where('created_at','>',$anterior_login)
                                    ->when(Auth::user()->perfil=='distribuidor',function($query){$query->where('user_id',Auth::user()->id);})
                                    ->select(DB::raw('count(*) as n'))
                                    ->get()
                                    ->first();
        return($nuevos->n);

    }
    public static function nuevas_facturas()
    {
        $anterior_login=Auth::user()->anterior_login;
        $nuevos=PagosDistribuidor::where('carga_facturas','>',$anterior_login)
                                    ->select(DB::raw('count(*) as n'))
                                    ->get()
                                    ->first();
        return($nuevos->n);

    }
}