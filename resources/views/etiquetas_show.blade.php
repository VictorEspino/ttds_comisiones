<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}?v=4">
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.js" defer></script>
<!-- PARA EL DASHBOARD -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    </head>
    <body>  
    <div class="flex flex-wrap">
        @php
            $ciclo=1;
        @endphp
        @foreach($registros as $registro)
        @if($ciclo==1)
            @php
                $fechaActual = $registro->fecha;
                $intervalo = new DateInterval('P180D');
                $fechaNueva = new DateTime($fechaActual);
                $fechaNueva->add($intervalo);
                $fechaNuevaFormateada = $fechaNueva->format('Y-m-d'); // Formatea la nueva fecha como yyyy-mm-dd
            @endphp
        @endif
        <div class="flex flex-row border">
            <div class="flex flex-row py-1 px-1 w-40">
                <div class="{{$marca=='unefon'?'px-2':''}} flex flex-col">
                    <div class="w-full flex justify-center">
                        <img class="w-8" src="{{asset('images/bsr.png')}}">
                    </div>
                    <div class="w-full">
                        <img class="{{$marca=='unefon'?'w-8':'w-12'}}" src="{{$marca=='unefon'?asset('images/unefon.png'):asset('images/att.png')}}">
                    </div>
                </div>               
                <div class="flex flex-col items-center">
                    <div class="h-1/3 text-xs font-bold flex justify-center pt-1">{{$registro->telefono}}</div>
                    <div class="text-xs">Act {{$registro->fecha}}</div>
                    <div class="text-xs">Ven {{$fechaNuevaFormateada}}</div>
                </div>
            </div>
        </div>
        @php
            $ciclo=$ciclo+1;
        @endphp
        @endforeach
    </div>
    </body>
</html>