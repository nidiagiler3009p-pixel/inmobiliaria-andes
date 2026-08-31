@extends('layouts.admin')

@section('admin_content')

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-extrabold text-[#1F3D32]">
                Mayor de Gastos
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Consolidación de gastos reales por categoría y subcategoría.
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('accounting.movements.create') }}"
               class="px-4 py-2 rounded-xl bg-[#2E624C] text-white text-sm font-semibold">
                + Registrar gasto
            </a>

            <a href="{{ route('accounting.index') }}"
               class="px-4 py-2 rounded-xl border bg-white text-sm font-semibold">
                Volver a Contabilidad
            </a>

        </div>
    </div>


    {{-- FILTRO --}}
    <div class="bg-white border rounded-2xl shadow-sm p-5 mb-6">

        <form method="GET"
              action="{{ route('accounting.ledger') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Mes
                </label>

                <select name="month"
                        class="w-full border rounded-xl px-4 py-2.5">

                    @php
                        $months = [
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

                    @foreach($months as $number => $name)

                        <option value="{{ $number }}"
                            {{ $month == $number ? 'selected' : '' }}>
                            {{ $name }}
                        </option>

                    @endforeach

                </select>
            </div>


            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Año
                </label>

                <select name="year"
                        class="w-full border rounded-xl px-4 py-2.5">

                    @for($y = now()->year; $y >= now()->year - 5; $y--)

                        <option value="{{ $y }}"
                            {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>

                    @endfor

                </select>
            </div>


            <div>
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-xl bg-[#1F3D32] text-white font-semibold">

                    Consultar período

                </button>
            </div>

        </form>

    </div>


    {{-- RESUMEN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">
                Gastos reales
            </p>

            <p class="text-2xl font-extrabold text-[#1F3D32] mt-2">
                ${{ number_format($totalExpenses, 2) }}
            </p>
        </div>


        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">
                Pagados
            </p>

            <p class="text-2xl font-extrabold text-emerald-700 mt-2">
                ${{ number_format($paidExpenses, 2) }}
            </p>
        </div>


        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">
                Pendientes
            </p>

            <p class="text-2xl font-extrabold text-amber-600 mt-2">
                ${{ number_format($pendingExpenses, 2) }}
            </p>
        </div>


        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold">
                No presupuestados
            </p>

            <p class="text-2xl font-extrabold text-red-600 mt-2">
                ${{ number_format($unbudgetedExpenses, 2) }}
            </p>
        </div>

    </div>


    {{-- CONSOLIDADO POR CATEGORÍA --}}
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden mb-6">

        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-[#1F3D32]">
                Consolidado por categoría
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-[#2E624C] text-white">

                    <tr>
                        <th class="px-4 py-3 text-left">
                            Categoría
                        </th>

                        <th class="px-4 py-3 text-center">
                            Movimientos
                        </th>

                        <th class="px-4 py-3 text-right">
                            Total real
                        </th>
                    </tr>

                </thead>

                <tbody class="divide-y">

                    @forelse($categoryTotals as $item)

                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3 font-semibold">
                                {{ $item['category']?->name ?? 'Sin categoría' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $item['count'] }}
                            </td>

                            <td class="px-4 py-3 text-right font-bold">
                                ${{ number_format($item['total'], 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="3"
                                class="px-4 py-8 text-center text-gray-500">

                                No existen gastos para este período.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

                @if($categoryTotals->isNotEmpty())

                    <tfoot class="bg-gray-100">

                        <tr>
                            <td colspan="2"
                                class="px-4 py-3 font-extrabold">
                                TOTAL
                            </td>

                            <td class="px-4 py-3 text-right font-extrabold text-[#1F3D32]">
                                ${{ number_format($totalExpenses, 2) }}
                            </td>
                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>


    {{-- DETALLE POR SUBCATEGORÍA --}}
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden mb-6">

        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-[#1F3D32]">
                Consolidado por subcategoría
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100">

                    <tr>
                        <th class="px-4 py-3 text-left">
                            Categoría
                        </th>

                        <th class="px-4 py-3 text-left">
                            Subcategoría
                        </th>

                        <th class="px-4 py-3 text-center">
                            Movimientos
                        </th>

                        <th class="px-4 py-3 text-right">
                            Total
                        </th>
                    </tr>

                </thead>

                <tbody class="divide-y">

                    @forelse($subcategoryTotals as $item)

                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3">
                                {{ $item['category']?->name ?? 'Sin categoría' }}
                            </td>

                            <td class="px-4 py-3 font-semibold">
                                {{ $item['subcategory']?->name ?? 'Sin subcategoría' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $item['count'] }}
                            </td>

                            <td class="px-4 py-3 text-right font-bold">
                                ${{ number_format($item['total'], 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4"
                                class="px-4 py-8 text-center text-gray-500">

                                No existen movimientos para este período.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- DETALLE DE MOVIMIENTOS --}}
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-[#1F3D32]">
                Detalle del período
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100">

                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Concepto</th>
                        <th class="px-4 py-3 text-left">Categoría</th>
                        <th class="px-4 py-3 text-left">Subcategoría</th>
                        <th class="px-4 py-3 text-left">Proveedor</th>
                        <th class="px-4 py-3 text-left">Presupuesto</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Documento</th>
                    </tr>

                </thead>

                <tbody class="divide-y">

                    @forelse($movements as $movement)

                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3">
                                {{ $movement->expense_date?->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3 font-medium">
                                {{ $movement->concept }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $movement->category?->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $movement->subcategory?->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $movement->provider ?? '—' }}
                            </td>

                            <td class="px-4 py-3">

                                @if($movement->was_budgeted)

                                    <span class="px-2 py-1 rounded-lg text-xs bg-emerald-100 text-emerald-800">
                                        Presupuestado
                                    </span>

                                @else

                                    <span class="px-2 py-1 rounded-lg text-xs bg-red-100 text-red-700">
                                        No presupuestado
                                    </span>

                                @endif

                            </td>

                            <td class="px-4 py-3 text-right font-bold">
                                ${{ number_format($movement->amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-center">

                                @if($movement->document_path)

                                    <a href="{{ asset('storage/' . $movement->document_path) }}"
                                       target="_blank"
                                       class="text-blue-700 font-semibold hover:underline">

                                        Ver

                                    </a>

                                @else
                                    <span class="text-gray-400">—</span>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="px-4 py-8 text-center text-gray-500">

                                No existen movimientos registrados.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection