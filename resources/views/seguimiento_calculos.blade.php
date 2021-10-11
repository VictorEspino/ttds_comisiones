<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ttds leading-tight">
            {{ __('Calculos Registrados') }}
        </h2>
    </x-slot>
    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Historial de Calculos</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        <div class="flex flex-wrap">
            @foreach($calculos as $calculo)
            
            <div class="w-full md:w-1/3 p-4">
                <a href="{{route('detalle_calculo',['id'=>$calculo->id])}}">
                <div class="w-full flex flex-col justify-center rounded-lg shadow-xl p-5 bg-ttds-secundario-2 rounded-lg p-4 shadow-xl">
                    <div class="w-full text-xl text-yellow-500 font-bold flex justify-start"><i class="fas fa-th-large"></i></div>
                    <div class="w-full text-3xl text-gray-600 font-semibold flex justify-start">{{$meses[$calculo->periodo->mes-1]}} {{$calculo->periodo->año}}</div>
                    <div class="w-full text-xs text-gray-700 flex justify-start">De {{$calculo->periodo->fecha_inicio}} a {{$calculo->periodo->fecha_fin}}</div>
                    <div class="w-full text-lg text-gray-700 font-semibold flex justify-start">{{$calculo->descripcion}}</div>                
                </div>
            </a>
            </div>
            
            @endforeach
        </div>
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 bg-green-300 rounded-b-lg">
                <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
            </div>    
        @endif
    </div>
    
</x-app-layout>