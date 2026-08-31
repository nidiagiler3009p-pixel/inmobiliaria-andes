@extends('layouts.admin')

@section('admin_content')

@php

    /*
    |--------------------------------------------------------------------------
    | AGRUPAR CARTERA POR PERSONA
    |--------------------------------------------------------------------------
    */

    $prospectGroups = $portfolioEntries->groupBy(function ($entry) {

        if ($entry->prospect_id) {
            return 'prospect_' . $entry->prospect_id;
        }

        if ($entry->client_id) {
            return 'client_' . $entry->client_id;
        }

        if (!empty($entry->prospect_phone)) {
            return 'phone_' . preg_replace(
                '/[^0-9]/',
                '',
                $entry->prospect_phone
            );
        }

        return 'entry_' . $entry->id;
    });


    /*
    |--------------------------------------------------------------------------
    | RECUPERAR DATOS DEL REGISTRO ORIGINAL
    |--------------------------------------------------------------------------
    */

    $recuperarOrigen = function ($entry) {

        try {

            if (
                $entry->source_type === 'contact' &&
                $entry->source_record_id
            ) {

                $registro = \App\Models\Contact::find(
                    $entry->source_record_id
                );

                if ($registro) {
                    return [
                        'nombre' => trim(
                            ($registro->name ?? '') . ' ' .
                            ($registro->last_name ?? '')
                        ),

                        'telefono' =>
                            $registro->phone ?? null,
                    ];
                }
            }


            if (
                $entry->source_type === 'advisory' &&
                $entry->source_record_id
            ) {

                $registro =
                    \App\Models\AdvisoryRequest::find(
                        $entry->source_record_id
                    );

                if ($registro) {
                    return [
                        'nombre' =>
                            trim($registro->full_name ?? ''),

                        'telefono' =>
                            $registro->phone ?? null,
                    ];
                }
            }


            if (
                $entry->source_type === 'tramite' &&
                $entry->source_record_id
            ) {

                $registro =
                    \App\Models\Tramite::find(
                        $entry->source_record_id
                    );

                if ($registro) {
                    return [
                        'nombre' => trim(
                            ($registro->first_name ?? '') . ' ' .
                            ($registro->last_name ?? '')
                        ),

                        'telefono' =>
                            $registro->phone ?? null,
                    ];
                }
            }


            if (
                $entry->source_type === 'appointment' &&
                $entry->source_record_id
            ) {

                $registro =
                    \App\Models\AppointmentTracking::with([
                        'prospect',
                        'client'
                    ])->find(
                        $entry->source_record_id
                    );

                if ($registro) {

                    $persona =
                        $registro->prospect ??
                        $registro->client;

                    if ($persona) {

                        return [
                            'nombre' => trim(
                                ($persona->first_name ??
                                    $persona->name ??
                                    '') .
                                ' ' .
                                ($persona->last_name ?? '')
                            ),

                            'telefono' =>
                                $persona->phone ?? null,
                        ];
                    }
                }
            }

        } catch (\Throwable $e) {
        }

        return [
            'nombre' => null,
            'telefono' => null,
        ];
    };


    /*
    |--------------------------------------------------------------------------
    | INDICADORES
    |--------------------------------------------------------------------------
    */

    $totalProspectos =
        $prospectGroups->count();


    $totalSeguimiento =
        $prospectGroups->filter(function ($group) {

            $ultimo =
                $group
                    ->sortByDesc('entered_at')
                    ->first();

            return $ultimo &&
                $ultimo->portfolio_status === 'Seguimiento';

        })->count();


    $totalNegociacion =
        $prospectGroups->filter(function ($group) {

            $ultimo =
                $group
                    ->sortByDesc('entered_at')
                    ->first();

            return $ultimo &&
                $ultimo->portfolio_status === 'Negociación';

        })->count();


    $totalPotenciales =
        $prospectGroups->filter(function ($group) {

            return $group->contains(
                fn($entry) =>
                    $entry->portfolio_status ===
                    'Cliente Potencial'
            );

        })->count();

@endphp


