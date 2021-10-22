<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ttds leading-tight">
            {{ __('Detalle de Control') }}
        </h2>
    </x-slot>
    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-row justify-between border-b border-gray-800"> <!--ENCABEZADO-->
            <div>
                <div class="w-full text-xl font-bold text-gray-100">Calculo de comisiones</div>
                <div class="w-full text-lg font-semibold text-gray-100">{{$descripcion}}</div>            
                <div class="w-full text-xs font-semibold text-gray-100">De {{$fecha_inicio}} a {{$fecha_fin}}</div>                        
            </div>
            @if($terminado=="0")
            <div class="md:px-7 flex items-center">
                <form method="post" action="{{route('calculo_reset')}}" id="forma_reset">
                    @csrf
                    <input type="hidden" name="id" value="{{$id_calculo}}">
                    @if(Auth::user()->perfil=="admin")
                    <button type="button" class="rounded px-3 py-2 border bg-gray-500 hover:bg-ttds-hover text-gray-100 font-semibold" onclick="confirmar_reset()">Reset</button>
                    @endif
                </form>
            </div>    
            @endif
        </div> <!--FIN ENCABEZADO-->
        @if(session('status')!='')
            <div class="w-full flex justify-between flex-row p-3 bg-green-300" id="estatus1">
                <div class="flex justify-center items-center">
                    <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
                </div>
                <div>
                    <a href="javascript:eliminar_estatus()"><span class="font-semibold text-base text-gray-600">X</span></a>
                </div>        
            </div>    
        @endif
        @if(session()->has('failures') || session()->has('error_validacion'))
        <div class="bg-red-200 p-4 flex justify-center font-bold">
            El archivo no fue cargado verifique detalles al final de la pagina
        </div>
        @endif
        <div class="flex flex-col md:space-x-5 md:space-y-0 items-start md:flex-row">
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Validacion Ventas</div>
                <div class="w-full flex flex-row border rounded-b-lg shadow-lg pb-5">  
                    <div class="w-full md:w-2/3 px-3 pt-2">
                        <div class="w-full flex flex-row border-b text-lg font-semibold">
                            <div class="w-2/3">
                                Total de Ventas Registradas  
                            </div>
                            <div class="w-1/3 border-b flex justify-center">
                                {{$totales}}
                            </div>
                        </div>
                        <div class="w-full flex flex-row border-b text-sm px-3">
                            <div class="w-2/3">
                                Ventas Validadas 
                            </div>
                            <div class="w-1/3 border-b flex justify-center">
                                {{$validados}}
                            </div>
                        </div>
                        <div class="w-full flex flex-row border-b text-sm px-3">
                            <div class="w-2/3">
                                Ventas NO Validadas 
                            </div>
                            <div class="w-1/3 flex justify-center">
                                {{$no_validados}}
                            </div>
                        </div>
                    </div>  
                    <div class="hidden md:block md:w-1/3 md:flex md:justify-center md:p-3">
                        <div class="flex justify-center" id="chart_div" style="width: 400px; height: 120px;"></div>
                    </div> 
                </div>
            </div>
            <div class="w-full p-3 md:w-1/2 md:p-5 flex flex-col">
                <div class="w-full bg-gray-200 p-2 rounded-t-lg">Callidus</div>
                <div class="w-full border-r border-l p-2 flex flex-row">
                    <div class="w-1/2">
                        <div class="w-full flex justify-center text-4xl font-semibold text-ttds">{{number_format($n_callidus,0)}}</div>
                        <div class="w-full flex justify-center text-sm">Registros Venta</div>
                    </div>
                    <div class="w-1/2">
                        <div class="w-full flex justify-center text-4xl font-semibold text-ttds">{{number_format($n_callidus_residual,0)}}</div>
                        <div class="w-full flex justify-center text-sm">Registros Residual</div>
                    </div>
                </div>
                @if(Auth::user()->perfil=="admin")
                <div class="w-full border-r border-l ">
                    @if($adelanto=="0" || $cierre=="0")
                    <form method="post" action="{{route('callidus_import')}}" enctype="multipart/form-data" id="carga_ventas_callidus">
                        @csrf
                    <div class="w-full rounded-b-lg p-3 flex flex-col"> <!--CONTENIDO-->
                        <div class="w-full flex flex-row space-x-2">
                            <div class="w-4/5">
                                
                                <span class="text-xs text-ttds">Archivo Ventas</span><br>
                                <input type="hidden" name="id_calculo" value="{{$id_calculo}}" id="id_calculo">
                                <input class="w-full rounded p-1 border border-gray-300 bg-white" type="file" name="file_v" value="{{old('file_v')}}" id="file_v">
                                @error('file_v')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror                    
                            </div>
                            <div class="w-1/5 flex items-end">
                                <button onClick="carga_ventas_callidus()" type="button" class="rounded px-3 py-2 border bg-ttds hover:bg-ttds-hover text-gray-100 font-semibold">Cargar</button>
                            </div>                
                        </div>
                    </div> <!--FIN CONTENIDO-->
                    
                    </form>
                    @endif
                </div>
                <div class="w-full border-b border-r border-l rounded-b shadow-lg">
                    @if($adelanto=="0" || $cierre=="0")
                    <form method="post" action="{{route('callidus_residual_import')}}" enctype="multipart/form-data" id="carga_residual_callidus">
                        @csrf
                    <div class="w-full rounded-b-lg p-3 flex flex-col"> <!--CONTENIDO-->
                        <div class="w-full flex flex-row space-x-2">
                            <div class="w-4/5">
                                <span class="text-xs text-ttds">Archivo Residual</span><br>
                                <input type="hidden" name="id_calculo" value="{{$id_calculo}}" id="id_calculo">
                                <input class="w-full rounded p-1 border border-gray-300 bg-white" type="file" name="file_r" value="{{old('file_r')}}" id="file_r">
                                @error('file_r')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror                    
                            </div>
                            <div class="w-1/5 flex items-end">
                                <button onClick="carga_residual_callidus()" type="button" class="rounded px-3 py-2 border bg-ttds hover:bg-ttds-hover text-gray-100 font-semibold">Cargar</button>
                            </div>                
                        </div>
                    </div> <!--FIN CONTENIDO-->
                    
                    </form>
                    @endif
                </div>                    
                @else
                <div class="w-full border-b border-r border-l rounded-b shadow-lg">
                </div>

                @endif
            </div>
        </div>
        <div class="flex flex-col md:space-x-5 md:space-y-0 items-start md:flex-row">
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">{{Auth::user()->perfil=='admin'?'Calculo de Comisiones':'Revision de Calculo'}}</div>
                @if(Auth::user()->perfil=="admin")
                <div class="w-full flex flex-col border rounded-b-lg shadow-lg p-3 space-y-4">  
                    @if($cierre=="0")
                    <div class="w-full">
                        <form class="w-full" method="post" action="{{route('calculo_ejecutar')}}" id="forma_adelanto">
                            @csrf
                            <input type="hidden" name="version" value="1">
                            <input type="hidden" name="id" value="{{$id_calculo}}">
                            @if($n_callidus>0)
                            <button type="button" onClick="ejecuta_calculo(1)" class="bg-ttds text-gray-200 text-4xl font-semibold rounded-lg hover:bg-ttds-hover shadow-lg w-full border p-10">
                                {{($adelanto=="1")?'Actualizar':'Ejecutar'}} Adelanto
                            </button>
                            @else
                            <span class="w-full flex text-center text-ttds-naranja text-2xl font-semibold p-10">
                                Cargue el archivo de CALLIDUS antes de ejecutar el adelanto
                            </span>   
                            @endif
                        </form>
                    </div>
                    @endif
                    @if($adelanto=="1" && $terminado=="0")
                    <div class="w-full">
                        <form class="w-full" method="post" action="{{route('calculo_ejecutar')}}" id="forma_cierre">
                            @csrf
                            <input type="hidden" name="version" value="2">
                            <input type="hidden" name="id" value="{{$id_calculo}}">
                            @if($n_callidus_residual>0)
                            <button type="button" onClick="ejecuta_calculo(2)" class="bg-ttds text-gray-200 text-4xl font-semibold rounded-lg hover:bg-ttds-hover shadow-lg w-full border p-10">
                                {{($cierre=="1")?'Actualizar':'Ejecutar'}} Cierre
                            </button>
                            @else
                            <span class="w-full flex text-center text-ttds-naranja text-2xl font-semibold p-10">
                                Cargue el archivo de RESIDUAL antes de ejecutar el cierre
                            </span>   
                            @endif
                        </form>
                    </div>
                    @endif
                    @if($cierre=="1" && $terminado=="0")
                    <div class="w-full">
                        <form class="w-full" method="post" action="{{route('calculo_terminar')}}" id="forma_finaliza">
                            @csrf
                            <input type="hidden" name="id" value="{{$id_calculo}}">
                            <button type="button" onClick="confirmar_finalizacion()" class="bg-ttds text-gray-200 text-4xl font-semibold rounded-lg hover:bg-ttds-hover shadow-lg w-full border p-10">
                                Finalizar Calculo
                            </button>
                        </form>
                    </div>
                    @endif
                    @if($terminado=="1")
                    <div class="w-full">
                            <span class="text-ttd-azul text-2xl font-semibold p-10">
                                El calculo de comisiones se encuentra finalizado
                            </span>
                    </div>
                    @endif
                </div>
                @else
                <div class="w-full flex justify-center text-center flex-col border rounded-b-lg shadow-lg p-3 space-y-4">  
                    <div class="w-full">
                        <span class="w-full flex text-center text-ttds-naranja text-2xl font-semibold p-10">
                            Adelanto ejecutado : {{$adelanto=="1"?'OK':'NO'}}<br>
                            Cierre ejecutado : {{$cierre=="1"?'OK':'NO'}}<br>
                            Calculo terminado: {{$terminado=="1"?'OK':'NO'}}<br>
                        </span>   
                    </div>
                </div>
                @endif
            </div>
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Resumen de Pago</div>
                <div class="w-full flex flex-col border rounded-b-lg shadow-lg">  
                    <div class="w-full px-3 pt-2">
                        <div class="w-full flex flex-row text-xs font-semibold">
                            <div class="w-1/2">
                                 
                            </div>
                            <div class="w-1/4 flex justify-center">
                                Adelanto
                            </div>
                            <div class="w-1/4 flex justify-center">
                                Cierre
                            </div>
                        </div>
                    </div>
                    <div class="w-full px-3 pt-2 pb-5 md:pb-1">
                        <div class="w-full flex flex-row border-b text-lg font-semibold">
                            <div class="w-1/2">
                                Total de Ventas Procesadas 
                            </div>
                            <div class="w-1/4 flex justify-center">
                                {{$totales_comision_adelanto}}
                            </div>
                            <div class="w-1/4 flex justify-center">
                                {{$totales_comision_cierre}}
                            </div>
                        </div>
                        <div class="w-full flex flex-row border-b text-sm">
                            <div class="w-1/2 px-3">
                                Ventas Pagadas
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <a href="/transacciones_resumen_calculo/{{$id_calculo}}/PAGO/1">{{$pagados_adelanto}}</a>
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <a href="/transacciones_resumen_calculo/{{$id_calculo}}/PAGO/2">{{$pagados_cierre}}</a>
                            </div>
                        </div>
                        <div class="w-full flex flex-row border-b text-sm">
                            <div class="w-1/2 px-3">
                                Ventas NO Pagadas 
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <a href="/transacciones_resumen_calculo/{{$id_calculo}}/NO PAGO/1">{{$no_pagados_adelanto}}</a>
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <a href="/transacciones_resumen_calculo/{{$id_calculo}}/NO PAGO/2">{{$no_pagados_cierre}}</a>
                            </div>
                        </div>
                    </div>  
                    <div class="hidden md:block w-full px-3 pt-2">
                        <div class="w-full flex flex-row text-lg font-semibold">
                            <div class="w-1/2">
                                 
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <div class="flex justify-center" id="chart_div_2" style="height: 110px;"></div>
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <div class="flex justify-center" id="chart_div_3" style="height: 110px;"></div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="w-1/3 flex justify-center p-3">
                        
                    </div> 
                -->
                </div>
            </div>
        </div>
        <div class="w-full flex flex-col items-start">
            <div class="w-full flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Acciones y Resultados</div>
                <div class="w-full flex flex-col border rounded-b-lg shadow-lg p-2"> 
                    <div class="w-full flex flex-row pt-3">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="w-full flex justify-between flex-row pb-2 md:pb-4">
                                <div class="w-1/2 flex justify-center">
                                    Adelanto
                                </div>
                                <div class="w-1/2 flex justify-center">
                                    Cierre
                                </div>
                            </div>
                            <div class="md:hidden w-full p-1"><span class="text-lg font-semibold text-gray-700">Pagos</span></div>
                            <div class="w-full flex justify-between flex-row">
                                <div class="w-1/2 flex justify-center">
                                    <a href="{{route('acciones_distribuidores_calculo',['id'=>$id_calculo,'version'=>1])}}">
                                        <i class="text-gray-700 text-6xl fas fa-balance-scale"></i>
                                    </a>
                                </div>
                                <div class="w-1/2 flex justify-center">
                                    <a href="{{route('acciones_distribuidores_calculo',['id'=>$id_calculo,'version'=>2])}}">
                                        <i class="text-green-700 text-6xl fas fa-balance-scale"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full flex justify-between flex-row">
                                <div class="w-1/2 flex justify-center text-center">
                                    <span class="text-xs md:text-sm text-gray-700">Pagos para {{$n_pagos_adelanto}} distribuidores</span>
                                </div>
                                <div class="w-1/2 flex justify-center text-center">
                                    <span class="text-xs md:text-sm text-gray-700">Pagos para {{$n_pagos_cierre}} distribuidores</span>
                                </div>
                            </div>
                        </div>
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-2xl font-semibold text-gray-700">Pagos</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Revisa los estados de cuenta de cada distribuidor
                                </span>
                            </div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Aplique adelantos por comisiones pendientes
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex flex-row pt-3 pt-8">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="w-full flex justify-center flex-row">
                                <div class="w-1/2 flex justify-center">
                                    <a href="{{route('pagos_export',['id'=>$id_calculo,'version'=>1])}}">
                                        <span class="text-gray-500 text-6xl font-bold fas fa-file-invoice-dollar"></span>
                                    </a>
                                </div>
                                <div class="w-1/2 flex justify-center">
                                    <a href="{{route('pagos_export',['id'=>$id_calculo,'version'=>2])}}">
                                        <span class="text-blue-500 text-6xl font-bold fas fa-file-invoice-dollar"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full flex justify-between flex-row">
                                <div class="w-1/2 flex justify-center text-center">
                                    <span class="text-xs md:text-sm text-gray-700">Pagos para {{$n_pagos_adelanto}} distribuidores</span>
                                </div>
                                <div class="w-1/2 flex justify-center text-center">
                                    <span class="text-xs md:text-sm text-gray-700">Pagos para {{$n_pagos_cierre}} distribuidores</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="hidden md:block text-2xl font-semibold text-gray-700">Formato de Pagos</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Obtenga el formato de pagos por comisones generadas de cada distribuidor
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden w-full pt-2"><span class="text-lg font-semibold text-gray-700">Charge-Back</span></div>
                    <div class="w-full flex flex-row pt-3 md:pt-8">
                        
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="w-full flex justify-center">
                                <a href="{{route('charge_back_calculo',['id'=>$id_calculo])}}">
                                    <span class="text-red-400 text-6xl font-bold"><i class="fas fa-hand-holding-usd"></i></span>
                                </a>
                            </div>
                            <div>
                                <span class="text-xs md:text-sm text-gray-700">Aplicados : {{$cb_aplicados}}, NO Aplicados : {{$cb_no_aplicados}}</span>
                            </div>
                        </div>
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-lg md:text-2xl font-semibold text-gray-700">Charge Back</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Charge back de la comision pagada + cargo por equipo en caso de cancelacion involuntaria
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden w-full pt-2"><span class="text-lg font-semibold text-gray-700">Residuales</span></div>
                    <div class="w-full flex flex-row pt-3 md:pt-8">
                        <div class="w-full md:w-1/2 flex flex-row justify-center items-center">
                            <div class="w-1/3 md:w-1/2 flex px-4 flex-col">
                                <div class="w-full flex justify-center py-2">
                                    <a href="{{route('residuales',['id'=>$id_calculo])}}">
                                        <span class="text-indigo-400 text-6xl font-bold"><i class="fa-file-invoice-dollar"></i></span>
                                    </a>
                                </div>
                                <div class="w-full flex justify-center text-center text-xs md:text-sm">Base Residuales</div>
                            </div>
                            <div class="w-2/3 md:w-1/2 flex flex-col px-4">
                                <div class="">
                                    <span class="text-base md:text-xl text-red-400 flex justify-center items-center">
                                        <a href="{{route('export_alertas',['id'=>$id_calculo,'user_id'=>0])}}">
                                            <i class="fas fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;Alertas : {{$alertas}}&nbsp;&nbsp;&nbsp;<i class="fas fa-exclamation-triangle"></i>
                                        </a>
                                    </span>
                                </div>
                                <div class="pt-2">
                                    <span class="text-xs md:text-sm text-gray-700 flex justify-center">Alertas Cobranza</span>
                                </div>
                            </div>
                        </div>
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-lg md:text-2xl font-semibold text-gray-700">Residuales</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Comision residual de acuerdo a la permanencia del cliente, aplica siempre que el cliente no tenga adeudos y este dentro de plazo
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden w-full pt-3"><span class="text-lg font-semibold text-gray-700">Inconsistencias</span></div>
                    <div class="w-full flex flex-row pt-3 md:pt-8">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="w-full flex justify-center">
                                <a href="{{route('ventas_inconsistencias',['id'=>$id_calculo,'version'=>($cierre=="1"?2:1)])}}">
                                    <span class="text-yellow-400 text-6xl font-bold">{{$n_inconsistencias}}</span>
                                </a>
                            </div>
                            <div>
                                <span class="text-xs md:text-sm text-gray-700">Inconsistencias encontradas</span>
                            </div>
                        </div>
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="hidden md:block text-2xl font-semibold text-gray-700">Inconsistencias</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Le permite revisar los registros que fueron encontrados en Callidus y que presentan diferencias con los parametros de la base de ventas, como el plazo, la renta, descuento multirenta y afectacion en comision.
                                </span>
                            </div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -En caso de que la inconsistencia persista le permite agregar dicha inconsistencia en el formato de aclaracion.
                                </span>
                            </div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Si la inconsistencia proviene de una falla en nuestro reporte interno de ventas, le permite la correccion interna y la eliminacion de la alerta.
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden w-full pt-2"><span class="text-lg font-semibold text-gray-700">Formato aclaracion</span></div>
                    <div class="w-full flex flex-row pt-3 pb-3">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="w-full flex justify-center">
                                <a href="{{route('reclamos_export',['id'=>$id_calculo])}}">
                                    <span class="text-red-500 text-6xl font-bold far fa-file-alt"></span>
                                </a>
                            </div>
                            <div>
                                <span class="text-xs md:text-sm text-gray-700">{{$n_reclamos}} registros generados</span>
                            </div>
                        </div>
                        
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-lg md:text-2xl font-semibold text-gray-700">Formato de Aclaracion</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Obtenga el formato de aclaraciones que debe ser enviado a AT&T
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden w-full pt-2"><span class="text-lg font-semibold text-gray-700">Callidus</span></div>
                    <div class="w-full flex flex-row pt-3 pb-6">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="w-full flex justify-center">
                                <a href="{{route('callidus_no_usados',['id'=>$id_calculo])}}">
                                    <span class="text-gray-500 text-6xl font-bold fas fa-database"></span>
                                </a>
                            </div>
                            <div>
                                <span class="text-xs md:text-sm text-gray-700">{{$n_callidus_sin_usar}} registros sin correspondiencia</span>
                            </div>
                        </div>
                        
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-2xl font-semibold text-gray-700">Registros de Callidus sin pago</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Le permite revisar los registros de Callidus que no encontraron relacion con algun registro interno de ventas
                                </span>
                            </div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Use esta informacion para identificar si algun identificador de los registros no pagados (folio/contrato, dn , cuenta) estan correctamente capturados en el reporte interno de ventas, cuya correccion le permita pasar a pago.
                                </span>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
        @if(session('status'))
        <div class="bg-green-200 p-4 flex justify-center font-bold rounded-b-lg" id="estatus2">
            {{session('status')}}
        </div>
        @endif
        @if(session()->has('failures'))
        <div class="bg-red-200 p-4 flex justify-center font-bold">
            El archivo no fue cargado!
        </div>
        <div class="bg-red-200 p-4 flex justify-center rounded-b-lg">
            <table class="text-sm">
                <tr>
                    <td class="bg-red-700 text-gray-100 px-3">Row</td>
                    <td class="bg-red-700 text-gray-100 px-3">Columna</td>
                    <td class="bg-red-700 text-gray-100 px-3">Error</td>
                    <td class="bg-red-700 text-gray-100 px-3">Valor</td>
                </tr>
            
                @foreach(session()->get('failures') as $validation)
                <tr>
                    <td class="px-3"><center>{{$validation->row()}}</td>
                    <td class="px-3"><center>{{$validation->attribute()}}</td>
                    <td class="px-3">
                        <ul>
                        @foreach($validation->errors() as $e)
                            <li>{{$e}}</li>
                        @endforeach
                        </ul>
                    </td>
                    
                    <td class="px-3"><center>
                    <?php
                     try{
                    ?>    
                        {{$validation->values()[$validation->attribute()]}}
                    <?php
                        }
                        catch(\Exception $e)
                        {
                            ;
                        }
                    ?>
                    </td>
                </tr>
                @endforeach

            </table>
        </div>
        @endif
        @if(session()->has('error_validacion'))
        <div class="bg-red-200 p-4 flex justify-center font-bold">
            El archivo no fue cargado!
        </div>
        <div class="bg-red-200 p-4 flex justify-center rounded-b-lg">
            <table class="text-sm">
                <tr>
                    <td class="bg-red-700 text-gray-100 px-3">Row</td>
                    <td class="bg-red-700 text-gray-100 px-3">Columna</td>
                    <td class="bg-red-700 text-gray-100 px-3">Error</td>
                    <td class="bg-red-700 text-gray-100 px-3">Valor</td>
                </tr>
            @foreach(session()->get('error_validacion') as $error)
                <tr>
                    <td class="px-3"><center>{{$error["row"]}}</td>
                    <td class="px-3"><center>{{$error["campo"]}}</td>
                    <td class="px-3"><center>{{$error["mensaje"]}}</td>
                    <td class="px-3"><center>{{$error["valor"]}}</td>
                </tr>
            @endforeach
            </table>
        </div>
        @endif
    </div>
