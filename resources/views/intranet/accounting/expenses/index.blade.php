@extends('layouts.admin')

@section('admin_content')

@php
    /*
    |--------------------------------------------------------------------------
    | Nombres de meses
    |--------------------------------------------------------------------------
    */

    $monthNames = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];
@endphp

<div
    class="min-h-screen bg-slate-50 py-8"
    x-data="{
        openGroup: null,
        openCategory: null,
        openSubcategory: null,

        modalGroup: false,
        modalCategory: false,
        modalSubcategory: false
    }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>

                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                            Gastos
                        </h1>

                        <p class="text-sm text-slate-500 mt-1">
                            Registro y organización de los gastos reales de la inmobiliaria.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">

                <a
                    href="{{ route('accounting.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Contabilidad
                </a>
<button
    type="button"
    @click="modalGroup = true"
    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-bold hover:bg-emerald-100 transition"
>
    <i class="fa-solid fa-layer-group"></i>
    Grupo
</button>

<button
    type="button"
    @click="modalCategory = true"
    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 text-sm font-bold hover:bg-blue-100 transition"
>
    <i class="fa-solid fa-folder-plus"></i>
    Categoría
</button>

<button
    type="button"
    @click="modalSubcategory = true"
    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-violet-200 bg-violet-50 text-violet-700 text-sm font-bold hover:bg-violet-100 transition"
>
    <i class="fa-solid fa-list"></i>
    Subcategoría
</button>
                <a
                    href="{{ route('accounting.movements.create') }}" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-sm hover:bg-emerald-700 transition"
                >
                    <i class="fa-solid fa-plus"></i>
                    Registrar gasto
                </a>

            </div>
        </div>


        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-check mt-1"></i>
                    <div class="font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div class="font-medium">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif


       {{-- FILTROS DEL PERÍODO --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">

    <div class="flex flex-col xl:flex-row xl:items-end gap-6">

        {{-- CONSULTA MENSUAL --}}
        <form
            method="GET"
            action="{{ route('accounting.expenses') }}"
            class="flex-1"
        >
            <div class="mb-3">
                <h3 class="text-sm font-extrabold text-slate-800">
                    Consulta mensual
                </h3>

                <p class="text-xs text-slate-400 mt-1">
                    Selecciona el mes contable que deseas consultar.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                        Mes
                    </label>

                    <select
                        name="month"
                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        @foreach($monthNames as $number => $name)
                            <option
                                value="{{ $number }}"
                                {{ (int) $month === $number ? 'selected' : '' }}
                            >
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                        Año
                    </label>

                    <select
                        name="year"
                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                            <option
                                value="{{ $y }}"
                                {{ (int) $year === $y ? 'selected' : '' }}
                            >
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition"
                    >
                        <i class="fa-solid fa-calendar-days"></i>
                        Consultar
                    </button>
                </div>

            </div>
        </form>


        {{-- DIVISOR --}}
        <div class="hidden xl:block w-px h-24 bg-slate-200"></div>


        {{-- RANGO PERSONALIZADO --}}
        <form
            method="GET"
            action="{{ route('accounting.expenses') }}"
            class="flex-1"
        >
            <div class="mb-3">
                <h3 class="text-sm font-extrabold text-slate-800">
                    Rango personalizado
                </h3>

                <p class="text-xs text-slate-400 mt-1">
                    Consulta los gastos registrados entre dos fechas.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                        Desde
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        value="{{ $fromDate ?? '' }}"
                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                        Hasta
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        value="{{ $toDate ?? '' }}"
                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Consultar
                    </button>
                </div>

            </div>
        </form>

    </div>


    {{-- PERÍODO ACTUAL + REPORTE --}}
    <div class="mt-5 pt-5 border-t border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>

            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">
                Período consultado
            </div>

            <div class="mt-1 font-bold text-slate-800">

                @if(($filterType ?? 'month') === 'range')

                    <i class="fa-solid fa-calendar-check text-emerald-600 mr-1"></i>

                    {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}

                    <span class="text-slate-400 mx-1">al</span>

                    {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}

                @else

                    <i class="fa-solid fa-calendar text-emerald-600 mr-1"></i>

                    {{ $monthNames[(int) $month] }} {{ $year }}

                @endif

            </div>

        </div>


        <div class="flex flex-wrap gap-2">

            @if(($filterType ?? 'month') === 'range')

                <a
                    href="{{ route('accounting.expenses') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    Volver al mes actual
                </a>

            @endif


            {{-- ACTIVAREMOS ESTA RUTA EN EL SIGUIENTE PASO --}}
@if(($filterType ?? 'month') === 'range')

    <a
        href="{{ route('accounting.expenses.report', [
            'from_date' => $fromDate,
            'to_date' => $toDate
        ]) }}"
        target="_blank"
        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition"
    >
        <i class="fa-solid fa-file-pdf"></i>
        Generar reporte
    </a>

