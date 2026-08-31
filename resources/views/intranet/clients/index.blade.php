@extends('layouts.admin')

@section('admin_content')

<div class="flex-grow p-6 max-w-7xl mx-auto w-full text-[#2C3E35] font-sans antialiased">

    {{-- ENCABEZADO --}}
    <header class="relative bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-6 shadow-sm mb-6">

        <div class="absolute left-1/2 -translate-x-1/2 -top-5 bg-[#2C5E43] text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md border-4 border-[#F9F7F2]">
            <i class="fa-solid fa-users"></i>
        </div>

        <div class="text-center pt-2">

            <h1 class="text-2xl font-bold text-[#1E392A]">
                Clientes / Trámites
            </h1>

            <p class="text-sm text-[#556B5D] mt-1">
                Gestión de clientes confirmados y seguimiento de sus trámites.
            </p>

        </div>

    </header>


    {{-- MENSAJES --}}
    @if(session('success'))

        <div class="mb-5 bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm">

            <i class="fa-solid fa-circle-check mr-2"></i>

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl text-sm">

            <i class="fa-solid fa-triangle-exclamation mr-2"></i>

            {{ session('error') }}

        </div>

    @endif


    {{-- TABLA --}}
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-2 mb-3">

            <h2 class="font-bold text-[#1E392A]">

                <i class="fa-solid fa-list-check mr-2 text-[#2C5E43]"></i>

                Clientes Confirmados

            </h2>

            <span class="text-xs text-[#556B5D]">
                Total: {{ $clients->total() }}
            </span>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse text-xs">

                <thead class="bg-[#2C5E43] text-white">

                    <tr>

                        <th class="p-3">Cliente</th>

                        <th class="p-3">Cédula</th>

                        <th class="p-3">Teléfono</th>

                        <th class="p-3">Estado Cliente</th>

                        <th class="p-3">Trámite</th>

                        <th class="p-3">Estado Trámite</th>

                        <th class="p-3 text-center">Acciones</th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#D8D3C8]">

                    @forelse($clients as $client)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | CLIENT_TRAMITES ACTIVOS
                            |--------------------------------------------------------------------------
                            |
                            | Ahora esta pantalla trabaja exclusivamente con
                            | la tabla client_tramites.
                            |
                            | Solo mostramos:
                            |
                            | Pendiente
                            | En Proceso
                            |
                            */

                            $tramitesActivos = $client->clientTramites
                                ->whereIn('status', [
                                    'Pendiente',
                                    'En Proceso'
                                ])
                                ->sortByDesc('id');

                        @endphp


                        @foreach($tramitesActivos as $tramite)

                            <tr class="hover:bg-[#F4F1EA] transition">


                                {{-- CLIENTE --}}
                                <td class="p-3 font-semibold text-[#1E392A]">

                                    {{ $client->name }}
                                    {{ $client->last_name }}

                                    <div class="text-[10px] text-gray-500 mt-1">

                                        Cliente #{{ $client->id }}

                                    </div>

                                </td>


                                {{-- CÉDULA --}}
                                <td class="p-3">

                                    {{ $client->identification_card ?: 'No registrada' }}

                                </td>


                                {{-- TELÉFONO --}}
                                <td class="p-3">

                                    {{ $client->phone ?: 'No registrado' }}

                                </td>


                                {{-- ESTADO CLIENTE --}}
                                <td class="p-3">

                                    @if($client->status === 'En Proceso')

                                        <span class="px-2 py-1 rounded bg-amber-100 text-amber-800 font-semibold">
                                            {{ $client->status }}
                                        </span>

                                    @else

                                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 font-semibold">
                                            {{ $client->status }}
                                        </span>

                                    @endif

                                </td>


                                {{-- TRÁMITE --}}
                                <td class="p-3">

                                    <div class="font-semibold text-[#1E392A]">

                                        Trámite / Servicio

                                    </div>

                                    <div class="text-[10px] text-gray-500 mt-1">

                                        Trámite #{{ $tramite->id }}

                                    </div>

                                    <div class="text-[10px] text-gray-500 mt-1">

                                        @if($tramite->source_type === 'appointment')

                                            Origen: Gestión de Citas

                                        @elseif($tramite->source_type === 'tramite')

                                            Origen: Citas Integrales

                                        @elseif($tramite->source_type === 'cartera')

                                            Origen: Cartera

                                        @else

                                            Origen: Clientes / Trámites

                                        @endif

                                    </div>

                                </td>


                                {{-- ESTADO TRÁMITE --}}
                                <td class="p-3">

                                    @if($tramite->status === 'Pendiente')

                                        <span class="inline-flex items-center px-2 py-1 rounded bg-amber-100 text-amber-800 font-semibold">

                                            <i class="fa-solid fa-clock mr-1"></i>

                                            Pendiente

                                        </span>

                                    @elseif($tramite->status === 'En Proceso')

                                        <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-800 font-semibold">

                                            <i class="fa-solid fa-spinner mr-1"></i>

                                            En Proceso

                                        </span>

                                    @else

                                        <span class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-600 font-semibold">

                                            {{ $tramite->status }}

                                        </span>

                                    @endif

                                </td>


                                {{-- ACCIONES --}}
                                <td class="p-3 text-center">

                                    <div class="flex items-center justify-center gap-1.5">


                                        {{-- INICIAR --}}
                                        @if($tramite->status === 'Pendiente')

                                            <form action="{{ route('client-tramites.iniciar', $tramite->id) }}"
                                                  method="POST"
                                                  class="inline">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-8 h-8 bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow transition"
                                                        title="Iniciar trámite">

                                                    <i class="fa-solid fa-play"></i>

                                                </button>

                                            </form>

                                        @endif


                                        {{-- ÉXITO / SIN ÉXITO --}}
                                        @if($tramite->status === 'En Proceso')


                                            {{-- FINALIZAR CON ÉXITO --}}
                                            <button type="button"
                                                    onclick="openExitoModal(
                                                        {{ $tramite->id }},
                                                        @js(trim($client->name . ' ' . $client->last_name))
                                                    )"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow transition"
                                                    title="Finalizar con éxito">

                                                <i class="fa-solid fa-check"></i>

                                            </button>


                                            {{-- FINALIZAR SIN ÉXITO --}}
                                            <button type="button"
                                                    onclick="openSinExitoModal(
                                                        {{ $tramite->id }},
                                                        @js(trim($client->name . ' ' . $client->last_name))
                                                    )"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition"
                                                    title="Finalizar sin éxito">

                                                <i class="fa-solid fa-xmark"></i>

                                            </button>

                                        @endif


                                        {{-- VER CLIENTE --}}
                                        <a href="{{ route('clients.show', $client->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 bg-[#2C5E43] hover:bg-[#1E392A] text-white rounded-lg shadow transition"
                                           title="Ver cliente">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>


                                    </div>

                                </td>

                            </tr>

                        @endforeach


                    @empty

                        <tr>

                            <td colspan="7"
                                class="p-8 text-center text-gray-500">

                                <i class="fa-solid fa-users text-3xl block mb-2"></i>

                                No existen clientes con trámites activos.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-4">

            {{ $clients->links() }}

        </div>

    </div>

