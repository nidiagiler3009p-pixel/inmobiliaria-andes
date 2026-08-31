@props([
    'property',
    'showContact' => true
])

@php
    $isPublic = $showContact && !request()->is('intranet*');
    $isIntranet = request()->is('intranet*');
    $propId = $property->id ?? 'default';
    $info = session('appointment_confirmed') ?? [];
    $isTargetProperty = isset($info['property_id']) ? ($info['property_id'] == $propId) : true;

    // Servicios básicos
    $basicServices = [
        'has_agua' => ['label' => 'Agua', 'icon' => 'fa-droplet'],
        'has_luz' => ['label' => 'Luz', 'icon' => 'fa-bolt'],
        'has_alcantarillado' => ['label' => 'Alcantarillado', 'icon' => 'fa-water'],
        'has_internet' => ['label' => 'Internet', 'icon' => 'fa-wifi'],
    ];

    // Características especiales
    $specialFeatures = [
        'has_jardin' => ['label' => 'Jardín', 'icon' => 'fa-seedling'],
        'has_balcon' => ['label' => 'Balcón', 'icon' => 'fa-building'],
        'has_seguridad' => ['label' => 'Seguridad', 'icon' => 'fa-shield-halved'],
        'has_piscina' => ['label' => 'Piscina', 'icon' => 'fa-person-swimming'],
        'has_bbq' => ['label' => 'BBQ / Asador', 'icon' => 'fa-fire-burner'],
        'has_amoblado' => ['label' => 'Amoblado', 'icon' => 'fa-couch'],
        'has_mascotas' => ['label' => 'Mascotas', 'icon' => 'fa-paw'],
    ];

    $hasServices = collect($basicServices)->keys()->contains(fn($k) => !empty($property->$k));
    $hasSpecial = collect($specialFeatures)->keys()->contains(fn($k) => !empty($property->$k));
@endphp

<div class="space-y-2 text-gray-800 text-[10px]">

    {{-- ALERTA DE ÉXITO --}}
    @if (session('success'))
        <div class="p-2.5 text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-300 rounded-xl shadow-xs flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 shrink-0">
                    <i class="fa-solid fa-check text-[10px]"></i>
                </div>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-bold text-sm px-1">&times;</button>
        </div>
    @endif

    {{-- TÍTULO CENTRADO Y COMPACTO --}}
<div class="text-center py-1 px-2">
    <div class="text-[20px] font-black text-gray-900 leading-tight">
        {{ $property->title ?? 'Propiedad Sin Título' }}
    </div>
