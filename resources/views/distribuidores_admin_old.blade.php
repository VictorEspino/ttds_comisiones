<x-app-layout>
    <x-slot name="header">
            {{ __('Administracion Distribuidores') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
    <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Distribuidores</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                <div class="w-full lg:w-1/2">
                    <form action="{{route('distribuidores_admin')}}" class="">
                        <input class="w-2/3 lg:w-1/2 rounded p-1 border border-gray-300" type="text" name="query" value="{{$query}}" placeholder="Buscar distribuidor"> 
                        <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Buscar</button>
                    </form>
                </div>
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end text-xs">
                {{$registros->links()}}
                </div>
            </div>
            <div class="flex flex-row space-x-5 flex items-start pt-2 overflow-auto md:overflow-scroll">
                <div id="tabla" class="w-full flex justify-centerpt-5 flex-col"> <!--TABLA DE CONTENIDO-->
                    <div class="w-full flex justify-center pb-3"><span class="font-semibold text-sm text-gray-700">Registros Distribuidores</span></div>
                    <div class="w-full flex justify-center px-2">
                    <table class="table-fixed">
                        <tr class="">
                            <td rowspan=2 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></td>
                            <td rowspan=2 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Distribuidor</td>
                            <td rowspan=2 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-8 text-sm"><center>Region</td>
                            <td colspan=3 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Activaciones</td>
                            <td colspan=3 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Renovaciones</td>
                            <td rowspan=2 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Bono</td>
                            <td colspan=2 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Residual</td>
                            <td colspan=2 class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Adelanto</td>
                        </tr>
                        <tr class="">
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>24</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>18</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>12</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>24</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>18</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>12</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Aplica</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>%</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Aplica</td>
                            <td class="border border-gray-300 font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>%</td>
                        </tr>
                    <?php
                        $color=false;
                        foreach($registros as $registro)
                        {
                    ?>
                        <tr class="">
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center><a href="javascript:toogleForma({{$registro->id}})"><i class="far fa-edit"></i>{{$registro->numero_distribuidor}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs">{{$registro->nombre}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs">{{$registro->region}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->a_24}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->a_18}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->a_12}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->r_24}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->r_18}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->r_12}}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-sm font-bold text-green-700"><center>{!!$registro->bono?'<i class="far fa-check-circle"></i>':''!!}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-sm font-bold text-green-700"><center>{!!$registro->residual?'<i class="far fa-check-circle"></i>':''!!}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->porcentaje_residual}}%</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-sm font-bold text-green-700"><center>{!!$registro->adelanto?'<i class="far fa-check-circle"></i>':''!!}</td>
                            <td class="border border-gray-300 font-light {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 text-xs"><center>{{$registro->porcentaje_adelanto}}%</td>
                        </tr>
                    <?php
                        $color=!$color;
                        }
                    ?>
                    </table>
                    </div>
                </div><!--FIN DE TABLA -->
                <div id=forma class="hidden w-1/3 flex justify-center pt-10 flex-col"> <!--FORMA DETALLES-->
                    <div class="w-full bg-ttds-encabezado p-1 px-3 flex flex-col border-b border-gray-800 rounded-t"> <!--ENCABEZADO-->
                        <div class="w-full text-lg font-semibold text-gray-100">Detalles</div>            
                    </div> <!--FIN ENCABEZADO-->
                    <div class="w-full flex flex-col border border-gray-300 bg-white rounded-b">
                        <form action="{{route('guarda_cambios_distribuidor')}}" method="POST">
                            @csrf
                            <input class="hidden" type="text" name="id_distribuidor" id="id_distribuidor" value="{{old('id_distribuidor')}}">
                            <div class="w-full px-2">
                                <span class="text-xs text-ttds">Nombre</span><br>
                                <input class="w-full rounded p-1 border border-gray-300" type="text" name="nombre" value="{{old('nombre')}}" id="nombre">
                                @error('cuenta')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror   
                            </div> 
                            <div class="w-full px-2">
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
                            <div class="w-full px-2 flex flex-row space-x-1 justify-between">
                                <div class="w-1/5">
                                    <span class="text-xs text-ttds">Bono</span><br>
                                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="bono" id="bono" {{old('bono')=='on'?'checked':''}}>
                                    @error('bono')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-2/5">
                                    <span class="text-xs text-ttds">Residual</span><br>
                                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="residual" id="residual" {{old('residual')=='on'?'checked':''}}>
                                    <input size=3 class="rounded p-1 border border-gray-300" type="text" name="porcentaje_residual" value="{{old('porcentaje_residual')}}" id="porcentaje_residual">%
                                    @error('porcentaje_residual')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                                <div class="w-2/5">
                                    <span class="text-xs text-ttds">Adelantos</span><br>
                                    <input class="rounded p-1 border border-gray-300" type="checkbox" name="adelanto" id="adelanto" {{old('adelanto')=='on'?'checked':''}}>
                                    <input size=3 class="rounded p-1 border border-gray-300" type="text" name="porcentaje_adelanto" value="{{old('porcentaje_adelanto')}}" id="porcentaje_adelanto">%
                                    @error('porcentaje_adelanto')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div>
                            <div class="w-full flex justify-center pt-6 pb-3 rounded-b">
                                <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Guardar</button>
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
            document.getElementById('tabla').classList.add("w-2/3");
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //document.getElementById("txtHint").innerHTML = this.responseText;
                    if(this.responseText!='')
                    {
                        respuesta=JSON.parse(this.response);
                        console.log(respuesta);
                        document.getElementById("id_distribuidor").value=respuesta.id;
                        document.getElementById("nombre").value=respuesta.nombre;
                        document.getElementById("region").value=respuesta.region;
                        document.getElementById("a_24").value=respuesta.a_24;
                        document.getElementById("a_18").value=respuesta.a_18;
                        document.getElementById("a_12").value=respuesta.a_12;
                        document.getElementById("r_24").value=respuesta.r_24;
                        document.getElementById("r_18").value=respuesta.r_18;
                        document.getElementById("r_12").value=respuesta.r_12;
                        document.getElementById("bono").checked=respuesta.bono;
                        document.getElementById("residual").checked=respuesta.residual;
                        document.getElementById("adelanto").checked=respuesta.adelanto;
                        document.getElementById("porcentaje_residual").value=respuesta.porcentaje_residual;
                        document.getElementById("porcentaje_adelanto").value=respuesta.porcentaje_adelanto;
 
                    }
                    else
                    {
                        document.getElementById("nombre").value='';
                        alert("Error al consultar la base de datos, intente nuevamente!");
                    }
    
                }
            };  
            xmlhttp.open("GET", "/distribuidores_consulta/" + id, true);
            xmlhttp.send();
        }
    <?php
        if (!$errors->isEmpty())
        {
    ?>  
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-2/3");
    <?php
        }
    ?>
    </script>
</x-app-layout>