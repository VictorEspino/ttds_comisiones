<x-app-layout>
    <x-slot name="header">
            {{ __('Detalles de Periodo') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Detalles</div> 
            <div class="w-full text-lg font-semibold text-gray-100">{{$calculo->descripcion}}</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{$actor->name}}</div>            
        </div> <!--FIN ENCABEZADO-->

        <div class="w-full rounded-b-lg bg-white p-3 flex flex-wrap"> <!--CONTENIDO-->
            @if($actor->perfil=="distribuidor")
            @if($actor->detalles->adelanto==0)
            <div class="w-full md:w-1/2 p-3">
                <div class="w-fullshadow-xl bg-ttds-secundario-2 rounded-lg flex flex-row p-2">
                    <div class="w-2/3 flex flex-col text-center px-12">
                        <div class="text-2xl font-semibold">PREVIO</div>
                        <div class="text-xl font-semibold">Sabana de comisiones</div>
                        <div class="text-sm font-normal">Emite el listado de ventas que seran incluidas en su calculo de comisiones</div>
                    </div>
                    <div class="w-1/3 flex items-center text-center">
                        <center><a href="/transacciones_pago_distribuidor/{{$calculo->id}}/{{$actor->id}}/1"><i class="far fa-file-excel text-green-500 text-7xl"></i></a></center>
                    </div>
                </div>
            </div> 
            @endif
            @endif
            @foreach ($pagos as $pago)
            @if(($pago->version=="2" && $calculo->terminado=="1") || ($pago->version=="1"))
            <div class="w-full md:w-1/2 p-3">
                <div class="w-fullshadow-xl bg-ttds-secundario-2 rounded-lg flex flex-row p-2">
                    <div class="w-2/3 flex flex-col text-center">
                        <div class="text-3xl font-semibold {{$pago->version=="1"?'text-yellow-600':'text-green-600'}}">{{$pago->version=="1"?'Adelanto':'Cierre'}}</div>
                        <div class="text-gray-700 text-2xl">${{number_format($pago->total_pago,0)}}</div>
                        <div class="text-gray-700 text-base">Lineas Nuevas: {{$pago->nuevas+$pago->adiciones}}</div>
                        <div class="text-gray-700 text-base">Renovaciones: {{$pago->renovaciones}}</div>
                    </div>
                    <div class="w-1/3 flex items-center">
                        <center><a href="/estado_cuenta_{{!is_null($actor->detalles)?'distribuidor':'empleado'}}/{{$calculo->id}}/{{$actor->id}}/{{$pago->version}}"><i class="fas fa-balance-scale text-ttds text-7xl"></i></center></a>
                    </div>
                </div>
            </div> 
            @endif  
            @endforeach
        </div> <!--FIN CONTENIDO-->

    </div>
</x-app-layout>
