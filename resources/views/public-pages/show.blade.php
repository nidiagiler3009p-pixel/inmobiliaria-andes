@extends('layouts.public')

@section('content')

<div
    class="max-w-[98rem] mx-auto px-4 pb-16 space-y-4 font-sans"
    x-data="{
        images: [
            @foreach($property->images as $image)
                @if(!empty($image->image_path))
                    '{{ asset('storage/' . $image->image_path) }}',
                @endif
            @endforeach
        ],

        currentIndex: 0,
        socialOpen: false,
        shareCopied: false,

        get activeImage() {
            return this.images.length > 0
                ? this.images[this.currentIndex]
                : '';
        },

        nextImage() {
            if (this.images.length === 0) return;

            this.currentIndex =
                (this.currentIndex + 1) % this.images.length;
        },

        prevImage() {
            if (this.images.length === 0) return;

            this.currentIndex =
                (this.currentIndex - 1 + this.images.length)
                % this.images.length;
        },

        nextThumb() {
            const container =
                document.getElementById('thumbnail-container');

            if (container) {
                container.scrollBy({
                    left: 150,
                    behavior: 'smooth'
                });
            }
        },

        prevThumb() {
            const container =
                document.getElementById('thumbnail-container');

            if (container) {
                container.scrollBy({
                    left: -150,
                    behavior: 'smooth'
                });
            }
        },

        async shareProperty() {

            const shareData = {
                title: document.title,
                text: 'Mira esta propiedad en Inmobiliaria Los Andes del Ecuador',
                url: window.location.href
            };

            try {

                if (navigator.share) {
                    await navigator.share(shareData);
                    return;
                }

                if (navigator.clipboard) {

                    await navigator.clipboard.writeText(
                        window.location.href
                    );

                    this.shareCopied = true;

                    setTimeout(() => {
                        this.shareCopied = false;
                    }, 2200);

                    return;
                }

                window.prompt(
                    'Copia este enlace:',
                    window.location.href
                );

            } catch (error) {

                if (error && error.name === 'AbortError') {
                    return;
                }

                try {

                    if (navigator.clipboard) {

                        await navigator.clipboard.writeText(
                            window.location.href
                        );

                        this.shareCopied = true;

                        setTimeout(() => {
                            this.shareCopied = false;
                        }, 2200);

                    } else {

                        window.prompt(
                            'Copia este enlace:',
                            window.location.href
                        );

                    }

                } catch (copyError) {

                    window.prompt(
                        'Copia este enlace:',
                        window.location.href
                    );

                }
            }
        }
    }"
