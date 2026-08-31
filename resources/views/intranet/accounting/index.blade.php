@extends('layouts.admin')

@section('admin_content')

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- ENCABEZADO / PANEL DE CONTROL --}}
    <div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700 mb-1">
                Intranet · Gestión financiera
            </p>

            <h1 class="text-3xl font-extrabold text-[#1F3D32]">
                Panel de Contabilidad
            </h1>

            <p class="text-sm text-gray-600 mt-1">
                Control de operaciones, movimientos reales, costeo y utilidad del negocio.
            </p>
        </div>

        <a href="{{ route('accounting.movements.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                  bg-[#2E624C] hover:bg-[#244E3D] text-white text-sm font-bold
                  shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            Registrar gasto real
        </a>
    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-8">

        
{{-- PyG --}}
<a href="{{ route('accounting.pyg') }}"
   class="group bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm
          hover:shadow-md hover:-translate-y-0.5 transition">

    <div class="flex items-center gap-3">

        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700
                    flex items-center justify-center">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div>
            <p class="font-bold text-sm text-[#1F3D32]">
                PyG
            </p>

            <p class="text-xs text-gray-500">
                Pérdidas y Ganancias
            </p>
        </div>

    </div>

</a>

        {{-- OPERACIONES --}}
       <a href="{{ route('accounting.operations') }}"
           class="group bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#1F3D32]">Operaciones</p>
                    <p class="text-xs text-gray-500">Registros contables</p>
                </div>
            </div>
        </a>

        {{-- GASTOS --}}
        <a href="{{ route('accounting.expenses') }}"
           class="group bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#1F3D32]">Gastos</p>
                    <p class="text-xs text-gray-500">Registro y reportes</p>
                </div>
            </div>
        </a>

        {{-- COSTEO VEHÍCULO --}}
        <a href="{{ route('accounting.vehicle-costs') }}"
           class="group bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-700 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-car"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-[#1F3D32]">Costeo vehículo</p>
                    <p class="text-xs text-gray-500">Costo por km</p>
                </div>
            </div>
        </a>

        {{-- CONFIGURACIÓN DE COSTOS / COMISIONES --}}
  <a href="{{ route('accounting.commission-report') }}"
   class="group bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm
          hover:shadow-md hover:-translate-y-0.5 transition">

    <div class="flex items-center gap-3">

        <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-700
                    flex items-center justify-center">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>

        <div>
            <p class="font-bold text-sm text-[#1F3D32]">
                Reporte comisiones
            </p>

            <p class="text-xs text-gray-500">
                Por asesor y período
            </p>
        </div>

    </div>

</a>


{{-- CONFIGURACIÓN DE COSTOS / COMISIONES --}}
<a href="{{ route('accounting.commission-settings') }}"
   class="group bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm
          hover:shadow-md hover:-translate-y-0.5 transition">

    <div class="flex items-center gap-3">

        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700
                    flex items-center justify-center shrink-0">

            <i class="fa-solid fa-sliders"></i>

        </div>

        <div class="min-w-0">

            <p class="font-bold text-sm text-[#1F3D32]">
                Configuración
            </p>

            <p class="text-xs text-gray-500">
                Costos y comisiones
            </p>

        </div>

    </div>

</a>
    </div>

    {{-- RESUMEN --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">
                Operaciones totales
            </p>

            <p class="text-2xl font-bold text-[#1F3D32]">
                {{ $totalTransactions }}
            </p>
        </div>

        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">
                Pendientes
            </p>

            <p class="text-2xl font-bold text-amber-600">
                {{ $pendingTransactions }}
            </p>
        </div>

        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">
                Ingresos brutos
            </p>

            <p class="text-2xl font-bold text-emerald-700">
                ${{ number_format($grossIncome, 2) }}
            </p>
        </div>

        <div class="bg-white border rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">
                Utilidad neta
            </p>

            <p class="text-2xl font-bold text-blue-700">
                ${{ number_format($netProfit, 2) }}
            </p>
        </div>

    </div>


    {{-- OPERACIONES CONTABLES --}}
    <div id="operaciones" class="bg-white border rounded-2xl shadow-sm overflow-hidden mb-8 scroll-mt-6">

        <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-[#1F3D32]">
                    Operaciones contables
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Revisa ingresos, costos, utilidad y cierre de cada operación.
                </p>
            </div>

            <span class="inline-flex items-center self-start sm:self-auto px-3 py-1 rounded-full
                         bg-emerald-50 text-emerald-700 text-xs font-semibold">
                {{ $totalTransactions }} registradas
            </span>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-[#2E624C] text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            ID
                        </th>

                        <th class="px-4 py-3 text-left">
                            Cliente
                        </th>

                        <th class="px-4 py-3 text-left">
                            Origen
                        </th>

                        <th class="px-4 py-3 text-left">
                            Tipo
                        </th>

                        <th class="px-4 py-3 text-left">
                            Estado
                        </th>

                        <th class="px-4 py-3 text-right">
                            Ingreso
                        </th>

                        <th class="px-4 py-3 text-right">
                            Utilidad
                        </th>

                        <th class="px-4 py-3 text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse($transactions as $transaction)

                        <tr class="hover:bg-gray-50">

                            {{-- ID --}}
                            <td class="px-4 py-3">
                                #{{ $transaction->id }}
                            </td>

                            {{-- CLIENTE --}}
                            <td class="px-4 py-3">

                                @if($transaction->client)

                                    {{ $transaction->client->name }}
                                    {{ $transaction->client->last_name }}

                                @elseif($transaction->prospect)

                                    {{ $transaction->prospect->name }}
                                    {{ $transaction->prospect->last_name }}

                                @else

                                    Sin cliente

                                @endif

                            </td>

                            {{-- ORIGEN --}}
                            <td class="px-4 py-3">

                                {{ $transaction->origin_module ?? 'Sin origen' }}

                                @if($transaction->tramite_id)

                                    <div class="text-xs text-gray-500">
                                        Trámite #{{ $transaction->tramite_id }}
                                    </div>

                                @endif

                            </td>

                            {{-- TIPO --}}
                            <td class="px-4 py-3">
                                {{ $transaction->operation_type }}
                            </td>

                            {{-- ESTADO --}}
                            <td class="px-4 py-3">

                                @if($transaction->status === 'Pendiente')

                                    <span class="px-2 py-1 rounded-lg text-xs bg-amber-100 text-amber-800">
                                        Pendiente
                                    </span>

                                @elseif($transaction->status === 'En cálculo')

                                    <span class="px-2 py-1 rounded-lg text-xs bg-blue-100 text-blue-800">
                                        En cálculo
                                    </span>

                                @elseif($transaction->status === 'Aprobada')

                                    <span class="px-2 py-1 rounded-lg text-xs bg-emerald-100 text-emerald-800">
                                        Aprobada
                                    </span>

                                @elseif($transaction->status === 'Cerrada')

                                    <span class="px-2 py-1 rounded-lg text-xs bg-gray-200 text-gray-800">
                                        Cerrada
                                    </span>

                                @else

                                    <span class="px-2 py-1 rounded-lg text-xs bg-gray-100 text-gray-700">
                                        {{ $transaction->status }}
                                    </span>

                                @endif

                            </td>

                            {{-- INGRESO --}}
                            <td class="px-4 py-3 text-right">
                                ${{ number_format($transaction->gross_income, 2) }}
                            </td>

                            {{-- UTILIDAD --}}
                            <td class="px-4 py-3 text-right font-semibold">
                                ${{ number_format($transaction->net_profit, 2) }}
                            </td>

                            {{-- ACCIONES --}}
<td class="px-4 py-3 text-center">

    @if($transaction->status === 'Cerrada')

        <a
            href="{{ route('accounting.transaction.report', $transaction->id) }}"
            target="_blank"
            class="inline-flex items-center justify-center w-9 h-9
                   bg-blue-600 hover:bg-blue-700
                   text-white rounded-lg shadow-sm transition"
            title="Ver reporte interno"
        >
            <i class="fa-solid fa-file-lines"></i>
        </a>

    @else

        <a
            href="{{ route('accounting.review', $transaction->id) }}"
            class="inline-flex items-center justify-center w-9 h-9
                   bg-[#2E624C] hover:bg-[#244E3D]
                   text-white rounded-lg shadow-sm transition"
            title="Revisar operación"
        >
            <i class="fa-solid fa-pen-to-square"></i>
        </a>

    @endif

</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="px-4 py-8 text-center text-gray-500">

                                No existen operaciones contables registradas.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="p-4">
            {{ $transactions->links() }}
        </div>

    </div>




        
       {{-- MOVIMIENTOS REALES RECIENTES --}}
<div id="movimientos" class="bg-white border border-emerald-100 rounded-2xl shadow-sm overflow-hidden mt-6 scroll-mt-6">

    <div class="px-6 py-4 border-b flex items-center justify-between">

        <div>
            <h2 class="font-bold text-[#1F3D32]">
                Movimientos reales recientes
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Últimos gastos reales registrados en contabilidad.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.expenses') }}"
               class="px-4 py-2 rounded-xl border border-[#2E624C] text-[#2E624C]
                      text-sm font-semibold hover:bg-emerald-50 transition">
                <i class="fa-solid fa-wallet mr-1"></i>
                Ver gastos
            </a>

            <a href="{{ route('accounting.movements.create') }}"
               class="px-4 py-2 rounded-xl bg-[#2E624C] text-white
                      text-sm font-semibold hover:bg-[#244E3D] transition">
                <i class="fa-solid fa-plus mr-1"></i>
                Registrar gasto
            </a>
        </div>

    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Fecha
                    </th>

                    <th class="px-4 py-3 text-left">
                        Concepto
                    </th>

                    <th class="px-4 py-3 text-left">
                        Categoría
                    </th>

                    <th class="px-4 py-3 text-left">
                        Subcategoría
                    </th>

                    <th class="px-4 py-3 text-left">
                        Estado
                    </th>

                    <th class="px-4 py-3 text-right">
                        Monto
                    </th>

                    <th class="px-4 py-3 text-center">
                        Comprobante
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

                @forelse($expenseMovements as $movement)

                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $movement->expense_date?->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $movement->concept }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $movement->category?->name ?? 'Sin categoría' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $movement->subcategory?->name ?? '—' }}
                        </td>

                        <td class="px-4 py-3">

                            @if($movement->payment_status === 'Pagado')

                                <span class="px-2 py-1 rounded-lg text-xs bg-emerald-100 text-emerald-800">
                                    Pagado
                                </span>

                            @elseif($movement->payment_status === 'Pendiente')

                                <span class="px-2 py-1 rounded-lg text-xs bg-amber-100 text-amber-800">
                                    Pendiente
                                </span>

                            @else

                                <span class="px-2 py-1 rounded-lg text-xs bg-gray-100 text-gray-700">
                                    {{ $movement->payment_status }}
                                </span>

                            @endif

                        </td>

                        <td class="px-4 py-3 text-right font-semibold">
                            ${{ number_format($movement->amount, 2) }}
                        </td>

                        <td class="px-4 py-3 text-center">

                            @if($movement->document_path)

                                <a href="{{ asset('storage/' . $movement->document_path) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100">

                                    <i class="fa-solid fa-file-lines"></i>

                                    Ver
                                </a>

                            @else

                                <span class="text-xs text-gray-400">
                                    Sin archivo
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7"
                            class="px-4 py-8 text-center text-gray-500">

                            Todavía no existen movimientos reales registrados.

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>
    </div>


@endsection