</div>

    {{-- PANEL INTRANET (PROPIETARIO) --}}
    @if($isIntranet)
        <div class="p-2.5 bg-amber-50 rounded-xl border border-amber-200 space-y-1.5 shadow-2xs">
            <div class="flex items-center gap-1.5 border-b border-amber-200/60 pb-1">
                <i class="fa-solid fa-user-shield text-amber-700 text-[10px]"></i>
                <h3 class="font-extrabold text-[9px] text-amber-900 uppercase tracking-wider">Gestión Interna / Propietario</h3>
            </div>
            <div class="grid grid-cols-1 gap-1 text-[10px] font-bold text-gray-700">
                @if(!empty($property->owner_name))
                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-user text-amber-600 w-3 text-center"></i><span class="text-gray-400 font-normal">Propietario:</span><span class="text-gray-900">{{ $property->owner_name }}</span></div>
                @endif
                @if(!empty($property->owner_phone))
                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-phone text-amber-600 w-3 text-center"></i><span class="text-gray-400 font-normal">Teléfono:</span><a href="tel:{{ $property->owner_phone }}" class="text-emerald-700 hover:underline">{{ $property->owner_phone }}</a></div>
                @endif
                @if(!empty($property->owner_dni))
                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-id-card text-amber-600 w-3 text-center"></i><span class="text-gray-400 font-normal">Cédula:</span><span class="text-gray-900">{{ $property->owner_dni }}</span></div>
                @endif
                @if(!empty($property->owner_email))
                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-envelope text-amber-600 w-3 text-center"></i><span class="text-gray-400 font-normal">Correo:</span><a href="mailto:{{ $property->owner_email }}" class="text-emerald-700 hover:underline">{{ $property->owner_email }}</a></div>
                @endif
            </div>
        </div>
    @endif
{{-- PANEL INTRANET (GESTIÓN COMERCIAL) --}}
@if($isIntranet)
    <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 space-y-1.5 shadow-2xs">

        <div class="flex items-center gap-1.5 border-b border-emerald-200/60 pb-1">
            <i class="fa-solid fa-briefcase text-emerald-700 text-[10px]"></i>

            <h3 class="font-extrabold text-[9px] text-emerald-900 uppercase tracking-wider">
                Gestión Comercial
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-1.5 text-[10px] font-bold text-gray-700">

            {{-- ORIGEN DE CAPTACIÓN --}}
            <div class="flex items-center gap-1.5">

                <i class="fa-solid fa-building text-emerald-600 w-3 text-center"></i>

                <span class="text-gray-400 font-normal">
                    Captada por:
                </span>

                <span class="text-gray-900">

                    @if(($property->capture_origin ?? 'agency') === 'advisor')
                        Asesor
                    @else
                        Inmobiliaria
                    @endif

                </span>

            </div>


            {{-- ASESOR CAPTADOR --}}
            @if(
                ($property->capture_origin ?? 'agency') === 'advisor'
                && $property->capturingAdvisor
            )

                <div class="flex items-center gap-1.5">

                    <i class="fa-solid fa-user-plus text-emerald-600 w-3 text-center"></i>

                    <span class="text-gray-400 font-normal">
                        Asesor captador:
                    </span>

                    <span class="text-gray-900">
                        {{ $property->capturingAdvisor->name }}
                    </span>

                </div>

            @endif


            {{-- ASESOR RESPONSABLE --}}
            @if($property->user)

                <div class="flex items-center gap-1.5">

                    <i class="fa-solid fa-user-tie text-emerald-600 w-3 text-center"></i>

                    <span class="text-gray-400 font-normal">
                        Asesor responsable:
                    </span>

                    <span class="text-gray-900">
                        {{ $property->user->name }}
                    </span>

                </div>

            @endif

        </div>

    </div>
@endif
   {{-- 1. CONTENEDOR UBICACIÓN --}}
@if(
    !empty($property->address) ||
    !empty($property->location) ||
    !empty($property->badge_left) ||
    !empty($property->badge_right) ||
    !empty($property->google_maps_url)
)

    <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

        {{-- ENCABEZADO --}}
        <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-1.5">

<div class="font-black text-[12px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    Ubicación
</div>

            {{-- GOOGLE MAPS --}}
            @if(!empty($property->google_maps_url))
                <a
                    href="{{ $property->google_maps_url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="Ver ubicación en Google Maps"
                    class="inline-flex items-center gap-1.5 px-4 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-[10px] font-black transition"
                >
                    <i class="fa-solid fa-map-location-dot text-emerald-600"></i>
                    Ver mapa
                </a>
            @endif

        </div>

        {{-- CIUDAD Y DIRECCIÓN --}}
        <div class="grid grid-cols-2 gap-2.5">

            @if(!empty($property->location))
                <div class="bg-gray-50 rounded-lg border border-gray-100 p-2">
                    <span class="text-[8px] text-gray-400 font-black uppercase tracking-wide block mb-0.5">
                        Ciudad
                    </span>

                    <div class="flex items-start gap-1.5">
                        <i class="fa-solid fa-city text-emerald-600 text-[12px] mt-0.5"></i>

                        <strong class="text-[15px] text-gray-800 leading-tight">
                            {{ $property->location }}
                        </strong>
                    </div>
                </div>
            @endif

            @if(!empty($property->address))
                <div class="bg-gray-50 rounded-lg border border-gray-100 p-2">
                    <span class="text-[8px] text-gray-400 font-black uppercase tracking-wide block mb-0.5">
                        Dirección
                    </span>

                    <div class="flex items-start gap-1.5">
                        <i class="fa-solid fa-road text-emerald-600 text-[10px] mt-0.5"></i>

                        <strong class="text-[14px] text-gray-800 leading-tight">
                            {{ $property->address }}
                        </strong>
                    </div>
                </div>
            @endif

        </div>

        {{-- ETIQUETAS DE UBICACIÓN --}}
        @if(!empty($property->badge_left) || !empty($property->badge_right))
            <div class="flex flex-wrap gap-1.5 pt-0.5">

                @if(!empty($property->badge_left))
                    <span class="bg-emerald-50 text-emerald-900 border border-emerald-200 text-[9px] px-2 py-1 rounded-lg font-extrabold inline-flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-emerald-600 text-[12px]"></i>
                        {{ $property->badge_left }}
                    </span>
                @endif

                @if(!empty($property->badge_right))
                    <span class="bg-emerald-50 text-emerald-900 border border-emerald-200 text-[9px] px-2 py-1 rounded-lg font-extrabold inline-flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-[12px]"></i>
                        {{ $property->badge_right }}
                    </span>
                @endif

            </div>
        @endif

    </div>
