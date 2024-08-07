<?php
$nuevos_pagos=App\Http\Servicios\Notificaciones::nuevos_pagos();
$nuevas_facturas=App\Http\Servicios\Notificaciones::nuevas_facturas();
$nuevos_anticipos=App\Http\Servicios\Notificaciones::nuevos_anticipos();
$nuevos_pagos_a_cuenta=App\Http\Servicios\Notificaciones::nuevos_pagos_a_cuenta();
$nuevas_facturas_anticipo=App\Http\Servicios\Notificaciones::nuevas_facturas_anticipo();
$nuevas_facturas_pagos_a_cuenta=App\Http\Servicios\Notificaciones::nuevas_facturas_pagos_a_cuenta();
?>
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
        @livewireStyles

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.js" defer></script>

<!-- PARA EL DASHBOARD -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    
    <style>
    /* The side navigation menu */
.sidenav {
  height: 100%; /* 100% Full-height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  /*z-index: 1; /* Stay on top */
  /*top: 0; /* Stay at the top */
  /*left: 0;*/
  font-size: 14px;
  background-color:#383c3f; /* Black*/
  overflow-x: hidden; /* Disable horizontal scroll */
  overflow-y: scroll;
  padding-top: 25px; /* Place content 60px from the top */
  transition: 0.2s; /* 0.5 second transition effect to slide in the sidenav */
}

/* The navigation menu links */
.OLD_sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 18px;
  color: #919191;
  display: block;
  transition: 0.3s;
}

/* When you mouse over the navigation links, change their color */
.sidenav a:hover {
  color: #f1f1f1;
}

/* Position and style the close button (top right corner) */
.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 25px;
  margin-left: 50px;
}

/* Style page content - use this if you want to push the page content to the right when you open the side navigation */
#main {
  transition: margin-left .5s;
  padding: 20px;
}

/* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 14px;}
}

</style>
    </head>
    
    <body class="font-sans antialiased" >
        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            <header class="bg-gray-100">
                <div id="mySidenav" class="sidenav flex flex-col">
                    <div class="flex flex-col overflow-y-auto">
                        <div><a href="javascript:void(0)" class="closebtn text-ttds" onclick="closeNav()">&times;</a></div>
                        @if(Auth::user()->perfil!="distribuidor" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo"))
                        <div class="px-3 text-white flex flex-col">
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Distribuidores
                            </div>
                            <div class="flex flex-col" id="distribuidores">
                                @if(Auth::user()->perfil=="admin")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('distribuidores_nuevo')}}">
                                        <span class="text-ttds"><i class="fas fa-user-tie"></i></span>
                                        Nuevo Distribuidor
                                    </a>
                                </div>     
                                @endif
                                @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('distribuidores_admin')}}">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Base Distribuidores
                                    </a>
                                </div>
                                @endif
                                @if(Auth::user()->perfil=="admin")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('distribuidores_anticipos_extraordinarios')}}">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Anticipos
                                    </a>
                                </div>
                                <div class="pl-5 pt-2">
                                    <a href="{{route('pagos_a_cuenta')}}">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Pago a cuenta de comisiones
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo")
                        <div class="px-3 pt-2 text-white flex flex-col">
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Plantilla Ventas
                            </div>
                            <div class="flex flex-col" id="empleados">
                                @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('empleados_nuevo')}}">
                                        <span class="text-ttds"><i class="fas fa-user-tie"></i></span>
                                        Nuevo
                                    </a>
                                </div>
                                <div class="pl-5 pt-2">
                                    <a href="{{route('empleados_admin')}}">
                                        <span class="text-ttds"><i class="fas fa-user-tie"></i></span>
                                        Base plantilla
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        <div class="px-3 pt-2 text-white flex flex-col">
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Ventas
                            </div>
                            
                            <div class="flex flex-col" id="distribuidores">
                                @if(Auth::user()->perfil=="gerente" || Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="mesa")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('venta_nueva')}}">
                                        <span class="text-ttds"><i class="fas fa-coins"></i></span>
                                        Registro Nueva
                                    </a>
                                </div>                                
                                <div class="pl-5 pt-2">
                                    <a href="{{route('venta_import')}}">
                                        <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                        Carga Archivo
                                    </a>
                                </div>
                                <div class="pl-5 pt-2">
                                    <a href="{{route('ventas_review')}}">
                                        <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                        Base de ventas
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="px-3 pt-2 text-white flex flex-col"> 
                            @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor" || Auth::user()->perfil=="ejecutivo" || Auth::user()->perfil=="gerente")
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Comisiones
                            </div>
                            @endif
                            <div class="flex flex-col" id="distribuidores">
                                @if(Auth::user()->perfil=="admin")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('calculo_nuevo')}}">
                                        <span class="text-ttds"><i class="fas fa-coins"></i></span>
                                        Nuevo Periodo
                                    </a>
                                </div>
                                @endif
                                @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor" || Auth::user()->perfil=="ejecutivo" || Auth::user()->perfil=="gerente")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('seguimiento_calculos')}}">
                                        <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                        Seguimiento
                                    </a>
                                </div>
                                @endif
                                @if(Auth::user()->perfil!="distribuidor" && Auth::user()->perfil!="gerente" && Auth::user()->perfil!="ejecutivo")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('ventas_admin')}}">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Validacion Ventas
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="px-3 pt-2 text-white flex flex-col">
                            @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor")
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Pagos Comisiones
                            </div>
                            @endif
                            <div class="flex flex-col"> 
                                @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('pagos')}}">
                                        <span class="text-ttds"><i class="fas fa-coins"></i></span>
                                        Base de Pagos
                                    </a>
                                </div>
                                @endif
                                @if($nuevos_pagos!="0" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor" ))
                                <div class="pl-5 pt-2">
                                    <a href="/pagos?np=true">
                                        <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                        Nuevos Pagos <span class="rounded-full bg-red-700 text-white p-2">{{$nuevos_pagos}}</span>
                                    </a>
                                </div>
                                @endif
                                
                                @if(Auth::user()->perfil!="distribuidor" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo"))
                                @if($nuevas_facturas!="0" )
                                <div class="pl-5 pt-2">
                                    <a href="/pagos?nf=true">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Nuevas Facturas <span class="rounded-full bg-red-700 text-white p-2">{{$nuevas_facturas}}</span>
                                    </a>
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                        <div class="px-3 pt-2 text-white flex flex-col">
                            @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor")
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Pagos Anticipos
                            </div>
                            @endif
                            <div class="flex flex-col"> 
                                @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('anticipos_extraordinarios')}}">
                                        <span class="text-ttds"><i class="fas fa-coins"></i></span>
                                        Base de Anticipos
                                    </a>
                                </div>
                                @endif
                                @if($nuevos_anticipos!="0" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor" ))
                                <div class="pl-5 pt-2">
                                    <a href="/anticipos_extraordinarios?np=true">
                                        <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                        Nuevos Pagos <span class="rounded-full bg-red-700 text-white p-2">{{$nuevos_anticipos}}</span>
                                    </a>
                                </div>
                                @endif
                                @if(Auth::user()->perfil!="distribuidor" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo"))
                                @if($nuevas_facturas_anticipo!="0")
                                <div class="pl-5 pt-2">
                                    <a href="/anticipos_extraordinarios?nf=true">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Nuevas Facturas <span class="rounded-full bg-red-700 text-white p-2">{{$nuevas_facturas_anticipo}}</span>
                                    </a>
                                </div>
                                @endif
                                @endif
                                @if(Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor")
                                <div class="pl-5 pt-2">
                                    <a href="{{route('base_pagos_a_cuenta')}}">
                                        <span class="text-ttds"><i class="fas fa-coins"></i></span>
                                        Pagos a cuenta de comisiones
                                    </a>
                                </div>
                                @endif
                                @if($nuevos_pagos_a_cuenta!="0" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo" || Auth::user()->perfil=="distribuidor" ))
                                <div class="pl-5 pt-2">
                                    <a href="/base_pagos_a_cuenta?np=true">
                                        <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                        Nuevos Pagos a cuenta <span class="rounded-full bg-red-700 text-white p-2">{{$nuevos_pagos_a_cuenta}}</span>
                                    </a>
                                </div>
                                @endif
                                @if(Auth::user()->perfil!="distribuidor" && (Auth::user()->perfil=="admin" || Auth::user()->perfil=="administrativo"))
                                @if($nuevas_facturas_pagos_a_cuenta!="0")
                                <div class="pl-5 pt-2">
                                    <a href="/base_pagos_a_cuenta?nf=true">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Nuevas Facturas (Pagos a cuenta) <span class="rounded-full bg-red-700 text-white p-2">{{$nuevas_facturas_pagos_a_cuenta}}</span>
                                    </a>
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                        @if(Auth::user()->perfil=="admin")
                        <div class="px-3 text-white flex flex-col pt-3">
                            <div class="text">
                                <i class="fas fa-tasks"></i>
                                Etiquetas Prepago
                            </div>
                            <div class="flex flex-col">                               
                                <div class="pl-5 pt-2">
                                    <a href="{{route('etiquetas_import')}}">
                                        <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                        Generar Etiquetas
                                    </a>
                                </div>     
                            </div>
                        </div>
                        @endif
                        
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                        <div class="px-3 text-[#383c3f] flex flex-col">.
                        </div>
                    </div> 
                </div>
                <div class="max-w-7xl mx-auto py-4 px-2 sm:px-2 px-4 flex justify-between flex-row">
                    <div class="flex">
                        <span onclick="openNav()" class="text-ttds font-bold text-2xl">
                        <i class="fas fa-bars"></i></span>
                    </div>
                    <div> 
                        <h2 class="font-semibold leading-tight text-ttds text-lg">    
                            {{ $header }} 
                        </h2>
                    </div>
                </div>
                
            </header>

            <!-- Page Content -->
            <main>
            <div class="flex -mb-4">
            
            
            
                <!--

                bg-side-nav w-1/2 md:w-1/6 lg:w-1/6 border-r border-side-nav hidden md:block lg:block
                -->
                <div class="w-full px-3 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
            </main>
        </div>

        @stack('modals')

        @livewireScripts
<script>
        /* Set the width of the side navigation to 250px */
function openNav() {
  document.getElementById("mySidenav").style.width = "220px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>
    </body>
    
</html>
