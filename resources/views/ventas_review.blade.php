<x-app-layout>
    <x-slot name="header">
            {{ __('Consolidado de Ventas') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Consolidado de Ventas</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                <div class="w-full lg:w-1/2">
                    <form action="{{route('ventas_review')}}" class="">
                        <input class="w-2/3 lg:w-1/2 rounded p-1 border border-gray-300" type="text" name="query" value="{{$query}}" placeholder="Buscar Cliente/DN/Cuenta/Folio"> 
                        <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Buscar</button>
                    </form>
                </div>
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end text-xs">
                {{$registros->links()}}
                </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:space-x-5 flex items-start justify-center pt-2">
                <div id="tabla" class="w-full pt-5 flex flex-col"> <!--TABLA DE CONTENIDO-->
                    <div class="w-full flex justify-center pb-3"><span class="font-semibold text-sm text-gray-700">Registros de Venta</span></div>
                    <div class="w-full flex justify-center px-2 hidden md:inline md:flex md:w-full md:justify-center">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Cliente</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">DN</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Cuenta</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Folio</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Plan</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Renta</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg">Plazo</div>
                                </div>
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><a href="javascript:toogleForma({{$registro->id}})"><i class="far fa-edit"></i></a></div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->name}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->cliente}}</div> 
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->dn}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->cuenta}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->folio}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->plan}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->renta}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->plazo}}</div>
                                </div>
                                <?php $color=!$color; ?>
                                @endforeach
                        
                            </div>
                        </div>
                    </div>
                    <!--TABLA RESPONSIVE-->
                    <div class="md:hidden w-full flex justify-center px-2">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Cliente</div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">DN</div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg">Contrato</div>
                                </div>
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><a href="javascript:toogleForma({{$registro->id}})"><i class="far fa-edit"></i></a></div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->cliente}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->dn}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->folio}}</div>                                    
                                </div>
                                <?php $color=!$color; ?>
                                @endforeach
                        
                            </div>
                        </div>
                    </div>
                    
                </div><!--FIN DE TABLA -->
                <div id=forma class="hidden w-full lg:w-1/3 flex justify-center pt-8 flex-col"> <!--FORMA DETALLES-->
                    <div class="w-full bg-ttds-encabezado p-1 px-3 flex flex-col border-b border-gray-800 rounded-t"> <!--ENCABEZADO-->
                        <div class="w-full text-base font-semibold text-gray-100">Detalles</div>            
                    </div> <!--FIN ENCABEZADO-->
                    <div class="w-full flex flex-col border border-gray-300 bg-white rounded-b">
                        <form action="{{route('guarda_cambios_venta')}}" method="POST" id="forma_datos">
                            @csrf
                            <input class="hidden" type="text" name="id_venta" id="id_venta" value="{{old('id_venta')}}">
                            <input class="hidden" type="text" name="validado" id="validado" value="1">
                            <div class="w-full px-2">
                                <span class="text-xs text-ttds">Cliente</span><br>
                                <input class="w-full rounded p-1 border border-gray-300" type="text" name="cliente" value="{{old('cliente')}}" id="cliente">
                                @error('cliente')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror   
                            </div> 
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Tipo</span><br>
                                    <select class="w-full rounded p-1 border border-gray-300" name="tipo" id="tipo">
                                        <option value=""></option>
                                        <option value="ADICION" {{old('tipo')=='ADICION'?'selected':''}}>ADICION</option>
                                        <option value="NUEVA" {{old('tipo')=='NUEVA'?'selected':''}}>NUEVA</option>
                                        <option value="RENOVACION" {{old('tipo')=='RENOVACION'?'selected':''}}>RENOVACION</option>
                                    </select>
                                    @error('tipo')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Fecha</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="fecha_movimiento" value="{{old('fecha_movimiento')}}" id="fecha_movimiento" placeholder="YYYY-MM-DD">
                                    @error('fecha_movimiento')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div> 
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">DN</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="dn" value="{{old('dn')}}" id="dn">
                                    @error('dn')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Cuenta</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="cuenta" value="{{old('cuenta')}}" id="cuenta">
                                    @error('cuenta')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Folio</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="folio" value="{{old('folio')}}" id="folio">
                                    @error('folio')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div> 
                            <div class="w-full px-2">
                                <span class="text-xs text-ttds">Ciudad</span><br>
                                <input class="w-full rounded p-1 border border-gray-300" type="text" name="ciudad" value="{{old('ciudad')}}" id="ciudad">
                                @error('ciudad')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror   
                            </div> 
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Plan</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="plan" value="{{old('plan')}}" id="plan">
                                    @error('plan')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Equipo</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="equipo" value="{{old('equipo')}}" id="equipo">
                                    @error('equipo')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Propiedad</span><br>
                                    <select class="w-full rounded p-1 border border-gray-300" name="propiedad" id="propiedad">
                                        <option value=""></option>
                                        <option value="NUEVO" {{old('propiedad')=='NUEVO'?'selected':''}}>NUEVO</option>
                                        <option value="PROPIO" {{old('propiedad')=='PROPIO'?'selected':''}}>PROPIO</option>
                                    </select>
                                    @error('propiedad')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div> 
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Renta</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="renta" value="{{old('renta')}}" id="renta">
                                    @error('renta')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Plazo</span><br>
                                    <select class="w-full rounded p-1 border border-gray-300" name="plazo" id="plazo">
                                        <option value=""></option>
                                        <option value="12" {{old('plazo')=='12'?'selected':''}}>12</option>
                                        <option value="18" {{old('plazo')=='18'?'selected':''}}>18</option>
                                        <option value="24" {{old('plazo')=='24'?'selected':''}}>24</option>
                                        <option value="24" {{old('plazo')=='36'?'selected':''}}>36</option>
                                    </select>
                                    @error('plazo')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div>
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Descuento Multirenta (%)</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="descuento_multirenta" value="{{old('descuento_multirenta')}}" id="descuento_multirenta">
                                    @error('descuento_multirenta')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Afectacion Comision (%)</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="afectacion_comision" value="{{old('afectacion_comision')}}" id="afectacion_comision">
                                    @error('afectacion_comision')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div>
                            
                            <div class="w-full flex justify-center pt-6 pb-3 rounded-b">
                                @if(Auth::user()->perfil=='admin' || Auth::user()->perfil=='administrativo' || Auth::user()->perfil=='mesa' || Auth::user()->perfil=='gerente')
                                <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold" type="button" onClick="Save()">Guardar</button>
                                @endif
                                <button class="rounded p-1 border bg-red-500 hover:bg-red-700 text-gray-100 font-semibold" type="button" onClick="Cancel()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
 
        </div> <!-- FIN DEL CONTENIDO -->
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 bg-green-300 rounded-b-lg">
                <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
            </div>    
        @endif
    </div> <!--DIV PRINCIPAL -->
    <script>
        function toogleForma(id)
        {
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full")
            document.getElementById('tabla').classList.add("lg:w-2/3");
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //document.getElementById("txtHint").innerHTML = this.responseText;
                    if(this.responseText!='')
                    {
                        respuesta=JSON.parse(this.response);
                        console.log(respuesta);
                        document.getElementById("id_venta").value=respuesta.id;
                        document.getElementById("cliente").value=respuesta.cliente;
                        document.getElementById("fecha_movimiento").value=respuesta.fecha;
                        document.getElementById("tipo").value=respuesta.tipo;
                        document.getElementById("dn").value=respuesta.dn;
                        document.getElementById("cuenta").value=respuesta.cuenta;
                        document.getElementById("folio").value=respuesta.folio;
                        document.getElementById("ciudad").value=respuesta.ciudad;
                        document.getElementById("plan").value=respuesta.plan;
                        document.getElementById("equipo").value=respuesta.equipo;
                        document.getElementById("propiedad").value=respuesta.propiedad;
                        document.getElementById("renta").value=respuesta.renta;
                        document.getElementById("plazo").value=respuesta.plazo;
                        document.getElementById("descuento_multirenta").value=respuesta.descuento_multirenta;
                        document.getElementById("afectacion_comision").value=respuesta.afectacion_comision;
 
                    }
                    else
                    {
                        document.getElementById("nombre").value='';
                        alert("Error al consultar la base de datos, intente nuevamente!");
                    }
    
                }
            };  
            xmlhttp.open("GET", "/ventas_consulta/" + id, true);
            xmlhttp.send();
        }
        function Save()
        {
            document.getElementById('forma_datos').submit();
        }
        function Cancel()
        {
            document.getElementById('forma').style.display="none";
            document.getElementById('tabla').classList.remove("lg:w-2/3");
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full");
        }
    <?php
        if (!$errors->isEmpty())
        {
    ?>  
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full")
            document.getElementById('tabla').classList.add("lg:w-2/3");
    <?php
        }
    ?>
    </script>
</x-app-layout>