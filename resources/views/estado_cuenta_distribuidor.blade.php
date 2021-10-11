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
                    @if($user->detalles->bono=="1")
                    <div>
                        <span class="text-sm font-semibold text-white">
                            Bono: ${{number_format($pago->bono_adiciones,0)}}
                        </span>
                    </div>
                    @endif
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
                    @if($user->detalles->bono=="1")
                    <div>
                        <span class="text-sm font-semibold text-white">
                            Bono: ${{number_format($pago->bono_nuevas,0)}}
                        </span>
                    </div>
                    @endif
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
                    @if($user->detalles->bono=="1")
                    <div>
                        <span class="text-xs font-semibold text-yellow-300">
                            Bono: ${{number_format($pago->nuevas_bono_no_pago,0)}}
                        </span>
                    </div>
                    @endif
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
        <div class="w-full flex flex-col md:flex-row space-y-3 md:space-x-3">
            @if($user->detalles->bono=="1")
            <div class="w-full md:w-1/3 flex flex-col space-y-3 justify-center">
                <div class="w-full flex justify-center">
                    <span class="text-lg font-bold text-gray-700">Medicion de Bono</span>
                </div>
                <div class="w-full">
                    <canvas id="myChart"  height="200"></canvas>
                </div>
            <?php
            $gano_bono=false;
                if(($pago->nuevas+$pago->adiciones+$pago->nuevas_no_pago+$pago->adiciones_no_pago)>(($pago->renovaciones+$pago->renovaciones_no_pago)*0.3))
                {$gano_bono=true;}
            ?>
                <div class="w-full flex justify-center">
                    <span class="text-lg font-bold {{$gano_bono?'text-green-700':'text-red-700'}}">{{$gano_bono?'':'NO'}} Acredor al bono</span>
                </div>
            </div>
            @endif
            <div class="w-full {{$user->detalles->bono=="1"?'md:w-2/3':'pt-8'}} flex justify-center items-center">
                <table class="w-full {{$user->detalles->bono=="1"?'md:w-2/3':'md:w-1/2'}} shadow-xl">
                    <tr class="">
                        <td class="p-3 bg-ttds-encabezado rounded-t-xl text-xl text-white" colspan=3>
                            Estado de cuenta
                        </td>
                    </tr>
                    <tr class="border-l border-r border-gray-300">
                        <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl">
                            <center>
                                <a href="/transacciones_pago_distribuidor/{{$calculo->id}}/{{$user->id}}/{{$version}}">
                                    <i class="fas fa-file-excel"></i>
                                </a>
                            </td>
                        <td class="border-b border-gray-500 px-3">Comisiones <span class="text-red-700">{{$version=="1"?number_format($user->detalles->porcentaje_adelanto).'%':'100%'}}</span></td>
                        <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->comision_nuevas+$pago->comision_adiciones+$pago->comision_renovaciones,0)}}</center></td>
                    </tr>
                    @if($user->detalles->bono=="1")
                    <tr class="border-l border-r border-gray-300">
                        <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl"></td>
                        <td class="border-b border-gray-500 px-3">Bonos <span class="text-red-700">{{$version=="1"?number_format($user->detalles->porcentaje_adelanto).'%':'100%'}}</span></td>
                        <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->bono_nuevas+$pago->bono_adiciones+$pago->bono_renovaciones,0)}}</center></td>
                    </tr>
                    @endif
                    @if($version=="2")
                    @if($user->detalles->residual=="1")
                    <tr class="border-l border-r border-gray-300">
                        <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl">
                            <a href="/residuales_distribuidor/{{$calculo->id}}/{{$user->id}}">
                                <center><i class="fas fa-file-excel"></i>
                            </a>
                        </td>
                        <td class="border-b border-gray-500 px-3">Residual</td>
                        <td class="border-b border-gray-500 px-3"><center>(+) ${{number_format($pago->residual,0)}}</center></td>
                    </tr>
                    @endif
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
                    <tr class="border-l border-r border-gray-300">
                        <td class="border-b border-gray-500 "></td>
                        <td class="border-b border-gray-500 px-3">Anticipo ordinario</td>
                        <td class="border-b border-gray-500 px-3 text-red-700"><center>(-) ${{number_format($pago->anticipo_ordinario,0)}}</center></td>
                    </tr>
                    @endif
                    <tr class="border-l border-r border-gray-300">
                        <td class="border-b border-gray-500 "></td>
                        <td class="border-b border-gray-500 px-3">Anticipos extraordinarios</td>
                        <td class="border-b border-gray-500 px-3 text-red-700"><center>(-) ${{number_format($pago->anticipos_extraordinarios,0)}}</center></td>
                    </tr>
                    @if($version=="2")
                    <tr class="border-l border-r border-gray-300">
                        <td class="border-b border-gray-500 mx-3 font-bold text-green-700 text-2xl">
                            <center>
                                <a href="/transacciones_charge_back_distribuidor/{{$calculo->id}}/{{$user->id}}/{{$version}}">
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
        <div class="w-full flex flex-col">
            <div class="w-full p-3 text-lg text-ttds bg-gray-200 rounded-t-lg font-semibold">Anticipos aplicados</div>
            <div class="w-full p-3 text-base bg-white rounded-b-lg shaddow-xl">
                <table>
                    <tr>
                        <td class="p-3 text-ttds">Periodo</td>
                        <td class="p-3 text-ttds">Anticipo</td>
                        <td class="p-3 text-ttds">Descripcion</td>
                        <td class="p-3 text-ttds">% Aplicado</td>
                        <td class="p-3 text-ttds"></td>
                    </tr>
                    @foreach($anticipos_aplicados as $anticipo)
                    <tr>
                        <td class="px-3 text-gray-600">{{$anticipo->periodo->descripcion}}</td>
                        <td class="px-3 text-gray-600">${{number_format($anticipo->anticipo,0)}}</td>
                        <td class="px-3 text-gray-600">{{$anticipo->descripcion}}</td>
                        <td class="px-3 {{$version=="1"?'text-red-700':'text-green-700'}}"><center>{{$version=="1"?'50':'100'}}%</center></td>
                        <td class="px-3 text-gray-600 text-xs"><center>{{$version=="1"?'Aplicacion de referencia':'Saldado'}}</center></td>
                    </tr>
                    @endforeach
                </table>
            </div>

        </div>
        <div class="w-full flex flex-row space-x-2 pt-4">
            <div class="w-full p-2 flex flex-col">
                <div class="w-full bg-gray-200 rounded-t-lg p-3 text-xl text-gray-100 bg-gradient-to-br from-yellow-700 to-yellow-400 flex flex-row justify-between">
                    <div class="font-bold">Tramite de pago</div>
                    <div class="font-bold text-sm"></div>
                </div>
                @if($user->detalles->emite_factura=="1")
                @if(!is_null($pago->pdf))
                <div class="w-full flex flex-col pt-3 pb-3">
                    <div class="w-full flex flex-row ">
                        <div class="w-1/3 flex flex-col">
                            <div class="flex justify-center">
                                <a href="/facturas/{{$pago->pdf}}" download>
                                    <i class="text-2xl text-red-700 far fa-file-pdf"></i> PDF 
                                </a>
                            </div>

                        </div>
                        <div class="w-1/3 flex flex-col">
                            <div class="flex justify-center">
                                <a href="/facturas/{{$pago->xml}}" download>
                                    <i class="text-2xl text-blue-600 far fa-file-code"></i> XML
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="w-full flex flex-col shadow-lg rounded-b-lg p-5">
                    <form method="POST" action="{{route('cargar_factura_distribuidor')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="w-full flex flex-row ">
                            <div class="w-5/12 flex flex-col">
                                <span class="text-sm text-gray-700">Archivo PDF</span>
                                <input class="w-full" type="file" name="pdf_file">
                                @error('pdf_file')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror  
                            </div>
                            <div class="w-5/12 flex flex-col">
                                <span class="text-sm text-gray-700">Archivo XML</span>
                                <input class="w-full" type="file" name="xml_file">
                                @error('xml_file')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror 
                            </div>
                            <div class="w-2/12 flex flex-col">
                                <input type="hidden" name="calculo_id" value="{{$calculo->id}}">
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="hidden" name="version" value="{{$version}}">
                                <button class="w-full p-3 bg-green-500 hover:bg-green-700 font-bold rounded-lg text-gray-200 text-xl">Cargar</button>
                            </div>
                        </div>
                    </form>
                </div>  
                @else
                <div class="w-full flex flex-col pt-3 pb-3">
                    <div class="w-full flex flex-row px-3">
                        Pongase en contacto con @TTDSolutions para dar seguimiento a su pago.
                    </div>
                </div>
                    
                @endif              
            </div>
        </div>

        
                
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.js"></script>
        