@endif

   {{-- 2. CONTENEDOR PRECIO --}}
<div class="p-2.5 bg-gradient-to-br from-emerald-50 to-emerald-100/70 rounded-xl border border-emerald-200 shadow-sm">

    <div class="flex items-center justify-between gap-3">

        <div>
            <span class="text-[8px] text-emerald-800 uppercase font-black tracking-wider block mb-0.5">
                Precio del inmueble
            </span>

            <span class="text-2xl font-black text-emerald-950 tracking-tight leading-none">
                @if(!empty($property->price) && is_numeric($property->price))
                    ${{ number_format($property->price, 2) }}
                @else
                    Consultar precio
                @endif
            </span>
        </div>

        @if(!empty($property->price_condition))
            <span class="shrink-0 bg-white border border-emerald-200 text-emerald-900 text-[12px] font-black px-2.5 py-1 rounded-lg shadow-sm">
                <i class="fa-solid fa-tag text-emerald-600 mr-0.5"></i>
                {{ $property->price_condition }}
            </span>
        @endif

    </div>

</div>


    {{-- 3. DETALLES DE LA PROPIEDAD --}}

@php
    $hasPropertyDetails =
        (!empty($property->bedrooms) && $property->bedrooms > 0) ||
        (!empty($property->bathrooms_full) && $property->bathrooms_full > 0) ||
        (!empty($property->bathrooms_half) && $property->bathrooms_half > 0) ||
        (!empty($property->garages) && $property->garages > 0) ||
        !empty($property->has_sala) ||
        !empty($property->has_sala_estar) ||
        !empty($property->has_cocina) ||
        !empty($property->has_comedor) ||
        !empty($property->has_estudio);
@endphp

{{-- 3. DETALLES DE LA PROPIEDAD --}}
@if($hasPropertyDetails)

    <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

<div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    Detalles
</div>

        <div class="grid grid-cols-2 gap-1.5">

            @if(!empty($property->bedrooms) && $property->bedrooms > 0)
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-bed text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[12px] text-gray-800">
                        {{ $property->bedrooms }}  ->Dormitorios
                    </span>
                </div>
            @endif


            @if(!empty($property->bathrooms_full) && $property->bathrooms_full > 0)
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-bath text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[12px] text-gray-800">
                        {{ $property->bathrooms_full }} ->Baños Completos
                    </span>
                </div>
            @endif


            @if(!empty($property->bathrooms_half) && $property->bathrooms_half > 0)
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-toilet text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[12px] text-gray-800">
                        {{ $property->bathrooms_half }} ->Baños Medios
                    </span>
                </div>
            @endif


            @if(!empty($property->has_sala) && $property->has_sala > 0)
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-couch text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[12px] text-gray-800">
                        Sala
                    </span>
                </div>
            @endif


            @if(!empty($property->has_sala_estar))
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-tv text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[10px] text-gray-800">
                        Sala de estar
                    </span>
                </div>
            @endif


            @if(!empty($property->has_cocina))
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-utensils text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[10px] text-gray-800">
                        Cocina
                    </span>
                </div>
            @endif


            @if(!empty($property->has_comedor))
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-chair text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[10px] text-gray-800">
                        Comedor
                    </span>
                </div>
            @endif


            @if(!empty($property->has_estudio))
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-laptop-house text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[10px] text-gray-800">
                        Estudio
                    </span>
                </div>
            @endif


            @if(!empty($property->garages) && $property->garages > 0)
                <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-car text-emerald-600 w-4 text-center"></i>

                    <span class="font-extrabold text-[12px] text-gray-800">
                        {{ $property->garages }} ->Garaje
                    </span>
                </div>
            @endif

        </div>

    </div>

