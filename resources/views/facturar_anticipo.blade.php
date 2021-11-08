<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ttds leading-tight">
            {{ __('Facturar anticipo') }}
        </h2>
    </x-slot>
    <div class="flex flex-col w-full bg-white text-gray-700 shadow-lg rounded-lg">
        <div class="w-full rounded-t-lg bg-ttds-encabezado p-3 flex flex-col border-b border-gray-800"> <!--ENCABEZADO-->
            <div class="w-full text-xl font-bold text-gray-100">Facturar Anticipo</div>
        </div> <!--FIN ENCABEZADO-->
        <div class="w-full flex justify-center pt-10">
            <span class="text-3xl text-gray-700">{{$anticipo->descripcion}}<span>
        </div>
        <div class="w-full flex justify-center pt-10">
            <span class="text-5xl text-green-600">${{number_format($anticipo->anticipo)}}<span>
        </div>
        <div class="w-full flex justify-center p-4">
            <span class="text-base text-gray-700">Ventas de {{$anticipo->periodo->descripcion}}<span>
        </div>
        @if(!is_null($anticipo->pdf))
        <div class="w-full flex flex-col pt-3 pb-3">
            <div class="w-full flex flex-row ">
                <div class="w-1/3 flex flex-col">
                    <div class="flex justify-center">
                        <a href="/facturas/{{$anticipo->pdf}}" download>
                            <i class="text-2xl text-red-700 far fa-file-pdf"></i> PDF 
                        </a>
                    </div>

                </div>
                <div class="w-1/3 flex flex-col">
                    <div class="flex justify-center">
                        <a href="/facturas/{{$anticipo->xml}}" download>
                            <i class="text-2xl text-blue-600 far fa-file-code"></i> XML
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="w-full p-4">
            <form action="{{route('facturar_anticipo_save')}}" method="POST" enctype="multipart/form-data">
                @csrf
                    <div class="w-full flex flex-col md:flex-row ">
                        <div class="w-full md:w-5/12 flex flex-col">
                            <span class="text-sm text-gray-700">Archivo PDF</span>
                            <input class="w-full" type="file" name="pdf_file">
                            @error('pdf_file')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                            @enderror  
                        </div>
                        <div class="w-full md:w-5/12 flex flex-col">
                            <span class="text-sm text-gray-700">Archivo XML</span>
                            <input class="w-full" type="file" name="xml_file">
                            @error('xml_file')
                                <br><span class="text-xs italic text-red-700 text-xs">{{ $message }}</span>
                            @enderror 
                        </div>
                        <div class="w-full md:w-2/12 flex flex-col justify-center">
                            <input type="hidden" name="id" value="{{$anticipo->id}}">
                            <button class="w-1/3 md:w-full p-3 bg-green-500 hover:bg-green-700 font-bold rounded-lg text-gray-200 text-xl">Cargar</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</x-app-layout>