</div>



{{-- ================================================================ --}}
{{-- MODAL FINALIZAR CON ÉXITO --}}
{{-- ================================================================ --}}

<div id="exito-modal"
     class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">

    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">


        {{-- CABECERA --}}
        <div class="bg-emerald-700 text-white px-6 py-4 flex justify-between items-center">

            <div>

                <h3 class="font-bold text-base">

                    <i class="fa-solid fa-circle-check mr-2"></i>

                    Finalizar trámite con éxito

                </h3>

                <p id="exito-cliente"
                   class="text-xs text-emerald-100 mt-1">
                </p>

            </div>


            <button type="button"
                    onclick="closeExitoModal()"
                    class="text-white hover:text-gray-200 text-lg">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        {{-- CONTENIDO --}}
        <form id="exito-form"
              method="POST"
              class="p-6">

            @csrf


            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-5 text-sm text-emerald-900">

                <div class="flex items-start gap-3">

                    <i class="fa-solid fa-circle-check text-xl mt-0.5"></i>

                    <div>

                        <p class="font-bold mb-1">
                            ¿Confirmar operación exitosa?
                        </p>

                        <p class="text-xs leading-relaxed">

                            El trámite será marcado como
                            <strong>Exitoso</strong>
                            y la operación será enviada a
                            <strong>Contabilidad</strong>
                            para su revisión y valoración económica.

                        </p>

                    </div>

                </div>

            </div>


            <div class="flex justify-end gap-2">

                <button type="button"
                        onclick="closeExitoModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-[#2C3E35] rounded-lg text-xs font-semibold">

                    Cancelar

                </button>


                <button type="submit"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow">

                    <i class="fa-solid fa-check mr-1"></i>

                    Sí, finalizar

                </button>

            </div>

        </form>

    </div>

