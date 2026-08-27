@extends('layouts.public')

@section('content')

<!-- CONTENEDOR PRINCIPAL: FONDO VERDE ESMERALDA DEGRADADO TIPO ESTUDIO -->
<div class="relative min-h-screen bg-[#02180e] bg-[radial-gradient(ellipse_at_30%_20%,_#10b981_0%,_#047857_25%,_#064e3b_50%,_#022c22_75%,_#01120a_100%)] py-6 overflow-hidden">

    <!-- Iluminación ambiental difusa central -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[450px] bg-emerald-400/15 rounded-full blur-[130px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <!-- ETIQUETA 1: SERVICIOS INTEGRALES -->
        <div class="text-left mb-3">
            <span class="inline-block text-xs sm:text-sm font-extrabold tracking-widest text-emerald-200 uppercase bg-emerald-950/70 px-4 py-2 rounded-full backdrop-blur-md border border-emerald-500/40 shadow-lg">
                + Servicios Integrales
            </span>
        </div>

        <!-- 1. TARJETAS DE SERVICIOS SUPERIORES (FLYERS MÁS GRANDES Y AJUSTADOS) -->
        <section class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 w-full">
                
                <!-- Tarjeta 1: Vender o arrendar -->
                <a href="{{ url('/asesorias') }}" class="block bg-white/95 rounded-2xl shadow-2xl hover:shadow-emerald-500/20 hover:-translate-y-1.5 transition-all duration-300 overflow-hidden border border-emerald-400/30 group">
                    <div class="w-full h-full p-2.5">
                        <img src="{{ asset('images/tarjeta1.png') }}" alt="¿Quieres vender o arrendar tu propiedad?" class="w-full h-auto object-cover rounded-xl group-hover:scale-[1.02] transition duration-300">
                    </div>
                </a>

                <!-- Tarjeta 2: Compraste o vendiste -->
                <a href="{{ url('/tramites') }}" class="block bg-white/95 rounded-2xl shadow-2xl hover:shadow-emerald-500/20 hover:-translate-y-1.5 transition-all duration-300 overflow-hidden border border-emerald-400/30 group">
                    <div class="w-full h-full p-2.5">
                        <img src="{{ asset('images/tarjeta2.png') }}" alt="¿Compraste o vendiste tu propiedad?" class="w-full h-auto object-cover rounded-xl group-hover:scale-[1.02] transition duration-300">
                    </div>
                </a>

                <!-- Tarjeta 3: Terreno y construir -->
                <a href="{{ url('/contacto') }}" class="block bg-white/95 rounded-2xl shadow-2xl hover:shadow-emerald-500/20 hover:-translate-y-1.5 transition-all duration-300 overflow-hidden border border-emerald-400/30 group">
                    <div class="w-full h-full p-2.5">
                        <img src="{{ asset('images/tarjeta3.png') }}" alt="¿Tienes un terreno y quieres empezar a construir?" class="w-full h-auto object-cover rounded-xl group-hover:scale-[1.02] transition duration-300">
                    </div>
                </a>

                <!-- Tarjeta 4: Trámites legales estancados -->
                <a href="{{ url('/contacto') }}" class="block bg-white/95 rounded-2xl shadow-2xl hover:shadow-emerald-500/20 hover:-translate-y-1.5 transition-all duration-300 overflow-hidden border border-emerald-400/30 group">
                    <div class="w-full h-full p-2.5">
                        <img src="{{ asset('images/tarjeta4.png') }}" alt="¿Estancado con los trámites legales de tu propiedad?" class="w-full h-auto object-cover rounded-xl group-hover:scale-[1.02] transition duration-300">
                    </div>
                </a>

            </div>
        </section>

        <!-- 2. SECCIÓN: OFERTA INMOBILIARIA (ETIQUETA CON MISMO DISEÑO Y CARRUSEL MÁS ALTO) -->
        <section class="max-w-7xl mx-auto my-4 relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                
                <!-- ETIQUETA 2: OFERTA INMOBILIARIA (DISEÑO IDÉNTICO A SERVICIOS INTEGRALES) -->
                <div class="text-left">
                    <span class="inline-block text-xs sm:text-sm font-extrabold tracking-widest text-emerald-200 uppercase bg-emerald-950/70 px-4 py-2 rounded-full backdrop-blur-md border border-emerald-500/40 shadow-lg">
                        + Oferta Inmobiliaria
                    </span>
                </div>

                <!-- Botones de Navegación del Carrusel -->
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <button id="carousel-prev" class="bg-emerald-950/80 hover:bg-emerald-600 text-white w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center border border-emerald-500/40 shadow-lg transition cursor-pointer active:scale-95" aria-label="Anterior">◀</button>
                    <button id="carousel-next" class="bg-emerald-950/80 hover:bg-emerald-600 text-white w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center border border-emerald-500/40 shadow-lg transition cursor-pointer active:scale-95" aria-label="Siguiente">▶</button>
                </div>
            </div>

           @php
    // Definimos únicamente las categorías limpias que deseas manejar
    $categorias = $categorias ?? [
        'Casa', 
        'Terrenos', 
        'Terrenos Grandes', 
        'Proyectos', 
        'Departamentos', 
        'Comerciales',
        'De Remate'
    ];

    $todasLasPropiedades = $propiedades 
        ?? $propiedadesCatalogo 
        ?? (isset($propiedadesPorTipo) ? $propiedadesPorTipo->flatten() : null);

    if (!$todasLasPropiedades && class_exists('\App\Models\Property')) {
        $todasLasPropiedades = \App\Models\Property::with('images')->get();
    } else if (!$todasLasPropiedades) {
        $todasLasPropiedades = collect();
    }
@endphp

<!-- Carrusel ajustado más arriba y con imágenes de propiedades más grandes -->
<div id="oferta-carousel" class="flex gap-5 overflow-x-auto scroll-smooth pb-3 no-scrollbar">
    @foreach($categorias as $categoria)
        @php
            // Lógica de búsqueda precisa para cada tarjeta del carrusel
            $propiedad = $todasLasPropiedades->first(function($p) use ($categoria) {
                $cat = trim(mb_strtolower($categoria));
                $tipo = trim(mb_strtolower($p->property_type ?? ''));

                if ($cat === 'de remate') {
                    // Propiedades en oferta o rebajadas de precio
                    return $p->price_dropped == 1;
                }

                if ($cat === 'terrenos') {
                    // Terrenos estándar (que coincidan pero que NO sean "Terrenos Grandes")
                    return ($tipo === 'terrenos' || $tipo === 'terreno') && !str_contains($tipo, 'grandes');
                }

                if ($cat === 'terrenos grandes') {
                    // Específicamente terrenos grandes
                    return str_contains($tipo, 'terrenos grandes') || str_contains($tipo, 'terreno grande');
                }

                if ($cat === 'proyectos') {
                    // Coincide con proyectos u oficinas (por si quedó alguna registrada con el nombre anterior)
                    return str_contains($tipo, 'proyecto') || str_contains($tipo, 'oficina');
                }

                // Para el resto de categorías (Casa, Comerciales, Departamentos)
                return $tipo === $cat || str_contains($tipo, $cat);
            });

            $rutaImagen = asset('images/default-property.jpg');

            if ($propiedad) {
                $primeraImagen = $propiedad->images ? $propiedad->images->first() : null;
                
                $path = null;
                if ($primeraImagen) {
                    $path = $primeraImagen->image_path 
                         ?? $primeraImagen->path 
                         ?? $primeraImagen->url 
                         ?? $primeraImagen->imagen 
                         ?? $primeraImagen->file_path;
                }

                if (!$path) {
                    $path = $propiedad->image_path 
                         ?? $propiedad->main_image 
                         ?? $propiedad->imagen 
                         ?? $propiedad->foto 
                         ?? $propiedad->cover;
                }

                if ($path) {
                    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                        $rutaImagen = $path;
                    } else {
                        $pathLimpio = ltrim($path, '/');
                        if (str_starts_with($pathLimpio, 'storage/')) {
                            $rutaImagen = asset($pathLimpio);
                        } else {
                            $rutaImagen = asset('storage/' . $pathLimpio);
                        }
                    }
                }
            }

            $textoVisible = $categoria;
        @endphp

        <!-- Tarjetas de Categoría Ampliadas -->
        <a href="{{ url('/catalogo') }}" 
            class="min-w-[300px] sm:min-w-[340px] relative rounded-2xl overflow-hidden shadow-2xl h-60 sm:h-64 flex-shrink-0 group hover:scale-[1.03] transition duration-300 border border-emerald-400/30 bg-slate-900">
            
            <img src="{{ $rutaImagen }}" 
                 alt="{{ $textoVisible }}" 
                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500 opacity-90 group-hover:opacity-100">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
            
            <div class="absolute bottom-4 left-4 text-white font-bold text-lg drop-shadow-md">
                {{ strtoupper($textoVisible) }}
            </div>
        </a>
    @endforeach
