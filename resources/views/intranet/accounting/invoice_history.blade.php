@extends('layouts.admin')

@section('admin_content')

<div class="min-h-screen bg-slate-50 py-8">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <p class="text-sm font-bold text-emerald-600">
                    Contabilidad · Facturación
                </p>

                <h1 class="text-3xl font-black text-slate-900 mt-1">
                    Historial de documentos
                </h1>

                <p class="text-slate-500 mt-2">
                    Consulta las facturas y comprobantes internos emitidos.
                </p>
            </div>

            <a href="{{ route('accounting.index') }}"
               class="inline-flex items-center justify-center gap-2
                      px-5 py-3 rounded-xl border border-slate-300
                      bg-white text-slate-700 font-bold
                      hover:bg-slate-50 transition">

                <i class="fa-solid fa-arrow-left"></i>
                Volver a Contabilidad
            </a>

        </div>


        {{-- TARJETAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-7">

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase font-black text-slate-400">
                            Documentos emitidos
                        </p>

                        <p class="text-3xl font-black text-slate-900 mt-2">
                            {{ $totalDocuments }}
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-slate-100
                                text-slate-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-lines text-xl"></i>
                    </div>
                </div>

            </div>


            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase font-black text-slate-400">
                            Facturas
                        </p>

                        <p class="text-3xl font-black text-slate-900 mt-2">
                            {{ $totalInvoices }}
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-emerald-100
                                text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-xl"></i>
                    </div>
                </div>

            </div>


            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase font-black text-slate-400">
                            Comprobantes internos
                        </p>

                        <p class="text-3xl font-black text-slate-900 mt-2">
                            {{ $totalReceipts }}
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-amber-100
                                text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-receipt text-xl"></i>
                    </div>
                </div>

            </div>


            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase font-black text-slate-400">
                            Total documentado
                        </p>

                        <p class="text-3xl font-black text-emerald-600 mt-2">
                            ${{ number_format((float) $totalBilled, 2) }}
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-emerald-100
                                text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-dollar-sign text-xl"></i>
                    </div>
                </div>

            </div>

        </div>


        {{-- FILTROS --}}
        <div class="bg-white rounded-3xl border border-slate-200
                    shadow-sm p-5 mb-6">

            <form method="GET"
                  action="{{ route('accounting.invoice.history') }}">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                    {{-- BUSCADOR --}}
                    <div class="md:col-span-5">

                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Buscar
                        </label>

                        <div class="relative">

                            <i class="fa-solid fa-magnifying-glass
                                      absolute left-4 top-1/2 -translate-y-1/2
                                      text-slate-400"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Número, cliente, identificación..."
                                class="w-full rounded-xl border-slate-300
                                       pl-11 focus:border-emerald-500
                                       focus:ring-emerald-500">

                        </div>

                    </div>


                    {{-- TIPO DOCUMENTO --}}
                    <div class="md:col-span-3">

                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Tipo de documento
                        </label>

                        <select
                            name="document_type"
                            class="w-full rounded-xl border-slate-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500">

                            <option value="">
                                Todos
                            </option>

                            <option value="factura"
                                @selected($documentType === 'factura')>
                                Facturas
                            </option>

                            <option value="comprobante"
                                @selected($documentType === 'comprobante')>
                                Comprobantes internos
                            </option>

                        </select>

                    </div>


                    {{-- ESTADO OPERACIÓN --}}
                    <div class="md:col-span-2">

                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Operación
                        </label>

                        <select
                            name="status"
                            class="w-full rounded-xl border-slate-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500">

                            <option value="">
                                Todas
                            </option>

                            <option value="cerrada"
                                @selected($status === 'cerrada')>
                                Cerradas
                            </option>

                            <option value="abierta"
                                @selected($status === 'abierta')>
                                Sin cerrar
                            </option>

                        </select>

                    </div>


                    {{-- BOTÓN --}}
                    <div class="md:col-span-2">

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center
                                   gap-2 px-5 py-3 rounded-xl
                                   bg-emerald-600 hover:bg-emerald-700
                                   text-white font-black transition">

                            <i class="fa-solid fa-filter"></i>
                            Filtrar

                        </button>

                    </div>

                </div>


                @if($search || $documentType || $status)

                    <div class="mt-4">

                        <a href="{{ route('accounting.invoice.history') }}"
                           class="inline-flex items-center gap-2
                                  text-sm font-bold text-slate-500
                                  hover:text-slate-800">

                            <i class="fa-solid fa-xmark"></i>
                            Limpiar filtros

                        </a>

                    </div>

                @endif

            </form>

        </div>


        {{-- TABLA --}}
        <div class="bg-white rounded-3xl border border-slate-200
                    shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">

                <h2 class="text-lg font-black text-slate-900">
                    Documentos emitidos
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Registro histórico de facturación y comprobantes internos.
                </p>

            </div>


            @if($invoices->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-slate-50">

                            <tr class="text-left">

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400">
                                    Documento
                                </th>

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400">
                                    Cliente
                                </th>

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400">
                                    Operación
                                </th>

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400">
                                    Emisión
                                </th>

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400 text-right">
                                    Total
                                </th>

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400 text-center">
                                    Estado
                                </th>

                                <th class="px-6 py-4 text-xs uppercase
                                           font-black text-slate-400 text-right">
                                    Acción
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach($invoices as $invoice)

                                @php
                                    $transaction = $invoice->transaction;
                                    $isClosed = $transaction?->status === 'Cerrada';
                                @endphp

                                <tr class="hover:bg-slate-50/70 transition">

                                    {{-- DOCUMENTO --}}
                                    <td class="px-6 py-5">

                                        <div class="flex items-center gap-3">

                                            <div class="w-10 h-10 rounded-xl
                                                {{ $invoice->document_type === 'factura'
                                                    ? 'bg-emerald-100 text-emerald-600'
                                                    : 'bg-amber-100 text-amber-600' }}
                                                flex items-center justify-center">

                                                <i class="fa-solid
                                                    {{ $invoice->document_type === 'factura'
                                                        ? 'fa-file-invoice'
                                                        : 'fa-receipt' }}">
                                                </i>

                                            </div>

                                            <div>

                                                <p class="font-black text-slate-900">
                                                    {{ $invoice->invoice_number }}
                                                </p>

                                                <p class="text-xs text-slate-500 mt-1">
                                                    {{ $invoice->document_type === 'factura'
                                                        ? 'Factura'
                                                        : 'Comprobante interno' }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- CLIENTE --}}
                                    <td class="px-6 py-5">

                                        <p class="font-bold text-slate-800">
                                            {{ $invoice->customer_name }}
                                        </p>

                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ strtoupper(str_replace(
                                                '_',
                                                ' ',
                                                $invoice->identification_type
                                            )) }}

                                            @if($invoice->identification_number)
                                                · {{ $invoice->identification_number }}
                                            @endif
                                        </p>

                                    </td>


                                    {{-- OPERACIÓN --}}
                                    <td class="px-6 py-5">

                                        <p class="font-bold text-slate-800">
                                            {{ $transaction?->operation_type ?? 'Sin información' }}
                                        </p>

                                        @if($transaction?->property)

                                            <p class="text-xs text-slate-500 mt-1">
                                                {{ $transaction->property->title }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- FECHA --}}
                                    <td class="px-6 py-5">

                                        <p class="font-bold text-slate-700">
                                            {{ $invoice->issued_at
                                                ? $invoice->issued_at->format('d/m/Y')
                                                : '-' }}
                                        </p>

                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $invoice->issued_at
                                                ? $invoice->issued_at->format('H:i')
                                                : '' }}
                                        </p>

                                    </td>


                                    {{-- TOTAL --}}
                                    <td class="px-6 py-5 text-right">

                                        <p class="font-black text-slate-900">
                                            ${{ number_format((float) $invoice->total, 2) }}
                                        </p>

                                    </td>


                                    {{-- ESTADO --}}
                                    <td class="px-6 py-5 text-center">

                                        @if($isClosed)

                                            <span class="inline-flex items-center gap-1
                                                         px-3 py-1 rounded-full
                                                         bg-emerald-100
                                                         text-emerald-700
                                                         text-xs font-black">

                                                <i class="fa-solid fa-lock"></i>
                                                Cerrada

                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1
                                                         px-3 py-1 rounded-full
                                                         bg-amber-100
                                                         text-amber-700
                                                         text-xs font-black">

                                                <i class="fa-solid fa-clock"></i>
                                                Sin cerrar

                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACCIÓN --}}
                                    <td class="px-6 py-5 text-right">

                                        @if($transaction)

                                            <a
                                                href="{{ route(
                                                    'accounting.invoice.document',
                                                    $transaction->id
                                                ) }}"
                                                target="_blank"
                                                class="inline-flex items-center
                                                       justify-center gap-2
                                                       px-4 py-2 rounded-xl
                                                       bg-slate-900
                                                       hover:bg-slate-800
                                                       text-white text-sm
                                                       font-black transition">

                                                <i class="fa-solid fa-eye"></i>
                                                Ver

                                            </a>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- PAGINACIÓN --}}
                <div class="px-6 py-5 border-t border-slate-100">

                    {{ $invoices->links() }}

                </div>

            @else

                <div class="py-16 px-6 text-center">

                    <div class="w-16 h-16 mx-auto rounded-full
                                bg-slate-100 text-slate-400
                                flex items-center justify-center text-2xl">

                        <i class="fa-solid fa-file-circle-xmark"></i>

                    </div>

                    <h3 class="text-lg font-black text-slate-800 mt-4">
                        No se encontraron documentos
                    </h3>

                    <p class="text-slate-500 mt-2">
                        No existen facturas o comprobantes internos
                        que coincidan con los filtros seleccionados.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection