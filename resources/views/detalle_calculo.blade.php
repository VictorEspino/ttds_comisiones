<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ttds leading-tight">
            {{ __('Detalle de Control') }}
        </h2>
    </x-slot>
    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-xl font-bold text-gray-100">Calculo de comisiones</div>
            <div class="w-full text-lg font-semibold text-gray-100">{{$descripcion}}</div>            
            <div class="w-full text-xs font-semibold text-gray-100">De {{$fecha_inicio}} a {{$fecha_fin}}</div>                        
        </div> <!--FIN ENCABEZADO-->
        @if(session('status')!='')
            <div class="w-full flex justify-center p-3 bg-green-300">
                <span class="font-semibold text-sm text-gray-600">{{session('status')}}</span>
            </div>    
        @endif
        <div class="flex flex-col md:space-x-5 md:space-y-0 items-start md:flex-row">
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Validacion Ventas</div>
                <div class="w-full flex flex-row border rounded-b-lg shadow-lg">  
                    <div class="w-2/3 px-3 pt-2">
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
                    <div class="w-1/3 flex justify-center p-3">
                        <div class="flex justify-center" id="chart_div" style="width: 400px; height: 120px;"></div>
                    </div> 
                </div>
            </div>
            <div class="w-full p-3 md:w-1/2 md:p-5 flex flex-col">
                <div class="w-full bg-gray-200 p-2 rounded-t-lg">Callidus</div>
                <div class="w-full border-r border-l p-2 flex flex-col">
                    <div class="w-full flex justify-center text-4xl font-semibold text-ttds">{{$n_callidus}}</div>
                    <div class="w-full flex justify-center text-sm">Registros</div>
                </div>
                <div class="w-full border-b border-r border-l rounded-b shadow-lg">
                    <form method="post" action="{{route('callidus_import')}}" enctype="multipart/form-data">
                        @csrf
                    <div class="w-full rounded-b-lg p-3 flex flex-col"> <!--CONTENIDO-->
                        <div class="w-full flex flex-row space-x-2">
                            <div class="w-full">
                                <span class="text-xs text-ttds">Archivo</span><br>
                                <input type="hidden" name="id_calculo" value="{{$id_calculo}}" id="id_calculo">
                                <input class="w-full rounded p-1 border border-gray-300 bg-white" type="file" name="file" value="{{old('file')}}" id="file">
                                @error('file')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                                @enderror                    
                            </div>                
                        </div>
                    </div> <!--FIN CONTENIDO-->
                    <div class="w-full flex justify-center py-4">
                        <button class="rounded p-1 border bg-ttds hover:bg-ttdshover text-gray-100 font-semibold">Guardar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:space-x-5 md:space-y-0 items-start md:flex-row">
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Calculo de Comisiones</div>
                <div class="w-full flex flex-row border rounded-b-lg shadow-lg p-3">  
                    <form class="w-full" method="post" action="{{route('calculo_ejecutar')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$id_calculo}}">
                        <button class="bg-ttds text-gray-200 text-4xl font-semibold rounded-lg hover:bg-ttds-hover shadow-lg w-full border p-10">
                            Ejecutar
                        </button>
                    </form>
                </div>
            </div>
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Resumen de Pago</div>
                <div class="w-full flex flex-row border rounded-b-lg shadow-lg">  
                    <div class="w-2/3 px-3 pt-2">
                        <div class="w-full flex flex-row border-b text-lg font-semibold">
                            <div class="w-2/3">
                                Total de Ventas Procesadas 
                            </div>
                            <div class="w-1/3 border-b flex justify-center">
                                {{$totales_comision}}
                            </div>
                        </div>
                        <div class="w-full flex flex-row border-b text-sm">
                            <div class="w-2/3 px-3">
                                Ventas Pagadas
                            </div>
                            <div class="w-1/3 border-b flex justify-center">
                                <a href="/transacciones_resumen_calculo/{{$id_calculo}}/PAGO">{{$pagados}}</a>
                            </div>
                        </div>
                        <div class="w-full flex flex-row border-b text-sm">
                            <div class="w-2/3 px-3">
                                Ventas NO Pagadas 
                            </div>
                            <div class="w-1/3 flex justify-center">
                            <a href="/transacciones_resumen_calculo/{{$id_calculo}}/NO PAGO">{{$no_pagados}}</a>
                            </div>
                        </div>
                    </div>  
                    <div class="w-1/3 flex justify-center p-3">
                        <div class="flex justify-center" id="chart_div_2" style="width: 400px; height: 120px;"></div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="flex flex-col md:space-x-5 md:space-y-0 items-start md:flex-row">
            <div class="w-full md:w-1/2 flex flex-col justify-center md:p-5 p-3">
                <div class="w-full bg-gray-200 flex flex-col p-2 rounded-t-lg">Detalles Distribuidor</div>
                <div class="w-full flex flex-row border rounded-b-lg shadow-lg p-3">  
                    <form class="w-full" method="get" action="{{route('acciones_distribuidores_calculo',['id'=>$id_calculo])}}">
                        @csrf
                        <button class="bg-green-500 text-gray-200 text-4xl font-semibold rounded-lg hover:bg-green-700 shadow-lg w-full border p-5">
                            Pagos
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);
        google.charts.setOnLoadCallback(drawChart2);

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
            ['', {{$porcentaje_comisionado}}],
            ]);

            var options = {
            width: 400, height: 120,
            redFrom: 0, redTo: 80,
            yellowFrom:80, yellowTo: 90,
            greenFrom:90, greenTo: 100,
            minorTicks: 5
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div_2'));

            chart.draw(data, options);
        }
        </script>
</x-app-layout>
