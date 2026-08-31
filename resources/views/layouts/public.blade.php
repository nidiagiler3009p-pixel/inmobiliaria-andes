<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Inmobiliaria Los Andes del Ecuador
    </title>


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])


    <!-- Alpine.js -->
    <script
        defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>


    <!-- FontAwesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <style>

        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

    </style>

</head>


@php

    /*
    |--------------------------------------------------------------------------
    | REDES SOCIALES
    |--------------------------------------------------------------------------
    */

    $redesSociales = \App\Models\SocialLink::where(
        'is_active',
        true
    )->get();


    /*
    |--------------------------------------------------------------------------
    | DETECTAR ICONO DE CADA RED
    |--------------------------------------------------------------------------
    */

    $iconoRed = function ($red) {

        $texto = strtolower(
            trim(
                ($red->platform ?? '') . ' ' .
                ($red->name ?? '') . ' ' .
                ($red->type ?? '') . ' ' .
                ($red->url_or_value ?? '')
            )
        );


        if (str_contains($texto, 'facebook')) {

            return 'fa-brands fa-facebook-f';

        }


        if (str_contains($texto, 'instagram')) {

            return 'fa-brands fa-instagram';

        }


        if (str_contains($texto, 'tiktok')) {

            return 'fa-brands fa-tiktok';

        }


        if (
            str_contains($texto, 'youtube') ||
            str_contains($texto, 'youtu.be')
        ) {

            return 'fa-brands fa-youtube';

        }


        if (
            str_contains($texto, 'whatsapp') ||
            str_contains($texto, 'wa.me')
        ) {

            return 'fa-brands fa-whatsapp';

        }


        if (str_contains($texto, 'linkedin')) {

            return 'fa-brands fa-linkedin-in';

        }


        return 'fa-solid fa-globe';
    };

@endphp



