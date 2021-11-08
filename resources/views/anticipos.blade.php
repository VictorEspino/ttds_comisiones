<x-app-layout>
    <x-slot name="header">
            {{ __('Anticipos extraordinarios') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-xl font-bold text-gray-100">Anticipos extraordinarios</div>
        </div> <!--FIN ENCABEZADO-->
        @if(session('status')!='')
        <div class="w-full text-sm rounded font-bold p-2 bg-green-300 text-gray-600">
            {{session('status')}}
        </div>
        @endif
        
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 pb-7 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-col lg:flex-row justify-between space-y-3 lg:space-y-0">
                
                <div class="w-full lg:w-1/2 flex flex-col">
                    <form action="{{route('anticipos_extraordinarios')}}" class="" method="GET">
                        <input type="hidden" name="f" value="1">
                        <div class="w-full flex flex-col md:flex-row p-1">
                            <div class="w-full md:w-1/6">
                                <span class="text-gray-700 text-sm">Estatus</span>
                            </div>
                            <div class="w-full md:w-5/6">
                                <select name="aplicado" class="w-full md:w-2/3 rounded p-1 border border-gray-300">
                                    <option value=""></option>
                                    <option value="1" {{$aplicado=='1'?'selected':''}}>Procesado</option>
                                    <option value="0" {{$aplicado=='0'?'selected':''}}>No procesado</option>
                                </select>
                            </div>
                        </div>
                        @if(Auth::user()->perfil!='distribuidor')
                        <div class="w-full flex flex-col md:flex-row p-1">
                            <div class="w-full md:w-1/6">
                                <span class="text-gray-700 text-sm">Distribuidor</span>
                            </div>
                            <div class="w-full md:w-5/6">
                                <select name="distribuidor" class="w-full md:w-2/3 rounded p-1 border border-gray-300">
                                    <option value=""></option>
                                    @foreach($distribuidores as $distribuidor)
                                    <option value="{{$distribuidor->user_id}}" {{$distribuidor->user_id==$distribuidor_id?'selected':''}}>{{$distribuidor->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="w-full flex flex-col md:flex-row p-1">
                            <div class="w-full md:w-1/6">
                                <span class="text-gray-700 text-sm">Periodo</span>
                            </div>
                            <div class="w-full md:w-5/6">
                                <select name="periodo" class="w-full md:w-2/3 rounded p-1 border border-gray-300">
                                    <option value=""></option>
                                    @foreach($periodos as $periodo)
                                    <option value="{{$periodo->id}}" {{$periodo_id==$periodo->id?'selected':''}}>{{$periodo->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                        <button class="rounded p-1 border bg-ttds hover:bg-ttds_hover text-gray-100 font-semibold">Buscar</button>
                        </div>
                    </form>
                </div>
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end text-xs">
                {{$pagos->links()}}
                </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:space-x-5 flex items-start justify-center pt-2">
                <div id="tabla" class="w-full pt-5 flex flex-col"> <!--TABLA DE CONTENIDO-->
                    <div class="w-full flex justify-center pb-3"><span class="font-semibold text-sm text-gray-700">Anticipos</span></div>
                    <div class="w-full flex justify-center px-2 hidden md:inline md:flex md:w-full md:justify-center">
                        <div class="table">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg"><center>Distribuidor</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Periodo</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Total a Anticipo</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>PDF</center></div>
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>XML</center></div>
                                    @if(Auth::user()->perfil=="distribuidor")
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm"><center>Tramitar<br>pago</center></div>
                                    @endif
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2  {{(Auth::user()->perfil=='distribuidor')?'rounded-tr-lg':''}} text-sm"><center>Procesado</center></div>
                                    @if(Auth::user()->perfil!="distribuidor")
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg"><center>Cambiar<br>estatus a:</center></div>
                                    @endif
                                </div>
                                <?php $color=true; ?>
                                @foreach($pagos as $pago)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$pago->user->name}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{{$pago->periodo->descripcion}}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>${{number_format($pago->anticipo,0)}}</center></div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{!!$pago->user->detalles->emite_factura=="1"?(is_null($pago->pdf)?'':'<a href="/facturas/'.$pago->pdf.'" download><i class="text-2xl text-red-700 far fa-file-pdf"></i></a>'):'NA'!!}</div>
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">{!!$pago->user->detalles->emite_factura=="1"?(is_null($pago->xml)?'':'<a href="/facturas/'.$pago->xml.'" download><i class="text-2xl text-blue-600 far fa-file-code"></i></a>'):'NA'!!}</div>
                                    @if(Auth::user()->perfil=="distribuidor")
                                        @if($pago->aplicado)
                                            <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>{!!$pago->user->detalles->emite_factura=="1"?'OK':'NA'!!}</div>
                                        @else
                                            <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm"><center>{!!$pago->user->detalles->emite_factura=="1"?'<a href="/facturar_anticipo/'.$pago->id.'">Tramitar</a>':'NA'!!}</div>
                                        @endif
                                    @endif
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-green-300 py-1 px-2 mx-2 text-sm"><center>{!!$pago->aplicado?'<i class="fas fa-check-circle"></i>':''!!}</center></div>
                                    @if(Auth::user()->perfil!="distribuidor")
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">
                                        <form method="POST" action="{{route('cambiar_estatus_anticipo')}}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$pago->id}}">
                                            <input type="hidden" name="nombre" value="{{$pago->user->name}}">
                                            <input type="hidden" name="monto" value="{{$pago->anticipo}}">
                                            <input type="hidden" name="nuevo_estatus" value="{{$pago->aplicado?'0':'1'}}">
                                            <button class="rounded {{$pago->aplicado=="0"?'bg-green-500 hover:bg-green-700':'bg-red-500 hover:bg-red-700'}} text-sm text-gray-100 font-semibold py-1 px-3">{{$pago->aplicado?'Pendiente':'Procesado'}}</button>
                                        </form>
                                    </div>
                                    @endif
                                    
                                </div>
                                <?php $color=!$color; ?>
                                @endforeach
                        
                            </div>
                        </div>
                    </div>  
                    <div class="md:hidden w-full flex justify-center px-2">
                        <div class="table w-10/12">
                            <div class="table-row-group flex">
                                <div class="table-row rounded-tl-lg rounded-tr-lg">
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tl-lg {{Auth::user()->perfil=="distribuidor"?'rounded-tr-lg':''}}"><center>Datos</center></div>
                                    @if(Auth::user()->perfil!="distribuidor")
                                    <div class="table-cell border-l font-semibold bg-ttds-encabezado text-gray-200 py-1 px-2 mx-2 text-sm rounded-tr-lg"><center>Cambiar<br>estatus a:</center></div>
                                    @endif
                                </div>
                                <?php $color=true; ?>
                                @foreach($pagos as $pago)
                                <div class="table-row">
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">
                                        <b>{{$pago->user->name}}</b><br>
                                        <span class="text-xs">{{$pago->periodo->descripcion}}</span><br>
                                        <b>${{number_format($pago->anticipo,0)}}</b><br>
                                        {!!$pago->aplicado?'PROCESADO':'NO PROCESADO'!!}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{!!$pago->user->detalles->emite_factura=="1"?(is_null($pago->pdf)?'':'<a href="/facturas/'.$pago->pdf.'" download><i class="text-2xl text-red-700 far fa-file-pdf"></i></a>'):'NA'!!}

                                    </div>
                                    @if(Auth::user()->perfil!="distribuidor")
                                    <div class="table-cell border-l border-b border-gray-300 font-ligth {{$color?'bg-gray-100':'bg-white'}} text-gray-700 py-1 px-2 mx-2 text-sm">
                                        <form method="POST" action="{{route('cambiar_estatus_anticipo')}}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$pago->id}}">
                                            <input type="hidden" name="nombre" value="{{$pago->user->name}}">
                                            <input type="hidden" name="monto" value="{{$pago->anticipo}}">
                                            <input type="hidden" name="nuevo_estatus" value="{{$pago->aplicado?'0':'1'}}">
                                            <button class="rounded {{$pago->aplicado=="0"?'bg-green-500 hover:bg-green-700':'bg-red-500 hover:bg-red-700'}} text-sm text-gray-100 font-semibold py-1 px-3">{{$pago->aplicado?'Pendiente':'Procesado'}}</button>
                                        </form>
                                    </div>
                                    @endif
                                    
                                </div>
                                <?php $color=!$color; ?>
                                @endforeach
                        
                            </div>
                        </div>
                    </div>                  
                </div><!--FIN DE TABLA -->
                
                
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
</x-app-layout>