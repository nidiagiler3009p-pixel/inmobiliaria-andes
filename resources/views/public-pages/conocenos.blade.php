@extends('layouts.public')

@section('content')

<div class="max-w-7xl mx-auto px-4 pt-4 pb-4">
    <!-- Título superior simple alineado a la izquierda -->
    <div class="text-left mb-2">
        <span class="text-sm font-bold tracking-wider text-emerald-800 uppercase">
            + Servicios
        </span>
    </div>

    <!-- 1. TARJETAS DE SERVICIOS SUPERIORES -->
   <section class="mt-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Tarjeta 1: Vender o arrendar -->
        <a href="{{ url('/asesorias') }}" class="block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-emerald-100 group">
            <div class="w-full h-full p-2">
                <img src="{{ asset('images/tarjeta1.png') }}" alt="¿Quieres vender o arrendar tu propiedad?" class="w-full h-auto object-contain rounded-xl group-hover:scale-[1.02] transition">
            </div>
        </a>

        <!-- Tarjeta 2: Compraste o vendiste -->
        <a href="{{ url('/tramites') }}" class="block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-emerald-100 group">
            <div class="w-full h-full p-2">
                <img src="{{ asset('images/tarjeta2.png') }}" alt="¿Compraste o vendiste tu propiedad?" class="w-full h-auto object-contain rounded-xl group-hover:scale-[1.02] transition">
            </div>
        </a>

        <!-- Tarjeta 3: Terreno y construir -->
        <a href="{{ url('/contacto') }}" class="block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-emerald-100 group">
            <div class="w-full h-full p-2">
                <img src="{{ asset('images/tarjeta3.png') }}" alt="¿Tienes un terreno y quieres empezar a construir?" class="w-full h-auto object-contain rounded-xl group-hover:scale-[1.02] transition">
            </div>
        </a>

        <!-- Tarjeta 4: Trámites legales estancados -->
        <a href="{{ url('/contacto') }}" class="block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-emerald-100 group">
            <div class="w-full h-full p-2">
                <img src="{{ asset('images/tarjeta4.png') }}" alt="¿Estancado con los trámites legales de tu propiedad?" class="w-full h-auto object-contain rounded-xl group-hover:scale-[1.02] transition">
            </div>
        </a>

    </div>
</div>
    </section>

    <!-- 2. SECCIÓN: OFERTA INMOBILIARIA (CARRUSEL DINÁMICO) -->
    <section class="max-w-7xl mx-auto py-8 px-6 relative z-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-[#2C4A3E]">Oferta Inmobiliaria</h2>
            <div class="flex gap-2">
                <button id="carousel-prev" class="bg-[#2C4A3E] text-white w-10 h-10 rounded-full flex items-center justify-center shadow hover:bg-emerald-800 transition">◀</button>
                <button id="carousel-next" class="bg-[#2C4A3E] text-white w-10 h-10 rounded-full flex items-center justify-center shadow hover:bg-emerald-800 transition">▶</button>
            </div>
        </div>
        <div id="oferta-carousel" class="flex gap-4 overflow-x-auto scroll-smooth pb-4 no-scrollbar">
            @isset($propiedadesCatalogo)
                @foreach($propiedadesCatalogo as $propiedad)
                    <a href="#" class="min-w-[280px] relative rounded-2xl overflow-hidden shadow-lg h-48 flex-shrink-0 group hover:scale-105 transition border border-emerald-100">
                        <img src="{{ $propiedad->imagen ? asset('storage/' . $propiedad->imagen) : asset('images/default.jpg') }}" class="w-full h-full object-cover group-hover:scale-110 transition">
                        <div class="absolute top-3 inset-x-2 text-center">
                            <span class="bg-[#2C4A3E]/95 text-white text-xs font-bold uppercase px-3 py-1 rounded-full shadow">{{ $propiedad->nombre }}</span>
                        </div>
                    </a>
                @endforeach
            @endisset
        </div>
    </section>

    <!-- 3. SECCIÓN CENTRAL: QUIÉNES SOMOS, NEVADO Y TRÁMITES -->
    <section class="max-w-7xl mx-auto py-12 px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
            
            <!-- Quiénes Somos -->
            <div class="bg-white/90 p-6 rounded-3xl shadow-xl border border-emerald-100 flex flex-col justify-between h-full">
                <div>
                    <h3 class="text-xl font-bold text-[#2C4A3E] text-center mb-3">Quiénes Somos</h3>
                    <p class="text-xs text-gray-700 leading-relaxed text-justify mb-3">En Inmobiliaria Los Andes, ubicada en Riobamba, convertimos tus metas inmobiliarias en realidades seguras.</p>
                    <h4 class="font-bold text-sm text-[#2C4A3E] mb-1">¿Qué hacemos por ti?</h4>
                    <p class="text-xs text-gray-700 leading-relaxed mb-3">Compra, venta y renta de espacios con asesoría integral de principio a fin.</p>
                </div>
                <p class="text-[11px] font-bold text-emerald-800 text-center border-t pt-3">Inmobiliaria Los Andes: Tu patrimonio en manos expertas.</p>
            </div>

            <!-- Imagen Central Nevado con Insignia de 10 Años -->
            <div class="flex flex-col items-center">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white w-full">
                    <img src="{{ asset('images/nevado-chimborazo.jpg') }}" class="w-full h-[340px] object-cover">
                    
                </div>
            </div>

            <!-- Gestión, Trámites y Socios Bancarios -->
            <div class="bg-white/90 p-6 rounded-3xl shadow-xl border border-emerald-100 flex flex-col justify-between h-full">
                <div>
                    <h3 class="text-xl font-bold text-[#2C4A3E] text-center mb-3">Gestión y Trámites</h3>
                    <p class="text-xs text-gray-700 mb-2 leading-relaxed">Nos encargamos de todo el proceso legal y administrativo de forma segura y sin estrés.</p>
                    <ul class="text-[11px] text-gray-600 space-y-1 mb-4">
                        <li>✔️ <strong>Escrituras y contratos:</strong> Redacción y legalización.</li>
                        <li>✔️ <strong>Documentación al día:</strong> Certificados y catastro.</li>
                    </ul>
                </div>
                <div class="border-t pt-4">
                    <h4 class="text-xs font-bold text-center text-[#2C4A3E] mb-3">Nuestros Socios</h4>
                    <div class="flex flex-wrap justify-center items-center gap-3">
                        @isset($bancosAliados)
                            @foreach($bancosAliados as $banco)
                                <a href="{{ $banco->link ?? '#' }}" target="_blank" class="hover:scale-110 transition">
                                    <img src="{{ asset('storage/' . $banco->logo) }}" class="h-6 object-contain">
                                </a>
                            @endforeach
                        @endisset
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Script para el carrusel horizontal -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnPrev = document.getElementById('carousel-prev');
            const btnNext = document.getElementById('carousel-next');
            const carousel = document.getElementById('oferta-carousel');

            if (btnNext && carousel) {
                btnNext.addEventListener('click', () => { carousel.scrollBy({ left: 300, behavior: 'smooth' }); });
            }
            if (btnPrev && carousel) {
                btnPrev.addEventListener('click', () => { carousel.scrollBy({ left: -300, behavior: 'smooth' }); });
            }
        });
    </script>
@endsection