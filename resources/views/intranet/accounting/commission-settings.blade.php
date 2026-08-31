@extends('layouts.admin')

@section('admin_content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ENCABEZADO --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-emerald-600">
                    Contabilidad
                </p>

                <h1 class="text-3xl font-extrabold text-slate-900">
                    Configuración de Comisiones
                </h1>

                <p class="mt-2 text-sm text-slate-500 max-w-3xl">
                    Define las reglas para captación, venta y distribución de
                    comisiones entre los asesores. Las configuraciones anteriores
                    se conservan como historial.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('accounting.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5
                           rounded-xl border border-slate-200 bg-white
                           text-sm font-bold text-slate-700
                           hover:bg-slate-50 transition"
                >
                    ← Contabilidad
                </a>

                <a
                    href="{{ route('accounting.vehicle-costs') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5
                           rounded-xl border border-emerald-200
                           bg-emerald-50 text-sm font-bold text-emerald-700
                           hover:bg-emerald-100 transition"
                >
                    Costeo Vehículo
                </a>

            </div>
        </div>
    </div>


    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200
                    bg-emerald-50 px-5 py-4 text-sm font-semibold
                    text-emerald-800">
            {{ session('success') }}
        </div>
    @endif


    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200
                    bg-red-50 px-5 py-4">

            <p class="font-bold text-red-800 mb-2">
                Revisa los siguientes datos:
            </p>

            <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- CONFIGURACIÓN ACTIVA --}}
    @if($activeConfiguration)

        <div class="mb-8 rounded-3xl border border-emerald-200
                    bg-white overflow-hidden">

            <div class="px-6 py-5 border-b border-emerald-100
                        bg-emerald-50/60">

                <div class="flex flex-col md:flex-row md:items-center
                            md:justify-between gap-4">

                    <div>
                        <p class="text-xs uppercase tracking-widest
                                  font-bold text-emerald-600">
                            Configuración activa
                        </p>

                        <h2 class="mt-1 text-xl font-extrabold text-slate-900">
                            {{ $activeConfiguration->name }}
                        </h2>
                    </div>

                    <div class="text-sm text-slate-600">
                        Vigente desde

                        <span class="font-bold text-slate-900">
                            {{ $activeConfiguration->effective_from?->format('d/m/Y') }}
                        </span>
                    </div>

                </div>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide
                                  font-bold text-slate-400">
                            Distribución vendedores
                        </p>

                        <p class="mt-1 font-extrabold text-slate-900">
                            {{ $activeConfiguration->default_sales_distribution === 'equal'
                                ? 'Igualitaria'
                                : 'Manual' }}
                        </p>
                    </div>


                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide
                                  font-bold text-slate-400">
                            Distribución manual
                        </p>

                        <p class="mt-1 font-extrabold text-slate-900">
                            {{ $activeConfiguration->allow_manual_distribution
                                ? 'Permitida'
                                : 'No permitida' }}
                        </p>
                    </div>


                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide
                                  font-bold text-slate-400">
                            Reglas activas
                        </p>

                        <p class="mt-1 text-2xl font-extrabold text-slate-900">
                            {{ $activeConfiguration->rules->where('is_active', true)->count() }}
                        </p>
                    </div>

                </div>


                <div class="overflow-x-auto rounded-2xl border border-slate-200">

                    <table class="min-w-full text-sm">

                        <thead class="bg-slate-50">

                            <tr class="text-left text-xs uppercase
                                       tracking-wide text-slate-500">

                                <th class="px-4 py-3">
                                    Participación
                                </th>

                                <th class="px-4 py-3">
                                    Origen
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Comisión
                                </th>

                                <th class="px-4 py-3">
                                    Distribución
                                </th>

                                <th class="px-4 py-3">
                                    Estado
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @foreach($activeConfiguration->rules as $rule)

                                <tr>

                                    <td class="px-4 py-4">

                                        <p class="font-bold text-slate-900">
                                            {{ $rule->name }}
                                        </p>

                                        <p class="text-xs text-slate-400 mt-1">
                                            @switch($rule->participation_type)

                                                @case('capture')
                                                    Captación
                                                    @break

                                                @case('sale')
                                                    Venta
                                                    @break

                                                @case('capture_and_sale')
                                                    Captación + venta
                                                    @break

                                                @case('support')
                                                    Apoyo
                                                    @break

                                                @case('closing')
                                                    Cierre
                                                    @break

                                                @default
                                                    Otro
                                            @endswitch
                                        </p>

                                    </td>


                                    <td class="px-4 py-4 text-slate-600">

                                        @switch($rule->capture_origin)

                                            @case('agency')
                                                Inmobiliaria
                                                @break

                                            @case('advisor')
                                                Asesor
                                                @break

                                            @default
                                                Cualquiera
                                        @endswitch

                                    </td>


                                    <td class="px-4 py-4 text-right">

                                        <span class="text-lg font-extrabold
                                                     text-emerald-700">
                                            {{ number_format((float) $rule->percentage, 2) }}%
                                        </span>

                                    </td>


                                    <td class="px-4 py-4 text-slate-600">

                                        @switch($rule->distribution_type)

                                            @case('individual')
                                                Individual
                                                @break

                                            @case('pool_equal')
                                                Bolsa igualitaria
                                                @break

                                            @case('pool_manual')
                                                Bolsa manual
                                                @break

                                        @endswitch

                                    </td>


                                    <td class="px-4 py-4">

                                        @if($rule->is_active)

                                            <span class="inline-flex px-3 py-1
                                                         rounded-full bg-emerald-100
                                                         text-emerald-700
                                                         text-xs font-bold">
                                                Activa
                                            </span>

                                        @else

                                            <span class="inline-flex px-3 py-1
                                                         rounded-full bg-slate-100
                                                         text-slate-500
                                                         text-xs font-bold">
                                                Inactiva
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif



    {{-- NUEVA CONFIGURACIÓN --}}
    <form
        method="POST"
        action="{{ route('accounting.commission-settings.store') }}"
        id="commission-settings-form"
        class="rounded-3xl border border-slate-200 bg-white overflow-hidden"
    >

        @csrf


        <div class="px-6 py-5 border-b border-slate-100">

            <h2 class="text-xl font-extrabold text-slate-900">
                {{ $activeConfiguration
                    ? 'Actualizar o crear nueva configuración'
                    : 'Crear primera configuración' }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Si utilizas la misma fecha de inicio de la configuración activa,
                se actualizará. Si utilizas una fecha posterior, la anterior
                quedará guardada en el historial.
            </p>

        </div>


        <div class="p-6 space-y-8">

            {{-- DATOS GENERALES --}}
            <div>

                <h3 class="font-extrabold text-slate-900 mb-4">
                    Datos generales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-sm font-bold
                                      text-slate-700 mb-2">
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old(
                                'name',
                                $activeConfiguration?->name
                                    ?? 'Comisiones ' . now()->format('m/Y')
                            ) }}"
                            required
                            class="w-full rounded-xl border-slate-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    <div>
                        <label class="block text-sm font-bold
                                      text-slate-700 mb-2">
                            Vigente desde
                        </label>

                        <input
                            type="date"
                            name="effective_from"
                            value="{{ old(
                                'effective_from',
                                $activeConfiguration?->effective_from?->format('Y-m-d')
                                    ?? now()->format('Y-m-d')
                            ) }}"
                            required
                            class="w-full rounded-xl border-slate-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">

                    <div>

                        <label class="block text-sm font-bold
                                      text-slate-700 mb-2">
                            Distribución predeterminada entre varios vendedores
                        </label>

                        <select
                            name="default_sales_distribution"
                            class="w-full rounded-xl border-slate-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >

                            <option
                                value="equal"
                                @selected(
                                    old(
                                        'default_sales_distribution',
                                        $activeConfiguration?->default_sales_distribution
                                            ?? 'equal'
                                    ) === 'equal'
                                )
                            >
                                Repartir por igual
                            </option>

                            <option
                                value="manual"
                                @selected(
                                    old(
                                        'default_sales_distribution',
                                        $activeConfiguration?->default_sales_distribution
                                            ?? 'equal'
                                    ) === 'manual'
                                )
                            >
                                Distribución manual
                            </option>

                        </select>

                    </div>


                    <div class="flex items-end">

                        <label class="w-full rounded-2xl border
                                      border-slate-200 p-4
                                      flex items-center gap-3 cursor-pointer">

                            <input
                                type="checkbox"
                                name="allow_manual_distribution"
                                value="1"
                                @checked(
                                    old(
                                        'allow_manual_distribution',
                                        $activeConfiguration?->allow_manual_distribution
                                            ?? true
                                    )
                                )
                                class="rounded border-slate-300
                                       text-emerald-600
                                       focus:ring-emerald-500"
                            >

                            <span>
                                <span class="block text-sm font-bold
                                             text-slate-800">
                                    Permitir distribución manual
                                </span>

                                <span class="block text-xs text-slate-500">
                                    Permite decidir qué porcentaje de la bolsa
                                    recibe cada vendedor.
                                </span>
                            </span>

                        </label>

                    </div>

                </div>

            </div>



            {{-- REGLAS --}}
            <div class="border-t border-slate-100 pt-7">

                <div class="flex flex-col md:flex-row md:items-center
                            md:justify-between gap-4 mb-5">

                    <div>

                        <h3 class="font-extrabold text-slate-900">
                            Reglas de participación
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            El porcentaje se calculará posteriormente sobre
                            el corretaje real antes de IVA.
                        </p>

                    </div>


                    <button
                        type="button"
                        id="add-commission-rule"
                        class="inline-flex items-center justify-center
                               px-4 py-2.5 rounded-xl
                               bg-emerald-600 text-white
                               text-sm font-bold
                               hover:bg-emerald-700 transition"
                    >
                        + Agregar participación
                    </button>

                </div>


                <div
                    id="commission-rules-container"
                    class="space-y-4"
                >

                    @php
                        $defaultRules = [
                            [
                                'name' => 'Captación por inmobiliaria',
                                'participation_type' => 'capture',
                                'capture_origin' => 'agency',
                                'percentage' => 0,
                                'distribution_type' => 'individual',
                                'is_active' => true,
                                'notes' => 'La propiedad fue captada directamente por la inmobiliaria.',
                            ],
                            [
                                'name' => 'Captación por asesor',
                                'participation_type' => 'capture',
                                'capture_origin' => 'advisor',
                                'percentage' => 20,
                                'distribution_type' => 'individual',
                                'is_active' => true,
                                'notes' => 'Participación sugerida cuando un asesor capta la propiedad.',
                            ],
                            [
                                'name' => 'Venta por asesor',
                                'participation_type' => 'sale',
                                'capture_origin' => 'any',
                                'percentage' => 20,
                                'distribution_type' => 'pool_equal',
                                'is_active' => true,
                                'notes' => 'Bolsa destinada a los asesores que intervienen en la venta.',
                            ],
                            [
                                'name' => 'Asesor capta y vende',
                                'participation_type' => 'capture_and_sale',
                                'capture_origin' => 'advisor',
                                'percentage' => 40,
                                'distribution_type' => 'individual',
                                'is_active' => true,
                                'notes' => 'Cuando el mismo asesor realiza la captación y la venta.',
                            ],
                        ];

                        $rulesForForm =
                            old('rules')
                            ?? (
                                $activeConfiguration
                                    ? $activeConfiguration->rules
                                        ->map(function ($rule) {
                                            return [
                                                'name' => $rule->name,
                                                'participation_type' =>
                                                    $rule->participation_type,
                                                'capture_origin' =>
                                                    $rule->capture_origin,
                                                'percentage' =>
                                                    $rule->percentage,
                                                'distribution_type' =>
                                                    $rule->distribution_type,
                                                'is_active' =>
                                                    $rule->is_active,
                                                'notes' =>
                                                    $rule->notes,
                                            ];
                                        })
                                        ->values()
                                        ->toArray()
                                    : $defaultRules
                            );
                    @endphp


                    @foreach($rulesForForm as $index => $rule)

                        <div
                            class="commission-rule rounded-2xl
                                   border border-slate-200 p-5"
                        >

                            <div class="flex items-center justify-between
                                        gap-4 mb-4">

                                <p class="font-extrabold text-slate-800">
                                    Regla de comisión
                                </p>

                                <button
                                    type="button"
                                    class="remove-commission-rule
                                           text-sm font-bold text-red-600
                                           hover:text-red-700"
                                >
                                    Eliminar
                                </button>

                            </div>


                            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

                                <div class="lg:col-span-2">

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Nombre
                                    </label>

                                    <input
                                        type="text"
                                        name="rules[{{ $index }}][name]"
                                        value="{{ $rule['name'] }}"
                                        required
                                        class="w-full rounded-xl
                                               border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500"
                                    >

                                </div>


                                <div>

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Participación
                                    </label>

                                    <select
                                        name="rules[{{ $index }}][participation_type]"
                                        class="w-full rounded-xl
                                               border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500"
                                    >

                                        <option value="capture"
                                            @selected($rule['participation_type'] === 'capture')>
                                            Captación
                                        </option>

                                        <option value="sale"
                                            @selected($rule['participation_type'] === 'sale')>
                                            Venta
                                        </option>

                                        <option value="capture_and_sale"
                                            @selected($rule['participation_type'] === 'capture_and_sale')>
                                            Captación + venta
                                        </option>

                                        <option value="support"
                                            @selected($rule['participation_type'] === 'support')>
                                            Apoyo
                                        </option>

                                        <option value="closing"
                                            @selected($rule['participation_type'] === 'closing')>
                                            Cierre
                                        </option>

                                        <option value="other"
                                            @selected($rule['participation_type'] === 'other')>
                                            Otro
                                        </option>

                                    </select>

                                </div>


                                <div>

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Origen captación
                                    </label>

                                    <select
                                        name="rules[{{ $index }}][capture_origin]"
                                        class="w-full rounded-xl
                                               border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500"
                                    >

                                        <option value="agency"
                                            @selected($rule['capture_origin'] === 'agency')>
                                            Inmobiliaria
                                        </option>

                                        <option value="advisor"
                                            @selected($rule['capture_origin'] === 'advisor')>
                                            Asesor
                                        </option>

                                        <option value="any"
                                            @selected($rule['capture_origin'] === 'any')>
                                            Cualquiera
                                        </option>

                                    </select>

                                </div>


                                <div>

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Comisión %
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        name="rules[{{ $index }}][percentage]"
                                        value="{{ $rule['percentage'] }}"
                                        required
                                        class="w-full rounded-xl
                                               border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500"
                                    >

                                </div>

                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">

                                <div>

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Forma de distribución
                                    </label>

                                    <select
                                        name="rules[{{ $index }}][distribution_type]"
                                        class="w-full rounded-xl
                                               border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500"
                                    >

                                        <option value="individual"
                                            @selected($rule['distribution_type'] === 'individual')>
                                            Individual
                                        </option>

                                        <option value="pool_equal"
                                            @selected($rule['distribution_type'] === 'pool_equal')>
                                            Bolsa igualitaria
                                        </option>

                                        <option value="pool_manual"
                                            @selected($rule['distribution_type'] === 'pool_manual')>
                                            Bolsa manual
                                        </option>

                                    </select>

                                </div>


                                <div>

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Estado
                                    </label>

                                    <label class="h-[42px] rounded-xl
                                                  border border-slate-300
                                                  flex items-center px-4 gap-3">

                                        <input
                                            type="checkbox"
                                            name="rules[{{ $index }}][is_active]"
                                            value="1"
                                            @checked($rule['is_active'] ?? true)
                                            class="rounded border-slate-300
                                                   text-emerald-600
                                                   focus:ring-emerald-500"
                                        >

                                        <span class="text-sm font-semibold
                                                     text-slate-700">
                                            Regla activa
                                        </span>

                                    </label>

                                </div>


                                <div>

                                    <label class="block text-xs font-bold
                                                  text-slate-500 mb-1">
                                        Observación
                                    </label>

                                    <input
                                        type="text"
                                        name="rules[{{ $index }}][notes]"
                                        value="{{ $rule['notes'] ?? '' }}"
                                        class="w-full rounded-xl
                                               border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500"
                                    >

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>



            {{-- NOTAS --}}
            <div class="border-t border-slate-100 pt-7">

                <label class="block text-sm font-bold
                              text-slate-700 mb-2">
                    Notas generales
                </label>

                <textarea
                    name="notes"
                    rows="3"
                    class="w-full rounded-xl border-slate-300
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                >{{ old('notes', $activeConfiguration?->notes) }}</textarea>

            </div>


            <div class="flex justify-end border-t border-slate-100 pt-6">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center
                           px-6 py-3 rounded-xl
                           bg-emerald-600 text-white
                           font-extrabold
                           hover:bg-emerald-700 transition"
                >
                    Guardar configuración
                </button>

            </div>

        </div>

    </form>



    {{-- HISTORIAL --}}
    @if($configurations->isNotEmpty())

        <div class="mt-8 rounded-3xl border
                    border-slate-200 bg-white overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">

                <h2 class="text-xl font-extrabold text-slate-900">
                    Historial de configuraciones
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Las configuraciones anteriores no se eliminan para
                    conservar el cálculo histórico de las operaciones.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-slate-50">

                        <tr class="text-left text-xs uppercase
                                   tracking-wide text-slate-500">

                            <th class="px-5 py-3">
                                Configuración
                            </th>

                            <th class="px-5 py-3">
                                Desde
                            </th>

                            <th class="px-5 py-3">
                                Hasta
                            </th>

                            <th class="px-5 py-3 text-center">
                                Reglas
                            </th>

                            <th class="px-5 py-3">
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach($configurations as $configuration)

                            <tr>

                                <td class="px-5 py-4">

                                    <p class="font-bold text-slate-900">
                                        {{ $configuration->name }}
                                    </p>

                                </td>


                                <td class="px-5 py-4 text-slate-600">
                                    {{ $configuration->effective_from?->format('d/m/Y') }}
                                </td>


                                <td class="px-5 py-4 text-slate-600">

                                    {{ $configuration->effective_to
                                        ? $configuration->effective_to->format('d/m/Y')
                                        : 'Vigente' }}

                                </td>


                                <td class="px-5 py-4 text-center font-bold">
                                    {{ $configuration->rules->count() }}
                                </td>


                                <td class="px-5 py-4">

                                    @if($configuration->is_active)

                                        <span class="inline-flex px-3 py-1
                                                     rounded-full
                                                     bg-emerald-100
                                                     text-emerald-700
                                                     text-xs font-bold">
                                            Activa
                                        </span>

                                    @else

                                        <span class="inline-flex px-3 py-1
                                                     rounded-full
                                                     bg-slate-100
                                                     text-slate-500
                                                     text-xs font-bold">
                                            Histórica
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @endif