</div>
        </section>

        <!-- 3. SECCIÓN CENTRAL: QUIÉNES SOMOS, NEVADO Y GESTIÓN Y TRÁMITES + CARRUSEL DE SOCIOS BANCOS -->
        <section class="max-w-7xl mx-auto py-4 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                
                <!-- BLOQUE 1: QUIÉNES SOMOS (TEXTOS Y TÍTULOS MÁS GRANDES) -->
                <div class="bg-white/95 backdrop-blur-md p-6 sm:p-7 rounded-3xl shadow-2xl border border-emerald-100 flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#2C4A3E] text-center mb-4 tracking-tight">Quiénes Somos</h3>
                        <p class="text-sm text-gray-800 leading-relaxed text-justify mb-4 font-medium">
                            En <strong class="text-[#2C4A3E]">Inmobiliaria Los Andes</strong>, ubicada en la ciudad de Riobamba, convertimos tus metas e inversión inmobiliaria en realidades sólidas y seguras.
                        </p>
                        <h4 class="font-extrabold text-base text-[#2C4A3E] mb-2">¿Qué hacemos por ti?</h4>
                        <p class="text-sm text-gray-700 leading-relaxed font-normal mb-4">
                            Compra, venta y arrendamiento de inmuebles con asesoramiento técnico y legal personalizado de principio a fin.
                        </p>
                    </div>
                    <p class="text-xs sm:text-sm font-extrabold text-emerald-900 text-center border-t border-emerald-200/80 pt-3.5 bg-emerald-50/50 rounded-b-xl">
                        Tu patrimonio en manos expertas.
                    </p>
                </div>

                <!-- BLOQUE 2: FOTO CENTRAL NEVADO CHIMBORAZO -->
                <div class="flex flex-col justify-center items-center h-full">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white/90 w-full h-full min-h-[320px] group">
                        <img src="{{ asset('images/nevado-chimborazo.jpg') }}" alt="Nevado Chimborazo - Riobamba" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                </div>

                <!-- BLOQUE 3: GESTIÓN Y TRÁMITES + CARRUSEL DE LOGOS BANCARIOS -->
                <div class="bg-white/95 backdrop-blur-md p-6 sm:p-7 rounded-3xl shadow-2xl border border-emerald-100 flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#2C4A3E] text-center mb-4 tracking-tight">Gestión y Trámites</h3>
                        <p class="text-sm text-gray-800 mb-3 leading-relaxed font-medium">
                            Nos encargamos de todo el proceso administrativo, fiscal y legal de tu propiedad con absoluta transparencia.
                        </p>
                        <ul class="text-xs sm:text-sm text-gray-700 space-y-2 mb-4 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600 font-bold">✔️</span>
                                <span><strong>Escrituras y contratos:</strong> Redacción y legalización.</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600 font-bold">✔️</span>
                                <span><strong>Documentación al día:</strong> Certificados y catastro.</span>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- CARRUSEL TIPO SLIDER DE LOGOS E INSTITUCIONES BANCARIAS -->
                    <div class="border-t border-emerald-200/80 pt-3">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-xs font-black text-[#2C4A3E] uppercase tracking-wider">Nuestros Socios Financieros</h4>
                            <div class="flex gap-1">
                                <button id="bancos-prev" class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-bold flex items-center justify-center hover:bg-emerald-200 transition">◀</button>
                                <button id="bancos-next" class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-bold flex items-center justify-center hover:bg-emerald-200 transition">▶</button>
                            </div>
                        </div>
                        
                        @php
                            // Lista de instituciones financieras locales con sus logos y portales
                            $bancosPorDefecto = [
                                [
                                    'nombre' => 'BIESS', 
                                    'url' => 'https://www.biess.fin.ec/hipotecarios', 
                                    'logo' => asset('images/bancos/biess.png')
                                ],
                                [
                                    'nombre' => 'Pichincha', 
                                    'url' => 'https://www.pichincha.com/detalle-producto/personas-credito-hipotecario-de-vivienda', 
                                    'logo' => asset('images/bancos/pichincha.png')
                                ],
                                [
                                    'nombre' => 'Coop.Daquilema', 
                                    'url' => 'https://www.coopdaquilema.com/creditos/#VIVIENDA', 
                                    'logo' => asset('images/bancos/Daquilema.png')
                                ],
                                [
                                    'nombre' => 'Coop. Riobamba', 
                                    'url' => 'https://www.cooprio.fin.ec/?page_id=563', 
                                    'logo' => asset('images/bancos/Riobamba.png')
                                ],
                                [
                                    'nombre' => 'ISSFA', 
                                    'url' => 'https://www.issfa.mil.ec/home/creditos-2/hipotecarios/', 
                                    'logo' => asset('images/bancos/issfa.png')
                                ],
                                [
                                    'nombre' => 'Mutualista', 
                                    'url' => 'https://www.mutualistapichincha.com/de/credito-inmobiliario/', 
                                    'logo' => asset('images/bancos/mutualista.png')
                                ],
                            ];
                        @endphp

                        <!-- Mini Carrusel de Logos -->
                        <div id="bancos-carousel" class="flex gap-2.5 overflow-x-auto scroll-smooth py-1 no-scrollbar items-center">
                            @foreach($bancosPorDefecto as $banco)
                                <a href="{{ $banco['url'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   title="Simular Crédito en {{ $banco['nombre'] }}" 
                                   class="flex-shrink-0 bg-gradient-to-br from-emerald-50 to-white hover:from-emerald-100 hover:to-teal-50 px-3 py-1.5 rounded-xl border border-emerald-200/80 shadow-sm hover:scale-105 transition-all duration-300 flex items-center gap-2 group">
                                    
                                    <!-- Carga del logo de la carpeta public/images/bancos/ con alternativa gráfica si aún no subes la foto -->
                                    <img src="{{ $banco['logo'] }}" 
                                         alt="Logo {{ $banco['nombre'] }}" 
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($banco['nombre']) }}&color=2C4A3E&background=E6F4EA&font-size=0.35';" 
                                         class="h-6 max-w-[70px] object-contain group-hover:scale-110 transition">

                                    <span class="text-[11px] font-bold text-[#2C4A3E]">{{ $banco['nombre'] }}</span>
                                    <span class="text-[9px] text-emerald-600 font-extrabold group-hover:translate-x-0.5 transition">↗</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
