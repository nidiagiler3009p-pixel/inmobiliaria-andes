@extends('layouts.admin')
@section('admin_content')

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F3D32]">Costeo de Vehículo</h1>
            <p class="text-sm text-gray-500 mt-1">Configuración y cálculo automático del costo estimado por kilómetro.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('accounting.expenses') }}" class="px-4 py-2 rounded-xl border bg-white text-sm font-semibold">Mayor de Gastos</a>
            <a href="{{ route('accounting.index') }}" class="px-4 py-2 rounded-xl border bg-white text-sm font-semibold">Volver</a>
        </div>
    </div>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- CONFIGURACIÓN ACTIVA --}}
    @if($activeConfiguration)
        <div class="bg-[#1F3D32] text-white rounded-2xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider text-emerald-200">Configuración activa</p>
                    <h2 class="text-xl font-bold mt-1">{{ $activeConfiguration->name }}</h2>
                    <p class="text-sm text-emerald-100 mt-1">Vigente desde {{ $activeConfiguration->effective_from?->format('d/m/Y') }}</p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs text-emerald-200">Costo estimado actual</p>
                    <p class="text-3xl font-extrabold">${{ number_format($activeConfiguration->total_cost_per_km, 4) }} <span class="text-base font-medium">/ km</span></p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- FORMULARIO --}}
        <div class="xl:col-span-2">
            <form action="{{ route('accounting.vehicle-costs.store') }}" method="POST" id="vehicleCostForm" class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                @csrf
                <div class="px-6 py-4 border-b bg-[#F8F7F3]">
                    <h2 class="font-bold text-[#1F3D32]">Nueva configuración</h2>
                    <p class="text-xs text-gray-500 mt-1">Al guardar una nueva configuración, la anterior quedará como histórico.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $activeConfiguration?->name ?? 'Costeo vehículo ' . now()->format('m/Y')) }}" required class="w-full border rounded-xl px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Vigente desde *</label>
                        <input type="date" name="effective_from" value="{{ old('effective_from', $activeConfiguration?->effective_from?->format('Y-m-d') ?? now()->toDateString()) }}" required class="w-full border rounded-xl px-4 py-3">
                    </div>
                </div>

                {{-- COMBUSTIBLE --}}
                <div class="border-t px-6 py-5">
                    <h3 class="font-bold text-[#1F3D32] mb-4">Combustible</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Precio por galón</label><input type="number" name="fuel_price_per_gallon" id="fuel_price" value="{{ old('fuel_price_per_gallon', $activeConfiguration?->fuel_price_per_gallon) }}" step="0.0001" min="0" class="w-full border rounded-xl px-4 py-3" placeholder="2.80"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Rendimiento km / galón</label><input type="number" name="vehicle_efficiency_km_per_gallon" id="fuel_efficiency" value="{{ old('vehicle_efficiency_km_per_gallon', $activeConfiguration?->vehicle_efficiency_km_per_gallon) }}" step="0.0001" min="0.01" class="w-full border rounded-xl px-4 py-3" placeholder="40"></div>
                    </div>
                </div>

                {{-- ACEITE --}}
                <div class="border-t px-6 py-5">
                    <h3 class="font-bold text-[#1F3D32] mb-4">Aceite</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Costo cambio de aceite</label><input type="number" name="oil_change_cost" id="oil_cost" value="{{ old('oil_change_cost', $activeConfiguration?->oil_change_cost) }}" step="0.01" min="0" class="w-full border rounded-xl px-4 py-3" placeholder="35.00"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Cambio cada (km)</label><input type="number" name="oil_change_interval_km" id="oil_interval" value="{{ old('oil_change_interval_km', $activeConfiguration?->oil_change_interval_km) }}" step="0.01" min="0.01" class="w-full border rounded-xl px-4 py-3" placeholder="5000"></div>
                    </div>
                </div>

                {{-- NEUMÁTICOS --}}
                <div class="border-t px-6 py-5">
                    <h3 class="font-bold text-[#1F3D32] mb-4">Neumáticos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Costo total neumáticos</label><input type="number" name="tires_total_cost" id="tires_cost" value="{{ old('tires_total_cost', $activeConfiguration?->tires_total_cost) }}" step="0.01" min="0" class="w-full border rounded-xl px-4 py-3" placeholder="400.00"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Vida útil estimada (km)</label><input type="number" name="tires_lifespan_km" id="tires_lifespan" value="{{ old('tires_lifespan_km', $activeConfiguration?->tires_lifespan_km) }}" step="0.01" min="0.01" class="w-full border rounded-xl px-4 py-3" placeholder="40000"></div>
                    </div>
                </div>

                {{-- MANTENIMIENTO --}}
                <div class="border-t px-6 py-5">
                    <h3 class="font-bold text-[#1F3D32] mb-4">Mantenimiento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Costo promedio de mantenimiento</label><input type="number" name="maintenance_cost" id="maintenance_cost" value="{{ old('maintenance_cost', $activeConfiguration?->maintenance_cost) }}" step="0.01" min="0" class="w-full border rounded-xl px-4 py-3" placeholder="120.00"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Mantenimiento cada (km)</label><input type="number" name="maintenance_interval_km" id="maintenance_interval" value="{{ old('maintenance_interval_km', $activeConfiguration?->maintenance_interval_km) }}" step="0.01" min="0.01" class="w-full border rounded-xl px-4 py-3" placeholder="10000"></div>
                    </div>
                </div>

                {{-- COSTOS ANUALES --}}
                <div class="border-t px-6 py-5">
                    <h3 class="font-bold text-[#1F3D32] mb-4">Costos anuales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Seguro anual</label><input type="number" name="annual_insurance_cost" id="insurance_cost" value="{{ old('annual_insurance_cost', $activeConfiguration?->annual_insurance_cost) }}" step="0.01" min="0" class="w-full border rounded-xl px-4 py-3"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Matrícula anual</label><input type="number" name="annual_registration_cost" id="registration_cost" value="{{ old('annual_registration_cost', $activeConfiguration?->annual_registration_cost) }}" step="0.01" min="0" class="w-full border rounded-xl px-4 py-3"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Otros costos anuales</label><input type="number" name="annual_other_vehicle_costs" id="other_annual_cost" value="{{ old('annual_other_vehicle_costs', $activeConfiguration?->annual_other_vehicle_costs) }}" step="0.01" min="0" class="w-full border rounded-xl px-4 py-3"></div>
                        <div><label class="block text-xs font-bold text-gray-700 mb-2">Kilómetros estimados por año</label><input type="number" name="estimated_annual_km" id="annual_km" value="{{ old('estimated_annual_km', $activeConfiguration?->estimated_annual_km) }}" step="0.01" min="0.01" class="w-full border rounded-xl px-4 py-3"></div>
                    </div>
                </div>

                {{-- NOTAS --}}
                <div class="border-t px-6 py-5">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Observaciones</label>
                    <textarea name="notes" rows="3" class="w-full border rounded-xl px-4 py-3">{{ old('notes', $activeConfiguration?->notes) }}</textarea>
                </div>

                <div class="border-t bg-gray-50 px-6 py-5 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-[#2E624C] text-white font-bold">Guardar configuración</button>
                </div>
            </form>
        </div>

        {{-- CALCULADORA EN VIVO --}}
        <div>
            <div class="bg-white border rounded-2xl shadow-sm p-6 sticky top-6">
                <h2 class="font-bold text-[#1F3D32]">Cálculo estimado</h2>
                <p class="text-xs text-gray-500 mt-1 mb-5">Se actualiza mientras ingresas los valores.</p>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between border-b pb-2"><span>Combustible / km</span><strong id="resultFuel">$0.0000</strong></div>
                    <div class="flex justify-between border-b pb-2"><span>Aceite / km</span><strong id="resultOil">$0.0000</strong></div>
                    <div class="flex justify-between border-b pb-2"><span>Neumáticos / km</span><strong id="resultTires">$0.0000</strong></div>
                    <div class="flex justify-between border-b pb-2"><span>Mantenimiento / km</span><strong id="resultMaintenance">$0.0000</strong></div>
                    <div class="flex justify-between border-b pb-2"><span>Costos anuales / km</span><strong id="resultAnnual">$0.0000</strong></div>
                </div>
                <div class="mt-6 rounded-2xl bg-[#1F3D32] text-white p-5">
                    <p class="text-xs text-emerald-200">COSTO TOTAL ESTIMADO</p>
                    <p class="text-3xl font-extrabold mt-1"><span id="resultTotal">$0.0000</span> <span class="text-sm">/ km</span></p>
                </div>
                <div class="mt-5 rounded-xl bg-amber-50 border border-amber-200 p-4">
                    <p class="text-xs text-amber-800">Este valor es un costo analítico por kilómetro. No crea automáticamente un gasto real en el Mayor.</p>
                </div>

                {{-- CALCULADORA DE KILOMETRAJE --}}
                <div class="mt-5 border-t pt-5">
                    <h3 class="font-bold text-[#1F3D32]">Calcular movilización</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Calcula el costo estimado según los kilómetros recorridos.</p>
                    <div><label class="block text-xs font-bold text-gray-700 mb-2">Kilómetros recorridos</label><input type="number" id="trip_km" min="0" step="0.01" placeholder="Ej. 100" class="w-full border rounded-xl px-4 py-3"></div>
                    <div class="mt-4 bg-gray-50 border rounded-xl p-4">
                        <div class="flex justify-between text-sm mb-2"><span class="text-gray-600">Costo por km</span><strong id="tripCostPerKm">$0.0000</strong></div>
                        <div class="flex justify-between text-sm mb-2"><span class="text-gray-600">Kilómetros</span><strong id="tripKmResult">0.00 km</strong></div>
                        <div class="border-t mt-3 pt-3 flex justify-between items-end"><span class="font-bold text-[#1F3D32]">Costo de movilización</span><strong id="tripTotal" class="text-xl text-[#1F3D32]">$0.00</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HISTÓRICO --}}
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b"><h2 class="font-bold text-[#1F3D32]">Histórico de configuraciones</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Configuración</th>
                        <th class="px-4 py-3 text-left">Desde</th>
                        <th class="px-4 py-3 text-left">Hasta</th>
                        <th class="px-4 py-3 text-right">Combustible/km</th>
                        <th class="px-4 py-3 text-right">Mantenimiento/km</th>
                        <th class="px-4 py-3 text-right">Total/km</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($configurations as $configuration)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $configuration->name }}</td>
                            <td class="px-4 py-3">{{ $configuration->effective_from?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $configuration->effective_until?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($configuration->fuel_cost_per_km, 4) }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($configuration->maintenance_cost_per_km, 4) }}</td>
                            <td class="px-4 py-3 text-right font-bold">${{ number_format($configuration->total_cost_per_km, 4) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($configuration->is_active)
                                    <span class="px-2 py-1 rounded-lg text-xs bg-emerald-100 text-emerald-800">Activa</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-xs bg-gray-100 text-gray-600">Histórica</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Todavía no existen configuraciones.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ids = ['fuel_price','fuel_efficiency','oil_cost','oil_interval','tires_cost','tires_lifespan','maintenance_cost','maintenance_interval','insurance_cost','registration_cost','other_annual_cost','annual_km'];
    function value(id) { return parseFloat(document.getElementById(id)?.value || 0); }
    function divide(amount, divisor) { return divisor <= 0 ? 0 : amount / divisor; }
    function money(value) { return '$' + value.toFixed(4); }
    function calculate() {
        const fuel = divide(value('fuel_price'), value('fuel_efficiency'));
        const oil = divide(value('oil_cost'), value('oil_interval'));
        const tires = divide(value('tires_cost'), value('tires_lifespan'));
        const maintenance = divide(value('maintenance_cost'), value('maintenance_interval'));
        const annualCosts = value('insurance_cost') + value('registration_cost') + value('other_annual_cost');
        const annual = divide(annualCosts, value('annual_km'));
        const total = fuel + oil + tires + maintenance + annual;
        document.getElementById('resultFuel').textContent = money(fuel);
        document.getElementById('resultOil').textContent = money(oil);
        document.getElementById('resultTires').textContent = money(tires);
        document.getElementById('resultMaintenance').textContent = money(maintenance);
        document.getElementById('resultAnnual').textContent = money(annual);
        document.getElementById('resultTotal').textContent = money(total);
        calculateTrip(total);
    }
    function calculateTrip(costPerKm = null) {
        if (costPerKm === null) {
            const fuel = divide(value('fuel_price'), value('fuel_efficiency'));
            const oil = divide(value('oil_cost'), value('oil_interval'));
            const tires = divide(value('tires_cost'), value('tires_lifespan'));
            const maintenance = divide(value('maintenance_cost'), value('maintenance_interval'));
            const annualCosts = value('insurance_cost') + value('registration_cost') + value('other_annual_cost');
            const annual = divide(annualCosts, value('annual_km'));
            costPerKm = fuel + oil + tires + maintenance + annual;
        }
        const kilometers = parseFloat(document.getElementById('trip_km')?.value || 0);
        const tripTotal = kilometers * costPerKm;
        document.getElementById('tripCostPerKm').textContent = '$' + costPerKm.toFixed(4);
        document.getElementById('tripKmResult').textContent = kilometers.toFixed(2) + ' km';
        document.getElementById('tripTotal').textContent = '$' + tripTotal.toFixed(2);
    }
    ids.forEach(function (id) {
        const element = document.getElementById(id);
        if (element) element.addEventListener('input', calculate);
    });
    const tripKmInput = document.getElementById('trip_km');
    if (tripKmInput) tripKmInput.addEventListener('input', function () { calculateTrip(); });
    calculate();
});
</script>

@endsection