@endif

{{-- 5. CARACTERÍSTICAS ESPECIALES --}}
@if($hasSpecial)

    <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

<div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    La Propiedad También Cuenta o Permite:
</div>

        <div class="grid grid-cols-2 gap-1.5">

            @foreach($specialFeatures as $field => $data)

                @if(!empty($property->$field))

                    <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-2">

                        <div class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $data['icon'] }} text-emerald-600 text-[10px]"></i>
                        </div>

                        <span class="font-extrabold text-[12px] text-gray-800">
                            {{ $data['label'] }}
                        </span>

                    </div>

                @endif

            @endforeach

        </div>

    </div>

@endif

   {{-- 6. ADICIONAL Y ÁREAS --}}

@php
    $hasAdditionalInfo =
        !empty($property->documentation_status) ||
        !empty($property->antiquity_years);

    $hasAreaInfo =
        !empty($property->land_area_m2) ||
        !empty($property->construction_area_m2);
@endphp

@if($hasAdditionalInfo || $hasAreaInfo)

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

        {{-- INFORMACIÓN ADICIONAL --}}
        @if($hasAdditionalInfo)

            <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

<div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    Información Adicional
</div>

                <div class="space-y-1.5">

                    @if(!empty($property->documentation_status))

                        <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-start gap-2">

                            <div class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-file-circle-check text-emerald-600 text-[10px]"></i>
                            </div>

                            <div>
                                <span class="text-[8px] text-gray-400 font-black uppercase block">
                                    Documentación en Regla
                                </span>

                                <span class="text-[10px] font-extrabold text-gray-800">
                                    {{ $property->documentation_status }}
                                </span>
                            </div>

                        </div>

                    @endif


                    @if(!empty($property->antiquity_years))

                        <div class="p-2 bg-gray-50 rounded-lg border border-gray-100 flex items-start gap-2">

                            <div class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-clock-rotate-left text-emerald-600 text-[10px]"></i>
                            </div>

                            <div>
                                <span class="text-[8px] text-gray-400 font-black uppercase block">
                                    Antigüedad
                                </span>

                                <span class="text-[10px] font-extrabold text-gray-800">
                                    {{ $property->antiquity_years }} años
                                </span>
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        @endif


        {{-- ÁREAS --}}
        @if($hasAreaInfo)

            <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

<div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    Áreas
</div>

                <div class="space-y-1.5">

                    @if(!empty($property->land_area_m2))

                        <div class="p-2 bg-emerald-50/70 rounded-lg border border-emerald-100 flex items-start gap-2">

                            <div class="w-6 h-6 rounded-full bg-white border border-emerald-200 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-vector-square text-emerald-600 text-[10px]"></i>
                            </div>

                            <div>
                                <span class="text-[8px] text-gray-400 font-black uppercase block">
                                    Terreno
                                </span>

                                <span class="text-[11px] font-black text-gray-800">
                                    {{ number_format((float)$property->land_area_m2, 2) }} m²
                                </span>
                            </div>

                        </div>

                    @endif


                    @if(!empty($property->construction_area_m2))

                        <div class="p-2 bg-emerald-50/70 rounded-lg border border-emerald-100 flex items-start gap-2">

                            <div class="w-6 h-6 rounded-full bg-white border border-emerald-200 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-building text-emerald-600 text-[10px]"></i>
                            </div>

                            <div>
                                <span class="text-[8px] text-gray-400 font-black uppercase block">
                                    Construcción
                                </span>

                                <span class="text-[11px] font-black text-gray-800">
                                    {{ number_format((float)$property->construction_area_m2, 2) }} m²
                                </span>
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        @endif

    </div>

