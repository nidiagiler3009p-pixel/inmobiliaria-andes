@extends('layouts.admin')
@section('admin_content')

<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ENCABEZADO --}}
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('accounting.review', $transaction->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition shadow-sm">←</a>
                        <div>
                            <p class="text-sm font-semibold text-emerald-600">Contabilidad · Facturación</p>
                            <h1 class="text-2xl lg:text-3xl font-black text-slate-900">Datos del cliente</h1>
                        </div>
                    </div>
                    <p class="text-slate-500 mt-2">Verifica y completa los datos que se utilizarán para el comprobante.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-4 py-2 rounded-full text-sm font-bold bg-amber-100 text-amber-700">Borrador</span>
                </div>
            </div>
        </div>

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800"><div class="font-bold">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800"><div class="font-bold">{{ session('error') }}</div></div>
        @endif
        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">
                <p class="font-black text-red-800 mb-2">Revisa la información ingresada:</p>
                <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- PROGRESO --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black">✓</div>
                    <div><p class="text-xs uppercase tracking-wide text-slate-400 font-bold">Paso 1</p><p class="font-bold text-slate-800">Datos económicos</p></div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">2</div>
                    <div><p class="text-xs uppercase tracking-wide text-emerald-600 font-bold">Paso actual</p><p class="font-black text-slate-900">Datos del cliente</p></div>
                </div>
                <div class="flex items-center gap-3 opacity-50">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-black">3</div>
                    <div><p class="text-xs uppercase tracking-wide text-slate-400 font-bold">Paso 3</p><p class="font-bold text-slate-700">Revisión y emisión</p></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- FORMULARIO --}}
            <div class="xl:col-span-2">
                <form method="POST" action="{{ route('accounting.invoice.customer.store', $transaction->id) }}" class="space-y-6">@csrf

                    {{-- IDENTIFICACIÓN --}}
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="mb-6"><h2 class="text-lg font-black text-slate-900">Identificación del cliente</h2><p class="text-sm text-slate-500 mt-1">Información que aparecerá en el comprobante.</p></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tipo de identificación *</label>
                                <select name="identification_type" id="identification_type" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="cedula" @selected(old('identification_type', $invoice->identification_type) === 'cedula')>Cédula</option>
                                    <option value="ruc" @selected(old('identification_type', $invoice->identification_type) === 'ruc')>RUC</option>
                                    <option value="pasaporte" @selected(old('identification_type', $invoice->identification_type) === 'pasaporte')>Pasaporte</option>
                                    <option value="consumidor_final" @selected(old('identification_type', $invoice->identification_type) === 'consumidor_final')>Consumidor final</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Número de identificación</label>
                                <input type="text" name="identification_number" id="identification_number" value="{{ old('identification_number', $invoice->identification_number) }}" placeholder="Ej. 0600000000" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    {{-- DATOS PERSONALES --}}
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="mb-6"><h2 class="text-lg font-black text-slate-900">Información de facturación</h2><p class="text-sm text-slate-500 mt-1">Puedes corregir estos datos antes de generar el comprobante.</p></div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nombres y apellidos *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $invoice->customer_name) }}" required placeholder="Nombre completo del cliente" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Razón social</label>
                                <input type="text" name="business_name" value="{{ old('business_name', $invoice->business_name) }}" placeholder="Opcional" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                <p class="text-xs text-slate-400 mt-2">Completa este campo cuando la facturación corresponda a una empresa.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Dirección</label>
                                <input type="text" name="billing_address" value="{{ old('billing_address', $invoice->billing_address) }}" placeholder="Dirección del cliente" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Teléfono</label>
                                    <input type="text" name="phone" value="{{ old('phone', $invoice->phone) }}" placeholder="Número de teléfono" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Correo electrónico</label>
                                    <input type="email" name="email" value="{{ old('email', $invoice->email) }}" placeholder="correo@ejemplo.com" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COMPROBANTE --}}
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="mb-6"><h2 class="text-lg font-black text-slate-900">Tipo de comprobante</h2><p class="text-sm text-slate-500 mt-1">Selecciona el documento que se preparará para esta operación.</p></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="document_type" value="factura" class="peer sr-only" @checked(old('document_type', $invoice->document_type) === 'factura')>
                                <div class="rounded-2xl border-2 border-slate-200 p-5 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition">
                                    <p class="font-black text-slate-900">Factura</p>
                                    <p class="text-sm text-slate-500 mt-1">Documento de facturación de la operación.</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="document_type" value="comprobante" class="peer sr-only" @checked(old('document_type', $invoice->document_type) === 'comprobante')>
                                <div class="rounded-2xl border-2 border-slate-200 p-5 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition">
                                    <p class="font-black text-slate-900">Comprobante interno</p>
                                    <p class="text-sm text-slate-500 mt-1">Registro interno de cobro de la inmobiliaria.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Observaciones</label>
                        <textarea name="notes" rows="4" placeholder="Observaciones adicionales para esta facturación..." class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>

                    {{-- BOTONES --}}
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
                            <a href="{{ route('accounting.review', $transaction->id) }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition">← Volver a facturación</a>
                            <button type="submit" class="inline-flex items-center justify-center px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-sm transition">Guardar y continuar <span class="ml-2">→</span></button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- RESUMEN LATERAL --}}
            <div class="xl:col-span-1">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 xl:sticky xl:top-6">
                    <p class="text-xs uppercase tracking-wider font-black text-emerald-600 mb-2">Resumen</p>
                    <h2 class="text-xl font-black text-slate-900 mb-6">Operación #{{ $transaction->id }}</h2>
                    <div class="space-y-5">
                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">Cliente</p>
                            <p class="font-bold text-slate-800 mt-1">{{ trim(($transaction->client?->name ?? '') . ' ' . ($transaction->client?->last_name ?? '')) ?: 'Sin cliente registrado' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase font-bold text-slate-400">Operación</p>
                            <p class="font-bold text-slate-800 mt-1">{{ $transaction->operation_type }}</p>
                        </div>
                        @if($transaction->property)
                            <div>
                                <p class="text-xs uppercase font-bold text-slate-400">Propiedad</p>
                                <p class="font-bold text-slate-800 mt-1">{{ $transaction->property->title }}</p>
                            </div>
                        @endif
                        <div class="border-t border-slate-100 pt-5">
                            <p class="text-xs uppercase font-bold text-slate-400">Valor a facturar</p>
                            <p class="text-3xl font-black text-emerald-600 mt-1">${{ number_format($transaction->operation_type === 'Corretaje / Propiedad' ? (float)($transaction->brokerage_amount ?? 0) : (float)($transaction->service_amount ?? 0), 2) }}</p>
                            <p class="text-xs text-slate-400 mt-2">Valor antes de impuestos.</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-sm font-bold text-slate-700">Los gastos internos y las comisiones de asesores no se agregan al valor facturado al cliente.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const identificationType = document.getElementById('identification_type');
    const identificationNumber = document.getElementById('identification_number');
    function updateIdentificationField() {
        if (!identificationType || !identificationNumber) return;
        if (identificationType.value === 'consumidor_final') {
            identificationNumber.value = '9999999999999';
            identificationNumber.readOnly = true;
            identificationNumber.classList.add('bg-slate-100', 'text-slate-500');
        } else {
            if (identificationNumber.value === '9999999999999') identificationNumber.value = '';
            identificationNumber.readOnly = false;
            identificationNumber.classList.remove('bg-slate-100', 'text-slate-500');
        }
    }
    identificationType?.addEventListener('change', updateIdentificationField);
    updateIdentificationField();
});
</script>

@endsection