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
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @livewireStyles

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.js" defer></script>

<!-- PARA EL DASHBOARD -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss/dist/tailwind.min.css">
    <style>
    /* The side navigation menu */
.sidenav {
  height: 100%; /* 100% Full-height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  /*z-index: 1; /* Stay on top */
  /*top: 0; /* Stay at the top */
  /*left: 0;*/
  background-color: #025170; /* Black*/
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px; /* Place content 60px from the top */
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
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
  .sidenav a {font-size: 18px;}
}

</style>
    </head>
    
    <body class="font-sans antialiased" >
        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            <header class="bg-gray-100">
                <div id="mySidenav" class="sidenav flex flex-col">
                    <div><a href="javascript:void(0)" class="closebtn text-ttds" onclick="closeNav()">&times;</a></div>
                    <div class="px-3 text-white flex flex-col">
                        <div class="text">
                            <i class="fas fa-tasks"></i>
                            Distribuidores
                        </div>
                        <div class="flex flex-col" id="distribuidores">
                            <div class="pl-5 pt-2">
                                <a href="{{route('distribuidores_nuevo')}}">
                                    <span class="text-ttds"><i class="fas fa-user-tie"></i></span>
                                    Nuevo Distribuidor
                                </a>
                            </div>
                            <div class="pl-5 pt-2">
                                <a href="{{route('distribuidores_admin')}}">
                                    <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                    Administracion
                                </a>
                            </div>
                            <div class="pl-5 pt-2">
                                <a href="{{route('distribuidores_anticipos_extraordinarios')}}">
                                    <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                    Anticipos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 pt-2 text-white flex flex-col">
                        <div class="text">
                            <i class="fas fa-tasks"></i>
                            Ventas
                        </div>
                        <div class="flex flex-col" id="distribuidores">
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
                        </div>
                    </div>
                    <div class="px-3 pt-2 text-white flex flex-col">
                        <div class="text">
                            <i class="fas fa-tasks"></i>
                            Periodo Control
                        </div>
                        <div class="flex flex-col" id="distribuidores">
                            <div class="pl-5 pt-2">
                                <a href="{{route('calculo_nuevo')}}">
                                    <span class="text-ttds"><i class="fas fa-coins"></i></span>
                                    Registro Nuevo
                                </a>
                            </div>
                            <div class="pl-5 pt-2">
                                <a href="{{route('seguimiento_calculos')}}">
                                    <span class="text-yellow-300"><i class="fas fa-file-upload"></i></span>
                                    Seguimiento
                                </a>
                            </div>
                            <div class="pl-5 pt-2">
                                <a href="{{route('ventas_admin')}}">
                                    <span class="text-yellow-300"><i class="fas fa-table"></i></span>
                                    Validacion Ventas
                                </a>
                            </div>
                        </div>
                    </div>
                    

                </div>
                <div class="max-w-7xl mx-auto py-4 px-2 sm:px-2 px-4 flex justify-between flex-row">
                    <div class="flex">
                        <span onclick="openNav()" class="text-ttds font-bold text-2xl"><i class="fas fa-bars"></i></span>
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
  document.getElementById("mySidenav").style.width = "250px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>
    </body>
    
</html>