<body

    class="
        bg-[#FDFBF7]
        text-[#2C4A3E]
        font-sans
        antialiased
        relative
        overflow-x-hidden
    "

    style="
        background-image:
            url('{{ asset('images/fondo3.png') }}');

        background-repeat:
            repeat;
    "

    x-data="{
        mobileMenuOpen: false
    }"

>



{{-- ====================================================================== --}}
{{-- HEADER Y NAVEGACIÓN SUPERIOR --}}
{{-- ====================================================================== --}}

<header

    class="
        bg-[#FAF7F2]/95
        backdrop-blur-md

        border-b
        border-emerald-100

        shadow-sm

        sticky
        top-0

        z-50

        py-1
        px-4
    "

>

    <div

        class="
            max-w-7xl
            mx-auto

            flex
            flex-col
            md:flex-row

            items-center
            justify-between
        "

    >


        {{-- ============================================================= --}}
        {{-- IZQUIERDA --}}
        {{-- ============================================================= --}}

        <div

            class="
                flex
                items-center
                gap-4

                w-full
                md:w-auto

                justify-between
                md:justify-start
            "

        >


            <button

                @click="
                    mobileMenuOpen =
                    !mobileMenuOpen
                "

                class="
                    flex
                    items-center
                    gap-2

                    text-[#2C4A3E]

                    focus:outline-none

                    p-1.5

                    rounded-xl

                    hover:bg-emerald-100/60

                    transition
                "

            >

                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M4 6h16M4 12h16M4 18h16">
                    </path>

                </svg>

            </button>



            <a

                href="{{ url('/') }}"

                class="
                    flex
                    items-center
                    gap-8
                "

            >

                <img

                    src="{{ asset('images/conocenos_2.jpg') }}"

                    alt="Logo"

                    class="
                        h-12
                        w-auto
                        object-contain
                    "

                >

                <div>

                    <p
                        class="
                            text-[9px]
                            text-gray-600
                            font-bold
                            tracking-wider
                        "
                    ></p>

                </div>

            </a>

        </div>



        {{-- ============================================================= --}}
        {{-- MENÚ SUPERIOR --}}
        {{-- ============================================================= --}}

        <nav

            class="
                hidden
                lg:flex

                flex-1

                justify-center
                items-center

                gap-6
                xl:gap-8
            "

        >


            {{-- ASESORÍAS --}}

            <a

                href="{{ url('/asesorias') }}"

                class="
                    flex
                    flex-col

                    items-center
                    justify-center

                    group

                    text-decoration-none

                    py-1
                "

            >

                <img

                    src="{{ asset('images/asesorias_2.jpg') }}"

                    alt="Asesorías"

                    class="
                        w-11
                        h-11

                        object-contain

                        group-hover:scale-110

                        transition
                    "

                >

                <span

                    class="
                        text-[10px]
                        font-bold

                        {{
                            request()->is('asesorias')
                                ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5'
                                : 'text-[#2C4A3E]'
                        }}

                        mt-1

                        whitespace-nowrap
                        text-center
                    "

                >

                    asesorias

                </span>

            </a>



            {{-- TRÁMITES --}}

            <a

                href="{{ url('/tramites') }}"

                class="
                    flex
                    flex-col

                    items-center
                    justify-center

                    group

                    text-decoration-none

                    py-1
                "

            >

                <img

                    src="{{ asset('images/tramites_2.jpg') }}"

                    alt="Trámites"

                    class="
                        w-11
                        h-11

                        object-contain

                        group-hover:scale-110

                        transition
                    "

                >

                <span

                    class="
                        text-[10px]
                        font-bold

                        {{
                            request()->is('tramites')
                                ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5'
                                : 'text-[#2C4A3E]'
                        }}

                        mt-1

                        whitespace-nowrap
                        text-center
                    "

                >

                    tramites

                </span>

            </a>



            {{-- ========================================================= --}}
            {{-- CONÓCENOS --}}
            {{-- VA AL FINAL DE LA PÁGINA CONÓCENOS --}}
            {{-- ========================================================= --}}

            <a

                href="{{ url('/conocenos') }}?seccion=quienes-somos"

                class="
                    flex
                    flex-col

                    items-center
                    justify-center

                    group

                    text-decoration-none

                    py-1
                "

            >

                <img

                    src="{{ asset('images/conocenos2_2.jpg') }}"

                    alt="Conócenos"

                    class="
                        w-12
                        h-12

                        object-contain

                        group-hover:scale-110

                        transition
                    "

                >

                <span

                    class="
                        text-[10px]
                        font-bold

                        {{
                            request()->is('conocenos')
                                ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5'
                                : 'text-[#2C4A3E]'
                        }}

                        mt-1

                        whitespace-nowrap
                        text-center
                    "

                >

                    conocenos

                </span>

            </a>



            {{-- CATÁLOGO --}}

            <a

                href="{{ route('public.catalogo.index') }}"

                class="
                    flex
                    flex-col

                    items-center
                    justify-center

                    group

                    text-decoration-none

                    py-1
                "

            >

                <img

                    src="{{ asset('images/catalgo_2.jpg') }}"

                    alt="Catálogo"

                    class="
                        w-11
                        h-11

                        object-contain

                        group-hover:scale-110

                        transition
                    "

                >

                <span

                    class="
                        text-[10px]
                        font-bold

                        {{
                            request()->is('catalogo')
                                ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5'
                                : 'text-[#2C4A3E]'
                        }}

                        mt-1

                        whitespace-nowrap
                        text-center
                    "

                >

                    catalogo

                </span>

            </a>



            {{-- CONTÁCTANOS --}}

            <a

                href="{{ url('/contacto') }}"

                class="
                    flex
                    flex-col

                    items-center
                    justify-center

                    group

                    text-decoration-none

                    py-1
                "

            >

                <img

                    src="{{ asset('images/contactanos_2.jpg') }}"

                    alt="Contáctanos"

                    class="
                        w-11
                        h-11

                        object-contain

                        group-hover:scale-110

                        transition
                    "

                >

                <span

                    class="
                        text-[10px]
                        font-bold

                        {{
                            request()->is('contacto')
                                ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5'
                                : 'text-[#2C4A3E]'
                        }}

                        mt-1

                        whitespace-nowrap
                        text-center
                    "

                >

                    contactanos

                </span>

            </a>



            {{-- ÚNETE AL EQUIPO --}}

            <a

                href="{{ url('/unete') }}"

                class="
                    flex
                    flex-col

                    items-center
                    justify-center

                    group

                    text-decoration-none

                    py-1
                "

            >

                <img

                    src="{{ asset('images/unetealequipo_2.jpg') }}"

                    alt="Únete al equipo"

                    class="
                        w-12
                        h-12

                        object-contain

                        group-hover:scale-110

                        transition
                    "

                >

                <span

                    class="
                        text-[10px]
                        font-bold

                        {{
                            request()->is('unete')
                                ? 'text-emerald-800 border-b-2 border-emerald-800 pb-0.5'
                                : 'text-[#2C4A3E]'
                        }}

                        mt-1

                        whitespace-nowrap
                        text-center
                    "

                >

                    unete al equipo

                </span>

            </a>

        </nav>

    </div>

</header>



{{-- ====================================================================== --}}
{{-- BARRA LATERAL DESPLEGABLE --}}
{{-- ====================================================================== --}}

<div x-cloak>


    {{-- FONDO OSCURO --}}

    <div

        x-show="mobileMenuOpen"

        @click="
            mobileMenuOpen = false
        "

        class="
            fixed
            inset-0

            bg-black/40

            z-50

            transition-opacity
        "

    ></div>



    {{-- SIDEBAR --}}

    <div

        x-show="mobileMenuOpen"

        x-transition:enter="
            transition
            ease-out
            duration-300
        "

        x-transition:enter-start="
            -translate-x-full
        "

        x-transition:enter-end="
            translate-x-0
        "

        x-transition:leave="
            transition
            ease-in
            duration-200
        "

        x-transition:leave-start="
            translate-x-0
        "

        x-transition:leave-end="
            -translate-x-full
        "

        class="
            fixed

            inset-y-0
            left-0

            w-70

            shadow-2xl

            z-60

            flex
            flex-col
            justify-between

            overflow-y-auto

            bg-cover
            bg-center

            p-0
        "

        style="
            background-image:
                url('{{ asset('images/images.jpg') }}');
        "

    >


        <div>


            {{-- ========================================================= --}}
            {{-- CABECERA --}}
            {{-- ========================================================= --}}

            <div

                class="
                    px-4
                    py-3

                    border-b
                    border-gray-700/50

                    relative

                    flex
                    justify-center
                    items-center

                    bg-[#1a2b24]
                "

            >

                <img

                    src="{{ asset('images/hamburguer.jpg') }}"

                    alt="Logo"

                    class="
                        h-15
                        w-auto
                        object-contain
                    "

                >


                <button

                    @click="
                        mobileMenuOpen = false
                    "

                    class="
                        absolute
                        right-3

                        text-gray-300

                        hover:text-white

                        font-bold
                        text-lg

                        p-1

                        focus:outline-none
                    "

                >

                    ✕

                </button>

            </div>



            {{-- ========================================================= --}}
            {{-- NAVEGACIÓN LATERAL --}}
            {{-- ========================================================= --}}

            <nav

                class="
                    p-3
                    space-y-1

                    text-xs
                    font-bold
                "

            >


                {{-- INICIO --}}

                <a

                    href="{{ url('/conocenos') }}"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('/')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-house
                            text-lg
                        "
                    ></i>

                    Inicio

                </a>



                {{-- ===================================================== --}}
                {{-- QUIÉNES SOMOS --}}
                {{-- VA AL FINAL DE CONÓCENOS --}}
                {{-- ===================================================== --}}

                <a

                    href="{{ url('/conocenos') }}?seccion=quienes-somos"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('conocenos')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-users
                            text-lg
                        "
                    ></i>

                    Quienes somos

                </a>



                {{-- CATÁLOGO --}}

                <a

                    href="{{ route('public.catalogo.index') }}"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('catalogo')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-book
                            text-lg
                        "
                    ></i>

                    Catálogo

                </a>



                {{-- ASESORÍAS --}}

                <a

                    href="{{ url('/asesorias') }}"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('asesorias')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-lightbulb
                            text-lg
                        "
                    ></i>

                    Asesorías

                </a>



                {{-- TRÁMITES --}}

                <a

                    href="{{ url('/tramites') }}"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('tramites')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-file
                            text-lg
                        "
                    ></i>

                    Trámites

                </a>



                {{-- TRABAJA CON NOSOTROS --}}

                <a

                    href="{{ url('/unete') }}"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('unete')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-briefcase
                            text-lg
                        "
                    ></i>

                    Trabaja con nosotros

                </a>



                {{-- CONTÁCTANOS --}}

                <a

                    href="{{ url('/contacto') }}"

                    class="
                        flex
                        items-center
                        gap-3

                        w-full

                        px-4
                        py-3

                        rounded-lg

                        text-decoration-none

                        text-lg

                        {{
                            request()->is('contacto')
                                ? 'bg-[#EAF2ED] text-emerald-900 border border-emerald-300 shadow-sm'
                                : 'text-white drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.9)] hover:bg-white/15'
                        }}

                        transition-colors
                    "

                >

                    <i
                        class="
                            fa-solid
                            fa-phone
                            text-lg
                        "
                    ></i>

                    Contáctanos

                </a>

            </nav>

        </div>



        {{-- ================================================================== --}}
        {{-- REDES SOCIALES --}}
        {{-- ================================================================== --}}

        <div

            class="
                p-5

                border-t

                bg-white/90
                backdrop-blur-sm

                m-3

                rounded-3xl

                border
                border-emerald-100

                shadow-sm
            "

        >

            <p

                class="
                    text-xs
                    font-extrabold

                    text-[#2C4A3E]

                    mb-1
                "

            >

                Encuéntranos en redes sociales

            </p>


            <p

                class="
                    text-[10px]
                    text-gray-600

                    mb-4
                "

            >

                Síguenos y mantente al día con nuestras novedades.

            </p>



            <div

                class="
                    flex
                    justify-around
                    items-center
                    gap-2
                "

            >


                @forelse($redesSociales as $red)

                    @php

                        $icono =
                            $iconoRed($red);

                    @endphp


                    <a

                        href="{{ $red->url_or_value ?? '#' }}"

                        target="_blank"

                        rel="noopener noreferrer"

                        class="
                            bg-emerald-900

                            text-white

                            w-9
                            h-9

                            flex
                            items-center
                            justify-center

                            rounded-full

                            hover:scale-110
                            hover:bg-emerald-800

                            transition

                            shadow-md

                            text-decoration-none
                        "

                    >

                        <i
                            class="
                                {{ $icono }}
                                text-sm
                            "
                        ></i>

                    </a>


                @empty


                    {{-- FACEBOOK --}}

                    <span

                        title="Facebook"

                        class="
                            bg-emerald-900/45

                            text-white

                            w-8
                            h-8

                            flex
                            items-center
                            justify-center

                            rounded-full
                        "

                    >

                        <i class="fa-brands fa-facebook-f"></i>

                    </span>



                    {{-- INSTAGRAM --}}

                    <span

                        title="Instagram"

                        class="
                            bg-emerald-900/45

                            text-white

                            w-8
                            h-8

                            flex
                            items-center
                            justify-center

                            rounded-full
                        "

                    >

                        <i class="fa-brands fa-instagram"></i>

                    </span>



                    {{-- TIKTOK --}}

                    <span

                        title="TikTok"

                        class="
                            bg-emerald-900/45

                            text-white

                            w-8
                            h-8

                            flex
                            items-center
                            justify-center

                            rounded-full
                        "

                    >

                        <i class="fa-brands fa-tiktok"></i>

                    </span>



                    {{-- WHATSAPP --}}

                    <span

                        title="WhatsApp"

                        class="
                            bg-emerald-900/45

                            text-white

                            w-8
                            h-8

                            flex
                            items-center
                            justify-center

                            rounded-full
                        "

                    >

                        <i class="fa-brands fa-whatsapp"></i>

                    </span>



                    {{-- YOUTUBE --}}

                    <span

                        title="YouTube"

                        class="
                            bg-emerald-900/45

                            text-white

                            w-8
                            h-8

                            flex
                            items-center
                            justify-center

                            rounded-full
                        "

                    >

                        <i class="fa-brands fa-youtube"></i>

                    </span>


                @endforelse

            </div>

        </div>

    </div>

</div>



{{-- ====================================================================== --}}
{{-- CONTENIDO DINÁMICO --}}
{{-- ====================================================================== --}}

<main

    class="
        min-h-[70vh]
    "

>

    @yield('content')

</main>



{{-- ====================================================================== --}}
{{-- FOOTER INSTITUCIONAL --}}
{{-- ====================================================================== --}}

<footer

    class="
        bg-[#EFECE6]/95
        backdrop-blur-md

        border-t
        border-emerald-200/60

        shadow-[0_-3px_14px_rgba(0,0,0,0.05)]

        py-4
        px-6

        mt-12

        relative
        z-10
    "

>

    <div

        class="
            max-w-7xl
            mx-auto

            flex
            flex-col
            md:flex-row

            justify-between
            items-center

            gap-5
        "

    >


        {{-- ============================================================= --}}
        {{-- IDENTIDAD --}}
        {{-- ============================================================= --}}

        <div

            class="
                flex
                items-center
                gap-3
            "

        >

            <div

                class="
                    w-10
                    h-10

                    rounded-full

                    bg-[#2C4A3E]

                    text-white

                    flex
                    items-center
                    justify-center

                    shadow-sm
                "

            >

                <i
                    class="
                        fa-solid
                        fa-mountain-sun
                    "
                ></i>

            </div>


            <div>

                <p

                    class="
                        text-[10px]
                        font-bold

                        text-[#2C4A3E]

                        mb-0
                    "

                >

                    © {{ date('Y') }}
                    Inmobiliaria Los Andes del Ecuador

                </p>


                <p

                    class="
                        text-[9px]
                        text-gray-500

                        mb-0
                        mt-1
                    "

                >

                    Todos los derechos reservados

                </p>

            </div>

        </div>



        {{-- ============================================================= --}}
        {{-- SOFTWARE ANDINOS --}}
        {{-- ============================================================= --}}

        <div

            class="
                flex
                flex-col

                items-center

                text-center
            "

        >

            <span

                class="
                    text-[11px]

                    font-extrabold

                    text-[#2C4A3E]
                "

            >

                <i
                    class="
                        fa-solid
                        fa-code

                        mr-1

                        text-emerald-700
                    "
                ></i>

                www.softwareandinos.com

            </span>


            <span

                class="
                    text-[9px]

                    text-gray-600

                    mt-1

                    font-medium
                "

            >

                De Riobamba para el mundo

                <span
                    class="
                        mx-2
                        text-emerald-700
                    "
                >
                    •
                </span>

                Lo hacemos fácil por ti

            </span>

        </div>



        {{-- ============================================================= --}}
        {{-- REDES + INTRANET --}}
        {{-- ============================================================= --}}

        <div

            class="
                flex
                items-center

                gap-4
            "

        >


            {{-- REDES --}}

            <div

                class="
                    flex
                    items-center

                    gap-2
                "

            >

                @foreach($redesSociales as $red)

                    @php

                        $icono =
                            $iconoRed($red);

                    @endphp


                    <a

                        href="{{ $red->url_or_value ?? '#' }}"

                        target="_blank"

                        rel="noopener noreferrer"

                        class="
                            w-8
                            h-8

                            rounded-full

                            bg-white

                            border
                            border-emerald-100

                            text-[#2C4A3E]

                            flex
                            items-center
                            justify-center

                            text-decoration-none

                            shadow-sm

                            hover:bg-[#2C4A3E]
                            hover:text-white
                            hover:scale-110

                            transition
                        "

                    >

                        <i class="{{ $icono }}"></i>

                    </a>

                @endforeach

            </div>



            {{-- ACCESO INTRANET --}}

            <a

                href="{{ route('login') }}"

                title="Acceso para personal autorizado"

                class="
                    inline-flex
                    items-center

                    gap-1.5

                    px-2.5
                    py-1.5

                    rounded-lg

                    bg-white/70

                    border
                    border-emerald-200

                    text-[9px]
                    font-semibold

                    text-[#2C4A3E]

                    text-decoration-none

                    hover:bg-white
                    hover:shadow-sm

                    transition
                "

            >

                <i
                    class="
                        fa-solid
                        fa-lock
                        text-[8px]
                    "
                ></i>

                Acceso Intranet

            </a>

        </div>

    </div>

</footer>



{{-- ====================================================================== --}}
{{-- LLEVAR CONÓCENOS A LA PARTE FINAL --}}
{{-- ====================================================================== --}}

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const parametros =
                new URLSearchParams(
                    window.location.search
                );


            /*
            |--------------------------------------------------------------------------
            | CONÓCENOS / QUIÉNES SOMOS
            |--------------------------------------------------------------------------
            |
            | No necesita modificar conocenos.blade.php.
            |
            | Si llega:
            |
            | /conocenos?seccion=quienes-somos
            |
            | lleva automáticamente a la parte final de la pantalla.
            |
            */

            if (
                window.location.pathname.includes('/conocenos') &&
                parametros.get('seccion') === 'quienes-somos'
            ) {

                setTimeout(
                    function () {

                        window.scrollTo({

                            top:
                                document.documentElement.scrollHeight,

                            behavior:
                                'smooth'

                        });

                    },
                    250
                );

            }

        }
    );

</script>


</body>

</html>