@extends('layouts.admin')

@section('admin_content')

@php
    $clientName = 'Sin cliente';

    if ($transaction->client) {
        $clientName = trim(
            ($transaction->client->name ?? '') . ' ' .
            ($transaction->client->last_name ?? '')
        );
    } elseif ($transaction->prospect) {
        $clientName = trim(
            ($transaction->prospect->name ?? '') . ' ' .
            ($transaction->prospect->last_name ?? '')
        );
    }

    $activeCommissions = $transaction->advisorCommissions
        ->where('status', '!=', 'Anulada');

    $commissionTotal = $activeCommissions->sum('commission_amount');

    $directExpenses = $transaction->expenses
        ->where('expense_type', 'Directo')
        ->where('is_active', true);

    $directExpensesTotal = $directExpenses->sum('amount');
@endphp


<div class="min-h-screen bg-slate-100 py-8 print:bg-white print:py-0">

    <div class="max-w-6xl mx-auto px-4 print:max-w-none print:px-0">


        {{-- ========================================================= --}}
        {{-- BOTONES --}}
        {{-- ========================================================= --}}

        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 print:hidden">

            <a
                href="{{ route('accounting.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                       border border-slate-300 bg-white text-slate-700
                       text-sm font-bold hover:bg-slate-50 transition"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Volver a Contabilidad
            </a>


            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                       bg-[#2E624C] text-white text-sm font-bold
                       hover:bg-[#244E3D] transition"
            >
                <i class="fa-solid fa-print"></i>
                Imprimir / Guardar PDF
            </button>

        </div>



        {{-- ========================================================= --}}
        {{-- DOCUMENTO --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden print:shadow-none print:border-0 print:rounded-none">


            {{-- ENCABEZADO --}}

            <div class="px-7 md:px-10 py-7 border-b border-slate-200">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">

                    <div>

                        <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-emerald-700">
                            Inmobiliaria Los Andes del Ecuador
                        </div>

                        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-2">
                            Reporte interno de operación
                        </h1>

                        <p class="text-sm text-slate-500 mt-1">
                            Resumen financiero definitivo de la operación contable.
                        </p>

                    </div>


                    <div class="md:text-right">

                        <div class="text-xs uppercase tracking-wide text-slate-400 font-bold">
                            Operación
                        </div>

                        <div class="text-2xl font-extrabold text-slate-900 mt-1">
                            #{{ $transaction->id }}
                        </div>


                        @if($transaction->status === 'Cerrada')

                            <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5
                                         rounded-full bg-slate-100 text-slate-700
                                         text-xs font-bold">

                                <i class="fa-solid fa-lock"></i>
                                Cerrada

                            </span>

                        @else

                            <span class="inline-flex items-center mt-2 px-3 py-1.5
                                         rounded-full bg-amber-100 text-amber-700
                                         text-xs font-bold">

                                {{ $transaction->status }}

                            </span>

                        @endif

                    </div>

                </div>

            </div>



            <div class="p-7 md:p-10 space-y-8">


                {{-- ================================================= --}}
                {{-- INFORMACIÓN GENERAL --}}
                {{-- ================================================= --}}

                <section>

                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800 mb-4">
                        Información de la operación
                    </h2>


                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">


                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">

                            <div class="text-xs uppercase font-bold text-slate-400">
                                Cliente
                            </div>

                            <div class="font-bold text-slate-800 mt-1">
                                {{ $clientName ?: 'Sin cliente' }}
                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">

                            <div class="text-xs uppercase font-bold text-slate-400">
                                Tipo
                            </div>

                            <div class="font-bold text-slate-800 mt-1">
                                {{ $transaction->operation_type ?: '—' }}
                            </div>

                        </div>


                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">

                            <div class="text-xs uppercase font-bold text-slate-400">
                                Origen
                            </div>

                            <div class="font-bold text-slate-800 mt-1">
                                {{ $transaction->origin_module ?: 'Sin origen' }}
                            </div>

                            @if($transaction->tramite_id)

                                <div class="text-xs text-slate-500 mt-1">
                                    Trámite #{{ $transaction->tramite_id }}
                                </div>

                            @endif

                        </div>


                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">

                            <div class="text-xs uppercase font-bold text-slate-400">
                                Fecha de cierre
                            </div>

                            <div class="font-bold text-slate-800 mt-1">

                                {{ $transaction->closed_at
                                    ? $transaction->closed_at->format('d/m/Y H:i')
                                    : '—'
                                }}

                            </div>

                        </div>

                    </div>


                    @if($transaction->description)

                        <div class="mt-4 rounded-2xl border border-slate-200 p-4">

                            <div class="text-xs uppercase font-bold text-slate-400 mb-1">
                                Descripción
                            </div>

                            <div class="text-sm text-slate-700">
                                {{ $transaction->description }}
                            </div>

                        </div>

                    @endif

                </section>



                {{-- ================================================= --}}
                {{-- RESULTADO FINANCIERO --}}
                {{-- ================================================= --}}

                <section>

                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800 mb-4">
                        Resultado financiero
                    </h2>


                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">


                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">

                            <div class="text-xs uppercase font-bold text-emerald-700">
                                Ingreso bruto
                            </div>

                            <div class="text-2xl font-extrabold text-emerald-800 mt-2">
                                ${{ number_format((float) $transaction->gross_income, 2) }}
                            </div>

                        </div>


                        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

                            <div class="text-xs uppercase font-bold text-red-600">
                                Gastos directos
                            </div>

                            <div class="text-2xl font-extrabold text-red-700 mt-2">
                                -${{ number_format((float) $transaction->direct_expenses_total, 2) }}
                            </div>

                        </div>


                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                            <div class="text-xs uppercase font-bold text-amber-700">
                                Comisiones asesores
                            </div>

                            <div class="text-2xl font-extrabold text-amber-800 mt-2">
                                -${{ number_format((float) $transaction->advisor_commissions_total, 2) }}
                            </div>

                        </div>


                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                            <div class="text-xs uppercase font-bold text-blue-700">
                                Utilidad neta
                            </div>

                            <div class="text-2xl font-extrabold text-blue-800 mt-2">
                                ${{ number_format((float) $transaction->net_profit, 2) }}
                            </div>

                        </div>

                    </div>

                </section>



                {{-- ================================================= --}}
                {{-- INGRESOS --}}
                {{-- ================================================= --}}

                <section class="rounded-2xl border border-slate-200 overflow-hidden">

                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">

                        <h2 class="font-extrabold text-slate-800">
                            Detalle de ingresos
                        </h2>

                    </div>


                    <div class="divide-y divide-slate-100">


                        @if((float) $transaction->published_price > 0)

                            <div class="px-5 py-3 flex justify-between gap-4">

                                <span class="text-sm text-slate-600">
                                    Precio publicado
                                </span>

                                <span class="text-sm font-bold text-slate-800">
                                    ${{ number_format((float) $transaction->published_price, 2) }}
                                </span>

                            </div>

                        @endif


                        @if((float) $transaction->closing_price > 0)

                            <div class="px-5 py-3 flex justify-between gap-4">

                                <span class="text-sm text-slate-600">
                                    Precio de cierre
                                </span>

                                <span class="text-sm font-bold text-slate-800">
                                    ${{ number_format((float) $transaction->closing_price, 2) }}
                                </span>

                            </div>

                        @endif


                        @if((float) $transaction->brokerage_amount > 0)

                            <div class="px-5 py-3 flex justify-between gap-4">

                                <span class="text-sm text-slate-600">

                                    Comisión de corretaje

                                    @if((float) $transaction->brokerage_percentage > 0)

                                        ({{ number_format((float) $transaction->brokerage_percentage, 2) }}%)

                                    @endif

                                </span>

                                <span class="text-sm font-bold text-emerald-700">
                                    ${{ number_format((float) $transaction->brokerage_amount, 2) }}
                                </span>

                            </div>

                        @endif


                        @if((float) $transaction->service_amount > 0)

                            <div class="px-5 py-3 flex justify-between gap-4">

                                <span class="text-sm text-slate-600">
                                    Servicios
                                </span>

                                <span class="text-sm font-bold text-emerald-700">
                                    ${{ number_format((float) $transaction->service_amount, 2) }}
                                </span>

                            </div>

                        @endif


                        <div class="px-5 py-4 bg-emerald-50 flex justify-between gap-4">

                            <span class="font-extrabold text-emerald-800">
                                Ingreso bruto
                            </span>

                            <span class="font-extrabold text-emerald-800">
                                ${{ number_format((float) $transaction->gross_income, 2) }}
                            </span>

                        </div>

                    </div>

                </section>



                {{-- ================================================= --}}
                {{-- GASTOS DIRECTOS --}}
                {{-- ================================================= --}}

                <section class="rounded-2xl border border-slate-200 overflow-hidden">

                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">

                        <div class="flex items-center justify-between gap-4">

                            <h2 class="font-extrabold text-slate-800">
                                Gastos directos de la operación
                            </h2>

                            <span class="font-extrabold text-red-600">
                                ${{ number_format((float) $directExpensesTotal, 2) }}
                            </span>

                        </div>

                    </div>


                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm">

                            <thead class="bg-slate-50">

                                <tr>

                                    <th class="px-5 py-3 text-left">
                                        Concepto
                                    </th>

                                    <th class="px-5 py-3 text-left">
                                        Estado
                                    </th>

                                    <th class="px-5 py-3 text-right">
                                        Monto
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-100">

                                @forelse($directExpenses as $expense)

                                    <tr>

                                        <td class="px-5 py-3">
                                            {{ $expense->expense_name }}
                                        </td>

                                        <td class="px-5 py-3">
                                            {{ $expense->payment_status }}
                                        </td>

                                        <td class="px-5 py-3 text-right font-bold">
                                            ${{ number_format((float) $expense->amount, 2) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="3"
                                            class="px-5 py-5 text-center text-slate-400"
                                        >
                                            No existen gastos directos registrados.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>



                {{-- ================================================= --}}
                {{-- COMISIONES --}}
                {{-- ================================================= --}}

                <section class="rounded-2xl border border-slate-200 overflow-hidden">

                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">

                        <div class="flex items-center justify-between gap-4">

                            <h2 class="font-extrabold text-slate-800">
                                Comisiones de asesores
                            </h2>

                            <span class="font-extrabold text-amber-700">
                                ${{ number_format((float) $commissionTotal, 2) }}
                            </span>

                        </div>

                    </div>


                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm">

                            <thead class="bg-slate-50">

                                <tr>

                                    <th class="px-5 py-3 text-left">
                                        Asesor
                                    </th>

                                    <th class="px-5 py-3 text-left">
                                        Estado
                                    </th>

                                    <th class="px-5 py-3 text-right">
                                        Comisión
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-100">

                                @forelse($activeCommissions as $commission)

                                    <tr>

                                        <td class="px-5 py-3">

                                            @if($commission->advisor)

                                                {{ $commission->advisor->name }}
                                                {{ $commission->advisor->last_name }}

                                            @else

                                                Asesor #{{ $commission->advisor_id }}

                                            @endif

                                        </td>

                                        <td class="px-5 py-3">
                                            {{ $commission->status }}
                                        </td>

                                        <td class="px-5 py-3 text-right font-bold">
                                            ${{ number_format((float) $commission->commission_amount, 2) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="3"
                                            class="px-5 py-5 text-center text-slate-400"
                                        >
                                            No existen comisiones registradas.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>



                {{-- ================================================= --}}
                {{-- GASTOS GENERALES --}}
                {{-- ================================================= --}}

                <section>

                    <div class="rounded-2xl border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div>

                            <div class="font-extrabold text-slate-800">
                                Gastos generales prorrateados
                            </div>

                            <div class="text-xs text-slate-500 mt-1">
                                Valor general asignado a esta operación.
                            </div>

                        </div>

                        <div class="text-xl font-extrabold text-slate-800">
                            ${{ number_format((float) $transaction->general_expenses_prorated, 2) }}
                        </div>

                    </div>

                </section>



                {{-- ================================================= --}}
                {{-- RESULTADO FINAL --}}
                {{-- ================================================= --}}

                <section class="rounded-2xl bg-slate-900 text-white p-6">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                        <div>

                            <div class="text-xs uppercase tracking-wide text-slate-400 font-bold">
                                Ingreso bruto
                            </div>

                            <div class="text-xl font-extrabold mt-1">
                                ${{ number_format((float) $transaction->gross_income, 2) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs uppercase tracking-wide text-slate-400 font-bold">
                                Gastos directos
                            </div>

                            <div class="text-xl font-extrabold mt-1">
                                -${{ number_format((float) $transaction->direct_expenses_total, 2) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs uppercase tracking-wide text-slate-400 font-bold">
                                Comisiones
                            </div>

                            <div class="text-xl font-extrabold mt-1">
                                -${{ number_format((float) $transaction->advisor_commissions_total, 2) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs uppercase tracking-wide text-slate-400 font-bold">
                                Utilidad neta
                            </div>

                            <div class="text-2xl font-extrabold text-emerald-400 mt-1">
                                ${{ number_format((float) $transaction->net_profit, 2) }}
                            </div>

                        </div>

                    </div>

                </section>



                {{-- ================================================= --}}
                {{-- FACTURA --}}
                {{-- ================================================= --}}

                <section>

                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800 mb-4">
                        Facturación
                    </h2>


                    @if($transaction->invoice)

                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                <div>

                                    <div class="font-extrabold text-emerald-800">
                                        Factura emitida
                                    </div>

                                    <div class="text-sm text-emerald-700 mt-1">
                                        Documento asociado a esta operación.
                                    </div>

                                </div>


                                <a
                                    href="{{ route('accounting.invoice.document', $transaction->id) }}"
                                    target="_blank"
                                    class="print:hidden inline-flex items-center justify-center gap-2
                                           px-4 py-2.5 rounded-xl bg-emerald-600
                                           text-white text-sm font-bold hover:bg-emerald-700"
                                >
                                    <i class="fa-solid fa-file-invoice"></i>
                                    Ver factura
                                </a>

                            </div>

                        </div>

                    @else

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-500">
                            No existe una factura asociada a esta operación.
                        </div>

                    @endif

                </section>



                {{-- ================================================= --}}
                {{-- OBSERVACIONES --}}
                {{-- ================================================= --}}

                @if($transaction->notes)

                    <section>

                        <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800 mb-3">
                            Observaciones
                        </h2>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-700 whitespace-pre-line">
                            {{ $transaction->notes }}
                        </div>

                    </section>

                @endif


                {{-- PIE --}}

                <div class="pt-6 border-t border-slate-200 text-center text-xs text-slate-400">

                    Reporte interno · Operación #{{ $transaction->id }}

                    @if($transaction->closed_at)
                        · Cerrada el {{ $transaction->closed_at->format('d/m/Y H:i') }}
                    @endif

                </div>


            </div>

        </div>

    </div>

</div>


<style>
    @media print {

        @page {
            size: Letter;
            margin: 10mm;
        }

        body {
            background: white !important;
        }

        nav,
        aside,
        header {
            display: none !important;
        }

        .print\:hidden {
            display: none !important;
        }

        .print\:bg-white {
            background: white !important;
        }

        .print\:shadow-none {
            box-shadow: none !important;
        }

        .print\:border-0 {
            border: 0 !important;
        }

        .print\:rounded-none {
            border-radius: 0 !important;
        }

        section {
            break-inside: avoid;
        }
    }
</style>

@endsection