@else

    <a
        href="{{ route('accounting.expenses.report', [
            'month' => $month,
            'year' => $year
        ]) }}"
        target="_blank"
        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition"
    >
        <i class="fa-solid fa-file-pdf"></i>
        Generar reporte
    </a>

@endif

        </div>

    </div>

</div>


        {{-- RESUMEN --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-7">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                    Total gastos
                </div>

                <div class="mt-2 text-2xl font-extrabold text-slate-900">
                    ${{ number_format((float) $totalExpenses, 2) }}
                </div>

                <div class="mt-1 text-xs text-slate-400">
                    {{ $monthNames[(int) $month] }} {{ $year }}
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                    Pagado
                </div>

                <div class="mt-2 text-2xl font-extrabold text-emerald-700">
                    ${{ number_format((float) $paidExpenses, 2) }}
                </div>

                <div class="mt-1 text-xs text-slate-400">
                    Gastos ya cancelados
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                    Pendiente
                </div>

                <div class="mt-2 text-2xl font-extrabold text-amber-600">
                    ${{ number_format((float) $pendingExpenses, 2) }}
                </div>

                <div class="mt-1 text-xs text-slate-400">
                    Valores pendientes de pago
                </div>
            </div>

        </div>


        {{-- GRUPOS DE GASTOS --}}
        <div class="space-y-4">

            @foreach($groups as $group)

                @php
                    $groupCategories = $categories
                        ->where('expense_group_id', $group->id);

                    $groupCategoryIds = $groupCategories->pluck('id');

                    $groupMovements = $movements
                        ->whereIn('expense_category_id', $groupCategoryIds);

                    $groupTotal = $groupMovements->sum('amount');

                    $groupKey = 'group-' . $group->id;

                    $groupIcon = match($group->code) {
                        'PUBLICIDAD' => 'fa-bullhorn',
                        'ADMINISTRATIVOS' => 'fa-briefcase',
                        'GENERALES' => 'fa-building',
                        'MOVILIZACION' => 'fa-car',
                        default => 'fa-folder-open',
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    {{-- CABECERA GRUPO --}}
                    <button
                        type="button"
                        @click="openGroup = openGroup === '{{ $groupKey }}' ? null : '{{ $groupKey }}'"
                        class="w-full flex items-center justify-between gap-4 px-5 md:px-6 py-5 hover:bg-slate-50 transition text-left"
                    >

                        <div class="flex items-center gap-4">

                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $groupIcon }}"></i>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <i
                                        class="fa-solid fa-plus text-xs text-slate-400"
                                        x-show="openGroup !== '{{ $groupKey }}'"
                                    ></i>

                                    <i
                                        class="fa-solid fa-minus text-xs text-slate-400"
                                        x-show="openGroup === '{{ $groupKey }}'"
                                        x-cloak
                                    ></i>

                                    <h2 class="font-extrabold text-slate-900 uppercase tracking-wide">
                                        {{ $group->name }}
                                    </h2>
                                </div>

                                <div class="text-xs text-slate-400 mt-1">
                                    {{ $groupMovements->count() }}
                                    {{ $groupMovements->count() === 1 ? 'movimiento' : 'movimientos' }}
                                </div>
                            </div>

                        </div>

                        <div class="text-right">
                            <div class="text-lg md:text-xl font-extrabold text-slate-900">
                                ${{ number_format((float) $groupTotal, 2) }}
                            </div>
                        </div>

                    </button>


                    {{-- CONTENIDO DEL GRUPO --}}
                    <div
                        x-show="openGroup === '{{ $groupKey }}'"
                        x-cloak
                        class="border-t border-slate-100"
                    >

                        @forelse($groupCategories as $category)

                            @php
                                $categoryMovements = $groupMovements
                                    ->where('expense_category_id', $category->id);

                                $categoryTotal = $categoryMovements->sum('amount');

                                $categoryKey = 'cat-' . $category->id;
                            @endphp

                            <div class="border-b border-slate-100 last:border-b-0">

                                {{-- CATEGORÍA --}}
                                <button
                                    type="button"
                                    @click="openCategory = openCategory === '{{ $categoryKey }}' ? null : '{{ $categoryKey }}'"
                                    class="w-full flex items-center justify-between gap-4 px-6 md:px-8 py-4 hover:bg-slate-50 transition text-left"
                                >

                                    <div class="flex items-center gap-3">

                                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                            <i
                                                class="fa-solid fa-plus text-[10px]"
                                                x-show="openCategory !== '{{ $categoryKey }}'"
                                            ></i>

                                            <i
                                                class="fa-solid fa-minus text-[10px]"
                                                x-show="openCategory === '{{ $categoryKey }}'"
                                                x-cloak
                                            ></i>
                                        </span>

                                        <div>
                                            <div class="font-bold text-slate-800">
                                                {{ $category->name }}
                                            </div>

                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $categoryMovements->count() }}
                                                {{ $categoryMovements->count() === 1 ? 'registro' : 'registros' }}
                                            </div>
                                        </div>

                                    </div>

                                    <div class="font-bold text-slate-800">
                                        ${{ number_format((float) $categoryTotal, 2) }}
                                    </div>

                                </button>


                                {{-- SUBCATEGORÍAS --}}
                                <div
                                    x-show="openCategory === '{{ $categoryKey }}'"
                                    x-cloak
                                    class="bg-slate-50 border-t border-slate-100"
                                >

                                    @forelse($category->subcategories as $subcategory)

                                        @php
                                            $subcategoryMovements = $categoryMovements
                                                ->where('expense_subcategory_id', $subcategory->id);

                                            $subcategoryTotal = $subcategoryMovements->sum('amount');

                                            $subcategoryKey = 'sub-' . $subcategory->id;
                                        @endphp

                                        <div class="border-b border-slate-100 last:border-b-0">

                                            <button
                                                type="button"
                                                @click="openSubcategory = openSubcategory === '{{ $subcategoryKey }}' ? null : '{{ $subcategoryKey }}'"
                                                class="w-full flex items-center justify-between gap-4 px-8 md:px-12 py-3.5 hover:bg-white transition text-left"
                                            >

                                                <div class="flex items-center gap-3">

                                                    <span class="text-slate-400">
                                                        <i
                                                            class="fa-solid fa-plus text-[9px]"
                                                            x-show="openSubcategory !== '{{ $subcategoryKey }}'"
                                                        ></i>

                                                        <i
                                                            class="fa-solid fa-minus text-[9px]"
                                                            x-show="openSubcategory === '{{ $subcategoryKey }}'"
                                                            x-cloak
                                                        ></i>
                                                    </span>

                                                    <span class="text-sm font-semibold text-slate-700">
                                                        {{ $subcategory->name }}
                                                    </span>

                                                </div>

                                                <div class="text-sm font-bold text-slate-700">
                                                    ${{ number_format((float) $subcategoryTotal, 2) }}
                                                </div>

                                            </button>


                                            {{-- MOVIMIENTOS --}}
                                            <div
                                                x-show="openSubcategory === '{{ $subcategoryKey }}'"
                                                x-cloak
                                                class="bg-white"
                                            >

                                                @forelse($subcategoryMovements as $movement)

                                                    <div class="px-8 md:px-14 py-4 border-t border-slate-100">

                                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:items-center">

                                                            <div class="lg:col-span-2">
                                                                <div class="text-xs text-slate-400">
                                                                    Fecha
                                                                </div>

                                                                <div class="text-sm font-semibold text-slate-700">
                                                                    {{ \Carbon\Carbon::parse($movement->expense_date)->format('d/m/Y') }}
                                                                </div>
                                                            </div>

                                                            <div class="lg:col-span-4">
                                                                <div class="text-xs text-slate-400">
                                                                    Concepto
                                                                </div>

                                                                <div class="text-sm font-semibold text-slate-800">
                                                                    {{ $movement->concept }}
                                                                </div>

                                                                @if($movement->provider)
                                                                    <div class="text-xs text-slate-500 mt-1">
                                                                        {{ $movement->provider }}
                                                                    </div>
                                                                @endif
                                                            </div>

                                                          <div class="lg:col-span-2">

    <div class="text-xs text-slate-400">
        Documento
    </div>

    <div class="text-sm text-slate-700">

        {{ $movement->document_type ?: '—' }}

        @if($movement->document_number)
            <span class="text-slate-400">
                #{{ $movement->document_number }}
            </span>
        @endif

    </div>


    {{-- COMPROBANTE ADJUNTO --}}
    @if($movement->document_path)

        @php
            $documentUrl = asset('storage/' . $movement->document_path);
        @endphp

        <div class="flex flex-wrap items-center gap-2 mt-2">

            {{-- VER --}}
            <a
                href="{{ $documentUrl }}"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg
                       bg-emerald-50 text-emerald-700
                       hover:bg-emerald-100
                       text-xs font-bold transition"
                title="Ver comprobante"
            >
                <i class="fa-solid fa-eye"></i>
                Ver
            </a>


            {{-- DESCARGAR --}}
            <a
                href="{{ $documentUrl }}"
                download
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg
                       bg-slate-100 text-slate-700
                       hover:bg-slate-200
                       text-xs font-bold transition"
                title="Descargar comprobante"
            >
                <i class="fa-solid fa-download"></i>
                Descargar
            </a>

        </div>

    @else

        <div class="mt-1 text-[11px] text-slate-400">
            Sin comprobante adjunto
        </div>

    @endif