<script>
var ctx = document.getElementById('myChart').getContext('2d');

var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            'Renovaciones',
            'Nuevas+Adiciones'
        ],
        datasets: [
            {
                label: 'Renovaciones',
                data: [
                    {{$pago->renovaciones+$pago->renovaciones_no_pago}},0
                    ],
                backgroundColor: [
                    'rgba(25, 99, 132, 0.2)',
                ],
                borderColor: [
                    'rgba(25, 99, 132, 1)',
                ],
            borderWidth: 1
            },
            {
                label: 'Nuevas+Adiciones',
                data: [
                        0,{{$pago->nuevas+$pago->adiciones+$pago->adiciones_no_pago+$pago->nuevas_no_pago}}
                    ],
                backgroundColor: [
                    'rgba(55, 190, 192, 0.2)',
                ],
                borderColor: [
                    'rgba(55, 190, 192, 1)',
                ],
            borderWidth: 1
            },
            
            {
                label: 'Umbral para obtener bono',
                data: [
                    {{($pago->renovaciones+$pago->renovaciones_no_pago)*0.3}}, {{($pago->renovaciones+$pago->renovaciones_no_pago)*0.3}}              
                    ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                ],
            borderWidth: 1,
            type: 'line',
              order: 0

            },
        ]
    },
    options: {
        indexAxis: 'y',
        plugins: {
            title: {
                display: false,
            },
            legend: {
                    display:false,
                },
        },
        responsive: true,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
          }
        }
    }
   
});
</script>    
</x-app-layout>