<div class="flex-grow px-3 py-4 max-w-6xl mx-auto w-full
            text-[#2C3E35] font-sans antialiased">


    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <header
        class="relative bg-[#EFECE6]
               border border-[#D8D3C8]
               rounded-xl px-4 py-4
               shadow-sm mb-3">

        <div
            class="absolute left-1/2
                   -translate-x-1/2
                   -top-4
                   bg-[#2C5E43]
                   text-white
                   w-10 h-10
                   rounded-full
                   flex items-center justify-center
                   shadow
                   border-4 border-[#F9F7F2]">

            <i class="fa-solid fa-folder-open text-sm"></i>

        </div>


        <div class="text-center pt-2">

            <h1
                class="text-xl font-bold text-[#1E392A]">

                Cartera General de Prospectos

            </h1>

            <p
                class="text-[11px]
                       text-[#556B5D]
                       mt-1">

                Seguimiento comercial y tratamiento de prospectos.

            </p>

        </div>

    </header>



    {{-- ========================================================= --}}
    {{-- NAVEGACIÓN RÁPIDA --}}
    {{-- ========================================================= --}}

    <div
        class="flex flex-wrap
               justify-end
               gap-2 mb-3">

        <a
            href="{{ route('gestion.citas') }}"
            title="Ir a Gestión de Citas"
            class="inline-flex items-center gap-1.5
                   bg-[#2C5E43]
                   hover:bg-[#1E392A]
                   text-white
                   px-3 py-1.5
                   rounded-lg
                   text-[10px]
                   font-semibold
                   shadow-sm transition">

            <i class="fa-solid fa-calendar-check"></i>

            Gestión de Citas

        </a>


        <a
            href="{{ route('admin.citas-totales') }}"
            title="Ir a Citas Integrales"
            class="inline-flex items-center gap-1.5
                   bg-[#556B5D]
                   hover:bg-[#2C5E43]
                   text-white
                   px-3 py-1.5
                   rounded-lg
                   text-[10px]
                   font-semibold
                   shadow-sm transition">

            <i class="fa-solid fa-list-check"></i>

            Citas Integrales

        </a>

    </div>



    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div
            class="bg-emerald-100
                   border border-emerald-400
                   text-emerald-800
                   px-3 py-2
                   rounded-lg
                   mb-3
                   text-[11px]
                   flex items-center justify-between">

            <span>

                <i class="fa-solid fa-circle-check mr-1"></i>

                {{ session('success') }}

            </span>

            <button
                type="button"
                onclick="this.parentElement.remove()"
                class="font-bold">

                &times;

            </button>

        </div>

    @endif


    @if(session('error'))

        <div
            class="bg-red-100
                   border border-red-400
                   text-red-800
                   px-3 py-2
                   rounded-lg
                   mb-3
                   text-[11px]
                   flex items-center justify-between">

            <span>

                <i class="fa-solid fa-triangle-exclamation mr-1"></i>

                {{ session('error') }}

            </span>

            <button
                type="button"
                onclick="this.parentElement.remove()"
                class="font-bold">

                &times;

            </button>

        </div>

    @endif



    {{-- ========================================================= --}}
    {{-- FILTROS --}}
    {{-- ========================================================= --}}

    <form
        method="GET"
        action="{{ route('admin.cartera') }}"
        class="bg-[#EFECE6]
               border border-[#D8D3C8]
               rounded-xl
               p-3 mb-3
               shadow-sm">

        <div
            class="grid grid-cols-1
                   md:grid-cols-2
                   lg:grid-cols-5
                   gap-2">


            {{-- BUSCAR --}}

            <div class="lg:col-span-2">

                <label
                    class="block
                           text-[10px]
                           font-semibold
                           text-[#1E392A]
                           mb-1">

                    Buscar prospecto

                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nombre o teléfono..."
                    class="w-full
                           bg-white
                           border border-[#D8D3C8]
                           rounded-lg
                           px-2.5 py-1.5
                           text-[11px]">

            </div>


            {{-- ESTADO --}}

            <div>

                <label
                    class="block
                           text-[10px]
                           font-semibold
                           text-[#1E392A]
                           mb-1">

                    Estado

                </label>

                <select
                    name="status"
                    class="w-full
                           bg-white
                           border border-[#D8D3C8]
                           rounded-lg
                           px-2.5 py-1.5
                           text-[11px]">

                    <option value="">
                        Todos
                    </option>

                    @foreach([
                        'Nuevo',
                        'Contactado',
                        'Seguimiento',
                        'Interesado',
                        'Negociación',
                        'Cliente Potencial'
                    ] as $estado)

                        <option
                            value="{{ $estado }}"
                            {{ request('status') === $estado
                                ? 'selected'
                                : '' }}>

                            {{ $estado }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ORIGEN --}}

            <div>

                <label
                    class="block
                           text-[10px]
                           font-semibold
                           text-[#1E392A]
                           mb-1">

                    Origen

                </label>

                <select
                    name="source_type"
                    class="w-full
                           bg-white
                           border border-[#D8D3C8]
                           rounded-lg
                           px-2.5 py-1.5
                           text-[11px]">

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="appointment"
                        {{ request('source_type') ===
                            'appointment'
                            ? 'selected'
                            : '' }}>

                        Gestión de Citas

                    </option>

                    <option
                        value="contact"
                        {{ request('source_type') ===
                            'contact'
                            ? 'selected'
                            : '' }}>

                        Contáctanos

                    </option>

                    <option
                        value="advisory"
                        {{ request('source_type') ===
                            'advisory'
                            ? 'selected'
                            : '' }}>

                        Asesorías

                    </option>

                    <option
                        value="tramite"
                        {{ request('source_type') ===
                            'tramite'
                            ? 'selected'
                            : '' }}>

                        Trámites

                    </option>

                </select>

            </div>


            {{-- ASESOR --}}

            <div>

                <label
                    class="block
                           text-[10px]
                           font-semibold
                           text-[#1E392A]
                           mb-1">

                    Asesor

                </label>

                <select
                    name="advisor_id"
                    class="w-full
                           bg-white
                           border border-[#D8D3C8]
                           rounded-lg
                           px-2.5 py-1.5
                           text-[11px]">

                    <option value="">
                        Todos
                    </option>

                    @foreach($asesores as $asesor)

                        <option
                            value="{{ $asesor->id }}"
                            {{ request('advisor_id') ==
                                $asesor->id
                                ? 'selected'
                                : '' }}>

                            {{ $asesor->name }}
                            {{ $asesor->last_name ?? '' }}

                        </option>

                    @endforeach

                </select>

            </div>

        </div>


        <div
            class="flex justify-end
                   gap-2 mt-2">

            <a
                href="{{ route('admin.cartera') }}"
                class="bg-gray-300
                       hover:bg-gray-400
                       text-[#2C3E35]
                       px-3 py-1.5
                       rounded-lg
                       text-[10px]
                       font-semibold">

                <i class="fa-solid fa-rotate-left mr-1"></i>

                Limpiar

            </a>


            <button
                type="submit"
                class="bg-[#2C5E43]
                       hover:bg-[#1E392A]
                       text-white
                       px-3 py-1.5
                       rounded-lg
                       text-[10px]
                       font-semibold
                       shadow-sm">

                <i class="fa-solid fa-filter mr-1"></i>

                Filtrar

            </button>

        </div>

    </form>



    {{-- ========================================================= --}}
    {{-- INDICADORES --}}
    {{-- ========================================================= --}}

    <div
        class="grid grid-cols-2
               md:grid-cols-4
               gap-2 mb-3">

        <div
            class="bg-white
                   border border-[#D8D3C8]
                   rounded-lg
                   px-3 py-2
                   shadow-sm">

            <p
                class="text-[8px]
                       uppercase
                       font-bold
                       text-gray-500">

                Prospectos

            </p>

            <p
                class="text-xl
                       leading-none
                       mt-1
                       font-bold
                       text-[#1E392A]">

                {{ $totalProspectos }}

            </p>

        </div>


        <div
            class="bg-white
                   border border-[#D8D3C8]
                   rounded-lg
                   px-3 py-2
                   shadow-sm">

            <p
                class="text-[8px]
                       uppercase
                       font-bold
                       text-gray-500">

                Seguimiento

            </p>

            <p
                class="text-xl
                       leading-none
                       mt-1
                       font-bold
                       text-amber-700">

                {{ $totalSeguimiento }}

            </p>

        </div>


        <div
            class="bg-white
                   border border-[#D8D3C8]
                   rounded-lg
                   px-3 py-2
                   shadow-sm">

            <p
                class="text-[8px]
                       uppercase
                       font-bold
                       text-gray-500">

                Negociación

            </p>

            <p
                class="text-xl
                       leading-none
                       mt-1
                       font-bold
                       text-[#2C5E43]">

                {{ $totalNegociacion }}

            </p>

        </div>


        <div
            class="bg-white
                   border border-[#D8D3C8]
                   rounded-lg
                   px-3 py-2
                   shadow-sm">

            <p
                class="text-[8px]
                       uppercase
                       font-bold
                       text-gray-500">

                Potenciales

            </p>

            <p
                class="text-xl
                       leading-none
                       mt-1
                       font-bold
                       text-emerald-700">

                {{ $totalPotenciales }}

            </p>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- TABLA CARTERA --}}
    {{-- ========================================================= --}}

    <section
        class="bg-[#EFECE6]
               border border-[#D8D3C8]
               rounded-xl
               px-3 py-3
               shadow-sm">

        <div
            class="flex flex-wrap
                   justify-between
                   items-center
                   gap-2
                   mb-2 px-1">

            <h2
                class="text-sm
                       font-bold
                       text-[#1E392A]">

                <i
                    class="fa-solid
                           fa-address-book
                           text-[#2C5E43]
                           mr-1">
                </i>

                Prospectos en Cartera

            </h2>

            <span
                class="text-[10px]
                       text-[#556B5D]
                       font-semibold">

                Total: {{ $totalProspectos }}

            </span>

        </div>


        <div
            class="overflow-x-auto
                   overflow-y-auto
                   max-h-[430px]
                   relative">

            <table
                class="w-full
                       text-left
                       border-collapse
                       text-[10px]">

                <thead>

                    <tr
                        class="text-white shadow-sm">

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Prospecto

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Teléfono / Contacto

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Origen

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Estado anterior

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Estado Cartera

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Propiedad

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Asesor

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Motivo

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2">

                            Último ingreso

                        </th>

                        <th
                            class="sticky top-0 z-20
                                   bg-[#2C5E43]
                                   px-2 py-2
                                   text-center">

                            Acciones

                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y divide-[#D8D3C8]">

                @forelse(
                    $prospectGroups
                    as $groupKey => $group
                )

                    @php

                        $groupOrdenado =
                            $group
                                ->sortByDesc('entered_at');

                        $latestEntry =
                            $groupOrdenado->first();

                        $movimientos =
                            $group->count();

                        $persona =
                            $latestEntry->prospect ??
                            $latestEntry->client;


                        $entryConNombre =
                            $groupOrdenado->first(
                                fn($r) =>
                                    !empty(
                                        trim(
                                            ($r->prospect_name ?? '') .
                                            ' ' .
                                            ($r->prospect_last_name ?? '')
                                        )
                                    )
                            );


                        $nombreGuardado =
                            $entryConNombre
                                ? trim(
                                    ($entryConNombre->prospect_name ?? '') .
                                    ' ' .
                                    ($entryConNombre->prospect_last_name ?? '')
                                )
                                : '';


                        $datosAntiguos =
                            $recuperarOrigen(
                                $latestEntry
                            );


                        $nombre = trim(
                            ($persona->first_name ??
                                $persona->name ??
                                '') .
                            ' ' .
                            ($persona->last_name ?? '')
                        );


                        if (empty($nombre)) {
                            $nombre = $nombreGuardado;
                        }

                        if (empty($nombre)) {
                            $nombre =
                                $datosAntiguos['nombre']
                                ?? '';
                        }

                        if (empty($nombre)) {
                            $nombre =
                                'Registro antiguo';
                        }


                        $entryConTelefono =
                            $groupOrdenado->first(
                                fn($r) =>
                                    !empty(
                                        $r->prospect_phone
                                    )
                            );


                        $telefono =
                            $persona->phone
                            ?? $entryConTelefono?->prospect_phone
                            ?? $datosAntiguos['telefono']
                            ?? 'N/A';


                        $socialEntry =
                            $groupOrdenado->first(
                                fn($r) =>
                                    !empty(
                                        $r->social_profile_url
                                    )
                            );


                        $socialUrl =
                            $socialEntry?->social_profile_url;

                        $socialPlatform =
                            $socialEntry?->social_platform;


                        $origenesConteo =
                            $group
                                ->pluck('source_type')
                                ->filter()
                                ->countBy();


                        $esPotencial =
                            $group->contains(
                                fn($r) =>
                                    $r->portfolio_status ===
                                    'Cliente Potencial'
                            );


                        $returnTitle =
                            match(
                                $latestEntry->source_type
                            ) {

                                'appointment' =>
                                    'Regresar a Gestión de Citas',

                                'contact' =>
                                    'Regresar a Citas Integrales - Contáctanos',

                                'advisory' =>
                                    'Regresar a Citas Integrales - Asesoría',

                                'tramite' =>
                                    'Regresar a Citas Integrales - Trámite',

                                default =>
                                    'Regresar al módulo de origen',
                            };

                    @endphp


                    <tr
                        class="hover:bg-[#F4F1EA]
                               transition
                               {{ $esPotencial
                                    ? 'border-l-2 border-amber-400'
                                    : '' }}">


                        {{-- PROSPECTO --}}

                        <td
                            class="px-2 py-2
                                   relative
                                   min-w-[130px]">

                            @if($esPotencial)

                                <div
                                    class="absolute
                                           top-1 right-1
                                           inline-flex
                                           items-center
                                           gap-0.5
                                           text-[7px]
                                           font-semibold
                                           text-amber-600
                                           opacity-60"
                                    title="Cliente Potencial">

                                    <i
                                        class="fa-solid fa-star">
                                    </i>

                                    Potencial

                                </div>

                            @endif


                            <div
                                class="font-bold
                                       text-[#1E392A]
                                       text-[11px]
                                       pr-11">

                                {{ $nombre }}

                            </div>


                            @if($movimientos > 0)

                                <div
                                    class="mt-0.5
                                           inline-flex
                                           items-center
                                           gap-1
                                           text-[8px]
                                           text-[#2C5E43]
                                           font-semibold">

                                    <i
                                        class="fa-solid
                                               fa-clock-rotate-left">
                                    </i>

                                    {{ $movimientos }}

                                    {{
                                        $movimientos === 1
                                            ? 'movimiento'
                                            : 'movimientos'
                                    }}

                                </div>

                            @endif

                        </td>



                        {{-- TELÉFONO --}}

                        <td
                            class="px-2 py-2
                                   min-w-[105px]">

                            <div
                                class="font-medium
                                       text-[#1E392A]">

                                {{ $telefono }}

                            </div>


                            @if($socialUrl)

                                <a
                                    href="{{ $socialUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Abrir {{ $socialPlatform ?? 'perfil social' }}"
                                    class="inline-flex
                                           items-center
                                           gap-1
                                           mt-0.5
                                           text-[8px]
                                           text-[#2C5E43]
                                           font-semibold
                                           hover:underline">

                                    @if(
                                        strtolower(
                                            $socialPlatform ?? ''
                                        ) === 'instagram'
                                    )

                                        <i
                                            class="fa-brands fa-instagram">
                                        </i>

                                    @elseif(
                                        strtolower(
                                            $socialPlatform ?? ''
                                        ) === 'facebook'
                                    )

                                        <i
                                            class="fa-brands fa-facebook">
                                        </i>

                                    @elseif(
                                        strtolower(
                                            $socialPlatform ?? ''
                                        ) === 'tiktok'
                                    )

                                        <i
                                            class="fa-brands fa-tiktok">
                                        </i>

                                    @else

                                        <i
                                            class="fa-solid fa-link">
                                        </i>

                                    @endif


                                    {{
                                        $socialPlatform
                                        ?? 'Ver perfil'
                                    }}

                                </a>

                            @endif

                        </td>



                        {{-- ORIGEN --}}

                        <td
                            class="px-2 py-2
                                   max-w-[145px]">

                            <div
                                class="flex flex-wrap gap-1">

                                @foreach(
                                    $origenesConteo
                                    as $origen => $cantidad
                                )

                                    @php

                                        $origenTexto =
                                            match($origen) {

                                                'appointment' =>
                                                    'Citas',

                                                'contact' =>
                                                    'Contáctanos',

                                                'advisory' =>
                                                    'Asesoría',

                                                'tramite' =>
                                                    'Trámite',

                                                default =>
                                                    ucfirst($origen),
                                            };

                                    @endphp


                                    <span
                                        class="inline-flex
                                               items-center
                                               gap-1
                                               bg-emerald-50
                                               text-[#2C5E43]
                                               border
                                               border-emerald-200
                                               px-1.5 py-0.5
                                               rounded
                                               text-[8px]
                                               font-semibold
                                               whitespace-nowrap">

                                        {{ $origenTexto }}


                                        @if($cantidad > 1)

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       justify-center
                                                       min-w-[13px]
                                                       h-[13px]
                                                       px-1
                                                       rounded-full
                                                       bg-[#2C5E43]/10
                                                       text-[#1E392A]
                                                       text-[7px]
                                                       font-bold">

                                                {{ $cantidad }}

                                            </span>

                                        @endif

                                    </span>

                                @endforeach

                            </div>

                        </td>



                        {{-- ESTADO ANTERIOR --}}

                        <td class="px-2 py-2">

                            <span
                                class="inline-flex
                                       bg-gray-100
                                       border
                                       border-gray-300
                                       px-1.5 py-0.5
                                       rounded
                                       text-[8px]
                                       font-semibold
                                       whitespace-nowrap">

                                {{
                                    $latestEntry->previous_status
                                    ?? 'N/A'
                                }}

                            </span>

                        </td>



                        {{-- ESTADO CARTERA --}}

                        <td class="px-2 py-2">

                            <span
                                class="inline-flex
                                       px-1.5 py-0.5
                                       rounded
                                       text-[8px]
                                       font-semibold
                                       whitespace-nowrap

                                {{
                                    $latestEntry->portfolio_status ===
                                    'Cliente Potencial'

                                        ? 'bg-emerald-100 text-emerald-800 border border-emerald-300'

                                        : (
                                            $latestEntry->portfolio_status ===
                                            'Negociación'

                                            ? 'bg-[#E4EFE8] text-[#1E392A] border border-[#8CB49A]'

                                            : 'bg-amber-100 text-amber-800 border border-amber-300'
                                        )
                                }}">

                                {{
                                    $latestEntry->portfolio_status
                                    ?? 'Nuevo'
                                }}

                            </span>

                        </td>



                        {{-- PROPIEDAD --}}

                        <td
                            class="px-2 py-2
                                   max-w-[120px]">

                            @if($latestEntry->property)

                                <span
                                    class="text-[#2C5E43]
                                           font-semibold">

                                    {{
                                        $latestEntry
                                            ->property
                                            ->title
                                    }}

                                </span>

                            @else

                                <span
                                    class="text-gray-400 italic">

                                    Sin propiedad

                                </span>

                            @endif

                        </td>



                        {{-- ASESOR --}}

                        <td
                            class="px-2 py-2
                                   max-w-[90px]">

                            @if($latestEntry->advisor)

                                <div
                                    class="leading-tight">

                                    {{
                                        $latestEntry
                                            ->advisor
                                            ->name
                                    }}

                                    <br>

                                    {{
                                        $latestEntry
                                            ->advisor
                                            ->last_name
                                        ?? ''
                                    }}

                                </div>

                            @else

                                <span
                                    class="text-gray-400 italic">

                                    Sin asesor

                                </span>

                            @endif

                        </td>



                        {{-- MOTIVO --}}

                        <td
                            class="px-2 py-2
                                   max-w-[150px]">

                            <div
                                class="whitespace-normal
                                       break-words
                                       leading-tight">

                                {{
                                    $latestEntry->entry_reason
                                }}

                            </div>


                            @if($latestEntry->notes)

                                <div
                                    class="text-[8px]
                                           text-gray-500
                                           mt-0.5
                                           leading-tight">

                                    {{
                                        $latestEntry->notes
                                    }}

                                </div>

                            @endif

                        </td>



                        {{-- FECHA --}}

                        <td
                            class="px-2 py-2
                                   whitespace-nowrap">

                            @if($latestEntry->entered_at)

                                <div>

                                    {{
                                        $latestEntry
                                            ->entered_at
                                            ->format('d/m/Y')
                                    }}

                                </div>

                                <div
                                    class="text-[8px]
                                           text-gray-500">

                                    {{
                                        $latestEntry
                                            ->entered_at
                                            ->format('H:i')
                                    }}

                                </div>

                            @else

                                N/A

                            @endif

                        </td>



                        {{-- ================================================= --}}
                        {{-- ACCIONES --}}
                        {{-- ================================================= --}}

                        <td class="px-2 py-2">

                            <div
                                class="flex
                                       justify-center
                                       items-center
                                       gap-1">


                                {{-- HISTORIAL --}}

                                @if($latestEntry->prospect_id)

                                    <a
                                        href="{{
                                            route(
                                                'admin.prospectos.historial',
                                                $latestEntry->prospect_id
                                            )
                                        }}"
                                        title="Ver historial completo del prospecto"
                                        class="bg-[#2C5E43]
                                               hover:bg-[#1E392A]
                                               text-white
                                               w-6 h-6
                                               rounded-md
                                               shadow-sm
                                               transition
                                               inline-flex
                                               items-center
                                               justify-center">

                                        <i
                                            class="fa-solid
                                                   fa-clock-rotate-left
                                                   text-[9px]">
                                        </i>

                                    </a>

                                @endif



                                {{-- REGRESAR AL ORIGEN --}}

                                <form
                                    action="{{
                                        route(
                                            'admin.cartera.regresar-origen',
                                            $latestEntry->id
                                        )
                                    }}"
                                    method="POST"
                                    class="inline-flex">

                                    @csrf

                                    <button
                                        type="submit"
                                        title="{{ $returnTitle }}"
                                        class="bg-amber-600
                                               hover:bg-amber-700
                                               text-white
                                               w-6 h-6
                                               rounded-md
                                               shadow-sm
                                               transition
                                               inline-flex
                                               items-center
                                               justify-center">

                                        <i
                                            class="fa-solid
                                                   fa-arrow-rotate-left
                                                   text-[9px]">
                                        </i>

                                    </button>

                                </form>



                                {{-- ENVIAR A CLIENTES --}}

                                @if($latestEntry->prospect_id)

                                    <form
                                        id="formCliente-{{ $latestEntry->id }}"
                                        method="POST"
                                        action="{{
                                            route(
                                                'admin.cartera.convertir-cliente',
                                                $latestEntry->prospect_id
                                            )
                                        }}"
                                        class="inline-flex">

                                        @csrf


                                        <button
                                            type="button"
                                            onclick='abrirConfirmacionCliente(
                                                "formCliente-{{ $latestEntry->id }}",
                                                @json($nombre)
                                            )'
                                            title="Enviar a Clientes"
                                            class="bg-[#1E392A]
                                                   hover:bg-[#14281D]
                                                   text-white
                                                   w-6 h-6
                                                   rounded-md
                                                   shadow-sm
                                                   inline-flex
                                                   items-center
                                                   justify-center
                                                   transition">

                                            <i
                                                class="fa-solid
                                                       fa-user-check
                                                       text-[9px]">
                                            </i>

                                        </button>

                                    </form>

                                @endif


                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="10"
                            class="px-4 py-8
                                   text-center
                                   text-gray-500">

                            <i
                                class="fa-solid
                                       fa-folder-open
                                       text-2xl
                                       block
                                       mb-2
                                       text-[#556B5D]">
                            </i>

                            No existen prospectos en Cartera
                            con los filtros seleccionados.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>



{{-- ============================================================= --}}
{{-- MODAL PERSONALIZADO: ENVIAR A CLIENTES --}}
{{-- ============================================================= --}}

<div
    id="modalConfirmarCliente"
    class="fixed inset-0
           z-[9999]
           hidden
           items-center
           justify-center
           bg-black/40
           px-4">


    <div
        class="w-full
               max-w-sm
               bg-[#F9F7F2]
               border border-[#D8D3C8]
               rounded-2xl
               shadow-2xl
               overflow-hidden">


        {{-- CABECERA --}}

        <div
            class="bg-[#2C5E43]
                   text-white
                   px-5 py-4
                   text-center">

            <div
                class="mx-auto
                       mb-2
                       w-10 h-10
                       rounded-full
                       bg-white/15
                       flex items-center
                       justify-center">

                <i
                    class="fa-solid
                           fa-user-check
                           text-lg">
                </i>

            </div>


            <h3
                class="font-bold text-base">

                Enviar a Clientes

            </h3>

        </div>



        {{-- MENSAJE --}}

        <div
            class="px-6 py-5
                   text-center">

            <p
                class="text-sm
                       text-[#2C3E35]">

                ¿Desea enviar a

                <strong
                    id="nombreConfirmarCliente"
                    class="text-[#1E392A]">
                </strong>

                al módulo de Clientes para iniciar su trámite?

            </p>


            <p
                class="text-[11px]
                       text-gray-500
                       mt-2">

                El registro será enviado a Clientes
                para revisión y confirmación de sus datos.

            </p>

        </div>



        {{-- BOTONES --}}

        <div
            class="flex
                   justify-center
                   gap-3
                   px-5 pb-5">


            {{-- NO --}}

            <button
                type="button"
                onclick="cerrarConfirmacionCliente()"
                class="px-5 py-2
                       rounded-lg
                       bg-gray-200
                       hover:bg-gray-300
                       text-[#2C3E35]
                       text-xs
                       font-bold
                       transition">

                <i
                    class="fa-solid
                           fa-xmark
                           mr-1">
                </i>

                No

            </button>



            {{-- SÍ --}}

            <button
                type="button"
                onclick="confirmarEnvioCliente()"
                class="px-5 py-2
                       rounded-lg
                       bg-[#2C5E43]
                       hover:bg-[#1E392A]
                       text-white
                       text-xs
                       font-bold
                       shadow-sm
                       transition">

                <i
                    class="fa-solid
                           fa-check
                           mr-1">
                </i>

                Sí, enviar

            </button>

        </div>

    </div>

</div>



{{-- ============================================================= --}}
{{-- JAVASCRIPT DEL MODAL --}}
{{-- ============================================================= --}}

<script>

    let formularioClientePendiente = null;


    function abrirConfirmacionCliente(
        formId,
        nombre
    ) {

        formularioClientePendiente =
            document.getElementById(formId);


        if (!formularioClientePendiente) {
            return;
        }


        document.getElementById(
            'nombreConfirmarCliente'
        ).textContent =
            nombre || 'este prospecto';


        const modal =
            document.getElementById(
                'modalConfirmarCliente'
            );


        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }



    function cerrarConfirmacionCliente() {

        const modal =
            document.getElementById(
                'modalConfirmarCliente'
            );


        modal.classList.add('hidden');
        modal.classList.remove('flex');


        formularioClientePendiente = null;
    }



    function confirmarEnvioCliente() {

        if (!formularioClientePendiente) {
            return;
        }


        const formulario =
            formularioClientePendiente;


        formularioClientePendiente = null;


        const modal =
            document.getElementById(
                'modalConfirmarCliente'
            );


        modal.classList.add('hidden');
        modal.classList.remove('flex');


        formulario.submit();
    }



    /*
    |--------------------------------------------------------------------------
    | CERRAR MODAL AL HACER CLIC FUERA
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('modalConfirmarCliente')
        .addEventListener(
            'click',
            function (event) {

                if (event.target === this) {
                    cerrarConfirmacionCliente();
                }

            }
        );

</script>

@endsection