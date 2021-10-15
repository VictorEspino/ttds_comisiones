<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ttds leading-tight">
            {{ __('Detalle de Conciliacion') }}
        </h2>
    </x-slot>
    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-row justify-between border-b border-gray-800"> <!--ENCABEZADO-->
            <div>
                <div class="w-full text-xl font-bold text-gray-100">Conciliacion AT&T</div>
                <div class="w-full text-lg font-semibold text-gray-100">{{$descripcion}}</div>            
                <div class="w-full text-xs font-semibold text-gray-100">De {{$fecha_inicio}} a {{$fecha_fin}}</div>                        
            </div>
            <div class="md:px-7 flex items-center">
                <form method="post" action="{{route('calculo_reset')}}" id="forma_reset">
                    @csrf
                    <input type="hidden" name="id" value="{{$id_calculo}}">
                    <button type="button" class="rounded px-3 py-2 border bg-gray-500 hover:bg-ttds-hover text-gray-100 font-semibold" onclick="confirmar_reset()">Reset</button>
                </form>
            </div>    
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
            <div class="w-full p-3 md:w-1/2 md:p-5 flex flex-col ">
                <div class="w-full bg-gray-200 p-2 rounded-t-lg">Callidus</div>
                <div class="w-full border rounded-b-lg shadow-lg p-4 md:p-8 flex flex-row">
                    <div class="w-1/2">
                        <div class="w-full flex justify-center text-4xl font-semibold text-ttds-naranja">{{number_format($n_callidus,0)}}</div>
                        <div class="w-full flex justify-center text-sm">Registros Venta</div>
                    </div>
                    <div class="w-1/2">
                        <div class="w-full flex justify-center text-4xl font-semibold text-ttds-naranja">{{number_format($n_callidus_residual,0)}}</div>
                        <div class="w-full flex justify-center text-sm">Registros Residual</div>
                    </div>
                </div>                
            </div>
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Conciliacion</div>
                <div class="w-full flex flex-col border rounded-b-lg shadow-lg p-3 space-y-4">  
                    <div class="w-full">
                        <form class="w-full" method="post" action="{{route('conciliacion_ejecutar')}}" id="forma_conciliacion">
                            @csrf
                            <input type="hidden" name="version" value="1">
                            <input type="hidden" name="id" value="{{$id_calculo}}">
                            <button type="button" onClick="ejecuta_conciliacion()" class="bg-ttds text-gray-200 text-4xl font-semibold rounded-lg hover:bg-ttds-hover shadow-lg w-full border p-10">
                                Ejecutar conciliacion
                            </button>
                        </form>
                    </div>                
                </div>
            </div>
        </div>
        <div class="w-full flex flex-col items-start">
            <div class="w-full flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Acciones y Resultados</div>
                <div class="w-full flex flex-col border rounded-b-lg shadow-lg p-2"> 
                    <div class="w-full flex flex-row pt-3">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="md:hidden w-full p-1"><span class="text-lg font-semibold text-gray-700">Comisiones</span></div>
                            <div class="w-full flex justify-between flex-row">
                                <div class="w-full flex justify-center">
                                    <a href="{{route('diferencias_comisiones',['id'=>$id_calculo])}}">
                                        <i class="text-green-700 text-6xl fas fa-balance-scale"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full flex justify-center text-center text-xs md:text-sm">{{$n_comisiones}} alertas</div>
                        </div>
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-2xl font-semibold text-gray-700">Comisiones</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Revisa los registros pagados que no corresponden con el pago de validacion interna
                                </span>
                            </div>
                        </div>
                    </div>                    
                    <div class="w-full flex flex-row pt-8 pb-6">
                        <div class="w-full md:w-1/2 flex flex-col justify-center items-center">
                            <div class="md:hidden w-full p-1"><span class="text-lg font-semibold text-gray-700">Residuales</span></div>
                            <div class="w-full flex justify-between flex-row">
                                <div class="w-full flex justify-center">
                                    <a href="{{route('diferencias_residual',['id'=>$id_calculo])}}">
                                        <i class="text-indigo-400 text-6xl fa-file-invoice-dollar"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full flex justify-center text-center text-xs md:text-sm">{{$n_residual}} alertas</div>
                        </div>
                        <div class="hidden md:block md:w-1/2 flex flex-col">
                            <div><span class="text-2xl font-semibold text-gray-700">Residuales</span></div>
                            <div class="hidden md:block">
                                <span class="text-xs md:text-sm text-gray-700">
                                    -Revisa aquellos registros que no fueron considerados para residual
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
    </div>
<!--MODALES-->

<div class="fixed hidden inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full" id="modal_reset">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="text-green-500 text-2xl font-bold far fa-check-circle"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 p-3">¿Desea continuar?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Esta accion eliminara todo registro presente en la conciliacion, NO afectara la carga de los archivos de CALLIDUS.
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
<script type="text/javascript">

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
    function ejecuta_conciliacion()
    {
        document.getElementById('modal_procesa').style.display="block";
        document.getElementById('mensaje').innerHTML = "Ejecutando Conciliacion";
        document.getElementById('forma_conciliacion').submit();
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
