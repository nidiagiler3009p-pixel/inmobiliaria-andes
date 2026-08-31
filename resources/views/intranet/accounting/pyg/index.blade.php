@extends('layouts.admin')

@section('admin_content')

<div class="max-w-7xl mx-auto px-4 py-8"
     x-data="{ ingresosOpen: true, gastosOpen: true, negociosOpen: false }">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700 mb-1">
                Contabilidad · PyG
            </p>

            <h1 class="text-3xl font-extrabold text-[#1F3D32]">
                Pérdidas y Ganancias
            </h1>

            <p class="text-sm text-gray-600 mt-1">
                Resumen financiero por período.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">

            <a href="{{ route('accounting.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      border border-gray-200 bg-white text-sm font-semibold text-gray-700
                      hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>

            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                           bg-emerald-700 text-white text-sm font-semibold
                           hover:bg-emerald-800 transition">
                <i class="fa-solid fa-file-pdf"></i>
                Reporte
            </button>

        </div>
    </div>


    {{-- FILTROS --}}
    <form method="GET"
          action="{{ route('accounting.pyg') }}"
          class="bg-white border border-emerald-100 rounded-2xl shadow-sm p-5 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Mes
                </label>

                <select name="month"
                        class="w-full rounded-xl border-gray-200 text-sm">

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

                    @foreach($months as $number => $monthName)
                        <option value="{{ $number }}"
                                {{ (int)$month === $number ? 'selected' : '' }}>
                            {{ $monthName }}
                        </option>
                    @endforeach

                </select>
            </div>


            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Año
                </label>

                <select name="year"
                        class="w-full rounded-xl border-gray-200 text-sm">

                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}"
                                {{ (int)$year === $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor

                </select>
            </div>


            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Desde
                </label>

                <input type="date"
                       name="from_date"
                       value="{{ $fromDate }}"
                       class="w-full rounded-xl border-gray-200 text-sm">
            </div>


            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Hasta
                </label>

                <input type="date"
                       name="to_date"
                       value="{{ $toDate }}"
                       class="w-full rounded-xl border-gray-200 text-sm">
            </div>


            <div class="md:col-span-2 flex items-end gap-2">

                <button type="submit"
                        class="flex-1 px-4 py-2 rounded-xl bg-[#1F3D32]
                               text-white text-sm font-bold hover:bg-emerald-900 transition">
                    <i class="fa-solid fa-filter mr-2"></i>
                    Aplicar
                </button>

                <a href="{{ route('accounting.pyg') }}"
                   class="px-4 py-2 rounded-xl border border-gray-200
                          bg-white text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Limpiar
                </a>

            </div>

        </div>

    </form>


    {{-- RESULTADO PRINCIPAL --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <div class="md:col-span-2 rounded-3xl p-6 shadow-sm border
                    {{ $netProfit >= 0
                        ? 'bg-emerald-50 border-emerald-200'
                        : 'bg-red-50 border-red-200' }}">

            <p class="text-xs uppercase tracking-[0.18em] font-bold
                      {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                Resultado total / utilidad neta
            </p>

            <div class="flex items-end justify-between mt-3 gap-4">

                <p class="text-4xl font-extrabold
                          {{ $netProfit >= 0 ? 'text-emerald-800' : 'text-red-700' }}">
                    ${{ number_format($netProfit, 2) }}
                </p>

                <p class="text-xl font-extrabold
                          {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ number_format($netProfitPercent, 2) }} %
                </p>

            </div>

            <p class="text-sm mt-3
                      {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $netProfit >= 0 ? 'Ganancia del período' : 'Pérdida del período' }}
            </p>

        </div>


        <div class="bg-white border border-emerald-100 rounded-2xl p-5 shadow-sm">

            <p class="text-xs font-bold uppercase text-gray-500">
                Ingresos brutos
            </p>

            <p class="text-2xl font-extrabold text-[#1F3D32] mt-2">
                ${{ number_format($grossIncome, 2) }}
            </p>

            <p class="text-xs text-gray-500 mt-1">
                100,00 %
            </p>

        </div>


        <div class="bg-white border border-emerald-100 rounded-2xl p-5 shadow-sm">

            <p class="text-xs font-bold uppercase text-gray-500">
                Negocios cerrados
            </p>

            <p class="text-2xl font-extrabold text-[#1F3D32] mt-2">
                {{ $closedBusinessCount }}
            </p>

            <p class="text-xs text-gray-500 mt-1">
                En el período seleccionado
            </p>

        </div>

    </div>


    {{-- RESUMEN IVA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <p class="text-xs font-bold uppercase text-gray-500">
                IVA
            </p>

            <div class="flex items-end justify-between mt-2">
                <p class="text-xl font-extrabold text-amber-700">
                    ${{ number_format($ivaAmount, 2) }}
                </p>

                <p class="text-sm font-bold text-amber-700">
                    {{ number_format($ivaPercent, 2) }} %
                </p>
            </div>

            <p class="text-xs text-gray-500 mt-1">
                IVA {{ $ivaPercentage }} %
            </p>

        </div>


        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <p class="text-xs font-bold uppercase text-gray-500">
                Ingresos netos
            </p>

            <div class="flex items-end justify-between mt-2">

                <p class="text-xl font-extrabold text-blue-700">
                    ${{ number_format($netIncome, 2) }}
                </p>

                <p class="text-sm font-bold text-blue-700">
                    {{ number_format($netIncomePercent, 2) }} %
                </p>

            </div>

        </div>


        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <p class="text-xs font-bold uppercase text-gray-500">
                Total costos y gastos
            </p>

            <div class="flex items-end justify-between mt-2">

                <p class="text-xl font-extrabold text-red-700">
                    ${{ number_format($totalExpenses, 2) }}
                </p>

                <p class="text-sm font-bold text-red-700">
                    {{ number_format($totalExpensesPercent, 2) }} %
                </p>

            </div>

        </div>

    </div>


    {{-- INGRESOS --}}
    <div class="bg-white border border-emerald-100 rounded-2xl shadow-sm overflow-hidden mb-5">

        <button type="button"
                @click="ingresosOpen = !ingresosOpen"
                class="w-full flex items-center justify-between p-5 bg-emerald-50">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700
                            flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>

                <div class="text-left">
                    <p class="font-extrabold text-[#1F3D32]">
                        Ingresos
                    </p>

                    <p class="text-xs text-gray-500">
                        Detalle de ingresos del período
                    </p>
                </div>

            </div>

            <div class="flex items-center gap-5">

                <div class="text-right">
                    <p class="font-extrabold text-[#1F3D32]">
                        ${{ number_format($grossIncome, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">
                        100,00 %
                    </p>
                </div>

                <i class="fa-solid fa-chevron-down text-gray-500 transition-transform"
                   :class="ingresosOpen ? 'rotate-180' : ''"></i>

            </div>

        </button>


        <div x-show="ingresosOpen"
             x-collapse>

            <div class="divide-y divide-gray-100">

                <div class="flex items-center justify-between px-6 py-4">

                    <div>
                        <p class="font-semibold text-gray-800">
                            Corretajes
                        </p>

                        <p class="text-xs text-gray-500">
                            Ingresos por corretaje inmobiliario
                        </p>
                    </div>

                    <div class="text-right">

                        <p class="font-bold text-gray-800">
                            ${{ number_format($brokerageIncome, 2) }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $grossIncome > 0
                                ? number_format(($brokerageIncome / $grossIncome) * 100, 2)
                                : '0.00' }} %
                        </p>

                    </div>

                </div>


                <div class="flex items-center justify-between px-6 py-4">

                    <div>
                        <p class="font-semibold text-gray-800">
                            Servicios / trámites
                        </p>

                        <p class="text-xs text-gray-500">
                            Otros servicios facturados
                        </p>
                    </div>

                    <div class="text-right">

                        <p class="font-bold text-gray-800">
                            ${{ number_format($serviceIncome, 2) }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $grossIncome > 0
                                ? number_format(($serviceIncome / $grossIncome) * 100, 2)
                                : '0.00' }} %
                        </p>

                    </div>

                </div>


                <div class="flex items-center justify-between px-6 py-4 bg-amber-50">

                    <div>
                        <p class="font-bold text-amber-800">
                            (-) IVA {{ $ivaPercentage }} %
                        </p>
                    </div>

                    <div class="text-right">

                        <p class="font-bold text-amber-800">
                            ${{ number_format($ivaAmount, 2) }}
                        </p>

                        <p class="text-xs text-amber-700">
                            {{ number_format($ivaPercent, 2) }} %
                        </p>

                    </div>

                </div>


                <div class="flex items-center justify-between px-6 py-4 bg-blue-50">

                    <p class="font-extrabold text-blue-800">
                        INGRESOS NETOS
                    </p>

                    <div class="text-right">

                        <p class="font-extrabold text-blue-800">
                            ${{ number_format($netIncome, 2) }}
                        </p>

                        <p class="text-xs font-bold text-blue-700">
                            {{ number_format($netIncomePercent, 2) }} %
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- COSTOS Y GASTOS --}}
    <div class="bg-white border border-red-100 rounded-2xl shadow-sm overflow-hidden mb-5">

        <button type="button"
                @click="gastosOpen = !gastosOpen"
                class="w-full flex items-center justify-between p-5 bg-red-50">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700
                            flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                </div>

                <div class="text-left">
                    <p class="font-extrabold text-[#1F3D32]">
                        Costos y gastos
                    </p>

                    <p class="text-xs text-gray-500">
                        Costos operativos y movimientos registrados
                    </p>
                </div>

            </div>

            <div class="flex items-center gap-5">

                <div class="text-right">
                    <p class="font-extrabold text-red-700">
                        ${{ number_format($totalExpenses, 2) }}
                    </p>

                    <p class="text-xs text-red-600">
                        {{ number_format($totalExpensesPercent, 2) }} %
                    </p>
                </div>

                <i class="fa-solid fa-chevron-down text-gray-500 transition-transform"
                   :class="gastosOpen ? 'rotate-180' : ''"></i>

            </div>

        </button>


        <div x-show="gastosOpen"
             x-collapse>

            {{-- GASTOS DIRECTOS --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">

                <div>
                    <p class="font-semibold text-gray-800">
                        Gastos directos de operaciones
                    </p>

                    <p class="text-xs text-gray-500">
                        Gastos asociados directamente a operaciones
                    </p>
                </div>

                <div class="text-right">

                    <p class="font-bold text-gray-800">
                        ${{ number_format($directExpenses, 2) }}
                    </p>

                    <p class="text-xs text-gray-500">
                        {{ number_format($directExpensesPercent, 2) }} %
                    </p>

                </div>

            </div>


            {{-- COMISIONES --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">

                <div>
                    <p class="font-semibold text-gray-800">
                        Comisiones asesores
                    </p>

                    <p class="text-xs text-gray-500">
                        Comisiones generadas por operaciones
                    </p>
                </div>

                <div class="text-right">

                    <p class="font-bold text-gray-800">
                        ${{ number_format($advisorCommissions, 2) }}
                    </p>

                    <p class="text-xs text-gray-500">
                        {{ number_format($advisorCommissionsPercent, 2) }} %
                    </p>

                </div>

            </div>


            {{-- GRUPOS DINÁMICOS --}}
            @foreach($expenseGroupsData as $group)

                <div x-data="{ open: false }"
                     class="border-b border-gray-100">

                    <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between px-6 py-4
                                   hover:bg-gray-50 transition">

                        <div class="flex items-center gap-3">

                            <i class="fa-solid fa-chevron-right text-xs text-gray-400 transition-transform"
                               :class="open ? 'rotate-90' : ''"></i>

                            <div class="text-left">
                                <p class="font-bold text-gray-800">
                                    {{ $group['name'] }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    Grupo de gasto
                                </p>
                            </div>

                        </div>

                        <div class="text-right">

                            <p class="font-bold text-gray-800">
                                ${{ number_format($group['total'], 2) }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ number_format($group['percentage'], 2) }} %
                            </p>

                        </div>

                    </button>


                    <div x-show="open"
                         x-collapse
                         class="bg-gray-50/70">

                        @foreach($group['categories'] as $category)

                            <div x-data="{ categoryOpen: false }"
                                 class="border-t border-gray-100">

                                <button type="button"
                                        @click="categoryOpen = !categoryOpen"
                                        class="w-full flex items-center justify-between
                                               pl-12 pr-6 py-3 hover:bg-gray-100">

                                    <div class="flex items-center gap-2">

                                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 transition-transform"
                                           :class="categoryOpen ? 'rotate-90' : ''"></i>

                                        <p class="font-semibold text-sm text-gray-700">
                                            {{ $category['name'] }}
                                        </p>

                                    </div>

                                    <div class="text-right">

                                        <p class="text-sm font-bold text-gray-700">
                                            ${{ number_format($category['total'], 2) }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ number_format($category['percentage'], 2) }} %
                                        </p>

                                    </div>

                                </button>


                                <div x-show="categoryOpen"
                                     x-collapse>

                                    {{-- MOVIMIENTOS SIN SUBCATEGORÍA --}}
                                    @foreach($category['without_subcategory_movements'] as $movement)

                                        <div class="pl-20 pr-6 py-3 border-t border-gray-100
                                                    flex items-center justify-between bg-white">

                                            <div>

                                                <p class="text-sm text-gray-700">
                                                    {{ $movement->description ?? 'Movimiento' }}
                                                </p>

                                                <p class="text-xs text-gray-500">
                                                    {{ optional($movement->expense_date)->format('d/m/Y') }}
                                                </p>

                                            </div>

                                            <p class="text-sm font-semibold text-gray-700">
                                                ${{ number_format((float)$movement->amount, 2) }}
                                            </p>

                                        </div>

                                    @endforeach


                                    {{-- SUBCATEGORÍAS --}}
                                    @foreach($category['subcategories'] as $subcategory)

                                        @if($subcategory['total'] > 0)

                                            <div x-data="{ subOpen: false }">

                                                <button type="button"
                                                        @click="subOpen = !subOpen"
                                                        class="w-full pl-16 pr-6 py-3 border-t border-gray-100
                                                               flex items-center justify-between bg-white">

                                                    <div class="flex items-center gap-2">

                                                        <i class="fa-solid fa-chevron-right text-[9px] text-gray-400 transition-transform"
                                                           :class="subOpen ? 'rotate-90' : ''"></i>

                                                        <p class="text-sm font-semibold text-gray-600">
                                                            {{ $subcategory['name'] }}
                                                        </p>

                                                    </div>

                                                    <div class="text-right">

                                                        <p class="text-sm font-semibold text-gray-700">
                                                            ${{ number_format($subcategory['total'], 2) }}
                                                        </p>

                                                        <p class="text-xs text-gray-400">
                                                            {{ number_format($subcategory['percentage'], 2) }} %
                                                        </p>

                                                    </div>

                                                </button>


                                                <div x-show="subOpen"
                                                     x-collapse>

                                                    @foreach($subcategory['movements'] as $movement)

                                                        <div class="pl-24 pr-6 py-3 border-t border-gray-100
                                                                    flex items-center justify-between bg-white">

                                                            <div>

                                                                <p class="text-sm text-gray-600">
                                                                    {{ $movement->description ?? 'Movimiento' }}
                                                                </p>

                                                                <p class="text-xs text-gray-400">
                                                                    {{ optional($movement->expense_date)->format('d/m/Y') }}
                                                                </p>

                                                            </div>

                                                            <p class="text-sm font-semibold text-gray-600">
                                                                ${{ number_format((float)$movement->amount, 2) }}
                                                            </p>

                                                        </div>

                                                    @endforeach

                                                </div>

                                            </div>

                                        @endif

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endforeach


            {{-- TOTAL --}}
            <div class="flex items-center justify-between px-6 py-5 bg-red-50">

                <p class="font-extrabold text-red-800">
                    TOTAL COSTOS Y GASTOS
                </p>

                <div class="text-right">

                    <p class="font-extrabold text-red-800">
                        ${{ number_format($totalExpenses, 2) }}
                    </p>

                    <p class="text-xs font-bold text-red-700">
                        {{ number_format($totalExpensesPercent, 2) }} %
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- NEGOCIOS POR TIPO --}}
    <div class="bg-white border border-blue-100 rounded-2xl shadow-sm overflow-hidden mb-6">

        <button type="button"
                @click="negociosOpen = !negociosOpen"
                class="w-full flex items-center justify-between p-5">

            <div>

                <p class="font-extrabold text-[#1F3D32]">
                    Negocios por tipo
                </p>

                <p class="text-xs text-gray-500">
                    Cantidad, ingreso y participación
                </p>

            </div>

            <i class="fa-solid fa-chevron-down text-gray-500 transition-transform"
               :class="negociosOpen ? 'rotate-180' : ''"></i>

        </button>


        <div x-show="negociosOpen"
             x-collapse>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">

                        <tr>
                            <th class="text-left px-6 py-3">
                                Tipo
                            </th>

                            <th class="text-center px-6 py-3">
                                Cantidad
                            </th>

                            <th class="text-right px-6 py-3">
                                Ingreso
                            </th>

                            <th class="text-right px-6 py-3">
                                %
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($businessTypes as $business)

                            <tr>

                                <td class="px-6 py-3 font-semibold text-gray-700">
                                    {{ $business['type'] }}
                                </td>

                                <td class="px-6 py-3 text-center">
                                    {{ $business['count'] }}
                                </td>

                                <td class="px-6 py-3 text-right font-semibold">
                                    ${{ number_format($business['income'], 2) }}
                                </td>

                                <td class="px-6 py-3 text-right">
                                    {{ number_format($business['percentage'], 2) }} %
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-6 py-8 text-center text-gray-500">
                                    No existen operaciones cerradas en este período.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- RESULTADO FINAL --}}
    <div class="rounded-2xl p-6 flex flex-col md:flex-row md:items-center
                md:justify-between gap-4
                {{ $netProfit >= 0 ? 'bg-[#1F3D32]' : 'bg-red-800' }}">

        <div>

            <p class="text-xs uppercase tracking-[0.2em] font-bold text-white/70">
                Resultado del período
            </p>

            <p class="text-xl font-extrabold text-white mt-1">
                UTILIDAD NETA
            </p>

        </div>

        <div class="text-left md:text-right">

            <p class="text-3xl font-extrabold text-white">
                ${{ number_format($netProfit, 2) }}
            </p>

            <p class="text-sm font-bold text-white/80">
                {{ number_format($netProfitPercent, 2) }} %
            </p>

        </div>

    </div>

</div>


<style>
@media print {

    nav,
    aside,
    button,
    form,
    .no-print {
        display: none !important;
    }

    body {
        background: white !important;
    }

    .shadow-sm,
    .shadow-md {
        box-shadow: none !important;
    }

    [x-cloak] {
        display: none !important;
    }
}
</style>

@endsection