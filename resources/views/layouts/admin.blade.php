<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Panel de Control - Inmobiliaria Los Andes
    </title>


    {{-- TAILWIND --}}
    <script src="https://cdn.tailwindcss.com"></script>


    {{-- FONT AWESOME --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    {{-- ALPINE --}}
    <script defer
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>


    <style>

        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

    </style>

</head>


@php

    /*
    |--------------------------------------------------------------------------
    | RESOLVER RUTAS SIN PROVOCAR ERROR SI ALGUNA CAMBIA DE NOMBRE
    |--------------------------------------------------------------------------
    */

    $resolverRuta = function (array $rutas, $fallback = '#') {

        foreach ($rutas as $ruta) {

            if (\Illuminate\Support\Facades\Route::has($ruta)) {
                return route($ruta);
            }

        }

        return $fallback;
    };


    /*
    |--------------------------------------------------------------------------
    | RUTAS PRINCIPALES
    |--------------------------------------------------------------------------
    */

    $rutaClientes = $resolverRuta([
        'clients.index',
        'admin.clients.index',
        'admin.clientes.index'
    ]);


/*
|--------------------------------------------------------------------------
| CUADRO DE MANDO INTEGRAL
|--------------------------------------------------------------------------
*/

$rutaContabilidad = route('accounting.index');

$rutaGastos = route('accounting.expenses');

$rutaComisiones = route('accounting.commission-report');

$rutaOperaciones = route('accounting.operations');

$rutaPyG = route('accounting.pyg');

$rutaCosteoVehiculo = route('accounting.vehicle-costs');

$rutaConfigComisiones = route('accounting.commission-settings');


    /*
    |--------------------------------------------------------------------------
    | REDES SOCIALES
    |--------------------------------------------------------------------------
    |
    | Más adelante estas variables se pueden enviar desde Resumen General.
    |
    | Ejemplo:
    |
    | $socialLinks = [
    |     'facebook'  => '...',
    |     'instagram' => '...',
    |     ...
    | ];
    |
    */

    $redes = $socialLinks ?? [];

    $facebook =
        data_get($redes, 'facebook');

    $instagram =
        data_get($redes, 'instagram');

    $tiktok =
        data_get($redes, 'tiktok');

    $youtube =
        data_get($redes, 'youtube');

    $linkedin =
        data_get($redes, 'linkedin');


    /*
    |--------------------------------------------------------------------------
    | WEB PÚBLICA
    |--------------------------------------------------------------------------
    */

    $webPublica = url('/');


    /*
    |--------------------------------------------------------------------------
    | SABER SI ESTAMOS EN EL ÁREA CONTABLE
    |--------------------------------------------------------------------------
    */

    $cuadroActivo =
        request()->routeIs('admin.accounting*') ||
        request()->routeIs('accounting*') ||
        request()->routeIs('admin.contabilidad*') ||
        request()->routeIs('admin.gastos*') ||
        request()->routeIs('admin.comisiones*') ||
        request()->routeIs('admin.operaciones*') ||
        request()->routeIs('admin.pyg*') ||
        request()->routeIs('admin.costeo-vehiculo*');


    $configuracionActiva =
        request()->routeIs('admin.accounting.vehicle*') ||
        request()->routeIs('admin.accounting.commission-config*') ||
        request()->routeIs('admin.accounting.commissions.config*') ||
        request()->routeIs('admin.costeo-vehiculo*') ||
        request()->routeIs('admin.comisiones.configuracion*');

@endphp



<body
    class="bg-[#FDFBF7]
           font-sans
           antialiased
           min-h-screen
           relative
           overflow-x-hidden
           m-0 p-0"

    style="
        background-image:
        url('{{ asset('images/tu-fondo.jpg') }}');

        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    ">


<div
    class="min-h-screen w-full relative"

    x-data="{

        sidebarOpen: false,

        cuadroOpen: {{ $cuadroActivo ? 'true' : 'false' }},

        configuracionOpen:
            {{ $configuracionActiva ? 'true' : 'false' }},

        footerOpen: true

    }">



    {{-- ============================================================= --}}
    {{-- BOTÓN HAMBURGUESA DEL MENÚ --}}
    {{-- ============================================================= --}}

    <div
        class="fixed top-1 z-[60]
               transition-all duration-300"

        :class="
            sidebarOpen
                ? 'left-[285px]'
                : 'left-0'
        ">

        <button
            type="button"

            @click="
                sidebarOpen = !sidebarOpen
            "

            class="text-[#2C4A3E]
                   hover:text-emerald-700
                   focus:outline-none
                   py-2 px-2.5
                   rounded-r-xl
                   bg-white/95
                   backdrop-blur-md
                   shadow-md
                   border-y border-r
                   border-emerald-100
                   transition
                   cursor-pointer
                   flex items-center
                   justify-center
                   w-9 h-9"

            :title="
                sidebarOpen
                    ? 'Ocultar menú'
                    : 'Mostrar menú'
            ">

            <i
                class="fa-solid text-base"

                :class="
                    sidebarOpen
                        ? 'fa-chevron-left'
                        : 'fa-bars'
                ">
            </i>

        </button>

    </div>



    {{-- ============================================================= --}}
    {{-- ESTRUCTURA GENERAL --}}
    {{-- ============================================================= --}}

    <div
        class="relative flex
               min-h-screen
               w-full">


        {{-- ========================================================= --}}
        {{-- SIDEBAR --}}
        {{-- ========================================================= --}}

        <aside
            id="sidebar"

            x-cloak

            x-show="sidebarOpen"

            x-transition:enter="
                transition
                ease-out
                duration-200
            "

            x-transition:enter-start="
                opacity-0
                -translate-x-4
            "

            x-transition:enter-end="
                opacity-100
                translate-x-0
            "

            x-transition:leave="
                transition
                ease-in
                duration-150
            "

            x-transition:leave-start="
                opacity-100
                translate-x-0
            "

            x-transition:leave-end="
                opacity-0
                -translate-x-4
            "

            @click.away="
                sidebarOpen = false
            "

            class="
                fixed
                left-0
                top-12
                bottom-0
                z-50
                w-72

                bg-[#EFECE6]/95
                backdrop-blur-md

                border-r
                border-emerald-200/60

                shadow-xl

                flex
                flex-col
            ">



            {{-- ===================================================== --}}
            {{-- CABECERA DEL MENÚ --}}
            {{-- ===================================================== --}}

            <div
                class="
                    px-5 py-3

                    flex
                    justify-between
                    items-center

                    border-b
                    border-emerald-200/50
                ">

                <div>

                    <h3
                        class="
                            font-extrabold
                            text-[#2C4A3E]
                            text-xs
                            uppercase
                            tracking-wider
                        ">

                        Menú Principal

                    </h3>

                    <p
                        class="
                            text-[8px]
                            text-gray-500
                            mt-0.5
                        ">

                        Inmobiliaria Los Andes

                    </p>

                </div>


                <button
                    type="button"

                    @click="
                        sidebarOpen = false
                    "

                    class="
                        text-gray-400
                        hover:text-[#2C4A3E]
                        cursor-pointer
                        p-1
                    ">

                    <i
                        class="
                            fa-solid
                            fa-xmark
                            text-sm
                        ">
                    </i>

                </button>

            </div>



            {{-- ===================================================== --}}
            {{-- NAVEGACIÓN --}}
            {{-- ===================================================== --}}

            <div
               
    class="
        flex-1
        min-h-0
        overflow-y-auto
        no-scrollbar
        px-4
        pt-3
        pb-10
    ">


                <nav
                    class="
                        space-y-1
                        text-xs
                        font-bold
                    ">


                    {{-- RESUMEN GENERAL --}}

                    <a
                        href="{{ route('admin.resumen') }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('admin.resumen')
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-house-chimney
                                w-4
                                text-center
                            ">
                        </i>

                        Resumen General

                    </a>



                    {{-- AGENDA --}}

                    <a
                        href="{{ route('admin.agenda') }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('admin.agenda*')
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-calendar-days
                                w-4
                                text-center
                            ">
                        </i>

                        Tu Agenda

                    </a>



                    {{-- GESTIÓN DE CITAS --}}

                    <a
                        href="{{ route('gestion.citas') }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('gestion.citas*')
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-clock
                                w-4
                                text-center
                            ">
                        </i>

                        Tus Citas

                    </a>



                    {{-- ASESORES --}}

                    <a
                        href="{{ route('users.index') }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('users.*')
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-users
                                w-4
                                text-center
                            ">
                        </i>

                        Asesores

                    </a>



                    {{-- CATÁLOGO --}}

                    <a
                        href="{{ route('properties.index') }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('properties*')
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-building
                                w-4
                                text-center
                            ">
                        </i>

                        Tu Catálogo

                    </a>



                    {{-- CLIENTES TRÁMITES --}}

                    <a
                        href="{{ $rutaClientes }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('clients.*') ||
                                request()->routeIs('admin.clients.*') ||
                                request()->routeIs('admin.clientes.*')

                                    ? 'bg-[#2C4A3E] text-white shadow-sm'

                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-user-check
                                w-4
                                text-center
                            ">
                        </i>

                        Clientes Trámites

                    </a>



                    {{-- CITAS INTEGRALES --}}

                    <a
                        href="{{ route('admin.citas-totales') }}"

                        class="
                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl
                            transition

                            {{
                                request()->routeIs('admin.citas-totales*')
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <i
                            class="
                                fa-solid
                                fa-calendar-check
                                w-4
                                text-center
                            ">
                        </i>

                        Citas Integrales

                    </a>



                    {{-- ================================================= --}}
                    {{-- SEPARADOR --}}
                    {{-- ================================================= --}}

                    <div
                        class="
                            pt-3
                            pb-1
                        ">

                        <div
                            class="
                                border-t
                                border-emerald-200/60
                            ">
                        </div>

                    </div>



                    {{-- ================================================= --}}
                    {{-- CUADRO DE MANDO --}}
                    {{-- ================================================= --}}

                    <button
                        type="button"

                        @click="
                            cuadroOpen = !cuadroOpen
                        "

                        class="
                            w-full

                            flex
                            items-center
                            justify-between

                            px-3
                            py-2.5

                            rounded-xl
                            transition

                            {{
                                $cuadroActivo
                                    ? 'bg-[#2C4A3E] text-white shadow-sm'
                                    : 'text-[#2C4A3E] hover:bg-emerald-100/60'
                            }}
                        ">

                        <span
                            class="
                                flex
                                items-center
                                gap-3
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-chart-line
                                    w-4
                                    text-center
                                ">
                            </i>

                            Cuadro de Mando

                        </span>


                        <i
                            class="
                                fa-solid
                                fa-chevron-down
                                text-[9px]
                                transition-transform
                                duration-200
                            "

                            :class="
                                cuadroOpen
                                    ? 'rotate-180'
                                    : ''
                            ">
                        </i>

                    </button>



                    {{-- SUBMENÚ CUADRO DE MANDO --}}

                    <div
                        x-cloak
                        x-show="cuadroOpen"

                        x-collapse

                        class="
                            ml-3
                            pl-3

                            border-l
                            border-emerald-300/70

                            space-y-1
                            py-1
                        ">


                        {{-- CONTABILIDAD --}}

                        <a
                            href="{{ $rutaContabilidad }}"

                            class="
                                flex
                                items-center
                                gap-2.5

                                px-3
                                py-2

                                rounded-lg

                                text-[11px]
                                font-semibold

                                text-[#35574A]

                                hover:bg-white/70
                                transition
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-calculator
                                    w-4
                                    text-center
                                ">
                            </i>

                            Contabilidad

                        </a>



                        {{-- GASTOS --}}

                        <a
                            href="{{ $rutaGastos }}"

                            class="
                                flex
                                items-center
                                gap-2.5

                                px-3
                                py-2

                                rounded-lg

                                text-[11px]
                                font-semibold

                                text-[#35574A]

                                hover:bg-white/70
                                transition
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-receipt
                                    w-4
                                    text-center
                                ">
                            </i>

                            Gastos

                        </a>



                        {{-- COMISIONES ASESORES --}}

                        <a
                            href="{{ $rutaComisiones }}"

                            class="
                                flex
                                items-center
                                gap-2.5

                                px-3
                                py-2

                                rounded-lg

                                text-[11px]
                                font-semibold

                                text-[#35574A]

                                hover:bg-white/70
                                transition
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-hand-holding-dollar
                                    w-4
                                    text-center
                                ">
                            </i>

                            Comisiones Asesores

                        </a>



                        {{-- OPERACIONES --}}

                        <a
                            href="{{ $rutaOperaciones }}"

                            class="
                                flex
                                items-center
                                gap-2.5

                                px-3
                                py-2

                                rounded-lg

                                text-[11px]
                                font-semibold

                                text-[#35574A]

                                hover:bg-white/70
                                transition
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-arrow-right-arrow-left
                                    w-4
                                    text-center
                                ">
                            </i>

                            Operaciones

                        </a>



                        {{-- PYG --}}

                        <a
                            href="{{ $rutaPyG }}"

                            class="
                                flex
                                items-center
                                gap-2.5

                                px-3
                                py-2

                                rounded-lg

                                text-[11px]
                                font-semibold

                                text-[#35574A]

                                hover:bg-white/70
                                transition
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-chart-column
                                    w-4
                                    text-center
                                ">
                            </i>

                            Pérdidas y Ganancias

                        </a>



                        {{-- ============================================= --}}
                        {{-- CONFIGURACIÓN --}}
                        {{-- ============================================= --}}

                        <button
                            type="button"

                            @click="
                                configuracionOpen =
                                !configuracionOpen
                            "

                            class="
                                w-full

                                flex
                                items-center
                                justify-between

                                px-3
                                py-2

                                rounded-lg

                                text-[11px]
                                font-semibold

                                text-[#35574A]

                                hover:bg-white/70
                                transition
                            ">

                            <span
                                class="
                                    flex
                                    items-center
                                    gap-2.5
                                ">

                                <i
                                    class="
                                        fa-solid
                                        fa-gears
                                        w-4
                                        text-center
                                    ">
                                </i>

                                Configuración

                            </span>


                            <i
                                class="
                                    fa-solid
                                    fa-chevron-down
                                    text-[8px]
                                    transition-transform
                                "

                                :class="
                                    configuracionOpen
                                        ? 'rotate-180'
                                        : ''
                                ">
                            </i>

                        </button>



                        {{-- SUBMENÚ CONFIGURACIÓN --}}

                        <div
                            x-cloak
                            x-show="configuracionOpen"

                            class="
                                ml-4
                                pl-2

                                border-l
                                border-emerald-200

                                space-y-1
                            ">


                            {{-- VEHÍCULO --}}

                            <a
                                href="{{ $rutaCosteoVehiculo }}"

                                class="
                                    flex
                                    items-center
                                    gap-2

                                    px-2.5
                                    py-1.5

                                    rounded-lg

                                    text-[10px]
                                    font-semibold

                                    text-gray-600

                                    hover:bg-white/70
                                    hover:text-[#2C4A3E]

                                    transition
                                ">

                                <i
                                    class="
                                        fa-solid
                                        fa-car
                                        w-4
                                        text-center
                                    ">
                                </i>

                                Costeo Vehículo

                            </a>



                            {{-- CONFIG COMISIONES --}}

                            <a
                                href="{{ $rutaConfigComisiones }}"

                                class="
                                    flex
                                    items-center
                                    gap-2

                                    px-2.5
                                    py-1.5

                                    rounded-lg

                                    text-[10px]
                                    font-semibold

                                    text-gray-600

                                    hover:bg-white/70
                                    hover:text-[#2C4A3E]

                                    transition
                                ">

                                <i
                                    class="
                                        fa-solid
                                        fa-percent
                                        w-4
                                        text-center
                                    ">
                                </i>

                                Cálculo de Comisiones

                            </a>

                        </div>

                    </div>

                </nav>

            </>



            {{-- ===================================================== --}}
            {{-- PARTE INFERIOR SIDEBAR --}}
            {{-- ===================================================== --}}

            <div
                class="
                    flex-none
                    px-4
                    pb-4
                    pt-3

                    border-t
                    border-emerald-200/50

                    bg-[#EFECE6]/95
                ">


                {{-- REDES SOCIALES --}}

                <div
                    class="
                        bg-white/70
                        border
                        border-emerald-100

                        rounded-xl

                        px-3
                        py-2

                        text-center

                        mb-2
                    ">

                    <span
                        class="
                            text-[8px]
                            font-bold
                            uppercase
                            tracking-wide
                            text-gray-500
                            block
                            mb-1.5
                        ">

                        Visítanos en redes

                    </span>


                    <div
                        class="
                            flex
                            justify-center
                            items-center
                            gap-3

                            text-sm
                            text-[#2C4A3E]
                        ">


                        {{-- INSTAGRAM --}}

                        @if($instagram)

                            <a
                                href="{{ $instagram }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="Instagram"
                                class="hover:text-pink-600 transition">

                                <i class="fa-brands fa-instagram"></i>

                            </a>

                        @else

                            <span
                                title="Instagram"
                                class="opacity-40">

                                <i class="fa-brands fa-instagram"></i>

                            </span>

                        @endif



                        {{-- FACEBOOK --}}

                        @if($facebook)

                            <a
                                href="{{ $facebook }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="Facebook"
                                class="hover:text-blue-700 transition">

                                <i class="fa-brands fa-facebook"></i>

                            </a>

                        @else

                            <span
                                title="Facebook"
                                class="opacity-40">

                                <i class="fa-brands fa-facebook"></i>

                            </span>

                        @endif



                        {{-- TIKTOK --}}

                        @if($tiktok)

                            <a
                                href="{{ $tiktok }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="TikTok"
                                class="hover:text-black transition">

                                <i class="fa-brands fa-tiktok"></i>

                            </a>

                        @else

                            <span
                                title="TikTok"
                                class="opacity-40">

                                <i class="fa-brands fa-tiktok"></i>

                            </span>

                        @endif



                        {{-- YOUTUBE --}}

                        @if($youtube)

                            <a
                                href="{{ $youtube }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="YouTube"
                                class="hover:text-red-600 transition">

                                <i class="fa-brands fa-youtube"></i>

                            </a>

                        @else

                            <span
                                title="YouTube"
                                class="opacity-40">

                                <i class="fa-brands fa-youtube"></i>

                            </span>

                        @endif



                        {{-- LINKEDIN --}}

                        @if($linkedin)

                            <a
                                href="{{ $linkedin }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="LinkedIn"
                                class="hover:text-blue-700 transition">

                                <i class="fa-brands fa-linkedin"></i>

                            </a>

                        @else

                            <span
                                title="LinkedIn"
                                class="opacity-40">

                                <i class="fa-brands fa-linkedin"></i>

                            </span>

                        @endif

                    </div>

                </div>



                {{-- CERRAR SESIÓN --}}

                <form
                    action="{{ route('logout') }}"
                    method="POST">

                    @csrf


                    <button
                        type="submit"

                        class="
                            w-full

                            flex
                            items-center
                            gap-3

                            px-3
                            py-2

                            rounded-xl

                            text-red-600

                            hover:bg-red-50

                            font-bold

                            transition

                            text-xs
                        ">

                        <i
                            class="
                                fa-solid
                                fa-right-from-bracket
                            ">
                        </i>

                        Cerrar Sesión

                    </button>

                </form>

            </div>

        </aside>



        {{-- ========================================================= --}}
        {{-- CONTENIDO PRINCIPAL --}}
        {{-- ========================================================= --}}

        <div
            class="
                flex-1
                min-w-0

                flex
                flex-col

                min-h-screen

                transition-all
                duration-300
            "

            :class="
                sidebarOpen
                    ? 'md:pl-72'
                    : 'pl-0'
            ">


            {{-- CONTENIDO DINÁMICO --}}

            <main
                class="
                    flex-1
                    w-full
                    min-w-0
                ">

                @yield('admin_content')

            </main>



            {{-- ===================================================== --}}
            {{-- BOTÓN PARA OCULTAR / MOSTRAR FOOTER --}}
            {{-- ===================================================== --}}

            <div
                class="
                    flex
                    justify-center

                    relative

                    z-20
                ">

                <button
                    type="button"

                    @click="
                        footerOpen =
                        !footerOpen
                    "

                    class="
                        -mb-3
                        z-20

                        w-9
                        h-7

                        rounded-t-xl

                        bg-[#2C4A3E]

                        text-white

                        shadow-md

                        border
                        border-emerald-700/30

                        hover:bg-[#1F3B31]

                        transition

                        flex
                        items-center
                        justify-center
                    "

                    :title="
                        footerOpen
                            ? 'Ocultar pie de página'
                            : 'Mostrar pie de página'
                    ">

                    <i
                        class="
                            fa-solid
                            text-[10px]
                            transition-transform
                        "

                        :class="
                            footerOpen
                                ? 'fa-chevron-down'
                                : 'fa-chevron-up'
                        ">
                    </i>

                </button>

            </div>



            {{-- ===================================================== --}}
            {{-- FOOTER PLEGABLE --}}
            {{-- ===================================================== --}}

            <footer
                x-cloak

                x-show="footerOpen"

                x-transition

                class="
                    bg-[#EFECE6]/95
                    backdrop-blur-md

                    border-t
                    border-emerald-200/60

                    shadow-[0_-3px_14px_rgba(0,0,0,0.05)]

                    px-4
                    py-4
                ">


                <div
                    class="
                        max-w-6xl
                        mx-auto

                        flex
                        flex-col

                        lg:flex-row

                        items-center
                        justify-between

                        gap-4
                    ">



                    {{-- ================================================= --}}
                    {{-- IDENTIDAD --}}
                    {{-- ================================================= --}}

                    <div
                        class="
                            flex
                            items-center
                            gap-3

                            text-center
                            lg:text-left
                        ">


                        {{-- ICONO AYA HUMA / DIABLO HUMA --}}

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

                                overflow-hidden
                            "

                            title="Aya Huma - Identidad Andina">


                            {{-- 
                                Si luego agregas:
                                public/images/aya-huma.png
                                se mostrará automáticamente.
                            --}}

                            <img
                                src="{{ asset('images/aya-huma.png') }}"

                                alt="Aya Huma"

                                class="
                                    w-full
                                    h-full
                                    object-cover
                                "

                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                ">


                            <span
                                style="display:none"

                                class="
                                    w-full
                                    h-full

                                    items-center
                                    justify-center

                                    text-lg
                                ">

                                <i class="fa-solid fa-sun"></i>

                            </span>

                        </div>



                        <div>

                            <p
                                class="
                                    text-[10px]
                                    font-bold
                                    text-[#2C4A3E]
                                ">

                                © {{ date('Y') }}
                                Derechos Reservados

                            </p>


                            <p
                                class="
                                    text-[9px]
                                    text-gray-600
                                    mt-0.5
                                ">

                                Realizado por

                                <span
                                    class="
                                        font-bold
                                        text-[#2C4A3E]
                                    ">

                                    Software Andinos

                                </span>

                                para

                                <span
                                    class="
                                        font-semibold
                                    ">

                                    Inmobiliaria Los Andes

                                </span>

                            </p>


                            <p
                                class="
                                    text-[9px]
                                    font-semibold
                                    text-[#556B5D]
                                    mt-0.5
                                ">

                                Riobamba - Ecuador para el Mundo

                            </p>

                        </div>

                    </div>



                    {{-- ================================================= --}}
                    {{-- WEB PÚBLICA --}}
                    {{-- ================================================= --}}

                    <div
                        class="
                            flex
                            items-center
                            justify-center
                        ">

                        <a
                            href="{{ $webPublica }}"

                            target="_blank"

                            title="Visitar sitio web público"

                            class="
                                inline-flex
                                items-center
                                gap-2

                                px-3
                                py-2

                                rounded-xl

                                bg-white/70

                                border
                                border-emerald-100

                                text-[10px]
                                font-bold
                                text-[#2C4A3E]

                                hover:bg-white

                                transition
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-globe
                                ">
                            </i>

                            Web Pública

                        </a>

                    </div>



                    {{-- ================================================= --}}
                    {{-- REDES FOOTER --}}
                    {{-- ================================================= --}}

                    <div
                        class="
                            flex
                            flex-col
                            items-center
                            gap-1
                        ">

                        <span
                            class="
                                text-[8px]
                                uppercase
                                font-bold
                                tracking-wider
                                text-gray-500
                            ">

                            Síguenos

                        </span>


                        <div
                            class="
                                flex
                                items-center
                                justify-center

                                gap-3

                                text-base
                                text-[#2C4A3E]
                            ">


                            @if($instagram)

                                <a
                                    href="{{ $instagram }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Instagram"
                                    class="hover:scale-110 transition">

                                    <i class="fa-brands fa-instagram"></i>

                                </a>

                            @else

                                <span class="opacity-30">

                                    <i class="fa-brands fa-instagram"></i>

                                </span>

                            @endif



                            @if($facebook)

                                <a
                                    href="{{ $facebook }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Facebook"
                                    class="hover:scale-110 transition">

                                    <i class="fa-brands fa-facebook"></i>

                                </a>

                            @else

                                <span class="opacity-30">

                                    <i class="fa-brands fa-facebook"></i>

                                </span>

                            @endif



                            @if($tiktok)

                                <a
                                    href="{{ $tiktok }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="TikTok"
                                    class="hover:scale-110 transition">

                                    <i class="fa-brands fa-tiktok"></i>

                                </a>

                            @else

                                <span class="opacity-30">

                                    <i class="fa-brands fa-tiktok"></i>

                                </span>

                            @endif



                            @if($youtube)

                                <a
                                    href="{{ $youtube }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="YouTube"
                                    class="hover:scale-110 transition">

                                    <i class="fa-brands fa-youtube"></i>

                                </a>

                            @else

                                <span class="opacity-30">

                                    <i class="fa-brands fa-youtube"></i>

                                </span>

                            @endif



                            @if($linkedin)

                                <a
                                    href="{{ $linkedin }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="LinkedIn"
                                    class="hover:scale-110 transition">

                                    <i class="fa-brands fa-linkedin"></i>

                                </a>

                            @else

                                <span class="opacity-30">

                                    <i class="fa-brands fa-linkedin"></i>

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </footer>

        </div>

    </div>

</div>


</body>

</html> 