</div>



{{-- PLANTILLA PARA NUEVAS REGLAS --}}
<template id="commission-rule-template">

    <div class="commission-rule rounded-2xl
                border border-slate-200 p-5">

        <div class="flex items-center justify-between gap-4 mb-4">

            <p class="font-extrabold text-slate-800">
                Nueva regla
            </p>

            <button
                type="button"
                class="remove-commission-rule
                       text-sm font-bold text-red-600"
            >
                Eliminar
            </button>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

            <div class="lg:col-span-2">

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    data-field="name"
                    required
                    class="w-full rounded-xl border-slate-300
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                >

            </div>


            <div>

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Participación
                </label>

                <select
                    data-field="participation_type"
                    class="w-full rounded-xl border-slate-300"
                >

                    <option value="capture">Captación</option>
                    <option value="sale">Venta</option>
                    <option value="capture_and_sale">
                        Captación + venta
                    </option>
                    <option value="support">Apoyo</option>
                    <option value="closing">Cierre</option>
                    <option value="other">Otro</option>

                </select>

            </div>


            <div>

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Origen captación
                </label>

                <select
                    data-field="capture_origin"
                    class="w-full rounded-xl border-slate-300"
                >
                    <option value="agency">Inmobiliaria</option>
                    <option value="advisor">Asesor</option>
                    <option value="any">Cualquiera</option>
                </select>

            </div>


            <div>

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Comisión %
                </label>

                <input
                    type="number"
                    data-field="percentage"
                    step="0.01"
                    min="0"
                    max="100"
                    value="0"
                    required
                    class="w-full rounded-xl border-slate-300"
                >

            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">

            <div>

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Distribución
                </label>

                <select
                    data-field="distribution_type"
                    class="w-full rounded-xl border-slate-300"
                >

                    <option value="individual">
                        Individual
                    </option>

                    <option value="pool_equal">
                        Bolsa igualitaria
                    </option>

                    <option value="pool_manual">
                        Bolsa manual
                    </option>

                </select>

            </div>


            <div>

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Estado
                </label>

                <label class="h-[42px] rounded-xl
                              border border-slate-300
                              flex items-center px-4 gap-3">

                    <input
                        type="checkbox"
                        data-field="is_active"
                        value="1"
                        checked
                        class="rounded border-slate-300
                               text-emerald-600"
                    >

                    <span class="text-sm font-semibold text-slate-700">
                        Activa
                    </span>

                </label>

            </div>


            <div>

                <label class="block text-xs font-bold
                              text-slate-500 mb-1">
                    Observación
                </label>

                <input
                    type="text"
                    data-field="notes"
                    class="w-full rounded-xl border-slate-300"
                >

            </div>

        </div>

    </div>