</div>

                                                            <div class="lg:col-span-2">
                                                                @if($movement->payment_status === 'Pagado')
                                                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                                                                        Pagado
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                                                                        Pendiente
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <div class="lg:col-span-2 lg:text-right">
                                                                <div class="text-base font-extrabold text-slate-900">
                                                                    ${{ number_format((float) $movement->amount, 2) }}
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>

                                                @empty

                                                    <div class="px-12 py-4 text-sm text-slate-400">
                                                        No existen gastos registrados en esta subcategoría durante el período.
                                                    </div>

                                                @endforelse

                                            </div>

                                        </div>

                                    @empty

                                        {{-- Categorías que todavía no tienen subcategorías --}}
                                        @if($categoryMovements->isNotEmpty())

                                            @foreach($categoryMovements as $movement)

                                                <div class="px-8 md:px-12 py-4 border-b border-slate-100 last:border-b-0 bg-white">

                                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                                                        <div>
                                                            <div class="text-sm font-semibold text-slate-800">
                                                                {{ $movement->concept }}
                                                            </div>

                                                            <div class="text-xs text-slate-400 mt-1">
                                                                {{ \Carbon\Carbon::parse($movement->expense_date)->format('d/m/Y') }}

                                                                @if($movement->provider)
                                                                    · {{ $movement->provider }}
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="font-extrabold text-slate-900">
                                                            ${{ number_format((float) $movement->amount, 2) }}
                                                        </div>

                                                    </div>

                                                </div>

                                            @endforeach

                                        @else

                                            <div class="px-8 md:px-12 py-4 text-sm text-slate-400">
                                                Sin movimientos registrados durante el período.
                                            </div>

                                        @endif

                                    @endforelse

                                </div>

                            </div>

                        @empty

                            <div class="px-6 py-5 text-sm text-slate-400">
                                No existen categorías configuradas para este grupo.
                            </div>

                        @endforelse

                    </div>

                </div>

            @endforeach

        </div>


        {{-- TOTAL FINAL --}}
        <div class="mt-6 bg-slate-900 rounded-2xl px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <div class="text-xs uppercase tracking-wider text-slate-400 font-bold">
                    Total gastos del período
                </div>

                <div class="text-sm text-slate-300 mt-1">
                    {{ $monthNames[(int) $month] }} {{ $year }}
                </div>
            </div>

            <div class="text-2xl font-extrabold">
                ${{ number_format((float) $totalExpenses, 2) }}
            </div>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- MODAL: NUEVO GRUPO --}}
    {{-- ============================================================= --}}
    <div
        x-show="modalGroup"
        x-cloak
        @keydown.escape.window="modalGroup = false"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-slate-900/60"
            @click="modalGroup = false"
        ></div>

        <div
            x-show="modalGroup"
            x-transition
            @click.stop
            class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Nuevo grupo de gasto
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Crea un nuevo grupo principal para organizar los gastos.
                    </p>
                </div>

                <button
                    type="button"
                    @click="modalGroup = false"
                    class="w-9 h-9 shrink-0 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('accounting.expense-groups.store') }}"
            >
                @csrf

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Nombre *
                        </label>

                        <input
                            type="text"
                            name="name"
                            required
                            maxlength="120"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Ej. Seguridad"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Código
                        </label>

                        <input
                            type="text"
                            name="code"
                            maxlength="80"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Opcional — se genera automáticamente"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Descripción
                        </label>

                        <textarea
                            name="description"
                            rows="3"
                            maxlength="500"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Descripción del grupo..."
                        ></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="modalGroup = false"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold hover:bg-slate-100 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition"
                    >
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        Crear grupo
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ============================================================= --}}
    {{-- MODAL: NUEVA CATEGORÍA --}}
    {{-- ============================================================= --}}
    <div
        x-show="modalCategory"
        x-cloak
        @keydown.escape.window="modalCategory = false"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-slate-900/60"
            @click="modalCategory = false"
        ></div>

        <div
            x-show="modalCategory"
            x-transition
            @click.stop
            class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Nueva categoría
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        La categoría quedará dentro del grupo seleccionado.
                    </p>
                </div>

                <button
                    type="button"
                    @click="modalCategory = false"
                    class="w-9 h-9 shrink-0 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('accounting.expense-categories.store') }}"
            >
                @csrf

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Grupo de gasto *
                        </label>

                        <select
                            name="expense_group_id"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">Seleccionar grupo</option>

                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                Nombre *
                            </label>

                            <input
                                type="text"
                                name="name"
                                required
                                maxlength="120"
                                class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Ej. Seguridad física"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                Tipo *
                            </label>

                            <select
                                name="expense_type"
                                required
                                class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                <option value="General">General</option>
                                <option value="Directo">Directo</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Código
                        </label>

                        <input
                            type="text"
                            name="code"
                            maxlength="80"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Opcional — se genera automáticamente"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Descripción
                        </label>

                        <textarea
                            name="description"
                            rows="3"
                            maxlength="500"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Descripción de la categoría..."
                        ></textarea>
                    </div>

                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <i class="fa-solid fa-circle-info mr-2"></i>
                        Al crear la categoría también se creará automáticamente la subcategoría
                        <strong>Otros</strong>.
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="modalCategory = false"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold hover:bg-slate-100 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition"
                    >
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        Crear categoría
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ============================================================= --}}
    {{-- MODAL: NUEVA SUBCATEGORÍA --}}
    {{-- ============================================================= --}}
    <div
        x-show="modalSubcategory"
        x-cloak
        @keydown.escape.window="modalSubcategory = false"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-slate-900/60"
            @click="modalSubcategory = false"
        ></div>

        <div
            x-show="modalSubcategory"
            x-transition
            @click.stop
            class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Nueva subcategoría
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Selecciona la categoría donde deseas crearla.
                    </p>
                </div>

                <button
                    type="button"
                    @click="modalSubcategory = false"
                    class="w-9 h-9 shrink-0 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('accounting.expense-subcategories.store') }}"
            >
                @csrf

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Categoría *
                        </label>

                        <select
                            name="expense_category_id"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">Seleccionar categoría</option>

                            @foreach($groups as $group)
                                @php
                                    $modalCategories = $categories
                                        ->where('expense_group_id', $group->id);
                                @endphp

                                @if($modalCategories->isNotEmpty())
                                    <optgroup label="{{ $group->name }}">
                                        @foreach($modalCategories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Nombre *
                        </label>

                        <input
                            type="text"
                            name="name"
                            required
                            maxlength="120"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Ej. Cámaras"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Código
                        </label>

                        <input
                            type="text"
                            name="code"
                            maxlength="80"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Opcional — se genera automáticamente"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Descripción
                        </label>

                        <textarea
                            name="description"
                            rows="3"
                            maxlength="500"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Descripción de la subcategoría..."
                        ></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="modalSubcategory = false"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold hover:bg-slate-100 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-violet-600 text-white font-bold hover:bg-violet-700 transition"
                    >
                        <i class="fa-solid fa-floppy-disk mr-2"></i>
                        Crear subcategoría
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection