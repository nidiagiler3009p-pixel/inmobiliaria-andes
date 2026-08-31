@extends('layouts.admin')

@section('admin_content')

<div class="min-h-screen bg-slate-50 py-8"
     x-data="invoiceReview()"
     x-init="subtotal = Number($el.dataset.subtotal || 0); tax = Number($el.dataset.tax || 0)"
     data-subtotal="{{ (float) $invoice->subtotal }}"
     data-tax="{{ (float) old('tax_percentage', $invoice->tax_percentage ?? 0) }}">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <p class="text-sm font-bold text-emerald-600">
                    Contabilidad · Facturación
                </p>

                <h1 class="text-3xl font-black text-slate-900 mt-1">
                    Revisión final
                </h1>

                <p class="text-slate-500 mt-2">
                    Revisa toda la información antes de emitir el documento.
                </p>
            </div>

            <div>
            @if($invoice->status === 'Emitida')
    <span class="inline-flex items-center gap-2 px-4 py-2
                 rounded-full bg-emerald-100
                 text-emerald-700 font-black">
        <i class="fa-solid fa-circle-check"></i>
        Emitida
    </span>

    <a href="{{ route('accounting.invoice.document', $transaction->id) }}"
       target="_blank"
       class="inline-flex items-center justify-center gap-2
              px-4 py-2 rounded-xl bg-emerald-600
              hover:bg-emerald-700 text-white font-black
              shadow transition">

        <i class="fa-solid fa-file-lines"></i>

        {{ $invoice->document_type === 'factura'
            ? 'Ver factura'
            : 'Ver comprobante interno' }}
    </a>
@if($invoice->status === 'Emitida' && $transaction->status !== 'Cerrada')

    <div x-data="{ closeModal: false }">

        <button
            type="button"
            @click="closeModal = true"
            class="mt-3 w-full inline-flex items-center justify-center gap-2
                   px-5 py-3 rounded-xl bg-slate-900
                   hover:bg-slate-800 text-white font-black
                   shadow transition">

            <i class="fa-solid fa-lock"></i>
            Cerrar operación

        </button>


        {{-- MODAL PERSONALIZADO --}}
        <div
            x-show="closeModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <div
                class="absolute inset-0 bg-slate-900/60"
                @click="closeModal = false">
            </div>


            <div
                x-show="closeModal"
                x-transition
                class="relative w-full max-w-md bg-white
                       rounded-3xl shadow-2xl p-6">

                <div class="w-14 h-14 rounded-full
                            bg-amber-100 text-amber-600
                            flex items-center justify-center
                            text-xl mb-4">

                    <i class="fa-solid fa-lock"></i>

                </div>


                <h3 class="text-xl font-black text-slate-900">
                    ¿Cerrar esta operación?
                </h3>

                <p class="text-slate-500 mt-2">
                    Se guardarán las comisiones definitivas,
                    se recalculará el resultado económico y
                    la operación quedará bloqueada.
                </p>


                <div class="flex gap-3 mt-6">

                    <button
                        type="button"
                        @click="closeModal = false"
                        class="flex-1 px-4 py-3 rounded-xl
                               border border-slate-300
                               text-slate-700 font-bold
                               hover:bg-slate-50">

                        Cancelar

                    </button>


                    <form
                        method="POST"
                        action="{{ route('accounting.transaction.close', $transaction->id) }}"
                        class="flex-1">

                        @csrf

                        <button
                            type="submit"
                            class="w-full px-4 py-3 rounded-xl
                                   bg-slate-900 hover:bg-slate-800
                                   text-white font-black">

                            Sí, cerrar

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endif
@if($transaction->status === 'Cerrada')

    <div class="mt-3 rounded-2xl border border-emerald-200
                bg-emerald-50 p-4 text-center">

        <div class="font-black text-emerald-700">
            <i class="fa-solid fa-lock mr-1"></i>
            Operación cerrada
        </div>

        <div class="text-xs text-emerald-600 mt-1">
            Cerrada el
            {{ $transaction->closed_at
                ? \Carbon\Carbon::parse($transaction->closed_at)->format('d/m/Y H:i')
                : '-' }}
        </div>

    </div>

