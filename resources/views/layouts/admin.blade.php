<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Inmobiliaria Los Andes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#FDFBF7] font-sans antialiased min-h-screen relative overflow-x-hidden m-0 p-0" style="background-image: url('{{ asset('images/tu-fondo.jpg') }}'); background-size: cover; background-position: center;">

    <!-- Contenedor absoluto global -->
    <div class="min-h-screen w-full m-0 p-0 relative" x-data="{ sidebarOpen: false }">
        
        <!-- Botón de Hamburguesa que se desplaza junto con el menú -->
        <div class="absolute top-1 z-50 transition-all duration-300" :class="sidebarOpen ? 'left-[285px]' : 'left-0'">
            <button @click="sidebarOpen = !sidebarOpen" class="text-[#2C4A3E] hover:text-emerald-700 focus:outline-none py-2 px-2.5 rounded-r-xl bg-white/95 backdrop-blur-md shadow-md border-y border-r border-emerald-100 transition cursor-pointer flex items-center justify-center w-9 h-9" :title="sidebarOpen ? 'Ocultar menú' : 'Mostrar menú'">
                <i class="fa-solid text-base" :class="sidebarOpen ? 'fa-chevron-up' : 'fa-bars'"></i>
            </button>
        </div>

        <!-- Contenedor general de la aplicación -->
        <div class="relative flex min-h-screen w-full m-0 p-0">
            
            <!-- BARRA LATERAL FIJA / DESPLEGABLE -->
            <aside id="sidebar" 
                   x-show="sidebarOpen" 
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 -translate-x-4 scale-95"
                   x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                   x-transition:leave-end="opacity-0 -translate-x-4 scale-95"
                   @click.away="sidebarOpen = false"
                   class="absolute left-0 top-12 z-40 w-72 bg-[#EFECE6]/95 backdrop-blur-md border-r border-emerald-200/60 shadow-xl flex flex-col justify-between p-4 space-y-4 h-[calc(100vh-3rem)]">
                
                <!-- Contenido del Menú -->
                <div class="space-y-4 overflow-y-auto no-scrollbar">
                    <!-- Título del Menú -->
                    <div class="px-2 flex justify-between items-center border-b border-emerald-200/40 pb-2">
                        <h3 class="font-extrabold text-[#2C4A3E] text-xs uppercase tracking-wider">Menú Principal</h3>
                        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-[#2C4A3E] cursor-pointer p-1">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Opciones de Navegación -->
                    <nav class="space-y-1 text-xs font-bold">
                        <!-- Resumen General -->
                        <a href="{{ route('admin.resumen') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition shadow-sm {{ request()->routeIs('admin.resumen') ? 'bg-[#2C4A3E] text-white' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-house-chimney text-sm"></i> Resumen General
                        </a>

                        <!-- Tu Agenda -->
                        <a href="{{ route('admin.agenda') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.agenda*') ? 'bg-[#2C4A3E] text-white shadow-sm' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-calendar-days text-sm"></i> Tu Agenda
                        </a>

                        <!-- Tu Citas -->
                        <a href="{{ route('gestion.citas') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.citas*') ? 'bg-[#2C4A3E] text-white shadow-sm' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-clock text-sm"></i> Tu Citas
                        </a>

                        <!-- Asesores -->
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition shadow-sm {{ request()->routeIs('users.*') ? 'bg-[#2C4A3E] text-white' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-users text-sm"></i> Asesores
                        </a>

                        <!-- Tu Catálogo -->
                        <a href="{{ route('properties.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('properties*') ? 'bg-[#2C4A3E] text-white shadow-sm' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-building text-sm"></i> Tu Catálogo
                        </a>

                        <!-- Tus Clientes -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.clientes*') ? 'bg-[#2C4A3E] text-white shadow-sm' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-user-group text-sm"></i> Tus Clientes
                        </a>

                        <!-- Citas Totales Integral -->
                        <a href="{{ route('admin.citas-totales') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.citas-totales*') ? 'bg-[#2C4A3E] text-white shadow-sm' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}"> 
                            <i class="fa-solid fa-calendar-check text-sm"></i> Citas Totales Integral
                        </a>

                        <!-- Sección Trámites -->
                        <div class="pt-1">
                            <div class="text-[9px] font-extrabold text-gray-500 uppercase px-3 mb-0.5">Trámites</div>
                            <div class="space-y-0.5 pl-3 text-gray-600 font-semibold text-[11px]">
                                <a href="#" class="block py-0.5 hover:text-[#2C4A3E] transition">▪ En Proceso: 5</a>
                                <a href="#" class="block py-0.5 hover:text-[#2C4A3E] transition">▪ Completados: 12</a>
                                <a href="#" class="block py-0.5 hover:text-[#2C4A3E] transition">▪ Pendientes: 3</a>
                            </div>
                        </div>

                        <!-- Cuadro de Mando Integral -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl transition pt-1 {{ request()->routeIs('admin.cuadro-mando*') ? 'bg-[#2C4A3E] text-white shadow-sm' : 'text-[#2C4A3E] hover:bg-emerald-100/50' }}">
                            <i class="fa-solid fa-gear text-sm"></i> Cuadro de Mando Integral
                        </a>
                    </nav>
                </div>

                <!-- Footer del Sidebar -->
                <div class="pt-3 border-t border-emerald-200/40 space-y-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-1.5 rounded-xl text-red-600 hover:bg-red-50 font-bold transition text-xs cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                        </button>
                    </form>

                    <div class="bg-white/60 p-1.5 rounded-xl border border-emerald-100 text-center">
                        <span class="text-[8px] font-bold text-gray-500 block mb-1">Visítanos en redes</span>
                        <div class="flex justify-center gap-2 text-xs text-[#2C4A3E]">
                            <i class="fab fa-instagram"></i>
                            <i class="fab fa-facebook"></i>
                            <i class="fab fa-tiktok"></i>
                            <i class="fab fa-youtube"></i>
                            <i class="fab fa-whatsapp"></i>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ÁREA DE CONTENIDO DINÁMICO (Se desplaza suavemente a la derecha cuando se abre el menú) -->
            <main class="flex-1 transition-all duration-300 w-full m-0 p-0" :class="sidebarOpen ? 'pl-72' : 'pl-0'">
                @yield('admin_content')
            </main>

        </div>
    </div>
</body>
</html>