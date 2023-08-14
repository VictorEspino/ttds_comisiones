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
        @foreach($registros as $registro)
        <div class="flex flex-row p-3 border">
            <div class="flex flex-row py-1 px-1 w-36 border">
                <div><img class="w-12" src="{{asset('images/att.png')}}"></div>
                <div class="flex flex-col">
                    <div class="text-xs font-bold">{{$registro->telefono}}</div>
                    <div class="text-xs">{{$registro->fecha}}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </body>
</html>