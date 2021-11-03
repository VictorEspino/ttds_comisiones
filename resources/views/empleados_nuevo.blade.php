<x-app-layout>
    <x-slot name="header">
            {{ __('Nuevo empleado ventas') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Nuevo Registo</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        <form method="post" action="{{route('empleados_nuevo')}}">
            @csrf
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-2/3">
                    <span class="text-xs text-ttds">Nombre</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="nombre" value="{{old('nombre')}}" id="nombre">
                    @error('nombre')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Region</span><br>
                    <select class="w-full rounded p-1 border border-gray-300" name="region" id="region">
                        <option value=""></option>
                        <option value="BAJIO" {{old('region')=='BAJIO'?'selected':''}}>BAJIO</option>
                        <option value="CENTRO" {{old('region')=='CENTRO'?'selected':''}}>CENTRO</option>
                        <option value="NORTE" {{old('region')=='NORTE'?'selected':''}}>NORTE</option>
                        <option value="SUR" {{old('region')=='SUR'?'selected':''}}>SUR</option>
                    </select>
                    @error('region')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror 
                </div>                
            </div>
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Fecha Ingreso</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="date" name="fecha_ingreso" value="{{old('fecha_ingreso')}}" id="fecha_ingreso">
                    @error('fecha_ingreso')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror 
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Puesto</span><br>
                    <select class="w-full rounded p-1 border border-gray-300" name="puesto" id="puesto">
                        <option value=""></option>
                        <option value="EJECUTIVO" {{old('puesto')=='EJECUTIVO'?'selected':''}}>EJECUTIVO</option>
                        <option value="GERENTE" {{old('puesto')=='GERENTE'?'selected':''}}>GERENTE</option>
                    </select>
                    @error('puesto')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror 
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Estatus</span><br>
                    <select class="w-full rounded p-1 border border-gray-300" name="estatus" id="estatus">
                        <option value="1" {{old('estatus')=='1'?'selected':''}}>ACTIVO</option>
                        <option value="0" {{old('estatus')=='0'?'selected':''}}>INACTIVO</option>
                    </select>
                    @error('estatus')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror 
                </div>                
            </div>      
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-full">
                    <span class="text-xs text-ttds">Supervisor</span><br>
                    <select class="w-full rounded p-1 border border-gray-300" name="supervisor" id="supervisor">
                        <option value=""></option>
                        @foreach ($supervisores as $supervisor)
                            <option value="{{$supervisor->user_id}}" {{old('supervisor')==$supervisor->user_id?'selected':''}}>{{$supervisor->nombre}}</option>    
                        @endforeach
                        </select>
                    @error('supervisor')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror 
                </div>
            </div>
            <div class="w-full px-2 pt-2">
                <span class="text-sm font-semibold text-gray-700">Cuotas</span>
            </div>
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-1/2">
                    <span class="text-xs text-ttds">Cuota Total (nuevas+renovaciones)</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="cuota_unidades" value="{{old('cuota_unidades')}}" id="cuota_unidades">
                    @error('cuota_unidades')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/2">
                    <span class="text-xs text-ttds">Cuota Nuevas</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="aduana_nuevas" value="{{old('aduana_nuevas')}}" id="cuota_unidades">
                    @error('aduana_nuevas')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
            </div>     
        </div> <!--FIN CONTENIDO-->
        <div class="w-full flex justify-center py-4 bg-ttds-secundario rounded-b">
            <button class="rounded p-1 border bg-ttds hover:bg-ttds-hover text-gray-100 font-semibold">Guardar</button>
        </div>
        </form>
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 bg-green-300 rounded-b-lg">
                <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
            </div>    
        @endif
    </div>
</x-app-layout>
