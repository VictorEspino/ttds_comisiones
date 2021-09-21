<x-app-layout>
    <x-slot name="header">
            {{ __('Nuevo Calculo') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Nuevo Registo</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        <form method="post" action="{{route('calculo_nuevo')}}">
            @csrf
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-2/3">
                    <span class="text-xs text-ttds">Descripcion</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="descripcion" value="{{old('descripcion')}}" id="descripcion">
                    @error('descripcion')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Tipo</span><br>
                    <select class="w-full rounded p-1 border border-gray-300" name="tipo" id="tipo">
                        <option value=""></option>
                        <option value="2" {{old('tipo')=='2'?'selected':''}}>Cierre Mensual</option>
                        <option value="1" {{old('tipo')=='1'?'selected':''}}>Adelanto Semanal</option>
                    </select>
                    @error('tipo')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror 
                </div>
            </div> 
            <div class="w-full px-2 pt-2">
                <span class="text-sm font-semibold text-gray-700">Rangos de Calculo</span>
            </div>
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-1/4">
                    <span class="text-xs text-ttds">Fecha Inicio</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="fecha_inicio" value="{{old('fecha_inicio')}}" id="fecha_inicio">
                    @error('fecha_inicio')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/4">
                    <span class="text-xs text-ttds">Fecha Fin</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="fecha_fin" value="{{old('fecha_fin')}}" id="fecha_fin">
                    @error('fecha_fin')
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