@endif
@else

    <span class="inline-flex items-center gap-2 px-4 py-2
                 rounded-full bg-amber-100
                 text-amber-700 font-black">
        <i class="fa-solid fa-file-pen"></i>
        Borrador
    </span>

@endif
            </div>

        </div>


        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200
                        bg-emerald-50 px-5 py-4 text-emerald-800 font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200
                        bg-red-50 px-5 py-4 text-red-800 font-bold">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">

                <p class="font-black text-red-800 mb-2">
                    Revisa la información:
                </p>

                <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- PASOS --}}
        <div class="bg-white rounded-3xl border border-slate-200
                    shadow-sm p-5 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-full bg-emerald-600
                                text-white flex items-center justify-center">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <div>
                        <p class="text-xs uppercase font-bold text-slate-400">
                            Paso 1
                        </p>

                        <p class="font-bold text-slate-800">
                            Datos económicos
                        </p>
                    </div>

                </div>


                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-full bg-emerald-600
                                text-white flex items-center justify-center">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <div>
                        <p class="text-xs uppercase font-bold text-slate-400">
                            Paso 2
                        </p>

                        <p class="font-bold text-slate-800">
                            Datos del cliente
                        </p>
                    </div>

                </div>


                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-full bg-emerald-100
                                text-emerald-700
                                flex items-center justify-center font-black">
                        3
                    </div>

                    <div>
                        <p class="text-xs uppercase font-bold text-emerald-600">
                            Paso actual
                        </p>

                        <p class="font-black text-slate-900">
                            Revisión y emisión
                        </p>
                    </div>

                </div>

            </div>

        </div>


        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


            {{-- COLUMNA PRINCIPAL --}}
            <div class="xl:col-span-2 space-y-6">


                {{-- CLIENTE --}}
                <div class="bg-white rounded-3xl border border-slate-200
                            shadow-sm p-6">

                    <div class="flex items-center justify-between gap-4 mb-6">

                        <div>
                            <p class="text-xs uppercase tracking-wider
                                      font-black text-emerald-600">
                                Cliente
                            </p>

                            <h2 class="text-xl font-black text-slate-900 mt-1">
                                Datos de facturación
                            </h2>
                        </div>

                        @if($invoice->status !== 'Emitida')
                            <a href="{{ route('accounting.invoice.customer', $transaction->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2
                                      rounded-xl bg-slate-100
                                      hover:bg-slate-200 text-slate-700
                                      font-bold transition">

                                <i class="fa-solid fa-pen"></i>
                                Editar
                            </a>
                        @endif

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Cliente
                            </p>

                            <p class="font-black text-slate-900 mt-1">
                                {{ $invoice->customer_name }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Identificación
                            </p>

                            <p class="font-bold text-slate-800 mt-1">
                                {{ strtoupper(str_replace('_', ' ', $invoice->identification_type)) }}
                                ·
                                {{ $invoice->identification_number ?: 'Sin número' }}
                            </p>
                        </div>


                        @if($invoice->business_name)
                            <div>
                                <p class="text-xs uppercase font-bold text-slate-400">
                                    Razón social
                                </p>

                                <p class="font-bold text-slate-800 mt-1">
                                    {{ $invoice->business_name }}
                                </p>
                            </div>
                        @endif


                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Dirección
                            </p>

                            <p class="font-bold text-slate-800 mt-1">
                                {{ $invoice->billing_address ?: 'No registrada' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Teléfono
                            </p>

                            <p class="font-bold text-slate-800 mt-1">
                                {{ $invoice->phone ?: 'No registrado' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Correo
                            </p>

                            <p class="font-bold text-slate-800 mt-1 break-all">
                                {{ $invoice->email ?: 'No registrado' }}
                            </p>
                        </div>

                    </div>

                </div>


                {{-- OPERACIÓN --}}
                <div class="bg-white rounded-3xl border border-slate-200
                            shadow-sm p-6">

                    <p class="text-xs uppercase tracking-wider
                              font-black text-emerald-600">
                        Operación
                    </p>

                    <h2 class="text-xl font-black text-slate-900 mt-1 mb-6">
                        Concepto facturado
                    </h2>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Tipo de operación
                            </p>

                            <p class="font-black text-slate-900 mt-1">
                                {{ $transaction->operation_type }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">
                                Documento
                            </p>

                            <p class="font-black text-slate-900 mt-1">
                                {{ $invoice->document_type === 'factura'
                                    ? 'Factura'
                                    : 'Comprobante interno' }}
                            </p>
                        </div>


                        @if($transaction->property)
                            <div class="md:col-span-2">

                                <p class="text-xs uppercase font-bold text-slate-400">
                                    Propiedad relacionada
                                </p>

                                <p class="font-black text-slate-900 mt-1">
                                    {{ $transaction->property->title }}
                                </p>

                            </div>
                        @endif

                    </div>

                </div>


                {{-- OBSERVACIONES --}}
                @if($invoice->notes)
                    <div class="bg-white rounded-3xl border border-slate-200
                                shadow-sm p-6">

                        <p class="text-xs uppercase font-bold text-slate-400">
                            Observaciones
                        </p>

                        <p class="text-slate-700 mt-2 whitespace-pre-line">
                            {{ $invoice->notes }}
                        </p>

                    </div>
                @endif

            </div>


            {{-- RESUMEN ECONÓMICO --}}
            <div class="xl:col-span-1">

                <div class="bg-white rounded-3xl border border-slate-200
                            shadow-sm p-6 xl:sticky xl:top-6">

                    <p class="text-xs uppercase tracking-wider
                              font-black text-emerald-600">
                        Resumen final
                    </p>

                    <h2 class="text-xl font-black text-slate-900 mt-1 mb-6">
                        Valores del documento
                    </h2>


                    @if($invoice->status !== 'Emitida')

                        <form method="POST"
                              action="{{ route('accounting.invoice.issue', $transaction->id) }}"
                              id="issueInvoiceForm">

                            @csrf


                            <div class="space-y-5">

                                <div class="flex items-center justify-between
                                            border-b border-slate-100 pb-4">

                                    <span class="text-slate-500 font-semibold">
                                        Subtotal
                                    </span>

                                    <span class="font-black text-slate-900">
                                        ${{ number_format((float)$invoice->subtotal, 2) }}
                                    </span>

                                </div>


                                <div>

                                    <label class="block text-sm font-bold
                                                  text-slate-700 mb-2">
                                        Impuesto %
                                    </label>

                                    <input
                                        type="number"
                                        name="tax_percentage"
                                        x-model.number="tax"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        required
                                        class="w-full rounded-xl border-slate-300
                                               focus:border-emerald-500
                                               focus:ring-emerald-500">

                                    <p class="text-xs text-slate-400 mt-2">
                                        Ingresa el porcentaje correspondiente
                                        antes de emitir.
                                    </p>

                                </div>


                                <div class="flex items-center justify-between
                                            border-b border-slate-100 pb-4">

                                    <span class="text-slate-500 font-semibold">
                                        Impuesto
                                    </span>

                                    <span class="font-black text-slate-900"
                                          x-text="money(taxAmount)">
                                    </span>

                                </div>


                                <div class="rounded-2xl bg-emerald-50
                                            border border-emerald-100 p-5">

                                    <p class="text-xs uppercase font-black
                                              text-emerald-600">
                                        Total a cobrar
                                    </p>

                                    <p class="text-3xl font-black
                                              text-emerald-700 mt-1"
                                       x-text="money(total)">
                                    </p>

                                </div>


                                <button
                                    type="button"
                                    @click="$dispatch('open-issue-modal')"
                                    class="w-full inline-flex items-center
                                           justify-center gap-2 px-6 py-3
                                           rounded-xl bg-emerald-600
                                           hover:bg-emerald-700 text-white
                                           font-black shadow transition">

                                    <i class="fa-solid fa-file-circle-check"></i>

                                    Emitir comprobante

                                </button>

                            </div>

                        </form>

                    @else

                        <div class="space-y-5">

                            <div class="flex justify-between border-b
                                        border-slate-100 pb-4">

                                <span class="text-slate-500 font-semibold">
                                    Subtotal
                                </span>

                                <span class="font-black">
                                    ${{ number_format((float)$invoice->subtotal, 2) }}
                                </span>

                            </div>


                            <div class="flex justify-between border-b
                                        border-slate-100 pb-4">

                                <span class="text-slate-500 font-semibold">
                                    Impuesto {{ number_format((float)$invoice->tax_percentage, 2) }}%
                                </span>

                                <span class="font-black">
                                    ${{ number_format((float)$invoice->tax_amount, 2) }}
                                </span>

                            </div>


                            <div class="rounded-2xl bg-emerald-50
                                        border border-emerald-100 p-5">

                                <p class="text-xs uppercase font-black
                                          text-emerald-600">
                                    Total
                                </p>

                                <p class="text-3xl font-black
                                          text-emerald-700 mt-1">
                                    ${{ number_format((float)$invoice->total, 2) }}
                                </p>

                            </div>


                            <div class="rounded-2xl bg-slate-50
                                        border border-slate-200 p-4">

                                <p class="text-xs uppercase font-bold text-slate-400">
                                    Número del documento
                                </p>

                                <p class="font-black text-slate-900 mt-1">
                                    {{ $invoice->invoice_number }}
                                </p>

                            </div>


                            <div class="rounded-2xl bg-slate-50
                                        border border-slate-200 p-4">

                                <p class="text-xs uppercase font-bold text-slate-400">
                                    Emitido
                                </p>

                                <p class="font-bold text-slate-800 mt-1">
                                    {{ $invoice->issued_at?->format('d/m/Y H:i') }}
                                </p>

                            </div>

                        </div>

                    @endif


                    <a href="{{ route('accounting.review', $transaction->id) }}"
                       class="mt-4 w-full inline-flex items-center
                              justify-center gap-2 px-5 py-3
                              rounded-xl border border-slate-300
                              text-slate-700 font-bold
                              hover:bg-slate-50 transition">

                        <i class="fa-solid fa-arrow-left"></i>
                        Volver a la operación

                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- MODAL PERSONALIZADO --}}
    @if($invoice->status !== 'Emitida')

        <div
            x-data="{ open: false }"
            @open-issue-modal.window="open = true"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-center
                   justify-center p-4">

            <div class="absolute inset-0 bg-slate-900/60"
                 @click="open = false">
            </div>


            <div x-show="open"
                 x-transition
                 class="relative w-full max-w-md
                        bg-white rounded-3xl shadow-2xl p-6">

                <div class="w-14 h-14 rounded-full bg-amber-100
                            text-amber-600 flex items-center justify-center
                            text-xl mb-4">

                    <i class="fa-solid fa-file-circle-check"></i>

                </div>


                <h3 class="text-xl font-black text-slate-900">
                    ¿Emitir este comprobante?
                </h3>


                <p class="text-slate-500 mt-2">
                    Después de emitirlo, los datos de esta facturación
                    quedarán registrados como información histórica.
                </p>


                <div class="flex gap-3 mt-6">

                    <button
                        type="button"
                        @click="open = false"
                        class="flex-1 px-4 py-3 rounded-xl
                               border border-slate-300 text-slate-700
                               font-bold hover:bg-slate-50">

                        Cancelar

                    </button>


                    <button
                        type="button"
                        @click="document.getElementById('issueInvoiceForm').submit()"
                        class="flex-1 px-4 py-3 rounded-xl
                               bg-emerald-600 hover:bg-emerald-700
                               text-white font-black">

                        Sí, emitir

                    </button>

                </div>

            </div>

        </div>

    @endif

</div>


<script>
function invoiceReview() {
    return {
        subtotal: 0,
        tax: 0,

        get taxAmount() {
            return this.subtotal * (this.tax / 100);
        },

        get total() {
            return this.subtotal + this.taxAmount;
        },

        money(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(Number(value || 0));
        }
    }
}
</script>

@endsection