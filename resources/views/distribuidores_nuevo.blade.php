<x-app-layout>
    <x-slot name="header">
            {{ __('Nuevo Distribuidor') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Nuevo Registo</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        <form method="post" action="{{route('distribuidores_nuevo')}}">
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
            <div class="w-full px-2 pt-2">
                <span class="text-sm font-semibold text-gray-700">Comision Activaciones (Factor)</span>
            </div>
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-1/3">
                    <span class="text-xs text-ttds">24 meses</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="a_24" value="{{old('a_24')}}" id="a_24">
                    @error('a_24')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">18 meses</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="a_18" value="{{old('a_18')}}" id="a_18">
                    @error('a_18')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">12 meses</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="a_12" value="{{old('a_12')}}" id="a_12">
                    @error('a_12')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
            </div> 
            <div class="w-full px-2 pt-2">
                <span class="text-sm font-semibold text-gray-700">Comision Renovaciones (Factor)</span>
            </div>
            <div class="w-full px-2 flex flex-row space-x-1">
                <div class="w-1/3">
                    <span class="text-xs text-ttds">24 meses</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="r_24" value="{{old('r_24')}}" id="r_24">
                    @error('r_24')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">18 meses</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="r_18" value="{{old('r_18')}}" id="r_18">
                    @error('r_18')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">12 meses</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="r_12" value="{{old('r_12')}}" id="r_12">
                    @error('r_12')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
            </div>
            <div class="w-full px-2 pt-2">
                <span class="text-sm font-semibold text-gray-700">Incluir calculos de :</span>
            </div>
            <div class="w-full px-2 flex flex-row space-x-1 justify-between">
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Bono</span><br>
                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="bono" id="bono" {{old('bono')=='on'?'checked':''}}>
                    @error('bono')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Residual</span><br>
                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="residual" id="residual" {{old('residual')=='on'?'checked':''}}>
                    <input size=3 class="rounded p-1 border border-gray-300" type="text" name="porcentaje_residual" value="{{old('porcentaje_residual')}}" id="porcentaje_residual">%
                    @error('porcentaje_residual')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Adelantos</span><br>
                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="adelanto" id="adelanto" {{old('adelanto')=='on'?'checked':''}}>
                    <input size=3 class="rounded p-1 border border-gray-300" type="text" name="porcentaje_adelanto" value="{{old('porcentaje_adelanto')}}" id="porcentaje_adelanto">%
                    @error('porcentaje_adelanto')
                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror   
                </div>
                <div class="w-1/3">
                    <span class="text-xs text-ttds">Emite Factura</span><br>
                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="factura" id="factura" {{old('factura')=='on'?'checked':''}}>
                    @error('factura')
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