</template>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const container =
        document.getElementById('commission-rules-container');

    const addButton =
        document.getElementById('add-commission-rule');

    const template =
        document.getElementById('commission-rule-template');


    function renumberRules() {

        const rules =
            container.querySelectorAll('.commission-rule');

        rules.forEach(function (rule, index) {

            rule.querySelectorAll(
                'input[name], select[name], textarea[name]'
            ).forEach(function (field) {

                const currentName = field.getAttribute('name');

                if (!currentName) {
                    return;
                }

                field.setAttribute(
                    'name',
                    currentName.replace(
                        /rules\[\d+\]/,
                        'rules[' + index + ']'
                    )
                );

            });


            rule.querySelectorAll('[data-field]')
                .forEach(function (field) {

                    const fieldName =
                        field.getAttribute('data-field');

                    field.setAttribute(
                        'name',
                        'rules[' + index + '][' + fieldName + ']'
                    );

                });

        });

    }


    addButton.addEventListener('click', function () {

        const clone =
            template.content.cloneNode(true);

        container.appendChild(clone);

        renumberRules();

    });


    container.addEventListener('click', function (event) {

        const removeButton =
            event.target.closest('.remove-commission-rule');

        if (!removeButton) {
            return;
        }

        const rules =
            container.querySelectorAll('.commission-rule');

        if (rules.length <= 1) {
            alert(
                'Debe existir al menos una regla de comisión.'
            );

            return;
        }

        removeButton
            .closest('.commission-rule')
            .remove();

        renumberRules();

    });


    renumberRules();

});
</script>

@endsection