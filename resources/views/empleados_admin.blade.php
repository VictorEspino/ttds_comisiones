<x-app-layout>
    <x-slot name="header">
            {{ __('Administracion Plantilla') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Plantilla Ventas</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                <div class="w-full lg:w-1/2">
                    <form action="{{route('empleados_admin')}}" class="">
                        <input class="w-2/3 lg:w-1/2 rounded p-1 border border-gray-300" type="text" name="query" value="{{$query}}" placeholder="Buscar"> 
                        <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Buscar</button>
                    </form>
                </div>
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end text-xs">
                {{$registros->links()}}
                </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:space-x-5 flex items-start justify-center pt-2">
                <div id="tabla" class="w-full pt-5 flex flex-col"> <!--TABLA DE CONTENIDO-->
                    <div class="w-full flex justify-center pb-3"><span class="font-semibold text-sm text-gray-700">Registros plantilla interna</span></div>
                    <div class="w-full flex justify-center px-2 hidden md:inline md:flex md:w-full md:justify-center">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Puesto</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Cuota<br>Unidades</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Minimo<br>nuevas</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Fecha<br>Ingreso</div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg">Activo</div>
                                </div>                                
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><a href="javascript:toogleForma({{$registro->id}})"><i class="far fa-edit"></i> {{$registro->numero_empleado}}</a></div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->nombre}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->region}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->puesto}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>{{$registro->cuota_unidades}}</center></div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>{{$registro->aduana_nuevas}}</center></div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->fecha_ingreso}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>{!!$registro->activo?'<i class="text-green-500 fas fa-check-circle"></i>':'<i class="text-red-500 fas fa-times-circle"></i>'!!}</center></div>
                                    

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
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Nombre</div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg">Region</div>
                                </div>
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><a href="javascript:toogleForma({{$registro->id}})"><i class="far fa-edit"></i></a></div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->nombre}}</div>
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->region}}</div>                                    
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
                        <form action="{{route('guarda_cambios_empleado')}}" method="POST">
                            @csrf
                            <input class="hidden" type="text" name="id_empleado" id="id_empleado" value="{{old('id_empleado')}}">
                            <input class="hidden" type="text" name="id_user" id="id_user" value="{{old('id_user')}}">
                            <div class="w-full px-2">
                                <span class="text-xs text-ttds">Nombre</span><br>
                                <input class="w-full rounded p-1 border border-gray-300" type="text" name="nombre" value="{{old('nombre')}}" id="nombre">
                                @error('nombre')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror   
                            </div> 
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/2">
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
                                <div class="w-1/2">
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
                            </div> 
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Estatus</span><br>
                                    <select class="w-full rounded p-1 border border-gray-300" name="activo" id="activo">
                                        <option value=""></option>
                                        <option value="1" {{old('activo')=='1'?'selected':''}}>ACTIVO</option>
                                        <option value="0" {{old('activo')=='0'?'selected':''}}>INACTIVO</option>
                                    </select>
                                    @error('activo')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Fecha Ingreso</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="date" name="fecha_ingreso" value="{{old('fecha_ingreso')}}" id="fecha_ingreso">
                                    @error('fecha_ingreso')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div>
                            <div class="w-full px-2">
                                <span class="text-xs text-ttds">Supervisor</span><br>
                                <select class="w-full rounded p-1 border border-gray-300" name="supervisor" id="supervisor">
                                    <option value=""></option>
                                    @foreach ($supervisores as $supervisor)
                                    <option value="{{$supervisor->user_id}}">{{$supervisor->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('supervisor')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror   
                            </div> 
                            <div class="w-full px-2 pt-2">
                                <span class="text-sm font-semibold text-gray-700">Comisiones</span>
                            </div>
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Cuota unidades</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="cuota_unidades" value="{{old('cuota_unidades')}}" id="cuota_unidades">
                                    @error('cuota_unidades')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Minimo nuevas</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" name="aduana_nuevas" value="{{old('aduana_nuevas')}}" id="aduana_nuevas">
                                    @error('aduana_nuevas')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div> 
                            <div class="w-full flex justify-center pt-6 pb-3 rounded-b">
                                @if(Auth::user()->perfil=="admin")
                                <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Guardar</button>
                                @endif
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
                        document.getElementById("id_empleado").value=respuesta.id;
                        document.getElementById("nombre").value=respuesta.nombre;
                        document.getElementById("region").value=respuesta.region;
                        document.getElementById("puesto").value=respuesta.puesto;
                        document.getElementById("activo").value=respuesta.activo;
                        document.getElementById("fecha_ingreso").value=respuesta.fecha_ingreso;
                        document.getElementById("cuota_unidades").value=respuesta.cuota_unidades;
                        document.getElementById("aduana_nuevas").value=respuesta.aduana_nuevas;
                        document.getElementById("supervisor").value=respuesta.user.supervisor;
                        document.getElementById("id_user").value=respuesta.user.id;
                    }
                    else
                    {
                        document.getElementById("nombre").value='';
                        alert("Error al consultar la base de datos, intente nuevamente!");
                    }
    
                }
            };  
            xmlhttp.open("GET", "/empleados_consulta/" + id, true);
            xmlhttp.send();
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