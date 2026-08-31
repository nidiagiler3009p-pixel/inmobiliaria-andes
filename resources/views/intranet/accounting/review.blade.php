@extends('layouts.admin')
@section('admin_content')

@php
    $closingValue = (float) old('closing_price', $transaction->closing_price ?? 0);
    $percentageValue = old('brokerage_percentage', $transaction->brokerage_percentage);
    $brokerageValue = (float) old('brokerage_amount', $transaction->brokerage_amount ?? 0);
    $initialBrokerageMode = old('brokerage_mode');
    if (!$initialBrokerageMode) {
        $percentageNumber = $percentageValue !== null ? (float) $percentageValue : null;
        $calculatedByPercentage = $percentageNumber !== null ? round($closingValue * ($percentageNumber / 100), 2) : null;
        $initialBrokerageMode = ($calculatedByPercentage !== null && abs($calculatedByPercentage - $brokerageValue) < 0.01) ? 'percentage' : 'fixed';
    }
    $participantLabels = ['capture' => 'Captación', 'sale' => 'Venta', 'support' => 'Apoyo', 'closing' => 'Cierre', 'other' => 'Otro'];
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.index') }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-200 rounded-xl text-[#2E624C] hover:bg-gray-50 shadow-sm"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-[#1F3D32]">Facturación de operación #{{ $transaction->id }}</h1>
                    @if($transaction->status === 'Pendiente')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-xs font-semibold"><i class="fa-solid fa-clock mr-1.5"></i>Pendiente</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">{{ $transaction->status }}</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Revisión interna, valoración y preparación de la facturación.</p>
            </div>
        </div>
    </div>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm"><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm"><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}</div>
    @endif

    {{-- INFORMACIÓN GENERAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-2 mb-3"><div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-user text-sm"></i></div><h2 class="font-bold text-sm text-[#1F3D32]">Cliente</h2></div>
            @if($transaction->client)
                <p class="font-bold text-sm text-gray-800">{{ $transaction->client->name }} {{ $transaction->client->last_name }}</p>
                <div class="mt-2 grid gap-1 text-xs text-gray-600">
                    <p><i class="fa-solid fa-id-card w-4"></i>{{ $transaction->client->identification_card ?? 'Sin cédula' }}</p>
                    <p><i class="fa-solid fa-phone w-4"></i>{{ $transaction->client->phone ?? 'Sin teléfono' }}</p>
                    <p class="truncate"><i class="fa-solid fa-envelope w-4"></i>{{ $transaction->client->email ?? 'Sin correo' }}</p>
                </div>
            @elseif($transaction->prospect)
                <p class="font-bold text-sm text-gray-800">{{ $transaction->prospect->name }} {{ $transaction->prospect->last_name }}</p>
                <p class="text-xs text-amber-700 mt-2">Prospecto sin cliente formal asociado.</p>
            @else
                <p class="text-xs text-gray-500">Sin cliente asociado.</p>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-2 mb-3"><div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-route text-sm"></i></div><h2 class="font-bold text-sm text-[#1F3D32]">Origen</h2></div>
            <div class="grid gap-2 text-xs">
                <div><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Módulo</p><p class="font-semibold text-gray-800">{{ $transaction->origin_module ?? 'Sin origen' }}</p></div>
                <div><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Fuente</p><p class="font-semibold text-gray-800">{{ $transaction->source_type ?? 'Sin fuente' }} @if($transaction->source_id)#{{ $transaction->source_id }}@endif</p></div>
                @if($transaction->tramite)
                    <div><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Trámite</p><p class="font-semibold text-gray-800">#{{ $transaction->tramite->id }} — {{ $transaction->tramite->tramite_type }}</p></div>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-2 mb-3"><div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar text-sm"></i></div><h2 class="font-bold text-sm text-[#1F3D32]">Operación</h2></div>
            <div class="grid gap-2 text-xs">
                <div><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Tipo actual</p><p class="font-bold text-gray-800">{{ $transaction->operation_type }}</p></div>
                <div><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Descripción</p><p class="text-gray-700">{{ $transaction->description ?? 'Sin descripción' }}</p></div>
            </div>
        </div>
    </div>

    {{-- DATOS DE FACTURACIÓN --}}
    @php
        $billingDataSaved = $transaction->operation_type === 'Corretaje / Propiedad'
            ? (!empty($transaction->property_id) && $transaction->closing_price !== null && (float) ($transaction->brokerage_amount ?? 0) > 0)
            : ($transaction->operation_type === 'Trámite / Servicio' && (float) ($transaction->service_amount ?? 0) > 0);
        $billingInitiallyEditing = $errors->any() || !$billingDataSaved;
    @endphp

    <form action="{{ route('accounting.transaction.update', $transaction->id) }}" method="POST" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-5" x-data="{ billingEditing: {{ $billingInitiallyEditing ? 'true' : 'false' }}, tipo: '{{ old('operation_type', $transaction->operation_type) }}' }">
        @csrf @method('PATCH')
        <div class="px-5 py-4 border-b bg-[#F8F7F3]">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center gap-2"><div class="w-8 h-8 rounded-lg bg-[#2E624C] text-white flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar text-sm"></i></div><div><h2 class="font-bold text-[#1F3D32]">Datos de facturación</h2><p class="text-xs text-gray-500 mt-0.5">Define el valor real de la operación antes de continuar con los datos del cliente.</p></div></div>
                <div class="flex items-center gap-2">
                    <span x-show="!billingEditing" x-cloak class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-semibold"><i class="fa-solid fa-lock text-[10px]"></i>Datos guardados</span>
                    <button type="button" x-show="!billingEditing" x-cloak @click="billingEditing = true" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-blue-200 bg-white text-blue-700 hover:bg-blue-50 text-xs font-semibold transition"><i class="fa-solid fa-pen"></i>Editar</button>
                </div>
            </div>
        </div>
        <div class="p-5 space-y-5">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm"><p class="font-bold mb-2">Revisa los siguientes datos:</p><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div><label class="block text-xs font-bold text-[#1F3D32] mb-2">Tipo de operación</label><select name="operation_type" x-model="tipo" :disabled="!billingEditing" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600"><option value="Trámite / Servicio">Trámite / Servicio</option><option value="Corretaje / Propiedad">Corretaje / Propiedad</option></select></div>

            <div x-show="tipo === 'Trámite / Servicio'" x-cloak>
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-4"><i class="fa-solid fa-file-signature text-emerald-700"></i><h3 class="font-bold text-emerald-900">Trámite / Servicio</h3></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Valor real del servicio</label><input type="number" name="service_amount" step="0.01" min="0" value="{{ old('service_amount', $transaction->service_amount) }}" :disabled="!billingEditing" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600" placeholder="Ej. 300.00"></div>
                </div>
            </div>

            <div x-show="tipo === 'Corretaje / Propiedad'" x-cloak x-data="{
                modoCorretaje: '{{ $initialBrokerageMode }}',
                precioCierre: Number('{{ old('closing_price', $transaction->closing_price ?? 0) }}') || 0,
                porcentaje: Number('{{ old('brokerage_percentage', $transaction->brokerage_percentage ?? 0) }}') || 0,
                valorAcordado: Number('{{ old('brokerage_amount', $transaction->brokerage_amount ?? 0) }}') || 0,
                propiedad: '{{ old('property_id', $transaction->property_id) }}',
                precios: { @foreach($properties as $property) '{{ $property->id }}': {{ (float) ($property->price ?? 0) }}, @endforeach },
                actualizarPrecio() { if (this.propiedad && this.precios[this.propiedad] !== undefined) { this.$refs.precioPublicado.value = Number(this.precios[this.propiedad]).toFixed(2); } },
                calcularCorretaje() { return ((Number(this.precioCierre || 0) * Number(this.porcentaje || 0)) / 100).toFixed(2); },
                porcentajeEquivalente() { if (Number(this.precioCierre || 0) <= 0) return '0.0000'; return ((Number(this.valorAcordado || 0) / Number(this.precioCierre)) * 100).toFixed(4); }
            }">
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-4"><i class="fa-solid fa-house text-blue-700"></i><h3 class="font-bold text-blue-900">Corretaje / Propiedad</h3></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Propiedad</label><select name="property_id" x-model="propiedad" @change="actualizarPrecio()" :disabled="!billingEditing" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600"><option value="">Seleccionar propiedad</option>@foreach($properties as $property)<option value="{{ $property->id }}">#{{ $property->id }} — {{ $property->title }} @if($property->price)— ${{ number_format($property->price, 2) }}@endif</option>@endforeach</select></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Precio publicado</label><input type="number" name="published_price" x-ref="precioPublicado" step="0.01" min="0" value="{{ old('published_price', $transaction->published_price ?? $transaction->property?->price) }}" :disabled="!billingEditing" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600" placeholder="0.00"><p class="text-[11px] text-gray-500 mt-1">Se carga automáticamente desde la propiedad.</p></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Precio real de cierre</label><input type="number" name="closing_price" step="0.01" min="0" x-model.number="precioCierre" :disabled="!billingEditing" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600" placeholder="Ej. 95000.00"><p class="text-[11px] text-gray-500 mt-1">Valor final acordado para el cierre.</p></div>
                    </div>
                    <div class="mt-5">
                        <label class="block text-xs font-bold text-gray-700 mb-2">¿Cómo se acordó el corretaje?</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                            <label class="flex items-center gap-3 border rounded-xl px-4 py-3 transition bg-white" :class="[modoCorretaje === 'percentage' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300', billingEditing ? 'cursor-pointer' : 'opacity-70']">
                                <input type="radio" name="brokerage_mode" value="percentage" x-model="modoCorretaje" :disabled="!billingEditing" class="text-emerald-600 focus:ring-emerald-500">
                                <div><p class="text-sm font-bold text-gray-800">Por porcentaje</p><p class="text-[11px] text-gray-500">Calculado sobre el precio real de cierre.</p></div>
                            </label>
                            <label class="flex items-center gap-3 border rounded-xl px-4 py-3 transition bg-white" :class="[modoCorretaje === 'fixed' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300', billingEditing ? 'cursor-pointer' : 'opacity-70']">
                                <input type="radio" name="brokerage_mode" value="fixed" x-model="modoCorretaje" :disabled="!billingEditing" class="text-emerald-600 focus:ring-emerald-500">
                                <div><p class="text-sm font-bold text-gray-800">Valor acordado</p><p class="text-[11px] text-gray-500">Ingresa directamente el corretaje negociado.</p></div>
                            </label>
                        </div>
                        <div x-show="modoCorretaje === 'percentage'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-gray-700 mb-2">% de corretaje negociado</label><input type="number" name="brokerage_percentage" step="0.0001" min="0" max="100" x-model.number="porcentaje" :disabled="!billingEditing || modoCorretaje !== 'percentage'" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600" placeholder="Ej. 3"></div>
                            <div><label class="block text-xs font-bold text-gray-700 mb-2">Valor calculado de corretaje</label><div class="w-full border border-emerald-200 bg-emerald-50 rounded-xl px-4 py-3 font-bold text-emerald-800">$ <span x-text="calcularCorretaje()"></span></div></div>
                            <input type="hidden" name="brokerage_amount" :value="calcularCorretaje()" :disabled="!billingEditing || modoCorretaje !== 'percentage'">
                        </div>
                        <div x-show="modoCorretaje === 'fixed'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-gray-700 mb-2">Valor real de corretaje acordado</label><input type="number" name="brokerage_amount" step="0.01" min="0" x-model.number="valorAcordado" :disabled="!billingEditing || modoCorretaje !== 'fixed'" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600" placeholder="Ej. 1000.00"></div>
                            <div><label class="block text-xs font-bold text-gray-700 mb-2">% equivalente sobre el precio de cierre</label><div class="w-full border border-emerald-200 bg-emerald-50 rounded-xl px-4 py-3 font-bold text-emerald-800"><span x-text="porcentajeEquivalente()"></span> %</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div><label class="block text-xs font-bold text-[#1F3D32] mb-2">Observaciones de facturación</label><textarea name="notes" rows="3" :disabled="!billingEditing" class="w-full border border-gray-300 rounded-xl px-4 py-3 disabled:bg-gray-100 disabled:text-gray-600" placeholder="Acuerdos, observaciones o detalles necesarios para la facturación...">{{ old('notes', $transaction->notes) }}</textarea></div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2">
                <button type="submit" x-show="billingEditing" class="inline-flex items-center justify-center gap-2 bg-[#2E624C] hover:bg-[#244E3D] text-white px-5 py-3 rounded-xl font-semibold shadow transition"><i class="fa-solid fa-floppy-disk"></i>Guardar datos de facturación</button>
               <a href="{{ route('accounting.invoice.customer', $transaction->id) }}" x-show="!billingEditing" x-cloak class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl font-semibold shadow transition">Continuar con datos del cliente <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </form>

    {{-- MOVILIZACIÓN --}}
    <div id="movilizacion" class="bg-white border border-emerald-100 rounded-2xl shadow-sm overflow-hidden mb-5 scroll-mt-6" x-data="{ editTripOpen: false, deleteTripOpen: false, editTripAction: '', deleteTripAction: '', editTrip: { date: '', concept: '', origin: '', destination: '', kilometers: '', notes: '' }, deleteTripLabel: '' }">
        <div class="px-5 py-4 border-b bg-gradient-to-r from-emerald-50 to-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-[#2E624C] text-white flex items-center justify-center"><i class="fa-solid fa-car"></i></div><div><div class="flex flex-wrap items-center gap-2"><h2 class="font-bold text-[#1F3D32]">Movilización de la operación</h2><span class="inline-flex px-2 py-1 rounded-lg bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wide">Información interna</span></div><p class="text-xs text-gray-500 mt-1">Registro analítico para reportes de movilidad. No interviene en el valor de la factura.</p></div></div>
                <div class="flex flex-wrap gap-2">
                    <div class="bg-white border rounded-xl px-3 py-2 min-w-[120px]"><p class="text-[10px] uppercase font-bold tracking-wide text-gray-400">Kilómetros</p><p class="text-sm font-bold text-[#1F3D32]">{{ number_format((float) $totalVehicleKilometers, 2) }} km</p></div>
                    <div class="bg-white border rounded-xl px-3 py-2 min-w-[135px]"><p class="text-[10px] uppercase font-bold tracking-wide text-gray-400">Costo analítico</p><p class="text-sm font-bold text-emerald-700">${{ number_format((float) $totalVehicleCost, 2) }}</p></div>
                </div>
            </div>
        </div>

        {{-- Formulario registro --}}
        <div class="p-5 bg-gray-50 border-b">
            <div class="mb-4"><h3 class="font-bold text-sm text-[#1F3D32]">Registrar recorrido</h3><p class="text-xs text-gray-500 mt-1">El costo por kilómetro se toma de la configuración vigente para la fecha seleccionada.</p></div>
            <form method="POST" action="{{ route('accounting.vehicle-trips.store', $transaction->id) }}" class="space-y-4">@csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Fecha <span class="text-red-500">*</span></label><input type="date" name="trip_date" value="{{ old('trip_date', now()->format('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"></div>
                    <div class="md:col-span-1 lg:col-span-2"><label class="block text-xs font-bold text-gray-700 mb-2">Motivo / concepto <span class="text-red-500">*</span></label><input type="text" name="concept" value="{{ old('concept') }}" required maxlength="180" placeholder="Ej. Visita a propiedad" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Kilómetros <span class="text-red-500">*</span></label><div class="relative"><input type="number" name="kilometers" value="{{ old('kilometers') }}" min="0.01" step="0.01" required placeholder="0.00" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">km</span></div></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Origen</label><input type="text" name="origin" value="{{ old('origin') }}" maxlength="180" placeholder="Ej. Oficina" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Destino</label><input type="text" name="destination" value="{{ old('destination') }}" maxlength="180" placeholder="Ej. Propiedad sector norte" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div>
                </div>
                <div><label class="block text-xs font-bold text-gray-700 mb-2">Observaciones</label><textarea name="notes" rows="2" placeholder="Información adicional del recorrido..." class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm">{{ old('notes') }}</textarea></div>
                <div class="flex justify-end"><button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#2E624C] hover:bg-[#244E3D] text-white text-sm font-semibold shadow-sm transition"><i class="fa-solid fa-plus"></i>Registrar recorrido</button></div>
            </form>
        </div>

        {{-- Tabla recorridos --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#2E624C] text-white"><tr><th class="px-4 py-3 text-left">Fecha</th><th class="px-4 py-3 text-left">Motivo</th><th class="px-4 py-3 text-left">Ruta</th><th class="px-4 py-3 text-right">Km</th><th class="px-4 py-3 text-right">Costo/km</th><th class="px-4 py-3 text-right">Costo</th><th class="px-4 py-3 text-center">Acciones</th></tr></thead>
                <tbody class="divide-y">
                    @forelse($transaction->vehicleTrips as $trip)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">{{ $trip->trip_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><div class="font-semibold text-gray-800">{{ $trip->concept }}</div>@if($trip->notes)<div class="text-xs text-gray-400 mt-1">{{ $trip->notes }}</div>@endif</td>
                            <td class="px-4 py-3">@if($trip->origin || $trip->destination)<div class="flex items-center gap-2 text-xs"><span class="text-gray-600">{{ $trip->origin ?? '—' }}</span><i class="fa-solid fa-arrow-right text-gray-300"></i><span class="text-gray-600">{{ $trip->destination ?? '—' }}</span></div>@else<span class="text-gray-400">—</span>@endif</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format((float) $trip->kilometers, 2) }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format((float) $trip->cost_per_km, 4) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-700">${{ number_format((float) $trip->calculated_cost, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" @click="editTripAction = '{{ route('accounting.vehicle-trips.update', $trip->id) }}'; editTrip.date = '{{ $trip->trip_date?->format('Y-m-d') }}'; editTrip.concept = @js($trip->concept); editTrip.origin = @js($trip->origin ?? ''); editTrip.destination = @js($trip->destination ?? ''); editTrip.kilometers = '{{ $trip->kilometers }}'; editTrip.notes = @js($trip->notes ?? ''); editTripOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Editar recorrido"><i class="fa-solid fa-pen"></i></button>
                                    <button type="button" @click="deleteTripAction = '{{ route('accounting.vehicle-trips.destroy', $trip->id) }}'; deleteTripLabel = @js($trip->concept); deleteTripOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Eliminar recorrido"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-9 text-center"><div class="text-gray-300 text-3xl mb-3"><i class="fa-solid fa-route"></i></div><p class="font-semibold text-gray-500">No existen recorridos registrados.</p><p class="text-xs text-gray-400 mt-1">Registra la primera movilización relacionada con esta operación.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL EDITAR RECORRIDO --}}
        <div x-show="editTripOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="editTripOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
                <div class="px-5 py-4 border-b bg-[#F8F7F3] flex items-center justify-between"><div><h3 class="font-bold text-[#1F3D32]">Editar recorrido</h3><p class="text-xs text-gray-500 mt-1">Al guardar, el costo se recalcula automáticamente.</p></div><button type="button" @click="editTripOpen = false" class="w-8 h-8 rounded-lg hover:bg-gray-200"><i class="fa-solid fa-xmark"></i></button></div>
                <form method="POST" :action="editTripAction" class="p-5 space-y-4">@csrf @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-gray-700 mb-2">Fecha</label><input type="date" name="trip_date" x-model="editTrip.date" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div><div><label class="block text-xs font-bold text-gray-700 mb-2">Kilómetros</label><input type="number" name="kilometers" x-model="editTrip.kilometers" step="0.01" min="0.01" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Motivo / concepto</label><input type="text" name="concept" x-model="editTrip.concept" maxlength="180" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-gray-700 mb-2">Origen</label><input type="text" name="origin" x-model="editTrip.origin" maxlength="180" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div><div><label class="block text-xs font-bold text-gray-700 mb-2">Destino</label><input type="text" name="destination" x-model="editTrip.destination" maxlength="180" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></div></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Observaciones</label><textarea name="notes" x-model="editTrip.notes" rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"></textarea></div>
                    <div class="flex justify-end gap-2 pt-2"><button type="button" @click="editTripOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button><button type="submit" class="px-4 py-2 rounded-xl bg-[#2E624C] hover:bg-[#244E3D] text-white text-sm font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i>Guardar cambios</button></div>
                </form>
            </div>
        </div>

        {{-- MODAL ELIMINAR RECORRIDO --}}
        <div x-show="deleteTripOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="deleteTripOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-red-50"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center"><i class="fa-solid fa-trash"></i></div><div><h3 class="font-bold text-red-800">Eliminar recorrido</h3><p class="text-xs text-red-600 mt-1">El registro se desactivará y dejará de aparecer en esta operación.</p></div></div></div>
                <div class="p-5"><p class="text-sm text-gray-700">¿Deseas eliminar el recorrido <strong x-text="deleteTripLabel"></strong>?</p><div class="flex justify-end gap-2 mt-5"><button type="button" @click="deleteTripOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button><form method="POST" :action="deleteTripAction">@csrf @method('DELETE')<button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold"><i class="fa-solid fa-trash mr-1"></i>Sí, eliminar</button></form></div></div>
            </div>
        </div>
    </div>

    {{-- PARTICIPANTES Y COMISIONES --}}
    @php
        $commissionSummary = $automaticCommissionSummary ?? ['configuration' => null, 'brokerage_base' => 0, 'commissions' => [], 'total_advisor_commissions' => 0, 'company_retention' => 0];
        $automaticCommissions = collect($commissionSummary['commissions'] ?? []);
        $brokerageBase = (float) ($commissionSummary['brokerage_base'] ?? 0);
        $totalAdvisorCommissions = (float) ($commissionSummary['total_advisor_commissions'] ?? 0);
        $companyRetention = (float) ($commissionSummary['company_retention'] ?? 0);
        $activeConfiguration = $commissionSummary['configuration'] ?? null;
        $captureParticipants = $transaction->activeParticipants->where('participation_type', 'capture');
        $saleParticipants = $transaction->activeParticipants->where('participation_type', 'sale');
        $agencyCaptured = $captureParticipants->isEmpty();
        $agencySold = $saleParticipants->isEmpty();
        $otherParticipants = $transaction->activeParticipants->whereNotIn('participation_type', ['capture', 'sale']);
    @endphp

    <div id="comisiones" class="bg-white border border-emerald-100 rounded-2xl shadow-sm overflow-hidden mb-5" x-data="{ addOpen: false, editOpen: false, combinedEditOpen: false, deleteOpen: false, combinedDeleteOpen: false, editRole: '', editKind: 'advisor', editUser: '', combinedUser: '', deleteAction: '', deleteLabel: '', captureDeleteAction: '', saleDeleteAction: '' }">
        <div class="px-5 py-4 border-b bg-gradient-to-r from-emerald-50 to-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-[#2E624C] text-white flex items-center justify-center"><i class="fa-solid fa-user-tie"></i></div><div><div class="flex flex-wrap items-center gap-2"><h2 class="font-bold text-[#1F3D32]">Participantes y comisiones</h2><span class="inline-flex px-2 py-1 rounded-lg bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wide">Información interna</span></div><p class="text-xs text-gray-500 mt-1">Cálculo automático según los participantes reales y la configuración vigente.</p></div></div>
                <a href="{{ route('accounting.commission-settings') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 text-xs font-semibold transition"><i class="fa-solid fa-sliders"></i>Configuración de comisiones</a>
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="p-5 border-b">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50"><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Corretaje base</p><p class="text-xl font-bold text-[#1F3D32] mt-1">${{ number_format($brokerageBase, 2) }}</p><p class="text-[11px] text-gray-500 mt-1">Base antes de IVA.</p></div>
                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50"><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Captación</p>@if($agencyCaptured)<p class="text-sm font-bold text-[#1F3D32] mt-1">Inmobiliaria</p><p class="text-[11px] text-emerald-700 mt-1 font-semibold">Sin comisión de captación a asesor</p>@else<p class="text-sm font-bold text-[#1F3D32] mt-1">Por asesor</p><p class="text-[11px] text-gray-500 mt-1">{{ $captureParticipants->count() }} participante{{ $captureParticipants->count() === 1 ? '' : 's' }}.</p>@endif</div>
                <div class="border border-amber-200 rounded-xl p-4 bg-amber-50"><p class="text-[10px] uppercase tracking-wide text-amber-600 font-bold">Comisiones asesores</p><p class="text-xl font-bold text-amber-700 mt-1">${{ number_format($totalAdvisorCommissions, 2) }}</p><p class="text-[11px] text-amber-700/70 mt-1">Total calculado automáticamente.</p></div>
                <div class="border border-emerald-200 rounded-xl p-4 bg-emerald-50"><p class="text-[10px] uppercase tracking-wide text-emerald-600 font-bold">Retención inmobiliaria</p><p class="text-xl font-bold text-emerald-700 mt-1">${{ number_format($companyRetention, 2) }}</p><p class="text-[11px] text-emerald-700/70 mt-1">Corretaje menos comisiones.</p></div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs"><span class="text-gray-500">Configuración aplicada:</span>@if($activeConfiguration)<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-semibold"><i class="fa-solid fa-circle-check text-[10px]"></i>{{ $activeConfiguration->name }}</span>@else<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-100 text-red-700 font-semibold"><i class="fa-solid fa-triangle-exclamation text-[10px]"></i>Sin configuración vigente</span>@endif</div>
        </div>

        {{-- TABLA --}}
        <div class="p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div><h3 class="font-bold text-sm text-[#1F3D32]">Participantes de la operación</h3><p class="text-xs text-gray-500 mt-1">La propiedad propone los participantes iniciales; aquí se conserva la participación real de la operación.</p></div>
                <button type="button" @click="addOpen = true" class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-[#2E624C] hover:bg-[#244E3D] text-white text-xs font-semibold transition"><i class="fa-solid fa-plus"></i>Agregar participante</button>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#2E624C] text-white"><tr><th class="px-4 py-3 text-left">Participación</th><th class="px-4 py-3 text-left">Participante</th><th class="px-4 py-3 text-center">%</th><th class="px-4 py-3 text-right">Corretaje base</th><th class="px-4 py-3 text-right">Valor a recibir</th><th class="px-4 py-3 text-center">Acciones</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- CAPTACIÓN INMOBILIARIA --}}
                        @if($agencyCaptured)
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 font-semibold">Captación</td><td class="px-4 py-3"><div class="flex items-center gap-2"><div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-building text-xs"></i></div><span class="font-bold">Inmobiliaria</span></div></td><td class="px-4 py-3 text-center text-gray-400">—</td><td class="px-4 py-3 text-right font-semibold">${{ number_format($brokerageBase, 2) }}</td><td class="px-4 py-3 text-right font-bold text-gray-500">$0.00</td><td class="px-4 py-3 text-center"><button type="button" @click="editRole = 'capture'; editKind = 'agency'; editUser = ''; editOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Editar captación"><i class="fa-solid fa-pen"></i></button></td></tr>
                        @endif

                        {{-- VENTA INMOBILIARIA --}}
                        @if($agencySold)
                            <tr class="hover:bg-gray-50"><td class="px-4 py-3 font-semibold">Venta</td><td class="px-4 py-3"><div class="flex items-center gap-2"><div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-building text-xs"></i></div><span class="font-bold">Inmobiliaria</span></div></td><td class="px-4 py-3 text-center text-gray-400">—</td><td class="px-4 py-3 text-right font-semibold">${{ number_format($brokerageBase, 2) }}</td><td class="px-4 py-3 text-right font-bold text-gray-500">$0.00</td><td class="px-4 py-3 text-center"><button type="button" @click="editRole = 'sale'; editKind = 'agency'; editUser = ''; editOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Editar venta"><i class="fa-solid fa-pen"></i></button></td></tr>
                        @endif

                        {{-- COMISIONES AUTOMÁTICAS --}}
                        @foreach($automaticCommissions as $commission)
                            @php
                                $commissionUser = $commission['user'] ?? null;
                                $roleLabel = $commission['role_label'] ?? 'Participante';
                                $commissionPercentage = (float) ($commission['percentage'] ?? 0);
                                $commissionAmount = (float) ($commission['commission_amount'] ?? 0);
                                $calculationBase = (float) ($commission['calculation_base'] ?? 0);
                                $participationType = $commission['participation_type'] ?? null;
                                $participantRecord = null; $captureRecord = null; $saleRecord = null;
                                if ($participationType === 'capture_and_sale' && $commissionUser) {
                                    $captureRecord = $transaction->activeParticipants->first(fn ($p) => (int) $p->user_id === (int) $commissionUser->id && $p->participation_type === 'capture');
                                    $saleRecord = $transaction->activeParticipants->first(fn ($p) => (int) $p->user_id === (int) $commissionUser->id && $p->participation_type === 'sale');
                                } elseif ($commissionUser) {
                                    $participantRecord = $transaction->activeParticipants->first(fn ($p) => (int) $p->user_id === (int) $commissionUser->id && $p->participation_type === $participationType);
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold">{{ $roleLabel }}</td>
                                <td class="px-4 py-3"><div class="flex items-center gap-2"><div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-user text-xs"></i></div><span class="font-bold">{{ $commissionUser?->name }} {{ $commissionUser?->last_name }}</span></div></td>
                                <td class="px-4 py-3 text-center"><span class="inline-flex px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold">{{ number_format($commissionPercentage, 2) }}%</span></td>
                                <td class="px-4 py-3 text-right font-semibold">${{ number_format($calculationBase, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-amber-700">${{ number_format($commissionAmount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        @if($participationType === 'capture_and_sale')
                                            <button type="button" @click="combinedUser = '{{ $commissionUser?->id }}'; combinedEditOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Editar captación o venta"><i class="fa-solid fa-pen"></i></button>
                                            @if($captureRecord || $saleRecord)
                                                <button type="button" @click="captureDeleteAction = '{{ $captureRecord ? route('accounting.participants.destroy', $captureRecord->id) : '' }}'; saleDeleteAction = '{{ $saleRecord ? route('accounting.participants.destroy', $saleRecord->id) : '' }}'; combinedDeleteOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Anular participación"><i class="fa-solid fa-ban"></i></button>
                                            @endif
                                        @else
                                            @if(in_array($participationType, ['capture', 'sale'], true))
                                                <button type="button" @click="editRole = '{{ $participationType }}'; editKind = 'advisor'; editUser = '{{ $commissionUser?->id }}'; editOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Editar participante"><i class="fa-solid fa-pen"></i></button>
                                            @endif
                                            @if($participantRecord)
                                                <button type="button" @click="deleteAction = '{{ route('accounting.participants.destroy', $participantRecord->id) }}'; deleteLabel = @js(trim(($commissionUser?->name ?? '') . ' ' . ($commissionUser?->last_name ?? ''))); deleteOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Anular participante"><i class="fa-solid fa-ban"></i></button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        {{-- OTROS PARTICIPANTES --}}
                        @foreach($otherParticipants as $participant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold">{{ $participantLabels[$participant->participation_type] ?? ucfirst($participant->participation_type) }}</td>
                                <td class="px-4 py-3"><div class="flex items-center gap-2"><div class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center"><i class="fa-solid fa-user text-xs"></i></div><span class="font-bold">{{ $participant->user?->name }} {{ $participant->user?->last_name }}</span></div></td>
                                <td class="px-4 py-3 text-center"><span class="text-xs text-gray-400">Sin regla</span></td>
                                <td class="px-4 py-3 text-right font-semibold">${{ number_format($brokerageBase, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-400">$0.00</td>
                                <td class="px-4 py-3 text-center"><button type="button" @click="deleteAction = '{{ route('accounting.participants.destroy', $participant->id) }}'; deleteLabel = @js(trim(($participant->user?->name ?? '') . ' ' . ($participant->user?->last_name ?? ''))); deleteOpen = true;" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Anular participante"><i class="fa-solid fa-ban"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL EDITAR CAPTACIÓN / VENTA --}}
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="editOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-[#F8F7F3] flex justify-between items-center">
                    <div><h3 class="font-bold text-[#1F3D32]">Modificar participante</h3><p class="text-xs text-gray-500 mt-1" x-text="editRole === 'capture' ? 'Modificar captación' : 'Modificar venta'"></p></div>
                    <button type="button" @click="editOpen = false" class="w-8 h-8 rounded-lg hover:bg-gray-200"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" :action="'{{ url('/intranet/accounting/transactions/'.$transaction->id.'/participants') }}/' + editRole" class="p-5 space-y-4">@csrf @method('PATCH')
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Participante</label><select name="participant_kind" x-model="editKind" class="w-full border border-gray-300 rounded-xl px-3 py-2.5"><option value="agency">Inmobiliaria</option><option value="advisor">Asesor</option></select></div>
                    <div x-show="editKind === 'advisor'" x-cloak><label class="block text-xs font-bold text-gray-700 mb-2">Asesor</label><select name="user_id" x-model="editUser" :required="editKind === 'advisor'" class="w-full border border-gray-300 rounded-xl px-3 py-2.5"><option value="">Seleccionar asesor</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} {{ $user->last_name }}</option>@endforeach</select></div>
                    <div class="flex justify-end gap-2 pt-2"><button type="button" @click="editOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button><button type="submit" class="px-4 py-2 rounded-xl bg-[#2E624C] hover:bg-[#244E3D] text-white text-sm font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i>Guardar</button></div>
                </form>
            </div>
        </div>

        {{-- MODAL ELEGIR QUÉ EDITAR CUANDO EL MISMO ASESOR CAPTA Y VENDE --}}
        <div x-show="combinedEditOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="combinedEditOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-[#F8F7F3]"><h3 class="font-bold text-[#1F3D32]">Modificar participación</h3><p class="text-xs text-gray-500 mt-1">El mismo asesor figura en captación y venta. Selecciona qué función deseas modificar.</p></div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button" @click="combinedEditOpen = false; editRole = 'capture'; editKind = 'advisor'; editUser = combinedUser; editOpen = true;" class="border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl px-4 py-4 text-sm font-semibold"><i class="fa-solid fa-house-user mr-1"></i>Editar captación</button>
                    <button type="button" @click="combinedEditOpen = false; editRole = 'sale'; editKind = 'advisor'; editUser = combinedUser; editOpen = true;" class="border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl px-4 py-4 text-sm font-semibold"><i class="fa-solid fa-handshake mr-1"></i>Editar venta</button>
                </div>
                <div class="px-5 pb-5 flex justify-end"><button type="button" @click="combinedEditOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button></div>
            </div>
        </div>

        {{-- MODAL AGREGAR PARTICIPANTE --}}
        <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="addOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-[#F8F7F3] flex justify-between items-center"><div><h3 class="font-bold text-[#1F3D32]">Agregar participante</h3><p class="text-xs text-gray-500 mt-1">Agrega otro asesor que haya intervenido en la operación.</p></div><button type="button" @click="addOpen = false" class="w-8 h-8 rounded-lg hover:bg-gray-200"><i class="fa-solid fa-xmark"></i></button></div>
                <form method="POST" action="{{ route('accounting.participants.store', $transaction->id) }}" class="p-5 space-y-4">@csrf
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Participación</label><select name="participation_type" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5"><option value="">Seleccionar</option><option value="capture">Captación</option><option value="sale">Venta</option><option value="support">Apoyo</option><option value="closing">Cierre</option><option value="other">Otro</option></select></div>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Asesor</label><select name="user_id" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5"><option value="">Seleccionar asesor</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} {{ $user->last_name }}</option>@endforeach</select></div>
                    <div class="flex justify-end gap-2 pt-2"><button type="button" @click="addOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button><button type="submit" class="px-4 py-2 rounded-xl bg-[#2E624C] hover:bg-[#244E3D] text-white text-sm font-semibold"><i class="fa-solid fa-plus mr-1"></i>Agregar</button></div>
                </form>
            </div>
        </div>

        {{-- MODAL ANULAR PARTICIPANTE --}}
        <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="deleteOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-red-50"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center"><i class="fa-solid fa-ban"></i></div><div><h3 class="font-bold text-red-800">Anular participante</h3><p class="text-xs text-red-600 mt-1">El registro se conservará en el historial.</p></div></div></div>
                <div class="p-5"><p class="text-sm text-gray-700">¿Deseas anular la participación de <strong x-text="deleteLabel"></strong>?</p><div class="flex justify-end gap-2 mt-5"><button type="button" @click="deleteOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button><form method="POST" :action="deleteAction">@csrf @method('DELETE')<button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold"><i class="fa-solid fa-ban mr-1"></i>Sí, anular</button></form></div></div>
            </div>
        </div>

        {{-- MODAL ANULAR CAPTACIÓN O VENTA DEL MISMO ASESOR --}}
        <div x-show="combinedDeleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="combinedDeleteOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-red-50"><h3 class="font-bold text-red-800">Anular participación</h3><p class="text-xs text-red-600 mt-1">El asesor participa en captación y venta. Selecciona qué función deseas anular.</p></div>
                <div class="p-5 space-y-3">
                    <form x-show="captureDeleteAction" method="POST" :action="captureDeleteAction">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-3 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-semibold">Anular captación</button></form>
                    <form x-show="saleDeleteAction" method="POST" :action="saleDeleteAction">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-3 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-semibold">Anular venta</button></form>
                    <button type="button" @click="combinedDeleteOpen = false" class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SIGUIENTE PASO: DATOS DEL CLIENTE Y EMISIÓN --}}
    <div id="paso-cliente" class="bg-white border border-emerald-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ nextStepOpen: false }">
        <div class="px-5 py-4 bg-gradient-to-r from-emerald-50 to-white border-b">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center"><i class="fa-solid fa-receipt"></i></div><div><h2 class="font-bold text-[#1F3D32]">Siguiente paso: datos del cliente</h2><p class="text-xs text-gray-500 mt-1">Con los datos internos guardados, el siguiente paso será completar la información del cliente y emitir la factura o comprobante.</p></div></div>
                @if($billingDataSaved)
                    <button type="button" @click="nextStepOpen = true" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow transition">Continuar con datos del cliente <i class="fa-solid fa-arrow-right"></i></button>
                @else
                    <button type="button" disabled class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gray-200 text-gray-500 text-sm font-semibold cursor-not-allowed"><i class="fa-solid fa-lock"></i>Guarda primero la facturación</button>
                @endif
            </div>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50"><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Cliente</p><p class="font-bold text-gray-800 mt-1">{{ trim(($transaction->client?->name ?? $transaction->prospect?->name ?? '') . ' ' . ($transaction->client?->last_name ?? $transaction->prospect?->last_name ?? '')) ?: 'Sin cliente' }}</p></div>
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50"><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Operación</p><p class="font-bold text-gray-800 mt-1">{{ $transaction->operation_type }}</p></div>
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50"><p class="text-[10px] uppercase tracking-wide text-gray-400 font-bold">Estado</p><p class="font-bold {{ $billingDataSaved ? 'text-emerald-700' : 'text-amber-700' }} mt-1">{{ $billingDataSaved ? 'Datos internos listos' : 'Pendiente de guardar' }}</p></div>
        </div>

        {{-- MODAL DE TRANSICIÓN --}}
        <div x-show="nextStepOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="nextStepOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b bg-emerald-50"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-user-check"></i></div><div><h3 class="font-bold text-emerald-900">Datos internos listos</h3><p class="text-xs text-emerald-700 mt-1">La operación ya puede continuar a la información fiscal del cliente.</p></div></div></div>
                <div class="p-5"><p class="text-sm text-gray-700 leading-relaxed">Esta pantalla queda preparada para continuar al módulo de <strong>Datos del cliente y emisión de factura</strong>. La ruta final de emisión se conectará en el siguiente paso para no apuntar a una ruta inexistente.</p><div class="flex justify-end mt-5"><button type="button" @click="nextStepOpen = false" class="px-4 py-2 rounded-xl bg-[#2E624C] hover:bg-[#244E3D] text-white text-sm font-semibold">Entendido</button></div></div>
            </div>
        </div>
    </div>
</div>

@endsection