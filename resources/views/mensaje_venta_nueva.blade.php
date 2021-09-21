<x-app-layout>
    <x-slot name="header">
            {{ __('Mensaje') }}
    </x-slot>

    <div class="flex justify-center flex-col">
        <div class="w-full flex flex-row pt-32 justify-center">
            <div class="w-10/12 lg:w-1/3 border-l-8 {{$estatus=='OK'?'border-green-700':'border-red-700'}} py-6 flex flex-row">
                <div class="px-4 text-4xl {{$estatus=='OK'?'text-green-700':'text-red-700'}}">
                    {!!$estatus=='OK'?'<i class="fas fa-check-circle"></i>':'<i class="fas fa-exclamation-circle"></i>'!!}
                </div>
                <div>{{$mensaje}}</div>
            </div>
        </div>
        <div class="w-full flex flex-row pt-10 justify-center">
            <a href="{{$liga.$parametros}}">Capturar nueva linea de la misma cuenta</a>
        </div>
        <div class="w-full flex flex-row py-5 justify-center">
            <a href="{{$liga}}">Capturar nueva linea</a>
        </div>

    </div>


</x-app-layout>
