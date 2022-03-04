<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ttds leading-tight">
            {{ __('Estado de cuenta') }}
        </h2>
    </x-slot>
    <div class="w-full flex flex-col space-y-6 pb-10">  
        <div class="w-full flex flex-col md:flex-row md:space-x-10 space-y-3 md:space-y-0">
            <div class="w-full md:w-5/12 p-3 flex flex-col">
                <div class="font-semibold text-2xl text-gray-700">{{$user->name}}</div>
                <div class="font-semibold text-lg text-gray-700">{{$calculo->descripcion}}</div>
                <div class="text-sm text-gray-700">De {{$calculo->periodo->fecha_inicio}} a {{$calculo->periodo->fecha_fin}}</div>
                <div class="font-semibold text-2xl text-red-700 pt-2">{{$version=="1"?'Anticipo Ordinario':'Calculo Final'}}</div>
                @if(session('status')!='')
                <div class="w-full text-sm rounded font-bold p-2 bg-green-300 text-gray-600">
                    {{session('status')}}
                </div>
                @endif
                @if($errors->any())
                <div class="w-full text-sm rounded font-bold p-2 bg-red-300 text-gray-600">
                    Revise la foma de FACTURA
                </div>
                @endif
            </div> 
            @if($alertas!="0")
            <div class="w-full md:hidden pb-4" colspan=3><center>
                <span class="text-xl text-red-400 flex justify-center items-center">
                    <a href="{{route('export_alertas',['id'=>$calculo->id,'user_id'=>$user->id])}}">
                        <i class="fas fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;Alertas Cobranza : {{$alertas}}&nbsp;&nbsp;&nbsp;<i class="fas fa-exclamation-triangle"></i>
                    </a>
                </span>    
            </div>
            @endif
            <div class="w-full md:w-3/12 flex flex-col bg-gradient-to-br from-blue-700 to-green-300 rounded-lg p-4 shadow-xl">
                <div class="w-full flex flex-row justify-between">
                    <div>
                        <span class="text-sm font-bold text-white">Adiciones</span>
                    </div>
                    <div class="flex justify-end">
                        <span class="text-base font-semibold text-white">{{number_format($pago->adiciones,0)}}</span>
                    </div>
                </div>
                <div class="w-full py-3 flex flex-col">
                    <div>
                        <span class="text-3xl font-semibold text-white">
                            ${{number_format($pago->comision_adiciones,0)}}
                        </span>
                    </div>
                </div>
                @if($pago->adiciones_no_pago!="0")
                <div class="w-full flex flex-row justify-between">
                    <div>
                        <span class="text-sm font-bold text-yellow-300">Pendientes</span>
                    </div>
                    <div class="flex justify-end">
                        <span class="text-sm font-semibold text-yellow-300">{{number_format($pago->adiciones_no_pago,0)}}</span>
                    </div>
                </div>
                    @if($version=="2")
                    <div class="w-full py-3 flex flex-col">
                        <div>
                            <span class="text-xl font-semibold text-yellow-300">
                                ${{number_format($pago->adiciones_comision_no_pago,0)}}
                            </span>
                        </div>
                        @if($user->detalles->bono=="1")
                        <div>
                            <span class="text-xs font-semibold text-yellow-300">
                                Bono: ${{number_format($pago->adiciones_bono_no_pago,0)}}
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                @endif
            </div>
            <div class="w-full md:w-3/12 flex flex-col bg-gradient-to-br from-pink-600 to-yellow-300 rounded-lg p-4 shadow-xl">
                <div class="w-full flex flex-row justify-between">
                    <div>
                        <span class="text-sm font-bold text-white">Nuevas</span>
                    </div>
                    <div class="flex justify-end">
                        <span class="text-base font-semibold text-white">{{number_format($pago->nuevas,0)}}</span>
                    </div>
                </div>
                <div class="w-full py-3 flex flex-col">
                    <div>
                        <span class="text-3xl font-semibold text-white">
                            ${{number_format($pago->comision_nuevas,0)}}
                        </span>
                    </div>
                </div>
                @if($pago->nuevas_no_pago!="0")
                <div class="w-full flex flex-row justify-between">
                    <div>
                        <span class="text-sm font-bold text-yellow-300">Pendientes</span>
                    </div>
                    <div class="flex justify-end">
                        <span class="text-sm font-semibold text-yellow-300">{{number_format($pago->nuevas_no_pago,0)}}</span>
                    </div>
                </div>
                @if($version=="2")
                <div class="w-full py-3 flex flex-col">
                    <div>
                        <span class="text-xl font-semibold text-yellow-300">
                            ${{number_format($pago->nuevas_comision_no_pago,0)}}
                        </span>
                    </div>
                </div>
                @endif
                @endif
            </div>
            <div class="w-full md:w-3/12 flex flex-col bg-gradient-to-br from-purple-700 to-pink-300 rounded-lg p-4 shadow-xl">
                <div class="w-full flex flex-row justify-between">
                    <div>
                        <span class="text-base font-bold text-white">Renovaciones</span>
                    </div>
                    <div class="flex justify-end">
                        <span class="text-sm font-semibold text-white">{{number_format($pago->renovaciones,0)}}</span>
                    </div>
                </div>
                <div class="w-full py-3">
                    <span class="text-3xl font-semibold text-white">
                        ${{number_format($pago->comision_renovaciones,0)}}
                    </span>
                </div>
                @if($pago->renovaciones_no_pago!="0")
                <div class="w-full flex flex-row justify-between">
                    <div>
                        <span class="text-sm font-bold text-yellow-300">Pendientes</span>
                    </div>
                    <div class="flex justify-end">
                        <span class="text-sm font-semibold text-yellow-300">{{number_format($pago->renovaciones_no_pago,0)}}</span>
                    </div>
                </div>
                @if($version=="2")
                <div class="w-full py-3 flex flex-col">
                    <div>
                        <span class="text-xl font-semibold text-yellow-300">
                            ${{number_format($pago->renovaciones_comision_no_pago,0)}}
                        </span>
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>
        <div class="w-full flex flex-col md:flex-row space-y-3 md:space-x-3 justify-center items-center">
            <div class="w-full md:w-2/3 pt-8 flex justify-center items-center flex-col space-y-5">
                @if($alertas!="0")
                <div class="md:pb-8 hidden md:block" colspan=3><center>
                    <span class="text-xl text-red-400 flex justify-center items-center">
                        <a href="{{route('export_alertas',['id'=>$calculo->id,'user_id'=>$user->id])}}">
                            <i class="fas fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;Alertas Cobranza : {{$alertas}}&nbsp;&nbsp;&nbsp;<i class="fas fa-exclamation-triangle"></i>
                        </a>
                    </span>    
                </div>
                @endif
                <div class="w-full"><center>
                    <table class="w-full shadow-xl">
                        <tr class="">
                            <td class="p-3 bg-ttds-encabezado rounded-t-xl text-xl text-white" colspan=4>
                                Mediciones
                            </td>
                        </tr>
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 px-3 text-gray-700">Concepto</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"><center>Cuota</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"><center>Logro</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"></td>
                        </tr>
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 px-3 text-gray-700 font-bold">Cuota Unidades</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"><center>{{number_format($user->empleado->cuota_unidades,0)}}</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"><center>{{number_format($medicion->nuevas+$medicion->adiciones+$medicion->renovaciones,0)}}</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700">{!!($medicion->nuevas+$medicion->adiciones+$medicion->renovaciones)>=($user->empleado->cuota_unidades)?'<i class="text-green-500 fas fa-check-circle"></i>':'<i class="text-red-500 fas fa-times-circle"></i>'!!}</td>
                        </tr>
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 px-3 text-gray-700 font-bold">Lineas nuevas (nuevas+adiciones)</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"><center>{{number_format($user->empleado->aduana_nuevas,0)}}</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700"><center>{{number_format($medicion->nuevas+$medicion->adiciones,0)}}</td>
                            <td class="border-b border-gray-500 px-3 text-gray-700">{!!($medicion->nuevas+$medicion->adiciones)>=($user->empleado->aduana_nuevas)?'<i class="text-green-500 fas fa-check-circle"></i>':'<i class="text-red-500 fas fa-times-circle"></i>'!!}</td>
                        </tr>
                        @php
                        $cumplio=false;
                        $leyenda="NO CUMPLE OBJETIVOS DE VENTA";
                        if(($medicion->nuevas+$medicion->adiciones+$medicion->renovaciones)>=($user->empleado->cuota_unidades) && ($medicion->nuevas+$medicion->adiciones)>=($user->empleado->aduana_nuevas))
                        {
                            $cumplio=true;
                            $leyenda="CUMPLE OBJETIVOS DE VENTA";
                        }
                        @endphp
                        <tr class="{{$cumplio?'bg-green-500':'bg-red-500'}} p-3 rounded-b-xl">
                            <td colspan=4 class="p-3 text-white  px-3 font-bold rounded-b-xl"><CENTER>{{$leyenda}}<br>
                                {{$autorizacion_especial=="SI"?'SE OTORGO AUTORIZACION ESPECIAL PARA COBRAR COMISIONES AL '.$porcentaje_autorizacion.'%':''}}
                            </td>
                        </tr>

                        
                    </table>
                </div>
                <div class="w-full"><center>
                    <table class="w-full shadow-xl">
                        <tr class="">
                            <td class="p-3 bg-ttds-encabezado rounded-t-xl text-xl text-white" colspan=3>
                                Estado de cuenta
                            </td>
                        </tr>
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl">
                                <center>
                                    <a href="/transacciones_pago_empleado/{{$calculo->id}}/{{$user->id}}/{{$version}}">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                </td>
                            <td class="border-b border-gray-500 px-3">Comisiones <span class="text-red-700">{{$version=="1"?'50%':'100%'}}</span></td>
                            <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->comision_nuevas+$pago->comision_adiciones+$pago->comision_renovaciones+$pago->leads,0)}}</center></td>
                        </tr>
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl">
                                </td>
                            <td class="border-b border-gray-500 px-3">ADDONS <span class="text-red-700">{{$version=="1"?'50%':'100%'}}</span></td>
                            <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->c_addons,0)}}</center></td>
                        </tr>
                        @if($version=="2")
        
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl"><center></td>
                            <td class="border-b border-gray-500 px-3">Retroactivos</td>
                            <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->retroactivos_reproceso,0)}}</center></td>
                        </tr>
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 "></td>
                            <td class="border-b border-gray-500 px-3">Anticipo por lineas pendientes</td>
                            <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->anticipo_no_pago,0)}}</center></td>
                        </tr>
                        @if($version=="2")
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 "></td>
                            <td class="border-b border-gray-500 px-3">Anticipo ordinario</td>
                            <td class="border-b border-gray-500 px-3 text-red-700"><center>(-) ${{number_format($pago->anticipo_ordinario,0)}}</center></td>
                        </tr>
                        @endif
                        @endif
                        @if($version=="2")
                        <tr class="border-l border-r border-gray-300">
                            <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl">
                                <center>
                                    <a href="/transacciones_charge_back_empleado/{{$calculo->id}}/{{$user->id}}/{{$version}}">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                </td>
                            </td>
                            <td class="border-b border-gray-500 px-3">Charge-Back</td>
                            <td class="border-b border-gray-500 px-3 text-red-700"><center>(-) ${{number_format($pago->charge_back,0)}}</center></td>
                        </tr>
                        @endif
                        <tr class="rounded-b-lg shadow-lg bg-black text-gray-200 font-bold">
                            <td class="rounded-bl-lg"></td>
                            <td class="p-3">Saldo a pagar</td>
                            <td class="p-3 rounded-br-lg"><center>${{number_format($pago->total_pago,0)}}</center></td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>  
    </div>
</x-app-layout>
