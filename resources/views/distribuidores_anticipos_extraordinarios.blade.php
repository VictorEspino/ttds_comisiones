<x-app-layout>
    <x-slot name="header">
            {{ __('Anticipos extraordinarios') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Distribuidores - Anticipo</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                <div class="w-full lg:w-1/2">
                    <?php
                    $ruta=route('distribuidores_anticipos_extraordinarios');
                    ?>
                    <form action="{{$ruta}}" class="">
                        <input class="w-2/3 lg:w-1/2 rounded p-1 border border-gray-300" type="text" name="query" value="{{$query}}" placeholder="Buscar distribuidor"> 
                        <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Buscar</button>
                    </form>
                </div>
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end text-xs">
                {{$registros->links()}}
                </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:space-x-5 flex items-start justify-center pt-2">
                <div id="tabla" class="w-full pt-5 flex flex-col"> <!--TABLA DE CONTENIDO-->
                    <div class="w-full flex justify-center pb-3"><span class="font-semibold text-sm text-gray-700">Registros Distribuidores</span></div>
                    <div class="w-full flex justify-center px-2 md:inline md:flex md:w-full md:justify-center">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Distribuidor</div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm">Region</div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg">Registrar anticipo</div>
                                    
                                </div>
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->numero_distribuidor}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->nombre}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->region}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth text-green-700 {{$color?'bg-gray-100':'bg-white'}}">
                                        <center><a href="javascript:toogleForma({{$registro->user_id}},'{{$registro->nombre}}')"><i class="far fa-money-bill-alt"></i></center></a>
                                    </div>
                                                                        
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
                        <form action="{{route('distribuidores_anticipos_extraordinarios')}}" method="POST">
                            @csrf
                            <input class="hidden" type="text" name="id_distribuidor" id="id_distribuidor" value="{{old('id_distribuidor')}}">
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full">
                                    <input class="w-full rounded p-1 border border-white" type="text" value="{{old('nombre')}}" id="nombre" name="nombre">
                                </div>
                            </div>
                            <div class="w-full text-xs text-ttds p-2">
                                Se refiere al periodo de ventas donde se cobrara el anticipo
                            </div>
                            <div class="w-full px-2 flex flex-row space-x-3">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Mes</span><br>
                                    <select class="w-full rounded p-1 border border-gray-300" name="mes"  id="mes">
                                        <option value=''></option>
                                        <option value='1' {{old('mes')=="1"?'selected':''}}>enero</option>
                                        <option value='2' {{old('mes')=="2"?'selected':''}}>febrero</option>
                                        <option value='3' {{old('mes')=="3"?'selected':''}}>marzo</option>
                                        <option value='4' {{old('mes')=="4"?'selected':''}}>abril</option>
                                        <option value='5' {{old('mes')=="5"?'selected':''}}>mayo</option>
                                        <option value='6' {{old('mes')=="6"?'selected':''}}>junio</option>
                                        <option value='7' {{old('mes')=="7"?'selected':''}}>julio</option>
                                        <option value='8' {{old('mes')=="8"?'selected':''}}>agosto</option>
                                        <option value='9' {{old('mes')=="9"?'selected':''}}>septiembre</option>
                                        <option value='10' {{old('mes')=="10"?'selected':''}}>octubre</option>
                                        <option value='11' {{old('mes')=="11"?'selected':''}}>noviembre</option>
                                        <option value='12' {{old('mes')=="12"?'selected':''}}>diciembre</option>
                                    </select>
                                    @error('mes')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror 
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Año</span><br>
                                    <select class="w-full rounded p-1 border border-gray-300" name="año" id="año">
                                        <option value=''></option>
                                        @foreach ($años as $año)
                                            <option value="{{$año->valor}}" {{old('año')==$año->valor?'selected':''}}>{{$año->valor}}</option>
                                        @endforeach
                                    </select>
                                    @error('año')
                                    <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror 
                                </div>
                            </div>
                                
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full">
                                    <span class="text-xs text-ttds">Monto Anticipo</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" value="{{old('anticipo')}}" id="anticipo" name="anticipo">
                                    @error('anticipo')
                                        <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full">
                                    <span class="text-xs text-ttds">Descripcion</span><br>
                                    <input class="w-full rounded p-1 border border-gray-300" type="text" value="{{old('descripcion')}}" id="descripcion" name="descripcion">
                                    @error('descripcion')
                                        <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full flex justify-center pt-6 pb-3 rounded-b">
                                <button class="rounded p-1 border bg-ttds hover:bg-ttds-hover text-gray-100 font-semibold">Guardar</button>
                                <button class="rounded p-1 border bg-red-500 hover:bg-red-700 text-gray-100 font-semibold" type="button" onClick="cerrarForma()">Cancelar</button>
                            </div>
                            <div id="no_aplicados" class="w-full text-sm px-5">
                            </div>    
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- FIN DEL CONTENIDO -->
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 {{substr(session('status'),0,2)=='OK'?'bg-green-300':'bg-red-300'}} rounded-b-lg" id="notas">
                <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
            </div>    
        @else
            <div id="notas"></div>
        @endif
    </div> <!--DIV PRINCIPAL -->
    <script>
        function cerrarForma()
        {
            document.getElementById('forma').style.display="none";
            document.getElementById('tabla').classList.remove("lg:w-1/2");
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full");
            document.getElementById('notas').style.display="none";
            
        }
        function toogleForma(user_id,nombre)
        {
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full")
            document.getElementById('tabla').classList.add("lg:w-1/2");
            document.getElementById('id_distribuidor').value=user_id;
            document.getElementById('nombre').value=nombre;
            document.getElementById('mes').value="";
            document.getElementById('año').value="";
            document.getElementById('anticipo').value="";
            document.getElementById('descripcion').value="";
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //document.getElementById("txtHint").innerHTML = this.responseText;
                    if(this.responseText!='')
                    {
                        respuesta=JSON.parse(this.response);
                        console.log(respuesta);
                        var formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',

                        // These options are needed to round to whole numbers if that's what you want.
                        //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                        //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                        });

                        formatter.format(2500); /* $2,500.00 */ 
                        cadena="Anticipos previos por cobrar<br><div class='table'><div class='table-row'><div class='table-cell bg-ttds px-3'></div><div class='table-cell bg-ttds text-gray-200 px-3'>Periodo</div><div class='table-cell bg-ttds text-gray-200 px-3'>Anticipo</div><div class='table-cell bg-ttds text-gray-200 px-3'>Descripcion</div></div>";
                        respuesta.forEach(element => cadena=cadena+"<div class='table-row'>"+"<div class='table-cell text-red-700 font-bold px-3'><a href='javascript:BorrarAnticipo("+element['id']+","+element['anticipo']+","+user_id+",\""+nombre+"\")'><i class='fas fa-trash'></i></a></div><div class='table-cell px-3'>"+element['periodo']['descripcion']+"</div><div class='table-cell px-3'>"+formatter.format(element['anticipo'])+"</div><div class='table-cell px-3'>"+element['descripcion']+"</div></div>");
                        cadena=cadena+"</div><br>"
                        console.log(cadena);
                        document.getElementById('no_aplicados').innerHTML=cadena;
                    }
                    else
                    {
                        alert("Error al consultar la base de datos, intente nuevamente!");
                    }
    
                }
            };  
            xmlhttp.open("GET", "/anticipos_extraordinarios_consulta/" + user_id, true);
            xmlhttp.send();
        }
        function BorrarAnticipo(id,anticipo,user_id,nombre)
        {
            if(confirm("Esta seguro que desea borrar este anticipo de "+anticipo+" para "+nombre))
            {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        //document.getElementById("txtHint").innerHTML = this.responseText;
                        if(this.responseText=='OK')
                        {
                            alert('refrescando');
                            toogleForma(user_id,nombre);
                        }
                    }
                }
                xmlhttp.open("GET", "/anticipos_extraordinarios_borrar/" + id, true);
                xmlhttp.send();
            }
        }
    <?php
        if (!$errors->isEmpty())
        {
    ?>  
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full")
            document.getElementById('tabla').classList.add("lg:w-1/2");
    <?php
        }
    ?>
    </script>
</x-app-layout>