</div>



{{-- ================================================================ --}}
{{-- MODAL FINALIZAR SIN ÉXITO --}}
{{-- ================================================================ --}}

<div id="sin-exito-modal"
     class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">

    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">


        {{-- CABECERA --}}
        <div class="bg-red-700 text-white px-6 py-4 flex justify-between items-center">

            <div>

                <h3 class="font-bold text-base">

                    <i class="fa-solid fa-circle-xmark mr-2"></i>

                    Finalizar trámite sin éxito

                </h3>

                <p id="sin-exito-cliente"
                   class="text-xs text-red-100 mt-1">
                </p>

            </div>


            <button type="button"
                    onclick="closeSinExitoModal()"
                    class="text-white hover:text-gray-200 text-lg">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        {{-- FORMULARIO --}}
        <form id="sin-exito-form"
              method="POST"
              class="p-6">

            @csrf


            <div class="mb-5">

                <label class="block text-xs font-bold text-[#2C3E35] mb-2">

                    Motivo por el cual no se concretó la transacción

                </label>


                <textarea name="entry_reason"
                          required
                          maxlength="2000"
                          rows="5"
                          class="w-full border border-[#D8D3C8] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Ejemplo: El cliente desistió, no obtuvo financiamiento, no llegó a un acuerdo, etc."></textarea>

            </div>


            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-800">

                <i class="fa-solid fa-triangle-exclamation mr-1"></i>

                El trámite será marcado como
                <strong>Sin Éxito</strong>
                y el cliente regresará a
                <strong>Cartera</strong>
                para seguimiento.

            </div>


            <div class="flex justify-end gap-2">

                <button type="button"
                        onclick="closeSinExitoModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-[#2C3E35] rounded-lg text-xs font-semibold">

                    Cancelar

                </button>


                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow">

                    <i class="fa-solid fa-folder-arrow-down mr-1"></i>

                    Enviar a Cartera

                </button>

            </div>

        </form>

    </div>

</div>



<script>

/*
|--------------------------------------------------------------------------
| MODAL ÉXITO
|--------------------------------------------------------------------------
*/

function openExitoModal(tramiteId, clienteNombre) {

    const modal =
        document.getElementById('exito-modal');

    const form =
        document.getElementById('exito-form');

    const cliente =
        document.getElementById('exito-cliente');


    form.action =
        `/intranet/client-tramites/${tramiteId}/con-exito`;


    cliente.textContent =
        `${clienteNombre} - Trámite #${tramiteId}`;


    modal.classList.remove('hidden');
}


function closeExitoModal() {

    document
        .getElementById('exito-modal')
        .classList
        .add('hidden');
}



/*
|--------------------------------------------------------------------------
| MODAL SIN ÉXITO
|--------------------------------------------------------------------------
*/

function openSinExitoModal(tramiteId, clienteNombre) {

    const modal =
        document.getElementById('sin-exito-modal');

    const form =
        document.getElementById('sin-exito-form');

    const cliente =
        document.getElementById('sin-exito-cliente');


    form.action =
        `/intranet/client-tramites/${tramiteId}/sin-exito`;


    cliente.textContent =
        `${clienteNombre} - Trámite #${tramiteId}`;


    modal.classList.remove('hidden');
}


function closeSinExitoModal() {

    const modal =
        document.getElementById('sin-exito-modal');

    modal.classList.add('hidden');


    const textarea =
        document.querySelector(
            '#sin-exito-form textarea[name="entry_reason"]'
        );

    if (textarea) {
        textarea.value = '';
    }
}


/*
|--------------------------------------------------------------------------
| CERRAR MODALES AL HACER CLIC FUERA
|--------------------------------------------------------------------------
*/

document
    .getElementById('exito-modal')
    .addEventListener('click', function(event) {

        if (event.target === this) {
            closeExitoModal();
        }

    });


document
    .getElementById('sin-exito-modal')
    .addEventListener('click', function(event) {

        if (event.target === this) {
            closeSinExitoModal();
        }

    });

</script>

@endsection