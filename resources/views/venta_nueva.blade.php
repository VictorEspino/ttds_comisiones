<x-app-layout>
    <x-slot name="header">
            {{ __('Registrar Venta') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Nuevo Registo</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        <form method="post" action="{{route('venta_nueva')}}">
            @csrf
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col space-x-0 lg:flex-row lg:space-x-2">
                
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Fecha movimiento</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="fecha_movimiento" value="{{old('fecha_movimiento')}}" placeholder="YYYY-MM-DD" id="fecha_movimiento">
                    @error('fecha_movimiento')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/2">
                    <span class="text-xs text-ttds">Nombre Cliente</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="nombre_cliente" value="{{old('nombre_cliente')}}" id="nombre_cliente">
                    @error('nombre_cliente')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">DN</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="dn" value="{{old('dn')}}">
                    @error('dn')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
            </div>
            <div class="w-full flex flex-col space-x-0 lg:flex-row lg:space-x-2">
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Cuenta</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="cuenta" value="{{old('cuenta')}}" id="cuenta">
                    @error('cuenta')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Tipo</span><br>
                                       
                    <select class="w-full rounded p-1 border border-gray-300" name="tipo" id="tipo">
                        <option value="" class=""></option>  
                        <option value="ADICION" class="" {{old('tipo')=="ADICION"?'selected':''}}>ADICION</option>                      
                        <option value="NUEVA" class="" {{old('tipo')=="NUEVA"?'selected':''}}>NUEVA</option>
                        <option value="RENOVACION" class="" {{old('tipo')=="RENOVACION"?'selected':''}}>RENOVACION</option>
                    </select> 
                    
                    @error('tipo')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                      
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Folio</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="folio" value="{{old('folio')}}" id="folio">
                    @error('folio')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Ciudad</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="ciudad" value="{{old('ciudad')}}" id="ciudad">
                    @error('ciudad')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
            </div>
            <div class="w-full flex flex-col space-x-0 lg:flex-row lg:space-x-2">
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Plan</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="plan" value="{{old('plan')}}">
                    @error('plan')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror    
                </div>   
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Renta</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="renta" value="{{old('renta')}}">
                    @error('renta')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Equipo</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="equipo" value="{{old('equipo')}}">
                    @error('equipo')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Plazo</span><br>
                    <select class="w-full rounded p-1 border border-gray-300" name="plazo" id="plazo">
                        <option value="" class=""></option>                        
                        <option value="12" class="" {{old('plazo')=="12"?'selected':''}}>12</option>
                        <option value="18" class="" {{old('plazo')=="18"?'selected':''}}>18</option>
                        <option value="24" class="" {{old('plazo')=="24"?'selected':''}}>24</option>
                    </select> 
                    @error('plazo')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>             
            </div>
            <div class="w-full flex flex-col space-x-0 lg:flex-row lg:space-x-2">
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Descuento multirenta</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="descuento_multirenta" value="{{old('descuento_multirenta')}}" id="descuento_multirenta"> %
                    @error('descuento_multirenta')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror    
                </div>                
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Afectacion de comision</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="afectacion_comision" value="{{old('afectacion_comision')}}" id="afectacion_comision"> %
                    @error('afectacion_comision')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Contrato</span><br>
                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="contrato" value="{{old('contrato')}}">
                    @error('contrato')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>
                <div class="w-full lg:w-1/4">
                    <span class="text-xs text-ttds">Propiedad equipo</span><br>
                                       
                    <select class="w-full rounded p-1 border border-gray-300" name="propiedad" id="propiedad">
                        <option value="" class=""></option>                        
                        <option value="NUEVO" class="" {{old('propiedad')=="NUEVO"?'selected':''}}>NUEVO</option>
                        <option value="PROPIO" class="" {{old('propiedad')=="PROPIO"?'selected':''}}>PROPIO</option>
                    </select> 
                    
                    @error('propiedad')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                      
                </div>
            </div>            
        </div> <!--FIN CONTENIDO-->
        <div class="w-full flex justify-center py-4 bg-ttds-secundario rounded-b">
            <button class="rounded p-1 border bg-ttds hover:bg-ttds-hover text-gray-100 font-semibold">Guardar</button>
        </div>
        </form>
    </div>
<?php
    if(isset($_GET['complete']))
    {
?>
    <script>
        document.getElementById('cuenta').value="{{$_GET['cuenta']}}";
        document.getElementById('nombre_cliente').value="{{$_GET['nombre_cliente']}}";
        document.getElementById('tipo').value="{{$_GET['tipo']}}";
        document.getElementById('propiedad').value="{{$_GET['propiedad']}}";
        document.getElementById('descuento_multirenta').value="{{$_GET['descuento_multirenta']}}";
        document.getElementById('afectacion_comision').value="{{$_GET['afectacion_comision']}}";
        document.getElementById('fecha_movimiento').value="{{$_GET['fecha_movimiento']}}";
    </script>
<?php
    }
?>
</x-app-layout>