@endif
 {{-- 7. DESCRIPCIÓN DETALLADA --}}
@if(!empty($property->description))

    <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

<div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    Descripción Detallada
</div>

        <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5">

            <p class="text-[12px] text-gray-600 leading-relaxed whitespace-pre-line">
                {{ $property->description }}
            </p>

        </div>

    </div>

@endif

   {{-- 8. ENLACES Y RECURSOS --}}

@php
    $hasLinks =
        !empty($property->google_maps_url) ||
        !empty($property->virtual_tour_url) ||
        !empty($property->url_youtube) ||
        !empty($property->url_instagram) ||
        !empty($property->url_tiktok) ||
        !empty($property->url_facebook);
@endphp

@if($hasLinks)

    <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

        <div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
            <i class="fa-solid fa-link text-emerald-600"></i>
             Mira esta Propiedad tambien en:
        </div>

        <div class="grid grid-cols-2 gap-1.5">

            {{-- GOOGLE MAPS --}}
            @if(!empty($property->google_maps_url))
                <a href="{{ $property->google_maps_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 flex items-center gap-2 transition">

                    <div class="w-7 h-7 rounded-full bg-white border border-emerald-200 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-map-location-dot text-emerald-600 text-[11px]"></i>
                    </div>

                    <div>
                        <span class="text-[8px] text-gray-400 font-black uppercase block">
                            Ubicación
                        </span>
                        <span class="text-[10px] font-extrabold text-gray-800">
                            Google Maps
                        </span>
                    </div>

                </a>
            @endif


            {{-- TOUR 360 --}}
            @if(!empty($property->virtual_tour_url))
                <a href="{{ $property->virtual_tour_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 flex items-center gap-2 transition">

                    <div class="w-7 h-7 rounded-full bg-white border border-emerald-200 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-vr-cardboard text-emerald-600 text-[11px]"></i>
                    </div>

                    <div>
                        <span class="text-[8px] text-gray-400 font-black uppercase block">
                            Recorrido Virtual
                        </span>
                        <span class="text-[10px] font-extrabold text-gray-800">
                            Tour 360°
                        </span>
                    </div>

                </a>
            @endif


            {{-- YOUTUBE --}}
            @if(!empty($property->url_youtube))
                <a href="{{ $property->url_youtube }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 bg-gray-50 hover:bg-red-50 rounded-lg border border-gray-100 hover:border-red-200 flex items-center gap-2 transition">

                    <div class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                        <i class="fa-brands fa-youtube text-red-600 text-[13px]"></i>
                    </div>

                    <div>
                        <span class="text-[8px] text-gray-400 font-black uppercase block">
                            Video
                        </span>
                        <span class="text-[10px] font-extrabold text-gray-800">
                            YouTube
                        </span>
                    </div>

                </a>
            @endif


            {{-- INSTAGRAM --}}
            @if(!empty($property->url_instagram))
                <a href="{{ $property->url_instagram }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 bg-gray-50 hover:bg-pink-50 rounded-lg border border-gray-100 hover:border-pink-200 flex items-center gap-2 transition">

                    <div class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                        <i class="fa-brands fa-instagram text-pink-600 text-[13px]"></i>
                    </div>

                    <div>
                        <span class="text-[8px] text-gray-400 font-black uppercase block">
                            Red Social
                        </span>
                        <span class="text-[10px] font-extrabold text-gray-800">
                            Instagram
                        </span>
                    </div>

                </a>
            @endif


            {{-- TIKTOK --}}
            @if(!empty($property->url_tiktok))
                <a href="{{ $property->url_tiktok }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-100 hover:border-gray-300 flex items-center gap-2 transition">

                    <div class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                        <i class="fa-brands fa-tiktok text-gray-900 text-[13px]"></i>
                    </div>

                    <div>
                        <span class="text-[8px] text-gray-400 font-black uppercase block">
                            Red Social
                        </span>
                        <span class="text-[10px] font-extrabold text-gray-800">
                            TikTok
                        </span>
                    </div>

                </a>
            @endif


            {{-- FACEBOOK --}}
            @if(!empty($property->url_facebook))
                <a href="{{ $property->url_facebook }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2 bg-gray-50 hover:bg-blue-50 rounded-lg border border-gray-100 hover:border-blue-200 flex items-center gap-2 transition">

                    <div class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0">
                        <i class="fa-brands fa-facebook-f text-blue-600 text-[13px]"></i>
                    </div>

                    <div>
                        <span class="text-[8px] text-gray-400 font-black uppercase block">
                            Red Social
                        </span>
                        <span class="text-[10px] font-extrabold text-gray-800">
                            Facebook
                        </span>
                    </div>

                </a>
            @endif

        </div>

    </div>