<!--MODAL CONFIRMACION-->
<div class="fixed hidden inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full" id="modal_finalizacion">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="text-green-500 text-2xl font-bold far fa-check-circle"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 p-3">¿Desea continuar?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Esta operacion dara por finalizado el calculo de comisiones y lo pondra disponible para los distribuidores, no se podran realizar mas cambios.
                </p>
            </div>
            <div class="px-4 py-3 flex flex-row">
                <div class="w-1/2 flex justify-center">
                    <button onClick="ejecuta_finalizacion()" class="px-3 w-2/3 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        OK
                    </button>
                </div>
                <div class="w-1/2 flex justify-center">
                    <button onClick="cancelar_finalizacion()" class="px-3 w-2/3 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed hidden inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full" id="modal_reset">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="text-green-500 text-2xl font-bold far fa-check-circle"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 p-3">¿Desea continuar?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Esta accion eliminara todo registro presente en el calculo, incluyendo las cargas de los archivos de Callidus.
                </p>
            </div>
            <div class="px-4 py-3 flex flex-row">
                <div class="w-1/2 flex justify-center">
                    <button onClick="ejecuta_reset()" class="px-3 w-2/3 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        OK
                    </button>
                </div>
                <div class="w-1/2 flex justify-center">
                    <button onClick="cancelar_reset()" class="px-3 w-2/3 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed hidden inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full" id="modal_reset">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="text-green-500 text-2xl font-bold far fa-check-circle"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 p-3">¿Desea continuar?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Esta accion eliminara todo registro presente en el calculo, incluyendo las cargas de los archivos de Callidus.
                </p>
            </div>
            <div class="px-4 py-3 flex flex-row">
                <div class="w-1/2 flex justify-center">
                    <button onClick="ejecuta_reset()" class="px-3 w-2/3 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        OK
                    </button>
                </div>
                <div class="w-1/2 flex justify-center">
                    <button onClick="cancelar_reset()" class="px-3 w-2/3 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed hidden inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full" id="modal_procesa">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-36 w-36 rounded-full bg-green-100">
                <svg version="1.1" id="L7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                <path fill="#fff" d="M31.6,3.5C5.9,13.6-6.6,42.7,3.5,68.4c10.1,25.7,39.2,38.3,64.9,28.1l-3.1-7.9c-21.3,8.4-45.4-2-53.8-23.3
                c-8.4-21.3,2-45.4,23.3-53.8L31.6,3.5z">
                    <animateTransform 
                        attributeName="transform" 
                        attributeType="XML" 
                        type="rotate"
                        dur="2s" 
                        from="0 50 50"
                        to="360 50 50" 
                        repeatCount="indefinite" />
                </path>
                <path fill="#fff" d="M42.3,39.6c5.7-4.3,13.9-3.1,18.1,2.7c4.3,5.7,3.1,13.9-2.7,18.1l4.1,5.5c8.8-6.5,10.6-19,4.1-27.7
                c-6.5-8.8-19-10.6-27.7-4.1L42.3,39.6z">
                    <animateTransform 
                        attributeName="transform" 
                        attributeType="XML" 
                        type="rotate"
                        dur="1s" 
                        from="0 50 50"
                        to="-360 50 50" 
                        repeatCount="indefinite" />
                </path>
                <path fill="#fff" d="M82,35.7C74.1,18,53.4,10.1,35.7,18S10.1,46.6,18,64.3l7.6-3.4c-6-13.5,0-29.3,13.5-35.3s29.3,0,35.3,13.5
                L82,35.7z">
                    <animateTransform 
                        attributeName="transform" 
                        attributeType="XML" 
                        type="rotate"
                        dur="2s" 
                        from="0 50 50"
                        to="360 50 50" 
                        repeatCount="indefinite" />
                </path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 p-3" id="mensaje">Procesando</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Esta operacion puede tardar algunos segundos.
                </p>
            </div>
        </div>
    </div>
