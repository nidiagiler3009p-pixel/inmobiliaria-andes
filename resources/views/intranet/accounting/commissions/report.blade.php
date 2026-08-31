@extends('layouts.admin')

@section('admin_content')

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- ENCABEZADO --}}
    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

        <div>

            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700 mb-1">
                Intranet · Gestión financiera
            </p>

            <h1 class="text-3xl font-extrabold text-[#1F3D32]">
                Reporte de Comisiones
            </h1>

            <p class="text-sm text-gray-600 mt-1">
                Consulta de comisiones generadas, pagadas y pendientes por asesor.
            </p>

        </div>


        <div class="flex flex-wrap gap-2 print:hidden">

            <a
                href="{{ route('accounting.index') }}"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       border border-[#2E624C]
                       bg-white text-[#2E624C]
                       text-sm font-bold
                       hover:bg-emerald-50 transition"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>


            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       bg-[#2E624C] text-white
                       text-sm font-bold
                       hover:bg-[#244E3D] transition"
            >
                <i class="fa-solid fa-print"></i>
                Imprimir / PDF
            </button>

        </div>

    </div>



    {{-- RESUMEN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- REGISTROS --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs text-gray-500">
                        Comisiones
                    </p>

                    <p class="text-2xl font-extrabold text-[#1F3D32] mt-1">
                        {{ $commissionCount }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-slate-100
                            text-slate-700 flex items-center justify-center">

                    <i class="fa-solid fa-list-check"></i>

                </div>

            </div>

        </div>


        {{-- GENERADAS --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs text-gray-500">
                        Total generado
                    </p>

                    <p class="text-xl font-extrabold text-blue-700 mt-1">
                        ${{ number_format((float) $totalGenerated, 2) }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50
                            text-blue-700 flex items-center justify-center">

                    <i class="fa-solid fa-coins"></i>

                </div>

            </div>

        </div>


        {{-- PAGADAS --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs text-gray-500">
                        Pagadas
                    </p>

                    <p class="text-xl font-extrabold text-emerald-700 mt-1">
                        ${{ number_format((float) $totalPaid, 2) }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50
                            text-emerald-700 flex items-center justify-center">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

            </div>

        </div>


        {{-- PENDIENTES --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs text-gray-500">
                        Pendientes
                    </p>

                    <p class="text-xl font-extrabold text-amber-600 mt-1">
                        ${{ number_format((float) $totalPending, 2) }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-amber-50
                            text-amber-600 flex items-center justify-center">

                    <i class="fa-solid fa-clock"></i>

                </div>

            </div>

        </div>

    </div>



    {{-- FILTROS --}}
    <div class="bg-white border border-emerald-100 rounded-2xl
                shadow-sm p-5 mb-6 print:hidden">

        <form
            method="GET"
            action="{{ route('accounting.commission-report') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end"
        >

            {{-- ASESOR --}}
            <div class="md:col-span-3">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Asesor
                </label>

                <select
                    name="advisor_id"
                    class="w-full rounded-xl border-gray-300
                           text-sm focus:border-emerald-500
                           focus:ring-emerald-500"
                >

                    <option value="">
                        Todos los asesores
                    </option>

                    @foreach($advisors as $advisor)

                        <option
                            value="{{ $advisor->id }}"
                            @selected((string) $advisorId === (string) $advisor->id)
                        >
                            {{ $advisor->name }}
                            {{ $advisor->last_name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ESTADO --}}
            <div class="md:col-span-2">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Estado
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border-gray-300
                           text-sm focus:border-emerald-500
                           focus:ring-emerald-500"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($statuses as $availableStatus)

                        <option
                            value="{{ $availableStatus }}"
                            @selected($status === $availableStatus)
                        >
                            {{ $availableStatus }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- DESDE --}}
            <div class="md:col-span-2">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Desde
                </label>

                <input
                    type="date"
                    name="from_date"
                    value="{{ $fromDate }}"
                    class="w-full rounded-xl border-gray-300
                           text-sm focus:border-emerald-500
                           focus:ring-emerald-500"
                >

            </div>


            {{-- HASTA --}}
            <div class="md:col-span-2">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Hasta
                </label>

                <input
                    type="date"
                    name="to_date"
                    value="{{ $toDate }}"
                    class="w-full rounded-xl border-gray-300
                           text-sm focus:border-emerald-500
                           focus:ring-emerald-500"
                >

            </div>


            {{-- BOTONES --}}
            <div class="md:col-span-3 flex gap-2">

                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           bg-[#2E624C] hover:bg-[#244E3D]
                           text-white text-sm font-bold transition"
                >
                    <i class="fa-solid fa-filter"></i>
                    Filtrar
                </button>


                @if($advisorId || $status || $fromDate || $toDate)

                    <a
                        href="{{ route('accounting.commission-report') }}"
                        title="Limpiar filtros"
                        class="inline-flex items-center justify-center
                               w-11 h-11 rounded-xl
                               border border-gray-300 text-gray-600
                               hover:bg-gray-50 transition"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>

                @endif

            </div>

        </form>

    </div>



    {{-- TABLA --}}
    <div class="bg-white border border-emerald-100
                rounded-2xl shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-2">

            <div>

                <h2 class="font-bold text-[#1F3D32]">
                    Comisiones registradas
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Historial de comisiones generadas por las operaciones contables.
                </p>

            </div>


            <span class="inline-flex self-start sm:self-auto
                         px-3 py-1 rounded-full
                         bg-emerald-50 text-emerald-700
                         text-xs font-semibold">

                {{ $commissions->total() }}
                {{ $commissions->total() === 1 ? 'registro' : 'registros' }}

            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-[#2E624C] text-white">

                    <tr>

                        <th class="px-4 py-3 text-left">
                            Fecha
                        </th>

                        <th class="px-4 py-3 text-left">
                            Asesor
                        </th>

                        <th class="px-4 py-3 text-center">
                            Operación
                        </th>

                        <th class="px-4 py-3 text-left">
                            Cliente
                        </th>

                        <th class="px-4 py-3 text-left">
                            Rol
                        </th>

                        <th class="px-4 py-3 text-right">
                            Base
                        </th>

                        <th class="px-4 py-3 text-right">
                            %
                        </th>

                        <th class="px-4 py-3 text-right">
                            Comisión
                        </th>

                        <th class="px-4 py-3 text-center">
                            Estado
                        </th>

                        <th class="px-4 py-3 text-left">
                            Pago
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y">

                    @forelse($commissions as $commission)

                        @php
                            $operation = $commission->accountingTransaction;

                            $clientName = 'Sin cliente';

                            if ($operation?->client) {

                                $clientName = trim(
                                    ($operation->client->name ?? '') . ' ' .
                                    ($operation->client->last_name ?? '')
                                );

                            } elseif ($operation?->prospect) {

                                $clientName = trim(
                                    ($operation->prospect->name ?? '') . ' ' .
                                    ($operation->prospect->last_name ?? '')
                                );
                            }
                        @endphp


                        <tr class="hover:bg-gray-50 transition">

                            {{-- FECHA --}}
                            <td class="px-4 py-3 whitespace-nowrap">

                                {{ $commission->created_at
                                    ? $commission->created_at->format('d/m/Y')
                                    : '—'
                                }}

                            </td>


                            {{-- ASESOR --}}
                            <td class="px-4 py-3">

                                @if($commission->advisor)

                                    <div class="font-semibold text-gray-800">

                                        {{ $commission->advisor->name }}
                                        {{ $commission->advisor->last_name }}

                                    </div>

                                @else

                                    <span class="text-gray-400">
                                        Usuario #{{ $commission->user_id }}
                                    </span>

                                @endif

                            </td>


                            {{-- OPERACIÓN --}}
                            <td class="px-4 py-3 text-center">

                                @if($operation)

                                    @if($operation->status === 'Cerrada')

                                        <a
                                            href="{{ route('accounting.transaction.report', $operation->id) }}"
                                            target="_blank"
                                            title="Ver reporte de operación"
                                            class="inline-flex items-center gap-1.5
                                                   px-2.5 py-1.5 rounded-lg
                                                   bg-blue-50 text-blue-700
                                                   font-bold hover:bg-blue-100 transition"
                                        >
                                            <i class="fa-solid fa-file-lines"></i>
                                            #{{ $operation->id }}
                                        </a>

                                    @else

                                        <a
                                            href="{{ route('accounting.review', $operation->id) }}"
                                            title="Revisar operación"
                                            class="inline-flex items-center gap-1.5
                                                   px-2.5 py-1.5 rounded-lg
                                                   bg-emerald-50 text-emerald-700
                                                   font-bold hover:bg-emerald-100 transition"
                                        >
                                            #{{ $operation->id }}
                                        </a>

                                    @endif

                                @else

                                    <span class="text-gray-400">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- CLIENTE --}}
                            <td class="px-4 py-3">
                                {{ $clientName ?: 'Sin cliente' }}
                            </td>


                            {{-- ROL --}}
                            <td class="px-4 py-3">
                                {{ $commission->role_in_transaction ?: '—' }}
                            </td>


                            {{-- BASE --}}
                            <td class="px-4 py-3 text-right">
                                ${{ number_format((float) $commission->calculation_base, 2) }}
                            </td>


                            {{-- PORCENTAJE --}}
                            <td class="px-4 py-3 text-right">

                                {{ number_format(
                                    (float) $commission->percentage,
                                    2
                                ) }}%

                            </td>


                            {{-- COMISIÓN --}}
                            <td class="px-4 py-3 text-right font-extrabold text-[#1F3D32]">

                                ${{ number_format(
                                    (float) $commission->commission_amount,
                                    2
                                ) }}

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-4 py-3 text-center">

                                @if($commission->status === 'Pagada')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 bg-emerald-100 text-emerald-800
                                                 text-xs font-semibold">

                                        <i class="fa-solid fa-check"></i>
                                        Pagada

                                    </span>

                                @elseif($commission->status === 'Pendiente')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 bg-amber-100 text-amber-800
                                                 text-xs font-semibold">

                                        <i class="fa-solid fa-clock"></i>
                                        Pendiente

                                    </span>

                                @elseif($commission->status === 'Anulada')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 bg-red-100 text-red-700
                                                 text-xs font-semibold">

                                        <i class="fa-solid fa-ban"></i>
                                        Anulada

                                    </span>

                                @else

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-gray-100 text-gray-700
                                                 text-xs font-semibold">

                                        {{ $commission->status ?: '—' }}

                                    </span>

                                @endif

                            </td>


                            {{-- PAGO --}}
                            <td class="px-4 py-3">

                                @if($commission->paid_at)

                                    <div class="font-medium text-gray-700">
                                        {{ $commission->paid_at->format('d/m/Y') }}
                                    </div>

                                    @if($commission->payment_method)

                                        <div class="text-xs text-gray-500">
                                            {{ $commission->payment_method }}
                                        </div>

                                    @endif

                                    @if($commission->payment_reference)

                                        <div class="text-xs text-gray-400">
                                            Ref: {{ $commission->payment_reference }}
                                        </div>

                                    @endif

                                @else

                                    <span class="text-xs text-gray-400">
                                        Sin pago registrado
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="px-4 py-10 text-center text-gray-500"
                            >

                                <div class="text-3xl text-gray-300 mb-2">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                </div>

                                No se encontraron comisiones para los filtros seleccionados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($commissions->hasPages())

            <div class="p-4 border-t print:hidden">
                {{ $commissions->links() }}
            </div>

        @endif

    </div>

</div>


<style>

    @media print {

        @page {
            size: landscape;
            margin: 8mm;
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

        .shadow-sm {
            box-shadow: none !important;
        }
    }

</style>

@endsection