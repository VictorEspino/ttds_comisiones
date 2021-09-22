<x-app-layout>
    <x-slot name="header">
            {{ __('Resultados Calculo') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-xl font-bold text-gray-100">Pagos</div>
            <div class="w-full text-lg font-semibold text-gray-100">{{$calculo->descripcion}}</div>            
            <div class="w-full text-xs font-semibold text-gray-100">De {{$calculo->fecha_inicio}} a {{$calculo->fecha_fin}}</div>            
        </div> <!--FIN ENCABEZADO-->
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                <div class="w-full lg:w-1/2">
                    <?php
                    $ruta=route('acciones_distribuidores_calculo',['id'=>$calculo->id]);
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
                    <div class="w-full flex justify-center pb-3"><span class="font-semibold text-sm text-gray-700">Pagos resultado</span></div>
                    <div class="w-full flex justify-center px-2 hidden md:inline md:flex md:w-full md:justify-center">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></div>
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Estado de<br>Cuenta</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Pago</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Anticipos<br>Previos</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Comisiones<br>Pendientes</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg"><center>Anticipo<br>Comisiones Pendientes</center></div>
                                </div>
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->numero_distribuidor}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->nombre}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth text-ttds {{$color?'bg-gray-100':'bg-white'}}">
                                        <center><a href="/estado_cuenta_distribuidor/{{$calculo->id}}/{{$registro->id}}"><i class="fas fa-balance-scale"></i></center></a>
                                    </div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>${{number_format($registro->total_pago,0)}}</center></div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>${{number_format($registro->anticipos_extraordinarios,0)}}</center></div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>${{number_format($registro->nuevas_comision_no_pago+$registro->adiciones_comision_no_pago+$registro->renovaciones_comision_no_pago+$registro->nuevas_bono_no_pago+$registro->adiciones_bono_no_pago+$registro->renovaciones_bono_no_pago,0)}}</center></div>
                                    <div class="table-cell border-r border-l border-b border-gray-300 font-ligth text-green-700 {{$color?'bg-gray-100':'bg-white'}}">
                                        <center>
                                            ${{number_format($registro->anticipo_no_pago,0)}}
                                            <a href="javascript:toogleForma({{$calculo->id}},{{$registro->id}});"><i class="far fa-money-bill-alt"></i></a>
                                        </center>   
                                     </div>
                                    
                                </div>
                                <?php $color=!$color; ?>
                                @endforeach
                        
                            </div>
                        </div>
                    </div>  
                    <div class="md:hidden w-full flex justify-center px-2">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Estado de<br>Cuenta</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Comisiones<br>Pendientes</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg"><center>Anticipo<br>Comisiones Pendientes</center></div>
                                </div>
                                <?php $color=true; ?>
                                @foreach($registros as $registro)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$registro->nombre}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth text-ttds {{$color?'bg-gray-100':'bg-white'}}">
                                        <center><a href="/estado_cuenta_distribuidor/{{$calculo->id}}/{{$registro->id}}"><i class="fas fa-balance-scale"></i></center></a>
                                    </div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>${{number_format($registro->nuevas_comision_no_pago+$registro->adiciones_comision_no_pago+$registro->renovaciones_comision_no_pago+$registro->nuevas_bono_no_pago+$registro->adiciones_bono_no_pago+$registro->renovaciones_bono_no_pago,0)}}</center></div>
                                    <div class="table-cell border-r border-l border-b border-gray-300 font-ligth text-green-700 {{$color?'bg-gray-100':'bg-white'}}">
                                        <center>
                                            ${{number_format($registro->anticipo_no_pago,0)}}
                                            <a href="javascript:toogleForma({{$calculo->id}},{{$registro->id}});"><i class="far fa-money-bill-alt"></i></a>
                                        </center>   
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
                        <form action="{{route('distribuidores_anticipo_no_pago')}}" method="POST">
                            @csrf
                            <input class="hidden" type="text" name="id_distribuidor" id="id_distribuidor" value="{{old('id_distribuidor')}}">
                            <input class="hidden" type="text" name="id_calculo" id="id_calculo" value="{{old('id_calculo')}}">
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Comisiones</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('comisiones')}}" id="comisiones" name="comisiones" readonly>
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Bonos</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('bonos')}}" id="bonos" name="bonos" readonly>
                                </div>
                            </div>
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Residual</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('residual')}}" id="residual" name="residual" readonly>
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Charge-Back</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('charge_back')}}" id="charge_back" name="charge_back" readonly>
                                </div>
                            </div>
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Anticipos Extraordinarios</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('anticipos_extraordinarios')}}" id="anticipos_extraordinarios" name="anticipos_extraordinarios" readonly>
                                </div>
                                <div class="w-1/2">
                                    <span class="text-xs text-ttds">Retroactivos (Reproceso)</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('retroactivos_reproceso')}}" id="retroactivos_reproceso" name="retroactivos_reproceso" readonly>
                                </div>
                            </div>
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Lineas Pendientes</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('lineas_pendientes')}}" id="lineas_pendientes" name="lineas_pendientes" readonly>
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Comision Pendiente</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('comision_pendiente')}}" id="comision_pendiente" name="comision_pendiente" readonly>
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Bono Pendiente</span><br>
                                    <input class="w-full border border-white rounded" type="text" value="{{old('bono_pendiente')}}" id="bono_pendiente" name="bono_pendiente" readonly>
                                </div>
                            </div>
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full">
                                    <span class="text-xs text-ttds">Anticipo por lineas pendientes</span><br>
                                    <input class="w-full border border-white rounded" type="hidden" name="total_pendientes" value="{{old('total_pendientes')}}" id="total_pendientes" readonly>
                                    <input class="w-2/3 border border-gray-600 rounded" type="text" name="anticipo_no_pago" value="{{old('anticipo_no_pago')}}" id="anticipo_no_pago">
                                    <button class="bg-ttds hover:bg-ttds-hover rounded p-2 text-gray-200" type="button" onClick="document.getElementById('anticipo_no_pago').value=document.getElementById('total_pendientes').value*0.5">50%</button>
                                    <button class="bg-ttds hover:bg-ttds-hover rounded p-2 text-gray-200" type="button" onClick="document.getElementById('anticipo_no_pago').value=document.getElementById('total_pendientes').value*0.25">25%</button>
                                    @error('anticipo_no_pago')
                                        <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                    @enderror   
                                </div>
                            </div>
                            <div class="w-full flex justify-center pt-6 pb-3 rounded-b">
                                <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Guardar</button>
                                <button class="rounded p-1 border bg-red-500 hover:bg-red-700 text-gray-100 font-semibold" type="button" onClick="cerrarForma()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- FIN DEL CONTENIDO -->
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 bg-green-300 rounded-b-lg" id="notas">
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
        function toogleForma(id,user_id)
        {
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full")
            document.getElementById('tabla').classList.add("lg:w-1/2");
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
                        document.getElementById("id_distribuidor").value=respuesta.user_id;
                        document.getElementById("id_calculo").value=respuesta.calculo_id;
                        document.getElementById("comisiones").value=formatter.format(parseFloat(respuesta.comision_adiciones)+parseFloat(respuesta.comision_nuevas)+parseFloat(respuesta.comision_renovaciones));
                        document.getElementById("bonos").value=formatter.format(parseFloat(respuesta.bono_adiciones)+parseFloat(respuesta.bono_nuevas)+parseFloat(respuesta.bono_renovaciones));
                        document.getElementById("residual").value=formatter.format(respuesta.residual);
                        document.getElementById("charge_back").value=formatter.format(respuesta.charge_back);
                        document.getElementById("anticipos_extraordinarios").value=formatter.format(respuesta.anticipos_extraordinarios);
                        document.getElementById("retroactivos_reproceso").value=formatter.format(respuesta.retroactivos_reproceso);
                        document.getElementById("lineas_pendientes").value=parseInt(respuesta.adiciones_no_pago)+parseInt(respuesta.nuevas_no_pago)+parseInt(respuesta.renovaciones_no_pago);
                        document.getElementById("comision_pendiente").value=formatter.format(parseFloat(respuesta.adiciones_comision_no_pago)+parseFloat(respuesta.nuevas_comision_no_pago)+parseFloat(respuesta.renovaciones_comision_no_pago));
                        document.getElementById("bono_pendiente").value=formatter.format(parseFloat(respuesta.adiciones_bono_no_pago)+parseFloat(respuesta.nuevas_bono_no_pago)+parseFloat(respuesta.renovaciones_bono_no_pago));
                        document.getElementById("total_pendientes").value=parseFloat(respuesta.adiciones_bono_no_pago)+parseFloat(respuesta.nuevas_bono_no_pago)+parseFloat(respuesta.renovaciones_bono_no_pago)+parseFloat(respuesta.adiciones_comision_no_pago)+parseFloat(respuesta.nuevas_comision_no_pago)+parseFloat(respuesta.renovaciones_comision_no_pago);
                        document.getElementById("anticipo_no_pago").value=parseFloat(respuesta.anticipo_no_pago);

 
                    }
                    else
                    {
                        alert("Error al consultar la base de datos, intente nuevamente!");
                    }
    
                }
            };  
            xmlhttp.open("GET", "/distribuidores_consulta_pago/" + id + "/" + user_id, true);
            xmlhttp.send();
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