</div>
<!--FIN MODALES-->





    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);
        google.charts.setOnLoadCallback(drawChart2);
        google.charts.setOnLoadCallback(drawChart3);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['', {{$porcentaje_validacion}}],
            ]);

            var options = {
            width: 400, height: 120,
            redFrom: 0, redTo: 80,
            yellowFrom:80, yellowTo: 90,
            greenFrom:90, greenTo: 100,
            minorTicks: 5
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
        function drawChart2() 
        {
            var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['', {{$porcentaje_comisionado_adelanto}}],
            ]);

            var options = {
            width: 300, height: 100,
            redFrom: 0, redTo: 80,
            yellowFrom:80, yellowTo: 90,
            greenFrom:90, greenTo: 100,
            minorTicks: 5
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div_2'));

            chart.draw(data, options);
        }
        function drawChart3() 
        {
            var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['', {{$porcentaje_comisionado_cierre}}],
            ]);

            var options = {
            width: 300, height: 100,
            redFrom: 0, redTo: 80,
            yellowFrom:80, yellowTo: 90,
            greenFrom:90, greenTo: 100,
            minorTicks: 5
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div_3'));

            chart.draw(data, options);
        }
        function ejecuta_finalizacion() 
        {
            document.getElementById('forma_finaliza').submit();
        }

        function confirmar_finalizacion()
        {
            document.getElementById('modal_finalizacion').style.display="block"
        }
        function cancelar_finalizacion()
        {
            document.getElementById('modal_finalizacion').style.display="none"
        }

        function confirmar_reset()
        {
            document.getElementById('modal_reset').style.display="block"
        }
        function ejecuta_reset()
        {
            document.getElementById('forma_reset').submit();
        }
        function cancelar_reset()
        {
            document.getElementById('modal_reset').style.display="none"
        }
        function ejecuta_calculo(tipo)
        {
            document.getElementById('modal_procesa').style.display="block";
            if(tipo==1){
                document.getElementById('mensaje').innerHTML = "Ejecutando Adelanto";
                document.getElementById('forma_adelanto').submit();
                }
            if(tipo==2){
                document.getElementById('mensaje').innerHTML = "Ejecutando Cierre";
                document.getElementById('forma_cierre').submit();
                }
                
        }
        function carga_ventas_callidus()
        {
            document.getElementById('modal_procesa').style.display="block";
            document.getElementById('mensaje').innerHTML = "Cargando Ventas Callidus";
            document.getElementById('carga_ventas_callidus').submit();
        }
        function carga_residual_callidus()
        {
            document.getElementById('modal_procesa').style.display="block";
            document.getElementById('mensaje').innerHTML = "Cargando Residual";
            document.getElementById('carga_residual_callidus').submit();
        }
        @if(session('status')!='')

            //setTimeout(eliminar_estatus(), 6000);
            function eliminar_estatus() {
                document.getElementById("estatus1").style.display="none";
                document.getElementById("estatus2").style.display="none";
                }   
        @endif
        </script>
</x-app-layout>
