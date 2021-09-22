<x-app-layout>
    <x-slot name="header">
            {{ __('Importar Ventas') }}
    </x-slot>

    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-lg font-semibold text-gray-100">Archivo de Ventas</div>            
            <div class="w-full text-sm font-semibold text-gray-100">{{Auth::user()->name}}</div>            
        </div> <!--FIN ENCABEZADO-->
        <form method="post" action="{{Auth::user()->perfil=='distribuidor'?route('ventas_import'):route('ventas_import_admin')}}" enctype="multipart/form-data">
            @csrf
        <div class="w-full rounded-b-lg bg-ttds-secundario p-3 flex flex-col"> <!--CONTENIDO-->
            <div class="w-full flex flex-row space-x-2">
                <div class="w-full">
                    <span class="text-xs text-ttds">Archivo</span><br>
                    <input class="w-full rounded p-1 border border-gray-300 bg-white" type="file" name="file" value="{{old('file')}}" id="file">
                    @error('file')
                      <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                    @enderror                    
                </div>                
            </div>
        </div> <!--FIN CONTENIDO-->
        <div class="w-full flex justify-center py-4 bg-ttds-secundario">
            <button class="rounded p-1 border bg-ttds hover:bg-ttdshover text-gray-100 font-semibold">Guardar</button>
        </div>
        </form>
        @if(session('status'))
        <div class="bg-green-200 p-4 flex justify-center font-bold rounded-b-lg">
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
</x-app-layout>
