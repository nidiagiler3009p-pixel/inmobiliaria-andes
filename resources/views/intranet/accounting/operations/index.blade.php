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
                Operaciones Contables
            </h1>

            <p class="text-sm text-gray-600 mt-1">
                Consulta y seguimiento de todas las operaciones financieras registradas.
            </p>
        </div>

        <a href="{{ route('accounting.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                  rounded-xl border border-[#2E624C] text-[#2E624C]
                  bg-white hover:bg-emerald-50 text-sm font-bold transition">

            <i class="fa-solid fa-arrow-left"></i>
            Volver a Contabilidad
        </a>

    </div>


    {{-- INDICADORES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">
                        Operaciones
                    </p>

                    <p class="text-2xl font-extrabold text-[#1F3D32] mt-1">
                        {{ $totalOperations }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700
                            flex items-center justify-center">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
            </div>
        </div>


        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">
                        Pendientes
                    </p>

                    <p class="text-2xl font-extrabold text-amber-600 mt-1">
                        {{ $pendingOperations }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600
                            flex items-center justify-center">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>


        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">
                        Cerradas
                    </p>

                    <p class="text-2xl font-extrabold text-gray-700 mt-1">
                        {{ $closedOperations }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-gray-100 text-gray-600
                            flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>


        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">
                        Ingreso bruto
                    </p>

                    <p class="text-xl font-extrabold text-emerald-700 mt-1">
                        ${{ number_format($totalGrossIncome, 2) }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700
                            flex items-center justify-center">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
            </div>
        </div>


        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">
                        Utilidad
                    </p>

                    <p class="text-xl font-extrabold text-blue-700 mt-1">
                        ${{ number_format($totalNetProfit, 2) }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700
                            flex items-center justify-center">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>

    </div>


    {{-- FILTROS --}}
    <div class="bg-white border border-emerald-100 rounded-2xl shadow-sm p-5 mb-6">

        <form method="GET"
              action="{{ route('accounting.operations') }}"
              class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

            {{-- BUSCAR --}}
            <div class="md:col-span-5">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Buscar operación o cliente
                </label>

                <div class="relative">

                    <i class="fa-solid fa-magnifying-glass
                              absolute left-3 top-1/2 -translate-y-1/2
                              text-gray-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="ID, nombre o apellido..."
                        class="w-full rounded-xl border-gray-300
                               pl-10 pr-4 py-2.5 text-sm
                               focus:border-emerald-500
                               focus:ring-emerald-500"
                    >

                </div>

            </div>


            {{-- ESTADO --}}
            <div class="md:col-span-2">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Estado
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border-gray-300
                           py-2.5 text-sm
                           focus:border-emerald-500
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


            {{-- TIPO --}}
            <div class="md:col-span-2">

                <label class="block text-xs font-bold text-gray-600 mb-2">
                    Tipo
                </label>

                <select
                    name="type"
                    class="w-full rounded-xl border-gray-300
                           py-2.5 text-sm
                           focus:border-emerald-500
                           focus:ring-emerald-500"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($types as $availableType)

                        <option
                            value="{{ $availableType }}"
                            @selected($type === $availableType)
                        >
                            {{ $availableType }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- BOTONES --}}
            <div class="md:col-span-3 flex gap-2">

                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl bg-[#2E624C]
                           hover:bg-[#244E3D] text-white
                           text-sm font-bold transition"
                >
                    <i class="fa-solid fa-filter"></i>
                    Filtrar
                </button>


                @if($search || $status || $type)

                    <a
                        href="{{ route('accounting.operations') }}"
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
    <div class="bg-white border border-emerald-100 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-2">

            <div>
                <h2 class="font-bold text-[#1F3D32]">
                    Registro de operaciones
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Las operaciones cerradas permanecen disponibles únicamente para consulta.
                </p>
            </div>

            <span class="inline-flex self-start sm:self-auto
                         px-3 py-1 rounded-full bg-emerald-50
                         text-emerald-700 text-xs font-semibold">

                {{ $operations->total() }}
                {{ $operations->total() === 1 ? 'resultado' : 'resultados' }}

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
                            Acción
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y">

                    @forelse($operations as $operation)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- ID --}}
                            <td class="px-4 py-3 font-semibold text-gray-700">
                                #{{ $operation->id }}
                            </td>


                            {{-- CLIENTE --}}
                            <td class="px-4 py-3">

                                @if($operation->client)

                                    <div class="font-medium text-gray-800">
                                        {{ $operation->client->name }}
                                        {{ $operation->client->last_name }}
                                    </div>

                                @elseif($operation->prospect)

                                    <div class="font-medium text-gray-800">
                                        {{ $operation->prospect->name }}
                                        {{ $operation->prospect->last_name }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        Prospecto
                                    </div>

                                @else

                                    <span class="text-gray-400">
                                        Sin cliente
                                    </span>

                                @endif

                            </td>


                            {{-- ORIGEN --}}
                            <td class="px-4 py-3">

                                <div class="text-gray-700">
                                    {{ $operation->origin_module ?? 'Sin origen' }}
                                </div>

                                @if($operation->tramite_id)

                                    <div class="text-xs text-gray-400 mt-0.5">
                                        Trámite #{{ $operation->tramite_id }}
                                    </div>

                                @endif

                            </td>


                            {{-- TIPO --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $operation->operation_type ?: '—' }}
                            </td>


                            {{-- ESTADO --}}
                            <td class="px-4 py-3">

                                @if($operation->status === 'Pendiente')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 text-xs font-semibold
                                                 bg-amber-100 text-amber-800">

                                        <i class="fa-solid fa-clock"></i>
                                        Pendiente

                                    </span>

                                @elseif($operation->status === 'En cálculo')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 text-xs font-semibold
                                                 bg-blue-100 text-blue-800">

                                        <i class="fa-solid fa-calculator"></i>
                                        En cálculo

                                    </span>

                                @elseif($operation->status === 'Aprobada')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 text-xs font-semibold
                                                 bg-emerald-100 text-emerald-800">

                                        <i class="fa-solid fa-check"></i>
                                        Aprobada

                                    </span>

                                @elseif($operation->status === 'Cerrada')

                                    <span class="inline-flex items-center gap-1
                                                 px-2.5 py-1 rounded-lg
                                                 text-xs font-semibold
                                                 bg-gray-200 text-gray-700">

                                        <i class="fa-solid fa-lock"></i>
                                        Cerrada

                                    </span>

                                @else

                                    <span class="inline-flex px-2.5 py-1
                                                 rounded-lg text-xs font-semibold
                                                 bg-gray-100 text-gray-700">

                                        {{ $operation->status }}

                                    </span>

                                @endif

                            </td>


                            {{-- INGRESO --}}
                            <td class="px-4 py-3 text-right font-semibold text-emerald-700">
                                ${{ number_format((float) $operation->gross_income, 2) }}
                            </td>


                            {{-- UTILIDAD --}}
                            <td class="px-4 py-3 text-right font-bold
                                {{ (float) $operation->net_profit >= 0
                                    ? 'text-blue-700'
                                    : 'text-red-600' }}">

                                ${{ number_format((float) $operation->net_profit, 2) }}

                            </td>


                            {{-- ACCIÓN --}}
                            <td class="px-4 py-3 text-center">

                                @if($operation->status === 'Cerrada')

                                    <a
                                        href="{{ route('accounting.transaction.report', $operation->id) }}"
                                        target="_blank"
                                        title="Ver reporte interno"
                                        class="inline-flex items-center justify-center
                                               w-9 h-9 rounded-lg
                                               bg-blue-600 hover:bg-blue-700
                                               text-white shadow-sm transition"
                                    >
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>

                                @else

                                    <a
                                        href="{{ route('accounting.review', $operation->id) }}"
                                        title="Revisar operación"
                                        class="inline-flex items-center justify-center
                                               w-9 h-9 rounded-lg
                                               bg-[#2E624C] hover:bg-[#244E3D]
                                               text-white shadow-sm transition"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="px-4 py-10 text-center">

                                <div class="text-gray-400 text-3xl mb-2">
                                    <i class="fa-solid fa-folder-open"></i>
                                </div>

                                <div class="font-semibold text-gray-600">
                                    No se encontraron operaciones.
                                </div>

                                <div class="text-xs text-gray-400 mt-1">
                                    Cambia los filtros para ampliar la búsqueda.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($operations->hasPages())

            <div class="p-4 border-t">
                {{ $operations->links() }}
            </div>

        @endif

    </div>

</div>

@endsection