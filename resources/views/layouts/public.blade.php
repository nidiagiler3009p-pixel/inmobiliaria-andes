<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Inmobiliaria Los Andes del Ecuador</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js para el menú lateral desplegable -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-[#FDFBF7] text-[#2C4A3E] font-sans antialiased relative overflow-x-hidden"
      style="background-image: url('{{ asset('images/fondo3.png') }}'); background-repeat: repeat;"
      x-data="{ mobileMenuOpen: false }">
    
  <!-- HEADER Y NAVEGACIÓN SUPERIOR UNIFICADA (Más delgada, logos intactos) -->
<header class="bg-[#FAF7F2]/95 backdrop-blur-md border-b border-emerald-100 shadow-sm sticky top-0 z-50 py-1 px-4">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between">
        
        <!-- Izquierda: Botón Menú Hamburguesa + Logo e Identidad -->
        <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-start">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex items-center gap-2 text-[#2C4A3E] focus:outline-none p-1.5 rounded-xl hover:bg-emerald-100/60 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ url('/') }}" class="flex items-center gap-8">
                <!-- Mantiene su tamaño original de altura pero se ajusta al nuevo grosor -->
                <img src="{{ asset('images/conocenos_2.jpg') }}" alt="Logo" class="h-12 w-auto object-contain">
                <div>
                    <p class="text-[9px] text-gray-600 font-bold tracking-wider"></p>
                </div>
            </a>
        </div>

        <!-- Centro: Menú de Iconos Superiores (Compacto en altura pero iconos con su tamaño intacto) -->
        <nav class="hidden lg:flex flex-1 justify-center items-center gap-6 xl:gap-8">
            <!-- Asesorías -->
            <a href="{{ url('/asesorias') }}" class="flex flex-col items-center justify-center group text-decoration-none py-1">
                <img src="{{ asset('images/asesorias_2.jpg') }}" alt="Asesorías" class="w-11 h-11 object-contain group-hover:scale-110 transition">
                <span class="text-[10px] font-bold {{ request()->is('asesorias') ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5' : 'text-[#2C4A3E]' }} mt-1 whitespace-nowrap text-center">asesorias</span>
            </a>

            <!-- Trámites -->
            <a href="{{ url('/tramites') }}" class="flex flex-col items-center justify-center group text-decoration-none py-1">
                <img src="{{ asset('images/tramites_2.jpg') }}" alt="Trámites" class="w-11 h-11 object-contain group-hover:scale-110 transition">
                <span class="text-[10px] font-bold {{ request()->is('tramites') ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5' : 'text-[#2C4A3E]' }} mt-1 whitespace-nowrap text-center">tramites</span>
            </a>

            <!-- Conócenos -->
            <a href="{{ url('/conocenos') }}" class="flex flex-col items-center justify-center group text-decoration-none py-1">
                <img src="{{ asset('images/conocenos2_2.jpg') }}" alt="Conócenos" class="w-12 h-12 object-contain group-hover:scale-110 transition">
                <span class="text-[10px] font-bold {{ request()->is('conocenos') ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5' : 'text-[#2C4A3E]' }} mt-1 whitespace-nowrap text-center">conocenos</span>
            </a>

            <!-- Catálogo -->
            <a href="{{ route('public.catalogo.index') }}" class="flex flex-col items-center justify-center group text-decoration-none py-1">
                <img src="{{ asset('images/catalgo_2.jpg') }}" alt="Catálogo" class="w-11 h-11 object-contain group-hover:scale-110 transition">
                <span class="text-[10px] font-bold {{ request()->is('catalogo') ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5' : 'text-[#2C4A3E]' }} mt-1 whitespace-nowrap text-center">catalogo</span>
            </a>

            <!-- Contáctanos -->
            <a href="{{ url('/contacto') }}" class="flex flex-col items-center justify-center group text-decoration-none py-1">
                <img src="{{ asset('images/contactanos_2.jpg') }}" alt="Contáctanos" class="w-11 h-11 object-contain group-hover:scale-110 transition">
                <span class="text-[10px] font-bold {{ request()->is('contacto') ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5' : 'text-[#2C4A3E]' }} mt-1 whitespace-nowrap text-center">contactanos</span>
            </a>

            <!-- Únete al equipo -->
            <a href="{{ url('/unete') }}" class="flex flex-col items-center justify-center group text-decoration-none py-1">
                <img src="{{ asset('images/unetealequipo_2.jpg') }}" alt="Únete al equipo" class="w-12 h-12 object-contain group-hover:scale-110 transition">
                <span class="text-[10px] font-bold {{ request()->is('unete') ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5' : 'text-[#2C4A3E]' }} mt-1 whitespace-nowrap text-center">unete al equipo</span>
            </a>
        </nav>

    </div>
</header>

<!-- BARRA LATERAL DESPLEGABLE (Menú Hamburguesa) - SIN SUBRAYADOS INDESEADOS -->
    <div x-cloak>
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/40 z-50 transition-opacity"></div>
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-70 shadow-2xl z-60 flex flex-col justify-between overflow-y-auto bg-cover bg-center p-0"
             style="background-image: url('{{ asset('images/images.jpg') }}');">
            <div>
                <!-- SECCIÓN SUPERIOR (CABECERA) -->
                <div class="px-4 py-3 border-b border-gray-700/50 relative flex justify-center items-center bg-[#1a2b24]">
                    <img src="{{ asset('images/hamburguer.jpg') }}" alt="Logo" class="h-15 w-auto object-contain">
                    <button @click="mobileMenuOpen = false" class="absolute right-3 text-gray-300 hover:text-white font-bold text-lg p-1 focus:outline-none">✕</button>
                </div>
                
                <!-- NAVEGACIÓN LATERAL (Con text-decoration-none para evitar líneas extra) -->
                <nav class="p-3 space-y-1 text-xs font-bold">
                    
                    <!-- Inicio -->
                   <a href="{{ url('/conocenos') }}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('/') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-house text-lg"></i> Inicio
</a>

                    <!-- Quienes somos -->
<a href="{{ url('/conocenos') }}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('conocenos') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-users text-lg"></i> Quienes somos
</a>

<!-- Catálogo -->
<a href="{{ route('public.catalogo.index')}}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('catalogo') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-book text-lg"></i> Catálogo
</a>

<!-- Asesorías -->
<a href="{{ url('/asesorias') }}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('asesorias') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-lightbulb text-lg"></i> Asesorías
</a>

<!-- Trámites -->
<a href="{{ url('/tramites') }}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('tramites') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-file text-lg"></i> Trámites
</a>

<!-- Trabaja con nosotros -->
<a href="{{ url('/unete') }}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('unete') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-briefcase text-lg"></i> Trabaja con nosotros
</a>

<!-- Contáctanos -->
<a href="{{ url('/contacto') }}" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-decoration-none text-lg {{ request()->is('contacto') ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm' : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15' }} transition-colors">
    <i class="fa-solid fa-phone text-lg"></i> Contáctanos
</a>
                </nav>
            </div>
            
            <!-- SECCIÓN REDES SOCIALES -->
            <div class="p-5 border-t bg-white/90 backdrop-blur-sm m-3 rounded-3xl border border-emerald-100 shadow-sm">
                <p class="text-xs font-extrabold text-[#2C4A3E] mb-1">Encuéntranos en redes sociales</p>
                <p class="text-[10px] text-gray-600 mb-4">Síguenos y mantente al día con nuestras novedades.</p>
                <div class="flex justify-around items-center">
                    @php
                        $redesSociales = \App\Models\SocialLink::where('is_active', true)->get();
                    @endphp
                    @forelse($redesSociales as $red)
                        <a href="{{ $red->url_or_value ?? '#' }}" target="_blank" class="bg-emerald-900 text-white w-9 h-9 flex items-center justify-center rounded-full hover:scale-110 transition shadow-md text-decoration-none">
                            <i class="fa-solid fa-globe text-sm"></i>
                        </a>
                    @empty
                        <a href="#" class="bg-emerald-900 text-white w-8 h-8 flex items-center justify-center rounded-full text-decoration-none">f</a>
                        <a href="#" class="bg-emerald-900 text-white w-8 h-8 flex items-center justify-center rounded-full text-decoration-none">ig</a>
                        <a href="#" class="bg-emerald-900 text-white w-8 h-8 flex items-center justify-center rounded-full text-decoration-none">w</a>
                        <a href="#" class="bg-emerald-900 text-white w-8 h-8 flex items-center justify-center rounded-full text-decoration-none">yt</a>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENIDO DINÁMICO DE CADA PANTALLA -->
    <main class="min-h-[70vh]">
        @yield('content')
    </main>

    <!-- FOOTER INSTITUCIONAL -->
    <footer class="bg-[#2C4A3E] text-white py-6 px-6 mt-12 relative z-10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6 text-xs">
            <div class="flex items-center gap-4">
                <span class="font-bold">Visítanos en redes</span>
                <div class="flex items-center gap-3">
                    @foreach($redesSociales as $red)
                        <a href="{{ $red->url_or_value ?? '#' }}" target="_blank" class="bg-white/10 p-2 rounded-full hover:scale-110 transition text-decoration-none">
                            <i class="fa-solid fa-globe"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-6 text-gray-200">
                <span>📍 Quito, Ecuador</span>
                <span>📞 098 805 9187</span>
                <span>✉️ info@losandesinmobiliaria.com</span>
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <a href="{{ route('login') }}" class="underline text-white">Acceso Empleados (Intranet)</a>
                <a href="https://www.losandesinmobiliaria.com" target="_blank" class="underline text-white">www.losandesinmobiliaria.com</a>
            </div>
        </div>
    </footer>
</body>
</html>