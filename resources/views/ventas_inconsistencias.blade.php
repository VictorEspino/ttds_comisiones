<x-app-layout>
    <x-slot name="header">
            {{ __('Revision de Inconsistencias') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-xl font-bold text-gray-100">Inconsistencias</div>
            <div class="w-full text-lg font-semibold text-gray-100">{{$calculo->descripcion}}</div>            
            <div class="w-full text-xs font-semibold text-gray-100">De {{$calculo->fecha_inicio}} a {{$calculo->fecha_fin}}</div>             
        </div> <!--FIN ENCABEZADO-->
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                <div class="w-full lg:w-1/2">
                    <form action="{{route('ventas_inconsistencias',['id'=>$calculo->id,'version'=>$version])}}" class="">
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
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><a href="javascript:toogleForma({{$registro->id}},'{{$registro->cliente}}','{{$registro->dn}}','{{$registro->cuenta}}','{{$registro->folio}}','{{$registro->plan}}','{{$registro->renta}}','{{$registro->plazo}}','{{$registro->descuento_multirenta}}','{{$registro->afectacion_comision}}','{{$registro->c_renta}}','{{$registro->c_plazo}}','{{$registro->c_descuento_multirenta}}','{{$registro->c_afectacion_comision}}')"><i class="far fa-edit"></i></a></div>
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
                                    <div class="table-cell border-l border-b border-r border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><a href="javascript:toogleForma({{$registro->id}},'{{$registro->cliente}}','{{$registro->dn}}','{{$registro->cuenta}}','{{$registro->folio}}','{{$registro->plan}}','{{$registro->renta}}','{{$registro->plazo}}','{{$registro->descuento_multirenta}}','{{$registro->afectacion_comision}}','{{$registro->c_renta}}','{{$registro->c_plazo}}','{{$registro->c_descuento_multirenta}}','{{$registro->c_afectacion_comision}}')"><i class="far fa-edit"></i></a></div>
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
                        <form action="{{route('accion_inconsistencia')}}" method="POST" id="forma_datos">
                            @csrf
                            <input class="hidden" type="text" name="id_venta" id="id_venta">
                            <input class="hidden" type="text" name="id_calculo" id="id_calculo" value="{{$calculo->id}}">
                            <input class="hidden" type="text" name="accion" id="accion">
                            <div class="w-full px-2">
                                <span class="text-xs text-ttds">Cliente</span><br>
                                <input class="w-full rounded p-1 border border-white" type="text" name="cliente" id="cliente" readonly>  
                            </div> 

                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">DN</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="dn" id="dn" readonly>
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Cuenta</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="cuenta" id="cuenta" readonly> 
                                </div>
                                <div class="w-1/3">
                                    <span class="text-xs text-ttds">Folio</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="folio" id="folio" readonly>  
                                </div>
                            </div> 
                            <div class="w-full px-2 pb-2 flex flex-row space-x-1">
                                <div class="w-full">
                                    <span class="text-xs text-ttds">Plan</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="plan" id="plan" readonly> 
                                </div>
                            </div> 
                            <div class="w-full px-2 font-semibold text-red-700">Registro Interno</div>
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Renta</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="renta" id="renta" readonly>  
                                </div>
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Plazo</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="plazo" id="plazo" readonly> 
                                </div>
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Descuento Multirenta (%)</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="descuento_multirenta"  id="descuento_multirenta" readonly>
                                </div>
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Afectacion Comision (%)</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="afectacion_comision" id="afectacion_comision" readonly>
                                </div>
                            </div>
                            <div class="w-full px-2 font-semibold text-red-700">Callidus</div>
                            <div class="w-full px-2 flex flex-row space-x-1">
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Renta</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="c_renta" id="c_renta" readonly>  
                                </div>
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Plazo</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="c_plazo" id="c_plazo" readonly> 
                                </div>
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Descuento Multirenta (%)</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="c_descuento_multirenta"  id="c_descuento_multirenta" readonly>
                                </div>
                                <div class="w-1/4">
                                    <span class="text-xs text-ttds">Afectacion Comision (%)</span><br>
                                    <input class="w-full rounded p-1 border border-white" type="text" name="c_afectacion_comision" id="c_afectacion_comision" readonly>
                                </div>
                            </div>
                            <div class="w-full flex flex-row justify-between pt-6 pb-3 rounded-b px-2">
                                <div class="flex justify-center"><button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold" type="button" onClick="Corrige()">Corrige Registro</button></div>
                                <div class="flex justify-center"><button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold" type="button" onClick="Aclara()">Emitir Aclaracion</button></div>
                                <div class="flex justify-center"><button class="rounded p-1 border bg-red-500 hover:bg-red-700 text-gray-100 font-semibold" type="button" onClick="Cancel()">Cancelar</button></div>
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
        function toogleForma(id,cliente,dn,cuenta,folio,plan,renta,plazo,descuento_multirenta,afectacion_comision,c_renta,c_plazo,c_descuento_multirenta,c_afectacion_comision)
        {
            document.getElementById('forma').style.display="block";
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full")
            document.getElementById('tabla').classList.add("lg:w-2/3");
            document.getElementById("id_venta").value=id;
            document.getElementById("cliente").value=cliente;
            document.getElementById("dn").value=dn;
            document.getElementById("cuenta").value=cuenta;
            document.getElementById("folio").value=folio;
            document.getElementById("plan").value=plan;
            document.getElementById("renta").value=renta;
            document.getElementById("plazo").value=plazo;
            document.getElementById("descuento_multirenta").value=descuento_multirenta;
            document.getElementById("afectacion_comision").value=afectacion_comision;
            document.getElementById("c_renta").value=c_renta;
            document.getElementById("c_plazo").value=c_plazo;
            document.getElementById("c_descuento_multirenta").value=c_descuento_multirenta;
            document.getElementById("c_afectacion_comision").value=c_afectacion_comision;
        }
        function Corrige()
        {
            document.getElementById("accion").value='corrige';
            if(confirm('Esta operacion realizara la actualizacion de los registros internos con los datos de callidus para RENTA, PLAZO, DESCUENTO MULTIRENTA y AFECTACION EN COMISION, asi mismo eliminara la nota de inconsistencia en los resultados del calculo.\n\n ¿Desea continuar?'))
            {
                document.getElementById('forma_datos').submit();
            }
        }
        function Aclara()
        {
            document.getElementById("accion").value='aclara';
            document.getElementById('forma_datos').submit();
        }
        function Cancel()
        {
            document.getElementById('forma').style.display="none";
            document.getElementById('tabla').classList.remove("lg:w-2/3");
            document.getElementById('tabla').classList.remove("w-full");
            document.getElementById('tabla').classList.add("w-full");
        }
    </script>
</x-app-layout>