>

    {{-- ===================================================== --}}
    {{-- REGRESAR AL CATÁLOGO - COMPACTO                      --}}
    {{-- ===================================================== --}}

    <div class="flex items-center mb-1">
        <a
            href="{{ route('public.catalogo.index') }}"
            class="group inline-flex items-center gap-2.5 text-[#2C4A3E] hover:text-emerald-700 transition"
        >
            <span
                class="w-8 h-8 bg-[#2C4A3E] group-hover:bg-emerald-700 text-white flex items-center justify-center shadow-sm transition"
            >
                <i class="fa-solid fa-chevron-left text-[12px]"></i>
            </span>

            <span class="text-[11px] font-black uppercase tracking-wide">
                Catálogo Inmobiliaria Los Andes
            </span>
        </a>
    </div>


    {{-- ===================================================== --}}
    {{-- CONTENEDOR PRINCIPAL                                  --}}
    {{-- ===================================================== --}}

    <div
        class="
            grid
            grid-cols-1
            lg:grid-cols-12
            gap-[2px]
            items-start
        "
    >


        {{-- ================================================= --}}
        {{-- COLUMNA IZQUIERDA                                --}}
        {{-- ================================================= --}}

        <div class="lg:col-span-4 w-full">

            <div
                class="
                    lg:h-[calc(100vh-190px)]
                    lg:flex
                    lg:flex-col
                    lg:overflow-hidden
                "
            >


                {{-- INFORMACIÓN DE LA PROPIEDAD --}}

                <div
                    class="
                        property-sidebar-scroll
                        space-y-2
                        lg:flex-1
                        lg:min-h-0
                        lg:overflow-y-auto
                        lg:overflow-x-hidden
                        lg:pr-3
                        lg:pb-2
                    "
                >

                    <x-property-details
                        :property="$property"
                        :showContact="true"
                    />

                </div>


                {{-- ================================================= --}}
                {{-- BOTÓN GRANDE CONTACTAR                            --}}
                {{-- ================================================= --}}

                <div
                    class="
                        hidden
                        lg:block
                        shrink-0
                        pr-3
                        pt-2
                        pb-1
                    "
                >

                    <button
                        type="button"
                        onclick="toggleClientModal('{{ $property->id }}', true)"
                        class="
                            property-contact-button
                            group
                            w-full
                        "
                    >

                        <div class="flex items-center gap-3">


                            {{-- ICONO --}}

                            <div
                                class="
                                    w-10
                                    h-10
                                    rounded-xl
                                    bg-emerald-400/15
                                    border
                                    border-emerald-300/20
                                    flex
                                    items-center
                                    justify-center
                                    shrink-0
                                    group-hover:bg-emerald-400/25
                                    transition
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-calendar-check
                                        text-emerald-300
                                        text-[16px]
                                    "
                                ></i>

                            </div>


                            {{-- TEXTO --}}

                            <div
                                class="
                                    text-left
                                    flex-1
                                    leading-tight
                                "
                            >

                                <span
                                    class="
                                        block
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-white
                                    "
                                >
                                    ¿Interesado en esta propiedad?
                                </span>

                                <span
                                    class="
                                        block
                                        text-[9px]
                                        font-bold
                                        text-emerald-100/90
                                        mt-1
                                    "
                                >
                                    Envíanos tu mensaje o agenda tu cita
                                </span>

                            </div>


                            {{-- ACCIÓN --}}

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-2
                                    shrink-0
                                "
                            >

                                <span
                                    class="
                                        hidden
                                        xl:block
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-emerald-200
                                    "
                                >
                                    Contactar
                                </span>

                                <span
                                    class="
                                        w-9
                                        h-9
                                        rounded-xl
                                        bg-white/10
                                        border
                                        border-white/10
                                        flex
                                        items-center
                                        justify-center
                                        group-hover:bg-emerald-400
                                        group-hover:text-[#20382f]
                                        group-hover:translate-x-1
                                        transition-all
                                        duration-200
                                    "
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-arrow-right
                                            text-[11px]
                                        "
                                    ></i>

                                </span>

                            </div>

                        </div>

                    </button>

                </div>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- COLUMNA DERECHA                                  --}}
        {{-- ================================================= --}}

        <div
            class="
                lg:col-span-8
                w-full
                lg:sticky
                lg:top-4
            "
        >

            <div
                class="
                    bg-white
                    p-3
                    rounded-3xl
                    border
                    border-emerald-100
                    shadow-sm
                    space-y-3
                "
            >


                {{-- ================================================= --}}
                {{-- GALERÍA PRINCIPAL                                 --}}
                {{-- ================================================= --}}

                <div
                    class="
                        relative
                        w-full
                        bg-black
                        rounded-2xl
                        overflow-hidden
                        flex
                        items-center
                        justify-center
                        border
                        border-emerald-100
                        shadow-inner
                    "
                >

                    <template x-if="images.length > 0">

                        <div
                            class="
                                relative
                                w-full
                                flex
                                items-center
                                justify-center
                            "
                        >


                            {{-- ===================================== --}}
                            {{-- IMAGEN PRINCIPAL                      --}}
                            {{-- ===================================== --}}

                            <img
                                :src="activeImage"
                                class="
                                    w-full
                                    h-auto
                                    max-h-[580px]
                                    object-cover
                                    transition-all
                                    duration-300
                                "
                            >


                            {{-- SOMBRA SUPERIOR --}}

                            <div
                                class="
                                    absolute
                                    inset-x-0
                                    top-0
                                    h-20
                                    bg-gradient-to-b
                                    from-black/30
                                    to-transparent
                                    pointer-events-none
                                "
                            ></div>


                            {{-- ================================================= --}}
                            {{-- TOUR 360                                           --}}
                            {{-- ================================================= --}}

                            @if(!empty($property->virtual_tour_url))

                                <a
                                    href="{{ $property->virtual_tour_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Abrir recorrido virtual"
                                    class="
                                        absolute
                                        top-4
                                        right-4
                                        z-30
                                        flex
                                        items-center
                                        gap-2
                                        px-3
                                        py-2
                                        bg-[#2C4A3E]/95
                                        hover:bg-[#20382f]
                                        backdrop-blur-md
                                        text-white
                                        rounded-xl
                                        border
                                        border-white/50
                                        shadow-lg
                                        transition-all
                                        duration-200
                                        hover:scale-[1.03]
                                    "
                                >

                                    <span
                                        class="
                                            w-8
                                            h-8
                                            rounded-lg
                                            bg-emerald-400/20
                                            flex
                                            items-center
                                            justify-center
                                            shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-vr-cardboard
                                                text-emerald-300
                                                text-[15px]
                                            "
                                        ></i>

                                    </span>


                                    <span class="text-left leading-tight">

                                        <span
                                            class="
                                                block
                                                text-[8px]
                                                uppercase
                                                tracking-wider
                                                text-emerald-200
                                                font-bold
                                            "
                                        >
                                            Recorrido Virtual
                                        </span>

                                        <span
                                            class="
                                                block
                                                text-[11px]
                                                font-black
                                            "
                                        >
                                            Ver Tour 360°
                                        </span>

                                    </span>

                                </a>

                            @endif


                            {{-- ================================================= --}}
                            {{-- ACCIONES FLOTANTES                                --}}
                            {{-- ================================================= --}}

                            <div
                                class="
                                    absolute
                                    right-4
                                    top-[82px]
                                    z-40
                                    flex
                                    flex-col
                                    items-end
                                    gap-2
                                "
                                @click.outside="socialOpen = false"
                            >


                                {{-- ============================================= --}}
                                {{-- 1. COMPARTIR                                   --}}
                                {{-- ============================================= --}}

                                <div class="gallery-action-item">

                                    <span class="gallery-action-tooltip">
                                        Compartir propiedad
                                    </span>

                                    <button
                                        type="button"
                                        @click="shareProperty()"
                                        aria-label="Compartir propiedad"
                                        class="
                                            gallery-action-button
                                            gallery-action-primary
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-share-nodes
                                            "
                                        ></i>

                                    </button>

                                </div>


                                {{-- ============================================= --}}
                                {{-- 2. REDES SOCIALES                              --}}
                                {{-- ============================================= --}}

                                <div class="gallery-action-item">


                                    {{-- REDES DESPLEGADAS --}}

                                    <div
                                        x-show="socialOpen"
                                        x-cloak

                                        x-transition:enter="
                                            transition
                                            ease-out
                                            duration-200
                                        "

                                        x-transition:enter-start="
                                            opacity-0
                                            translate-x-3
                                            scale-95
                                        "

                                        x-transition:enter-end="
                                            opacity-100
                                            translate-x-0
                                            scale-100
                                        "

                                        x-transition:leave="
                                            transition
                                            ease-in
                                            duration-150
                                        "

                                        x-transition:leave-start="
                                            opacity-100
                                            translate-x-0
                                            scale-100
                                        "

                                        x-transition:leave-end="
                                            opacity-0
                                            translate-x-3
                                            scale-95
                                        "

                                        class="
                                            social-dropdown
                                        "
                                    >


                                        {{-- WHATSAPP --}}

                                        @if(!empty($property->contact_phone))

                                            @php
                                                $socialWhatsapp =
                                                    preg_replace(
                                                        '/[^0-9]/',
                                                        '',
                                                        $property->contact_phone
                                                    );
                                            @endphp

                                            <a
                                                href="https://wa.me/{{ $socialWhatsapp }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="WhatsApp"
                                                class="
                                                    property-social-circle
                                                    bg-[#25D366]
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-brands
                                                        fa-whatsapp
                                                    "
                                                ></i>

                                            </a>

                                        @endif


                                        {{-- FACEBOOK --}}

                                        @if(!empty($property->url_facebook))

                                            <a
                                                href="{{ $property->url_facebook }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="Facebook"
                                                class="
                                                    property-social-circle
                                                    bg-[#1877F2]
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-brands
                                                        fa-facebook-f
                                                    "
                                                ></i>

                                            </a>

                                        @endif


                                        {{-- INSTAGRAM --}}

                                        @if(!empty($property->url_instagram))

                                            <a
                                                href="{{ $property->url_instagram }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="Instagram"
                                                class="
                                                    property-social-circle
                                                    property-instagram
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-brands
                                                        fa-instagram
                                                    "
                                                ></i>

                                            </a>

                                        @endif


                                        {{-- TIKTOK --}}

                                        @if(!empty($property->url_tiktok))

                                            <a
                                                href="{{ $property->url_tiktok }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="TikTok"
                                                class="
                                                    property-social-circle
                                                    bg-black
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-brands
                                                        fa-tiktok
                                                    "
                                                ></i>

                                            </a>

                                        @endif


                                        {{-- YOUTUBE --}}

                                        @if(!empty($property->url_youtube))

                                            <a
                                                href="{{ $property->url_youtube }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="YouTube"
                                                class="
                                                    property-social-circle
                                                    bg-[#FF0000]
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-brands
                                                        fa-youtube
                                                    "
                                                ></i>

                                            </a>

                                        @endif

                                    </div>


                                    {{-- TOOLTIP --}}

                                    <span
                                        class="gallery-action-tooltip"
                                        x-text="
                                            socialOpen
                                                ? 'Cerrar redes sociales'
                                                : 'Ver redes sociales'
                                        "
                                    ></span>


                                    {{-- BOTÓN --}}

                                    <button
                                        type="button"
                                        @click="socialOpen = !socialOpen"
                                        :aria-expanded="socialOpen"
                                        aria-label="Mostrar redes sociales"
                                        class="
                                            gallery-action-button
                                            gallery-action-secondary
                                        "
                                    >

                                        <i
                                            x-show="!socialOpen"
                                            class="
                                                fa-solid
                                                fa-icons
                                            "
                                        ></i>

                                        <i
                                            x-show="socialOpen"
                                            x-cloak
                                            class="
                                                fa-solid
                                                fa-xmark
                                            "
                                        ></i>

                                    </button>

                                </div>


                                {{-- ============================================= --}}
                                {{-- 3. UBICACIÓN                                   --}}
                                {{-- ============================================= --}}

                                @if(!empty($property->google_maps_url))

                                    <div class="gallery-action-item">

                                        <span class="gallery-action-tooltip">
                                            Ver ubicación
                                        </span>

                                        <a
                                            href="{{ $property->google_maps_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="Ver ubicación"
                                            class="
                                                gallery-action-button
                                                gallery-action-secondary
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-location-dot
                                                "
                                            ></i>

                                        </a>

                                    </div>

                                @endif


                                {{-- ============================================= --}}
                                {{-- 4. MENSAJE / CITA                              --}}
                                {{-- ============================================= --}}

                                <div class="gallery-action-item">

                                    <span class="gallery-action-tooltip">
                                        Mensaje / Agendar cita
                                    </span>

                                    <button
                                        type="button"
                                        onclick="toggleClientModal('{{ $property->id }}', true)"
                                        aria-label="Enviar mensaje o agendar cita"
                                        class="
                                            gallery-action-button
                                            gallery-action-secondary
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-calendar-check
                                            "
                                        ></i>

                                    </button>

                                </div>

                            </div>


                            {{-- ================================================= --}}
                            {{-- MENSAJE ENLACE COPIADO                             --}}
                            {{-- ================================================= --}}

                            <div
                                x-show="shareCopied"
                                x-cloak
                                x-transition
                                class="
                                    absolute
                                    right-[72px]
                                    top-[82px]
                                    z-50
                                    bg-[#20382f]/95
                                    text-white
                                    px-3
                                    py-2
                                    rounded-xl
                                    shadow-xl
                                    backdrop-blur-md
                                    text-[10px]
                                    font-bold
                                    flex
                                    items-center
                                    gap-2
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-circle-check
                                        text-emerald-300
                                    "
                                ></i>

                                Enlace copiado

                            </div>


                            {{-- ================================================= --}}
                            {{-- FLECHA ANTERIOR - MÁS ABAJO                       --}}
                            {{-- ================================================= --}}

                            <button
                                type="button"
                                @click="prevImage()"
                                aria-label="Fotografía anterior"
                                title="Fotografía anterior"
                                class="
                                    absolute
                                    left-3
                                    top-[68%]
                                    -translate-y-1/2
                                    z-20

                                    bg-black/55
                                    hover:bg-[#2C4A3E]

                                    text-white

                                    w-11
                                    h-11

                                    rounded-full

                                    border
                                    border-white/30

                                    backdrop-blur-md

                                    transition-all
                                    duration-200

                                    flex
                                    items-center
                                    justify-center

                                    shadow-lg

                                    hover:scale-110

                                    cursor-pointer
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-chevron-left
                                        text-[15px]
                                    "
                                ></i>

                            </button>


                            {{-- ================================================= --}}
                            {{-- FLECHA SIGUIENTE - MÁS ABAJO                      --}}
                            {{-- ================================================= --}}

                            <button
                                type="button"
                                @click="nextImage()"
                                aria-label="Fotografía siguiente"
                                title="Fotografía siguiente"
                                class="
                                    absolute
                                    right-3
                                    top-[68%]
                                    -translate-y-1/2
                                    z-20

                                    bg-black/55
                                    hover:bg-[#2C4A3E]

                                    text-white

                                    w-11
                                    h-11

                                    rounded-full

                                    border
                                    border-white/30

                                    backdrop-blur-md

                                    transition-all
                                    duration-200

                                    flex
                                    items-center
                                    justify-center

                                    shadow-lg

                                    hover:scale-110

                                    cursor-pointer
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-chevron-right
                                        text-[15px]
                                    "
                                ></i>

                            </button>


                            {{-- ================================================= --}}
                            {{-- CONTADOR                                           --}}
                            {{-- ================================================= --}}

                            <div
                                class="
                                    absolute
                                    bottom-3
                                    right-3
                                    z-20

                                    bg-black/70

                                    text-white
                                    text-[11px]
                                    font-bold

                                    px-3
                                    py-1

                                    rounded-xl

                                    backdrop-blur-md

                                    shadow
                                "
                            >

                                <span x-text="currentIndex + 1"></span>

                                /

                                <span x-text="images.length"></span>

                            </div>

                        </div>

                    </template>


                    {{-- ================================================= --}}
                    {{-- SIN FOTOGRAFÍAS                                   --}}
                    {{-- ================================================= --}}

                    <template x-if="images.length === 0">

                        <div
                            class="
                                h-[400px]

                                flex
                                flex-col
                                items-center
                                justify-center

                                text-emerald-800/40

                                space-y-2
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-house-chimney-crack
                                    text-4xl
                                "
                            ></i>

                            <span
                                class="
                                    text-xs
                                    font-bold
                                    uppercase
                                "
                            >
                                Sin fotografías registradas
                            </span>

                        </div>

                    </template>

                </div>


                {{-- ================================================= --}}
                {{-- MINIATURAS                                        --}}
                {{-- ================================================= --}}

                <template x-if="images.length > 0">

                    <div
                        class="
                            relative
                            flex
                            items-center
                            px-6
                        "
                    >


                        {{-- MINIATURAS ANTERIORES --}}

                        <button
                            type="button"
                            @click="prevThumb()"
                            aria-label="Miniaturas anteriores"
                            title="Miniaturas anteriores"
                            class="
                                absolute
                                left-0
                                z-10

                                bg-white
                                hover:bg-emerald-50

                                text-emerald-900

                                shadow-md

                                border
                                border-emerald-200

                                p-2.5

                                rounded-full

                                transition

                                flex
                                items-center
                                justify-center

                                cursor-pointer
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-chevron-left
                                    text-xs
                                "
                            ></i>

                        </button>


                        {{-- CONTENEDOR DE MINIATURAS --}}

                        <div
                            id="thumbnail-container"
                            class="
                                flex
                                gap-2.5

                                overflow-x-auto
                                scroll-smooth

                                py-1
                                px-1

                                no-scrollbar

                                w-full
                            "
                        >

                            <template
                                x-for="(img, index) in images"
                                :key="index"
                            >

                                <div
                                    @click="currentIndex = index"

                                    class="
                                        h-20
                                        w-28

                                        flex-shrink-0

                                        rounded-xl
                                        overflow-hidden

                                        border-2

                                        cursor-pointer

                                        transition
                                        transform

                                        hover:scale-105
                                    "

                                    :class="
                                        currentIndex === index
                                            ? 'border-emerald-700 shadow-md ring-2 ring-emerald-600/30'
                                            : 'border-transparent opacity-60 hover:opacity-100'
                                    "
                                >

                                    <img
                                        :src="img"
                                        class="
                                            w-full
                                            h-full
                                            object-cover
                                        "
                                    >

                                </div>

                            </template>

                        </div>


                        {{-- MINIATURAS SIGUIENTES --}}

                        <button
                            type="button"
                            @click="nextThumb()"
                            aria-label="Miniaturas siguientes"
                            title="Miniaturas siguientes"
                            class="
                                absolute
                                right-0
                                z-10

                                bg-white
                                hover:bg-emerald-50

                                text-emerald-900

                                shadow-md

                                border
                                border-emerald-200

                                p-2.5

                                rounded-full

                                transition

                                flex
                                items-center
                                justify-center

                                cursor-pointer
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-chevron-right
                                    text-xs
                                "
                            ></i>

                        </button>

                    </div>

                </template>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- ESTILOS                                                   --}}
{{-- ========================================================= --}}

<style>

    /*
    |--------------------------------------------------------------------------
    | ALPINE
    |--------------------------------------------------------------------------
    */

    [x-cloak] {
        display: none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | BOTÓN PRINCIPAL DE CONTACTO
    |--------------------------------------------------------------------------
    */

    .property-contact-button {

        position: relative;

        padding: 10px 12px;

        background:
            linear-gradient(
                135deg,
                #2d4a3e 0%,
                #244237 100%
            );

        color: #ffffff;

        border:
            1px solid
            rgba(52, 211, 153, 0.35);

        border-radius: 14px;

        cursor: pointer;

        box-shadow:
            0 5px 12px rgba(22, 57, 45, 0.24),
            0 1px 2px rgba(0, 0, 0, 0.10);

        transition:
            transform 0.20s ease,
            box-shadow 0.20s ease,
            border-color 0.20s ease;

    }


    .property-contact-button:hover {

        transform: translateY(-2px);

        border-color:
            rgba(52, 211, 153, 0.70);

        box-shadow:
            0 10px 22px rgba(22, 57, 45, 0.34),
            0 2px 4px rgba(0, 0, 0, 0.12);

    }


    .property-contact-button:active {

        transform:
            translateY(0)
            scale(0.99);

    }


    .property-contact-button::before {

        content: '';

        position: absolute;

        left: 0;
        top: 20%;
        bottom: 20%;

        width: 3px;

        background: #34d399;

        border-radius:
            0
            10px
            10px
            0;

        opacity: 0;

        transition:
            opacity 0.20s ease;

    }


    .property-contact-button:hover::before {

        opacity: 1;

    }


    /*
    |--------------------------------------------------------------------------
    | CONTENEDOR INDIVIDUAL DE CADA ACCIÓN
    |--------------------------------------------------------------------------
    */

    .gallery-action-item {

        position: relative;

        display: flex;
        align-items: center;

    }


    /*
    |--------------------------------------------------------------------------
    | BOTONES FLOTANTES
    |--------------------------------------------------------------------------
    */

    .gallery-action-button {

        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 50%;

        font-size: 19px;

        cursor: pointer;

        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);

        box-shadow:
            0 4px 12px rgba(0, 0, 0, 0.28),
            0 1px 3px rgba(0, 0, 0, 0.18);

        transition:
            transform 0.18s ease,
            box-shadow 0.18s ease,
            background-color 0.18s ease,
            color 0.18s ease,
            border-color 0.18s ease;

    }


    /*
    |--------------------------------------------------------------------------
    | COMPARTIR
    |--------------------------------------------------------------------------
    */

    .gallery-action-primary {

        background: #2C4A3E;

        color: #ffffff;

        border:
            2px solid
            rgba(255, 255, 255, 0.90);

    }


    .gallery-action-primary:hover {

        background: #20382f;

        transform: scale(1.10);

        box-shadow:
            0 7px 18px
            rgba(0, 0, 0, 0.32);

    }


    /*
    |--------------------------------------------------------------------------
    | REDES / UBICACIÓN / CITA
    |--------------------------------------------------------------------------
    */

    .gallery-action-secondary {

        background:
            rgba(255, 255, 255, 0.97);

        color: #2C4A3E;

        border:
            2px solid
            rgba(44, 74, 62, 0.18);

    }


    .gallery-action-secondary:hover {

        background: #2C4A3E;

        color: #ffffff;

        border-color:
            rgba(255, 255, 255, 0.90);

        transform: scale(1.10);

        box-shadow:
            0 7px 18px
            rgba(0, 0, 0, 0.30);

    }


    /*
    |--------------------------------------------------------------------------
    | TOOLTIP
    |--------------------------------------------------------------------------
    */

    .gallery-action-tooltip {

        position: absolute;

        right: 57px;
        top: 50%;

        transform:
            translateY(-50%)
            translateX(6px);

        white-space: nowrap;

        padding:
            7px
            11px;

        background:
            rgba(32, 56, 47, 0.97);

        color: #ffffff;

        font-size: 10px;
        font-weight: 800;

        letter-spacing: 0.01em;

        border:
            1px solid
            rgba(255, 255, 255, 0.14);

        border-radius: 8px;

        box-shadow:
            0 5px 14px
            rgba(0, 0, 0, 0.25);

        opacity: 0;
        visibility: hidden;

        pointer-events: none;

        z-index: 80;

        transition:
            opacity 0.18s ease,
            visibility 0.18s ease,
            transform 0.18s ease;

    }


    /*
    | Pequeña flecha del tooltip
    */

    .gallery-action-tooltip::after {

        content: '';

        position: absolute;

        right: -5px;
        top: 50%;

        transform:
            translateY(-50%)
            rotate(45deg);

        width: 10px;
        height: 10px;

        background:
            rgba(32, 56, 47, 0.97);

    }


    /*
    | Mostrar tooltip
    */

    .gallery-action-item:hover
    .gallery-action-tooltip {

        opacity: 1;
        visibility: visible;

        transform:
            translateY(-50%)
            translateX(0);

    }


    /*
    |--------------------------------------------------------------------------
    | PANEL DESPLEGABLE DE REDES
    |--------------------------------------------------------------------------
    */

    .social-dropdown {

        position: absolute;

        right: 57px;
        top: 50%;

        transform:
            translateY(-50%);

        display: flex;
        align-items: center;

        gap: 7px;

        padding: 8px;

        background:
            rgba(255, 255, 255, 0.96);

        border:
            1px solid
            rgba(209, 250, 229, 0.95);

        border-radius: 15px;

        box-shadow:
            0 8px 24px
            rgba(0, 0, 0, 0.22);

        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        z-index: 70;

    }


    /*
    |--------------------------------------------------------------------------
    | CUANDO REDES ESTÁ ABIERTO
    | Evitamos que el tooltip quede encima del panel.
    |--------------------------------------------------------------------------
    */

    .gallery-action-item:has(.social-dropdown:not([style*="display: none"]))
    .gallery-action-tooltip {

        display: none;

    }


    /*
    |--------------------------------------------------------------------------
    | ICONOS DE REDES
    |--------------------------------------------------------------------------
    */

    .property-social-circle {

        width: 38px;
        height: 38px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 50%;

        color: #ffffff;

        font-size: 16px;

        border:
            2px solid
            rgba(255, 255, 255, 0.95);

        box-shadow:
            0 3px 9px
            rgba(0, 0, 0, 0.20);

        transition:
            transform 0.18s ease,
            box-shadow 0.18s ease;

    }


    .property-social-circle:hover {

        transform:
            translateY(-2px)
            scale(1.10);

        box-shadow:
            0 5px 13px
            rgba(0, 0, 0, 0.28);

    }


    /*
    |--------------------------------------------------------------------------
    | INSTAGRAM
    |--------------------------------------------------------------------------
    */

    .property-instagram {

        background:
            linear-gradient(
                135deg,
                #833ab4 0%,
                #fd1d1d 55%,
                #fcb045 100%
            );

    }


    /*
    |--------------------------------------------------------------------------
    | SCROLL DE INFORMACIÓN
    |--------------------------------------------------------------------------
    */

    @media (min-width: 1024px) {

        .property-sidebar-scroll {

            scrollbar-width: thin;

            scrollbar-color:
                #10b981
                #f1f5f9;

        }


        .property-sidebar-scroll::-webkit-scrollbar {

            width: 7px;

        }


        .property-sidebar-scroll::-webkit-scrollbar-track {

            background: #f1f5f9;

            border-radius: 20px;

        }


        .property-sidebar-scroll::-webkit-scrollbar-thumb {

            background: #10b981;

            border-radius: 20px;

        }


        .property-sidebar-scroll::-webkit-scrollbar-thumb:hover {

            background: #047857;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | MINIATURAS SIN SCROLLBAR
    |--------------------------------------------------------------------------
    */

    .no-scrollbar {

        -ms-overflow-style: none;

        scrollbar-width: none;

    }


    .no-scrollbar::-webkit-scrollbar {

        display: none;

    }


    /*
    |--------------------------------------------------------------------------
    | TABLET / CELULAR
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1023px) {

        /*
        | En táctil no dependemos del hover.
        */

        .gallery-action-tooltip {

            display: none;

        }


        .gallery-action-button {

            width: 42px;
            height: 42px;

            font-size: 17px;

        }


        .property-social-circle {

            width: 35px;
            height: 35px;

            font-size: 14px;

        }


        .social-dropdown {

            right: 50px;

            gap: 5px;

            padding: 6px;

        }

    }

</style>

@endsection