@endif

 {{-- CONTACTO --}}
@if(
    !empty($property->contact_phone) ||
    !empty($property->contact_email)
)

    <div class="p-2.5 bg-white rounded-xl border border-gray-200 shadow-sm space-y-2">

<div class="font-black text-[11px] text-gray-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-gray-100 pb-1.5">
    <i class="fa-solid fa-location-dot text-emerald-600"></i>
    Contactonos:
</div>

        <div class="space-y-1.5">

            {{-- WHATSAPP / TELÉFONO --}}
            @if(!empty($property->contact_phone))

                @php
                    $whatsappNumber = preg_replace(
                        '/[^0-9]/',
                        '',
                        $property->contact_phone
                    );
                @endphp

                <a
                    href="https://wa.me/{{ $whatsappNumber }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full p-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg flex items-center gap-2 transition shadow-sm"
                >

                    <div class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                    </div>

                    <div class="text-left flex-1 min-w-0">

                        <span class="text-[7px] font-black uppercase tracking-wide text-emerald-100 block">
                            Teléfono / WhatsApp
                        </span>

                        <span class="text-[10px] font-extrabold block">
                            {{ $property->contact_phone }}
                        </span>

                    </div>

                    <i class="fa-solid fa-arrow-up-right-from-square text-[8px] opacity-70"></i>

                </a>

            @endif


            {{-- CORREO --}}
            @if(!empty($property->contact_email))

                <a
                    href="mailto:{{ $property->contact_email }}"
                    class="w-full p-2 bg-gray-50 hover:bg-emerald-50 border border-gray-100 hover:border-emerald-200 rounded-lg flex items-center gap-2 transition"
                >

                    <div class="w-7 h-7 bg-white border border-gray-200 rounded-full flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-envelope text-emerald-600 text-[10px]"></i>
                    </div>

                    <div class="text-left flex-1 min-w-0">

                        <span class="text-[7px] text-gray-400 font-black uppercase block">
                            Correo
                        </span>

                        <span class="text-[9px] text-gray-800 font-extrabold block truncate">
                            {{ $property->contact_email }}
                        </span>

                    </div>

                </a>

            @endif

        </div>

    </div>

@endif

    {{-- BOTÓN PÚBLICO --}}
{{-- ACCIÓN PÚBLICA: MENSAJE / CITA --}}
@if($isPublic)

    <div class="pt-1">

        <button
            type="button"
            onclick="toggleClientModal('{{ $propId }}', true)"
            class="group w-full bg-[#2d4a3e] hover:bg-[#233a30] text-white rounded-xl p-3 transition shadow-md shadow-[#2d4a3e]/20"
        >

            <div class="flex items-center gap-3">

                <div class="w-9 h-9 bg-white/10 group-hover:bg-white/15 rounded-full flex items-center justify-center shrink-0 transition">

                    <i class="fa-solid fa-calendar-check text-emerald-300"></i>

                </div>

                <div class="text-left flex-1">

                    <span class="text-[12px] text-emerald-200 font-black uppercase tracking-wider block">
                        ¿Interesado en esta propiedad?
                    </span>

                    <span class="text-[11px] font-black block">
                        Envianos tu Mensaje / Agenda tu Cita
                    </span>

                </div>

                <i class="fa-solid fa-chevron-right text-[10px] text-emerald-200 group-hover:translate-x-0.5 transition-transform"></i>

            </div>

        </button>

    </div>

@endif

