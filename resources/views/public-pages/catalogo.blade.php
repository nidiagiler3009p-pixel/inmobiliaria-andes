@extends('layouts.public')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pt-1 pb-16 space-y-3 font-sans" 
     x-data="{ 
         sidebarOpen: true, 
         precioMin: '{{ request('min_price', 0) }}', 
         precioMax: '{{ request('max_price', 300000) }}',
         formatMoney(value) {
             if (!value) return '0';
             return Number(value).toLocaleString('en-US');  
         }
     }">
    
    <!-- BARRA SUPERIOR UNIFICADA Y COMPACTA -->
    <div class="flex flex-wrap justify-between items-center bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-emerald-100 shadow-sm gap-2">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Botón de menú / flecha -->
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 bg-[#2C4A3E] text-white text-xs rounded-xl shadow-sm hover:bg-emerald-800 transition flex items-center justify-center w-8 h-8 shrink-0 cursor-pointer" :title="sidebarOpen ? 'Ocultar filtros' : 'Mostrar filtros'">
                <i class="fa-solid" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-bars'"></i>
            </button>
            <span class="text-xs font-black text-[#2C4A3E] uppercase tracking-wide px-2">Catálogo Inmobiliaria Los Andes</span>
        </div>

        <!-- Buscador -->
        <form method="GET" action="{{ route('public.catalogo.index') }}" class="flex-1 max-w-md flex gap-2">
            @foreach(request()->except(['search', 'page']) as $key => $value)
                @if(is_array($value))
                    @foreach($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por título o ciudad..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-3.5 py-1.5 text-xs font-bold text-[#2C4A3E] focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="px-4 py-1.5 bg-[#2C4A3E] text-white rounded-xl text-xs font-bold hover:bg-emerald-800 transition">Buscar</button>
        </form>
    </div>

    <!-- CONTENEDOR PRINCIPAL A ANCHO COMPLETO -->
    <div class="flex gap-4 items-start w-full">
        
        <!-- BARRA LATERAL DE FILTROS -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-4"
             class="w-80 bg-white/95 backdrop-blur-md p-5 rounded-3xl border border-emerald-100 shadow-sm shrink-0 space-y-5 text-[#2C4A3E]">
            
            <div class="flex justify-between items-center border-b border-emerald-100 pb-3">
                <h3 class="font-extrabold text-sm uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-sliders text-emerald-700"></i> Filtros
                </h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('public.catalogo.index') }}" class="text-[11px] font-bold text-gray-400 hover:text-emerald-700 transition">Limpiar ⟲</a>
                    <button @click="sidebarOpen = false" class="text-gray-400 hover:text-[#2C4A3E] p-0.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- FORMULARIO GENERAL DE FILTROS -->
            <form method="GET" action="{{ route('public.catalogo.index') }}" class="space-y-4 text-xs font-bold">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('property_type'))
                    <input type="hidden" name="property_type" value="{{ request('property_type') }}">
                @endif
                @if(request('service_type'))
                    <input type="hidden" name="service_type" value="{{ request('service_type') }}">
                @endif
                
                <!-- Tipo de Operación -->
                <div>
                    <label class="block mb-2 uppercase text-[10px] text-gray-400 font-extrabold tracking-wider">Tipo de Operación</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" name="service_type" value="Venta" class="py-2 rounded-xl border border-emerald-200 text-center hover:bg-[#2C4A3E] hover:text-white transition cursor-pointer {{ request('service_type') == 'Venta' ? 'bg-[#2C4A3E] text-white shadow-sm' : 'bg-white text-[#2C4A3E]' }}">Venta</button>
                        <button type="submit" name="service_type" value="Arriendo" class="py-2 rounded-xl border border-emerald-200 text-center hover:bg-[#2C4A3E] hover:text-white transition cursor-pointer {{ request('service_type') == 'Arriendo' ? 'bg-[#2C4A3E] text-white shadow-sm' : 'bg-white text-[#2C4A3E]' }}">Arriendo</button>
                    </div>
                </div>

                <!-- Tipo de Inmueble -->
                <div class="pt-2 border-t border-emerald-100">
                    <label class="block mb-2 uppercase text-[10px] text-gray-400 font-extrabold tracking-wider">Tipo de Inmueble</label>
                    <div class="space-y-1">
                        @php
                            $propertyTypes = [
                                ['name' => 'Casa', 'label' => 'Casas', 'icon' => 'fa-house', 'count' => $countCasas ?? 0], 
                                ['name' => 'Terrenos', 'label' => 'Terrenos', 'icon' => 'fa-mountain', 'count' => $countTerrenos ?? 0], 
                                ['name' => 'Comerciales', 'label' => 'Comerciales', 'icon' => 'fa-store', 'count' => $countComerciales ?? 0], 
                                ['name' => 'Oficinas', 'label' => 'Proyectos', 'icon' => 'fa-building', 'count' => $countProyectos ?? 0]
                            ];
                        @endphp
                        @foreach($propertyTypes as $type)
                            <a href="{{ route('public.catalogo.index', array_merge(request()->all(), ['property_type' => $type['name']])) }}" class="flex justify-between items-center px-3 py-2 rounded-xl hover:bg-emerald-50 transition {{ request('property_type') == $type['name'] ? 'bg-emerald-100/70 text-emerald-900 font-extrabold shadow-2xs' : 'text-gray-600' }}">
                                <span class="flex items-center gap-2.5"><i class="fa-solid {{ $type['icon'] }} text-emerald-700 w-4"></i> {{ $type['label'] }}</span>
                                <span class="bg-[#2C4A3E] text-white text-[9px] px-2 py-0.5 rounded-full font-bold">{{ $type['count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- UBICACIÓN -->
                <div class="space-y-2 pt-2 border-t border-emerald-100">
                    <label class="block uppercase text-[10px] text-gray-400 font-extrabold tracking-wider">
                        <i class="fa-solid fa-location-dot mr-1 text-emerald-700"></i> Ubicación
                    </label>
                    <select name="location" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-3 py-2 text-xs font-bold text-[#2C4A3E] focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                        <option value="">Ubicación: Todas</option>
                        @php
                            $locationsList = \App\Models\Property::whereNotNull('location')->where('location', '!=', '')->distinct()->pluck('location');
                        @endphp
                        @foreach($locationsList as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- RANGO DE PRECIO -->
                <div class="space-y-2.5 pt-2 border-t border-emerald-100">
                    <div class="flex justify-between items-center">
                        <label class="block uppercase text-[10px] text-gray-400 font-extrabold tracking-wider">
                            <i class="fa-solid fa-dollar-sign mr-0.5 text-emerald-700"></i> Rango de Precio
                        </label>
                        <span class="text-[10px] text-emerald-700 font-bold" x-text="`$${formatMoney(precioMin)} - $${formatMoney(precioMax)}`"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-400 text-xs">$</span>
                            <input type="number" name="min_price" x-model="precioMin" placeholder="Mínimo" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl pl-6 pr-3 py-1.5 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-400 text-xs">$</span>
                            <input type="number" name="max_price" x-model="precioMax" placeholder="Máximo" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl pl-6 pr-3 py-1.5 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="flex gap-1.5 pt-1">
                        <button type="button" @click="precioMin = 0; precioMax = 50000" class="flex-1 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-[10px] font-bold transition">&lt; 50k</button>
                        <button type="button" @click="precioMin = 50000; precioMax = 150000" class="flex-1 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-[10px] font-bold transition">50k-150k</button>
                        <button type="button" @click="precioMin = 150000; precioMax = 500000" class="flex-1 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-[10px] font-bold transition">&gt; 150k</button>
                    </div>
                </div>

                <!-- Botón Aplicar -->
                <button type="submit" class="w-full py-2.5 bg-[#2C4A3E] text-white rounded-2xl text-xs font-extrabold hover:bg-emerald-800 transition shadow-sm flex items-center justify-center gap-2 cursor-pointer mt-3">
                    <i class="fa-solid fa-filter"></i> Aplicar filtros
                </button>
            </form>
        </div>

        <!-- ZONA CENTRAL DE CATÁLOGO -->
        <div class="flex-1 space-y-6 transition-all duration-300 w-full">
            @if(count(request()->except('page')) > 0)
                <!-- VISTA FILTRADA -->
                <div class="bg-white/90 backdrop-blur-md p-5 rounded-3xl border border-emerald-100 shadow-sm space-y-4 w-full">
                    <div class="flex justify-between items-center">
                        <h3 class="font-extrabold text-[#2C4A3E] text-sm flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-700 inline-block"></span> Resultados de Búsqueda / Filtros
                        </h3>
                        <a href="{{ route('public.catalogo.index') }}" class="px-3 py-1 bg-emerald-100 text-[#2C4A3E] text-xs font-bold rounded-xl hover:bg-emerald-200 transition">
                            Volver al Catálogo Principal
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 w-full">
                        @forelse($properties as $prop)
                            <a href="{{ route('public.catalogo.show', $prop->id) }}" class="rounded-2xl border border-emerald-100 overflow-hidden block hover:shadow-lg transition relative group flex flex-col h-72">
                                <div class="absolute inset-0 bg-emerald-50/60 flex items-center justify-center overflow-hidden">
                                    @php
                                        $validImage = $prop->images->first(function($img) {
                                            return !empty(trim($img->image_path)) && Storage::disk('public')->exists($img->image_path);
                                        });
                                    @endphp
                                    @if($validImage)
                                        <img src="{{ asset('storage/' . $validImage->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="flex flex-col items-center justify-center text-emerald-800/60 space-y-1">
                                            <i class="fa-solid fa-image text-lg"></i>
                                            <span class="text-[8px] font-extrabold uppercase tracking-wider">Sin imagen</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/50 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/80 via-black/40 to-transparent pointer-events-none"></div>

                                <div class="absolute top-3 left-3 z-10">
                                    <span class="px-3.5 py-1.5 bg-[#5A8275]/95 backdrop-blur-md text-white text-[11px] font-black rounded-xl uppercase tracking-widest shadow-md">
                                        {{ $prop->service_type }}
                                    </span>
                                </div>

                                <div class="absolute bottom-0 inset-x-0 p-3.5 flex justify-between items-end z-10">
                                    <span class="text-xs font-bold text-white flex items-center gap-1.5 drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] truncate max-w-[50%]">
                                        <i class="fa-solid fa-location-dot text-emerald-400 text-sm"></i> {{ $prop->location }}
                                    </span>
                                    <span class="text-sm font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] tracking-tight">
                                        $ {{ number_format($prop->price, 2) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-gray-500 col-span-full text-center py-8">No se encontraron propiedades disponibles con estos filtros.</p>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $properties->links() }}
                    </div>
                </div>
            @else
                <!-- VISTA PRINCIPAL ESTILO NETFLIX -->
                <div class="space-y-6 pr-2 w-full">
                    @php
                        $sections = [
                            ['title' => 'Bajaron de Precio', 'items' => $bajaronPrecio ?? collect(), 'filter_key' => 'price_dropped', 'filter_val' => '1'],
                            ['title' => 'Terrenos', 'items' => $terrenos ?? collect(), 'filter_key' => 'property_type', 'filter_val' => 'Terrenos'],
                            ['title' => 'Casas', 'items' => $casas ?? collect(), 'filter_key' => 'property_type', 'filter_val' => 'Casa'],
                            ['title' => 'Comerciales', 'items' => $comerciales ?? collect(), 'filter_key' => 'property_type', 'filter_val' => 'Comerciales'],
                            ['title' => 'Proyectos / Lotizaciones', 'items' => $proyectos ?? collect(), 'filter_key' => 'property_type', 'filter_val' => 'Oficinas']
                        ];
                    @endphp

                    @foreach($sections as $section)
                        <div class="bg-white/90 backdrop-blur-md p-4 rounded-3xl border border-emerald-100 shadow-sm space-y-3 w-full">
                            <div class="flex justify-between items-center px-1">
                                <h3 class="font-extrabold text-[#2C4A3E] text-sm flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-700 inline-block"></span> {{ $section['title'] }}
                                </h3>
                                <a href="{{ route('public.catalogo.index', [$section['filter_key'] => $section['filter_val']]) }}" class="text-xs font-extrabold text-emerald-700 hover:underline">Ver todas &gt;</a>
                            </div>

                            <!-- Carrusel Horizontal -->
                            <div class="flex gap-4 overflow-x-auto pb-2 scroll-smooth no-scrollbar">
                                @forelse($section['items'] as $prop)
                                    <a href="{{ route('public.catalogo.show', $prop->id) }}" class="w-72 shrink-0 rounded-2xl border border-emerald-100 overflow-hidden block hover:shadow-lg transition relative group flex flex-col h-72">
                                        <div class="absolute inset-0 bg-emerald-50/60 flex items-center justify-center overflow-hidden">
                                            @php
                                                $validImage = $prop->images->first(function($img) {
                                                    return !empty(trim($img->image_path)) && Storage::disk('public')->exists($img->image_path);
                                                });
                                            @endphp
                                            @if($validImage)
                                                <img src="{{ asset('storage/' . $validImage->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                            @else
                                                <div class="flex flex-col items-center justify-center text-emerald-800/60 space-y-1">
                                                    <i class="fa-solid fa-image text-lg"></i>
                                                    <span class="text-[8px] font-extrabold uppercase tracking-wider">Sin imagen</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/50 via-transparent to-transparent pointer-events-none"></div>
                                        <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/80 via-black/40 to-transparent pointer-events-none"></div>

                                        <div class="absolute top-3 left-3 z-10">
                                            <span class="px-3.5 py-1.5 bg-[#5A8275]/95 backdrop-blur-md text-white text-[11px] font-black rounded-xl uppercase tracking-widest shadow-md">
                                                {{ $prop->service_type }}
                                            </span>
                                        </div>

                                        <div class="absolute bottom-0 inset-x-0 p-3.5 flex justify-between items-end z-10">
                                            <span class="text-xs font-bold text-white flex items-center gap-1.5 drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] truncate max-w-[50%]">
                                                <i class="fa-solid fa-location-dot text-emerald-400 text-sm"></i> {{ $prop->location }}
                                            </span>
                                            <span class="text-sm font-black text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)] tracking-tight">
                                                $ {{ number_format($prop->price, 2) }}
                                            </span>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-xs text-gray-400 py-4 px-2">No hay propiedades registradas en esta categoría.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection