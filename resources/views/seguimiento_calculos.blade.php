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
        @foreach($calculos as $calculo)
        <div class="w-full flex flex-col justify-center p-10">
            <div class="w-full text-lg font-semibold flex justify-center">{{$calculo->descripcion}}</div>
            <div class="w-full text-sm flex justify-center">De {{$calculo->fecha_inicio}} a {{$calculo->fecha_fin}}</div>
            <div class="w-full text-sm flex justify-center p-5"><a href="{{route('detalle_calculo',['id'=>$calculo->id])}}">Detalles</a></div>
            <div class="w-full text-sm flex justify-center p-5"><a href="{{route('acciones_distribuidores_calculo',['id'=>$calculo->id])}}">Acciones y Complementos</a></div>
        </div>
        @endforeach
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 bg-green-300 rounded-b-lg">
                <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
            </div>    
        @endif
    </div>
</x-app-layout>