</div>

{{-- MODAL DE CONTACTO / CITAS --}}
@if($isPublic)
    <div id="clientMessageModal-{{ $propId }}" class="fixed inset-0 overflow-hidden z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="toggleClientModal('{{ $propId }}', false)"></div>

        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl p-5 flex flex-col justify-between border-l border-[#e2dcc8] z-10">
                <div class="overflow-y-auto max-h-full pr-1">
                    <div class="flex items-center justify-between pb-3 border-b border-[#e2dcc8]">
                        <h2 id="modal-title" class="text-base font-bold text-[#2d4a3e]">Contacto y Cita Inmobiliaria</h2>
                        <button type="button" onclick="toggleClientModal('{{ $propId }}', false)" class="text-gray-400 hover:text-gray-600 font-bold text-base">✕</button>
                    </div>

                    @if (session('appointment_confirmed') && $isTargetProperty)
                        <div id="confirmationCard-{{ $propId }}" class="my-3 bg-emerald-50 border border-emerald-500 p-4 rounded-xl shadow-xs">
                            <h6 class="font-bold text-sm text-emerald-900 mb-1">¡Cita Registrada Exitosamente!</h6>
                            <p class="text-xs text-gray-700 mb-3">
                                Hola <strong>{{ $info['name'] ?? $info['client_name'] ?? 'Cliente' }}</strong>, tu cita para ver la propiedad <strong>{{ $info['property_title'] ?? '' }}</strong> el día <strong>{{ $info['date'] ?? $info['appointment_date'] ?? '' }}</strong> a las <strong>{{ $info['time'] ?? $info['appointment_time'] ?? '' }}</strong> ha sido recibida.
                            </p>
                            <div class="flex gap-2">
                                <form action="{{ route('public.appointments.confirm') }}" method="POST" class="w-1/2">
                                    @csrf
                                    <input type="hidden" name="appointment_id" value="{{ $info['appointment_id'] ?? '' }}">
                                    <button type="submit" class="w-full bg-emerald-600 text-white text-[11px] font-bold py-2 rounded-lg hover:bg-emerald-700 transition">
                                        SÍ, Confirmar
                                    </button>
                                </form>
                                <button type="button" onclick="modifyAppointment('{{ $propId }}', '{{ $info['appointment_id'] ?? '' }}')" class="w-1/2 bg-white text-gray-700 border border-gray-300 text-[11px] font-bold py-2 rounded-lg hover:bg-gray-100 transition">
                                    NO, Modificar
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mt-3 p-2.5 bg-red-100 border border-red-300 text-red-900 text-xs rounded-lg">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('public.messages.send') }}" method="POST" id="contactForm-{{ $propId }}" class="mt-3 space-y-2.5">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id ?? '' }}">
                        <input type="hidden" name="appointment_id" id="appointment_id-{{ $propId }}" value="{{ $info['appointment_id'] ?? '' }}">

                        <div>
                            <label for="name-{{ $propId }}" class="block text-[11px] font-bold text-gray-700 mb-0.5">Nombres y Apellidos <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name-{{ $propId }}" class="w-full text-xs p-2 border rounded-lg" placeholder="Ingrese su nombre completo" value="{{ old('name', $info['name'] ?? '') }}" required>
                        </div>

                        <div>
                            <label for="phone-{{ $propId }}" class="block text-[11px] font-bold text-gray-700 mb-0.5">Teléfono / Celular <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone-{{ $propId }}" class="w-full text-xs p-2 border rounded-lg" placeholder="Ej. 0998887777" value="{{ old('phone', $info['phone'] ?? '') }}" required>
                        </div>

                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                            <input class="w-3.5 h-3.5 text-emerald-600 rounded border-gray-300" type="checkbox" id="toggleAppointment-{{ $propId }}" name="want_appointment" value="1" onchange="toggleAppointmentFields('{{ $propId }}')" {{ old('want_appointment') || !empty($info) ? 'checked' : '' }}>
                            <label class="text-[11px] font-bold text-gray-700 cursor-pointer" for="toggleAppointment-{{ $propId }}">
                                ¿Desea agendar una cita presencial?
                            </label>
                        </div>

                        <div id="appointmentSection-{{ $propId }}" style="{{ old('want_appointment') || !empty($info) ? 'display: block;' : 'display: none;' }}" class="p-2.5 rounded-lg border border-emerald-200 bg-emerald-50/50 space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="appointment_date-{{ $propId }}" class="block text-[10px] font-bold text-gray-700 mb-0.5">Fecha</label>
                                    <input type="date" name="appointment_date" id="appointment_date-{{ $propId }}" class="w-full text-xs p-1.5 border rounded-md" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', $info['appointment_date'] ?? '') }}">
                                </div>
                                <div>
                                    <label for="appointment_time-{{ $propId }}" class="block text-[10px] font-bold text-gray-700 mb-0.5">Hora</label>
                                    <input type="time" name="appointment_time" id="appointment_time-{{ $propId }}" class="w-full text-xs p-1.5 border rounded-md" value="{{ old('appointment_time', $info['appointment_time'] ?? '') }}">
                                </div>
                            </div>
                            <div>
                                <label for="meeting_place-{{ $propId }}" class="block text-[10px] font-bold text-gray-700 mb-0.5">Lugar de Encuentro</label>
                                <input type="text" name="meeting_place" id="meeting_place-{{ $propId }}" class="w-full text-xs p-1.5 border rounded-md" placeholder="Ej: En el inmueble" value="{{ old('meeting_place', $info['meeting_place'] ?? '') }}">
                            </div>
                        </div>

                        <div>
                            <label for="message-{{ $propId }}" class="block text-[11px] font-bold text-gray-700 mb-0.5">Mensaje / Observación</label>
                            <textarea name="message" id="message-{{ $propId }}" class="w-full text-xs p-2 border rounded-lg" rows="2.5" placeholder="Escriba su consulta...">{{ old('message', $info['message'] ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-emerald-700 text-white text-xs font-bold py-2 rounded-lg hover:bg-emerald-800 transition" id="submitBtn-{{ $propId }}">
                            {{ old('want_appointment') || !empty($info) ? 'Agendar Cita y Enviar Mensaje' : 'Enviar Mensaje' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (typeof toggleClientModal !== 'function') {
            window.toggleClientModal = function(id, show) {
                const modal = document.getElementById('clientMessageModal-' + (id || 'default'));
                if (modal) {
                    if (show) modal.classList.remove('hidden');
                    else modal.classList.add('hidden');
                }
            };
        }

        if (typeof toggleAppointmentFields !== 'function') {
            window.toggleAppointmentFields = function(propId) {
                const toggle = document.getElementById('toggleAppointment-' + propId);
                if (!toggle) return;
                const section = document.getElementById('appointmentSection-' + propId);
                const btn = document.getElementById('submitBtn-' + propId);

                if (section) section.style.display = toggle.checked ? 'block' : 'none';
                if (btn) btn.textContent = toggle.checked ? 'Agendar Cita y Enviar Mensaje' : 'Enviar Mensaje';
            };
        }

        if (typeof modifyAppointment !== 'function') {
            window.modifyAppointment = function(propId, appointmentId) {
                const card = document.getElementById('confirmationCard-' + propId);
                if (card) card.style.display = 'none';

                if (appointmentId) {
                    const inputId = document.getElementById('appointment_id-' + propId);
                    if (inputId) inputId.value = appointmentId;
                }

                const toggle = document.getElementById('toggleAppointment-' + propId);
                if (toggle) {
                    toggle.checked = true;
                    toggleAppointmentFields(propId);
                }
            };
        }

        @if(session('appointment_confirmed'))
            @php
                $targetPropId = session('appointment_confirmed.property_id') ?? $propId;
            @endphp
            @if($targetPropId == $propId)
                document.addEventListener("DOMContentLoaded", function() {
                    toggleClientModal('{{ $targetPropId }}', true);
                    toggleAppointmentFields('{{ $targetPropId }}');
                });
            @endif
        @elseif($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                toggleClientModal('{{ $propId }}', true);
                toggleAppointmentFields('{{ $propId }}');
            });
        @endif
    </script>
@endif