</div>

<!-- Ocultar barra de desplazamiento horizontal -->
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<!-- Scripts para el carrusel de Oferta Inmobiliaria y el Mini-Carrusel de Bancos -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. CONTROL CARRUSEL PRINCIPAL (OFERTA INMOBILIARIA)
        const btnPrev = document.getElementById('carousel-prev');
        const btnNext = document.getElementById('carousel-next');
        const carousel = document.getElementById('oferta-carousel');

        if (carousel) {
            const scrollStep = 340;
            let autoScrollTimer = null;

            function slideNext() {
                if (carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 10) {
                    carousel.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    carousel.scrollBy({ left: scrollStep, behavior: 'smooth' });
                }
            }

            function slidePrev() {
                if (carousel.scrollLeft <= 0) {
                    carousel.scrollTo({ left: carousel.scrollWidth, behavior: 'smooth' });
                } else {
                    carousel.scrollBy({ left: -scrollStep, behavior: 'smooth' });
                }
            }

            function startAutoScroll() {
                autoScrollTimer = setInterval(slideNext, 3500);
            }

            function resetTimer() {
                clearInterval(autoScrollTimer);
                startAutoScroll();
            }

            if (btnNext) btnNext.addEventListener('click', () => { slideNext(); resetTimer(); });
            if (btnPrev) btnPrev.addEventListener('click', () => { slidePrev(); resetTimer(); });

            carousel.addEventListener('mouseenter', () => clearInterval(autoScrollTimer));
            carousel.addEventListener('mouseleave', startAutoScroll);

            startAutoScroll();
        }

        // 2. CONTROL MINI CARRUSEL (NUESTROS SOCIOS BANCOS)
        const btnBancosPrev = document.getElementById('bancos-prev');
        const btnBancosNext = document.getElementById('bancos-next');
        const bancosCarousel = document.getElementById('bancos-carousel');

        if (bancosCarousel) {
            const bancoStep = 180;
            let autoBancosTimer = null;

            function slideBancosNext() {
                if (bancosCarousel.scrollLeft + bancosCarousel.clientWidth >= bancosCarousel.scrollWidth - 5) {
                    bancosCarousel.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    bancosCarousel.scrollBy({ left: bancoStep, behavior: 'smooth' });
                }
            }

            function slideBancosPrev() {
                if (bancosCarousel.scrollLeft <= 0) {
                    bancosCarousel.scrollTo({ left: bancosCarousel.scrollWidth, behavior: 'smooth' });
                } else {
                    bancosCarousel.scrollBy({ left: -bancoStep, behavior: 'smooth' });
                }
            }

            function startBancosAutoScroll() {
                autoBancosTimer = setInterval(slideBancosNext, 2800);
            }

            if (btnBancosNext) btnBancosNext.addEventListener('click', slideBancosNext);
            if (btnBancosPrev) btnBancosPrev.addEventListener('click', slideBancosPrev);

            bancosCarousel.addEventListener('mouseenter', () => clearInterval(autoBancosTimer));
            bancosCarousel.addEventListener('mouseleave', startBancosAutoScroll);

            startBancosAutoScroll();
        }
    });
</script>
@endsection