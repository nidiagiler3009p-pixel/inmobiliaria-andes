@extends('layouts.admin')

@section('admin_content')
<div class="flex-grow p-6 max-w-7xl mx-auto w-full text-[#2C3E35] font-sans antialiased">

    <!-- ENCABEZADO -->
    <header class="relative bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-6 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="absolute left-1/2 -translate-x-1/2 -top-5 bg-[#2C5E43] text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md border-4 border-[#F9F7F2]">
            <i class="fa-solid fa-handshake text-lg"></i>
        </div>
        <div class="text-center w-full pt-2">
            <h1 class="text-2xl font-bold text-[#1E392A]">Gestión de Citas Inmobiliarias</h1>
            <p class="text-sm text-[#556B5D] mt-1">Control integral de prospectos y citas. Cobertura: Riobamba, Guano y Quito.</p>
        </div>
    </header>

    <!-- FILTROS -->
    <form method="GET" action="{{ route('gestion.citas') }}" class="bg-[#EFECE6] border border-[#D8D3C8] rounded-xl p-4 mb-6 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            
            <!-- Asesor Asignado -->
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Asesor Asignado:</label>
                <select name="advisor_id" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-sm text-[#2C3E35]">
                    <option value="">Todos los asesores</option>
                    @foreach($asesores as $asesor)
                        <option value="{{ $asesor->id }}" {{ request('advisor_id') == $asesor->id ? 'selected' : '' }}>
                            {{ $asesor->name }} {{ $asesor->last_name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Propiedad -->
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Propiedad:</label>
                <select name="property_id" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-sm text-[#2C3E35]">
                    <option value="">Todas las propiedades</option>
                    @foreach($propiedades as $prop)
                        <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>
                            {{ $prop->code ?? 'PROP-'.$prop->id }} - {{ Str::limit($prop->title ?? 'Sin título', 25) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Estado:</label>
                <select name="status" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-sm text-[#2C3E35]">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Agendado" {{ request('status') == 'Agendado' ? 'selected' : '' }}>Agendado</option>
                    <option value="Confirmada" {{ request('status') == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="Realizado" {{ request('status') == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                    <option value="Cancelado" {{ request('status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <!-- Fecha Desde -->
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Desde:</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-1.5 text-sm">
            </div>

            <!-- Fecha Hasta -->
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Hasta:</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-1.5 text-sm">
            </div>

            <!-- Botones -->
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow transition h-[34px]">
                    <i class="fa-solid fa-filter mr-1"></i> Filtrar
                </button>
                @if(request()->anyFilled(['advisor_id', 'property_id', 'status', 'desde', 'hasta']))
                    <a href="{{ route('gestion.citas') }}" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-3 py-2 rounded-lg text-xs font-semibold shadow transition h-[34px] flex items-center">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Limpiar
                    </a>
                @endif
            </div>
        </div>

        <div class="w-full lg:w-auto flex justify-end">
            <button type="button" onclick="openCreateModal()" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Ingresar Nueva Cita Manual
            </button>
        </div>
    </form>

    <!-- MENSAJES DE ALERTA -->
    @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-800 px-4 py-3 rounded-xl mb-6 text-xs flex items-center justify-between">
            <span><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-xl mb-6 text-xs flex items-center justify-between">
            <span><i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-800 font-bold">&times;</button>
        </div>
    @endif

    <!-- TABLA DE CITAS -->
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden mb-8">
        <div class="flex justify-between items-center mb-4 px-2">
            <h2 class="text-lg font-bold text-[#1E392A]"><i class="fa-solid fa-list-check text-[#2C5E43] mr-2"></i> Listado General de Citas y Prospectos</h2>
            <span class="text-xs text-[#556B5D] font-medium">Total de registros: {{ $appointments->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#2C5E43] text-white border-b border-[#1E392A]">
                        <th class="p-2.5 font-semibold">Propiedad</th>
                        <th class="p-2.5 font-semibold">Asesor</th>
                        <th class="p-2.5 font-semibold">Cliente / Prospecto</th>
                        <th class="p-2.5 font-semibold">Teléfono</th>
                        <th class="p-2.5 font-semibold">Canal</th>
                        <th class="p-2.5 font-semibold">Lugar / Ubicación</th>
                        <th class="p-2.5 font-semibold">Fecha Cita</th>
                        <th class="p-2.5 font-semibold">Prioridad</th>
                        <th class="p-2.5 font-semibold">Estado</th>
                        <th class="p-2.5 font-semibold">Observaciones</th>
                        <th class="p-2.5 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D8D3C8]">
                    @forelse($appointments as $appointment) 
                        <tr class="hover:bg-[#F4F1EA] transition">
                            
                            <!-- 1. PROPIEDAD -->
                            <td class="p-2.5">
                                @if($appointment->property)
                                    <div class="font-bold text-[#2C5E43]">
                                        {{ $appointment->property->title ?? $appointment->property->code ?? 'PROP-' . $appointment->property_id }}
                                    </div>
                                    <div class="text-[10px] text-gray-500">
                                         {{ $appointment->property->owner_name ?? $appointment->property->user->name ?? 'N/A' }}
                                    </div>
                                @else
                                    <span class="text-gray-500 font-mono italic">
                                        {{ $appointment->property_id ? 'PROP-' . $appointment->property_id : 'N/A' }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-2.5">
                                {{ $appointment->user ? $appointment->user->name . ' ' . ($appointment->user->last_name ?? '') : 'Sin Asesor' }}
<td class="p-2.5 font-medium">

    @php
        $persona = $appointment->prospect ?? $appointment->client;
    @endphp

    @if($persona)

        {{ $persona->first_name ?? $persona->name ?? '' }}
        {{ $persona->last_name ?? '' }}

    @else

        <span class="text-gray-500 italic">
            Sin Cliente
        </span>

    @endif

</td>

<td class="p-2.5">

    {{ $appointment->prospect->phone
        ?? $appointment->client->phone
        ?? $appointment->phone
        ?? 'N/A'
    }}

</td>

                            <td class="p-2.5">
                                <span class="bg-blue-50 text-blue-800 border border-blue-200 px-1.5 py-0.5 rounded text-[10px] font-semibold">
                                    {{ $appointment->channel ?? $appointment->source_channel ?? 'Web' }}
                                </span>
                            </td>

                            <!-- 2. LUGAR / UBICACIÓN -->
<td class="p-2.5">
    <div class="flex flex-col gap-1">
        
        <!-- 1. REFERENCIA DEL CLIENTE (Destacado en primer lugar y más grande) -->
        @if($appointment->location_reference)
            <div class="flex items-center gap-1.5 font-semibold text-xs text-[#1E392A]">
                <!-- Icono de Pin / Punto de Encuentro -->
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ $appointment->location_reference }}</span>
            </div>
        @else
            <div class="flex items-center gap-1.5 text-xs text-gray-400 italic">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Por confirmar</span>
            </div>
        @endif

        <!-- 2. UBICACIÓN DE LA PROPIEDAD (Secundario, más pequeño y en tono gris) -->
        @if($appointment->property)
            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                <!-- Icono de Casa / Inmueble -->
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ $appointment->property->address ?? $appointment->property->city ?? 'Ubicación de propiedad' }}</span>
            </div>
        @endif

    </div>
</td>

                            <td class="p-2.5 font-semibold text-[#1E392A]">
                                {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y H:i') : 'Por Confirmar' }}
                            </td>

                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded font-semibold text-[10px]
                                    {{ strtolower($appointment->priority) == 'urgente' ? 'bg-red-100 text-red-800 border border-red-200' : (strtolower($appointment->priority) == 'alta' ? 'bg-orange-100 text-orange-800 border border-orange-200' : 'bg-gray-100 text-gray-800 border border-gray-200') }}">
                                    {{ ucfirst($appointment->priority ?? 'normal') }}
                                </span>
                            </td>

                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded font-semibold text-[10px] 
                                    {{ $appointment->status == 'Confirmada' || $appointment->status == 'Realizado' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($appointment->status == 'Cancelado' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>

                            <!-- 3. OBSERVACIONES -->
                            <td class="p-2.5 text-gray-600 max-w-xs whitespace-normal break-words">
                                {!! $appointment->notes ? nl2br(e($appointment->notes)) : 'Sin observaciones' !!}
                            </td>

                            <td class="p-2.5 text-center flex items-center justify-center gap-1.5">
                                <!-- Cambiar Estado -->
                                <button type="button" 
                                    onclick="openStatusModal('{{ $appointment->id }}', '{{ $appointment->status }}', '{{ addslashes($appointment->cancellation_reason ?? '') }}', {{ $appointment->rescued_to_portfolio ? 'true' : 'false' }})"
                                    class="bg-amber-600 hover:bg-amber-700 text-white p-1.5 rounded-lg shadow transition" 
                                    title="Cambiar Estado">
                                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                </button>

                                <!-- Modificar Datos -->
                                <button type="button" 
                                    data-appointment='@json($appointment)'
                                    onclick="openEditModal(this)" 
                                    class="bg-[#2C5E43] hover:bg-[#1E392A] text-white p-1.5 rounded-lg shadow transition" 
                                    title="Modificar Datos">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                {{-- PASAR A CARTERA --}}
@if($appointment->client_id || $appointment->prospect_id)

    @if($appointment->portfolioEntry)

        {{-- YA ESTÁ EN CARTERA --}}
        <button
            type="button"
            disabled
            class="bg-emerald-100 text-emerald-700 border border-emerald-300 p-1.5 rounded-lg cursor-not-allowed"
            title="Este cliente ya fue enviado a Cartera"
        >
            <i class="fa-solid fa-check text-xs"></i>
        </button>

    @else

        {{-- ENVIAR A CARTERA --}}
        <button
            type="button"
            onclick="openPortfolioModal(this)"
            data-appointment-id="{{ $appointment->id }}"
            data-client-id="{{ $appointment->client_id }}"
            data-client-name="{{
    trim(
        (
            $appointment->prospect->first_name
            ?? $appointment->prospect->name
            ?? $appointment->client->first_name
            ?? $appointment->client->name
            ?? ''
        )
        . ' ' .
        (
            $appointment->prospect->last_name
            ?? $appointment->client->last_name
            ?? ''
        )
    )
}}"
            data-property-name="{{ $appointment->property->title ?? 'Sin propiedad específica' }}"
            data-advisor-name="{{ trim(($appointment->user->name ?? '') . ' ' . ($appointment->user->last_name ?? '')) }}"
            data-channel="{{ $appointment->source_channel ?? $appointment->channel ?? '' }}"
            class="bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded-lg shadow transition"
            title="Pasar a Cartera"
        >
            <i class="fa-solid fa-folder-plus text-xs"></i>
        </button>

    @endif

@else

    {{-- SIN CLIENTE ASOCIADO --}}
    <button
        type="button"
        disabled
        class="bg-gray-200 text-gray-400 p-1.5 rounded-lg cursor-not-allowed"
        title="La cita no tiene un cliente registrado"
    >
        <i class="fa-solid fa-folder-plus text-xs"></i>
    </button>

@endif
                                <!-- CONDICIÓN DE BORRADO: Solo visible para el rol AMINISTRADOR/GERENTE -->
                                @if(auth()->user()->role === 'Administrador/Gerente' || (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('Administrador/Gerente')))
<form
    id="delete-form-{{ $appointment->id }}"
    action="{{ route('gestion.citas.destroy', $appointment->id) }}"
    method="POST"
    class="inline"
>
    @csrf
    @method('DELETE')

    <button
        type="button"
        onclick="openDeleteModal('{{ $appointment->id }}')"
        class="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-lg shadow transition"
        title="Eliminar Cita"
    >
        <i class="fa-solid fa-trash text-xs"></i>
    </button>
</form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-6 text-center text-gray-500">
                                <i class="fa-solid fa-calendar-xmark text-2xl mb-2 text-[#556B5D] block"></i>
                                No hay citas registradas con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL REGISTRO / EDICIÓN DE CITA -->
<div id="appointment-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        <div class="bg-[#2C5E43] text-white px-6 py-4 flex justify-between items-center">
            <h3 id="modal-title" class="font-bold text-base"><i class="fa-solid fa-calendar-plus mr-2"></i> Ingresar Nueva Cita</h3>
            <button type="button" onclick="closeModal('appointment-modal')" class="text-white hover:text-gray-200 text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="appointment-form" method="POST" action="{{ route('gestion.citas.store') }}" class="p-6 overflow-y-auto space-y-4 text-xs">
            @csrf
            <div id="method-field"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- SECCIÓN DE CLIENTE -->
                <div class="md:col-span-2 bg-[#F9F7F2] p-4 rounded-xl border border-[#D8D3C8]">
                    <div class="flex justify-between items-center mb-3">
                        <label class="font-bold text-[#1E392A] text-sm"><i class="fa-solid fa-user-tag mr-1 text-[#2C5E43]"></i> Datos del Cliente / Prospecto</label>
                        <button type="button" id="toggle-client-btn" onclick="toggleClientMode()" class="text-[#2C5E43] hover:underline font-semibold text-xs bg-white border border-[#2C5E43] px-3 py-1 rounded-lg shadow-sm">
                            <span id="toggle-client-text">Registrar nuevo cliente en lugar de seleccionar</span>
                        </button>
                    </div>

                    <!-- Opción A: Seleccionar existente -->
                    <div id="existing-client-container">
                        <label class="text-[#556B5D] block mb-1 font-medium">Seleccionar de la lista de clientes registrados:</label>
                        <select name="client_id" id="field-client-id" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[#2C3E35]">
                            <option value="">Seleccione un cliente...</option>
                            @isset($clientes)
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">
                                        {{ $cliente->first_name ?? $cliente->name }} {{ $cliente->last_name ?? '' }} - Tel: {{ $cliente->phone }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <!-- Opción B: Crear nuevo cliente -->
                    <div id="new-client-container" class="hidden space-y-3">
                        <p class="text-[11px] text-[#2C5E43] font-semibold italic">Ingrese los datos completos del nuevo cliente para guardarlo automáticamente en el sistema:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-[#1E392A] mb-1">Nombres *</label>
                                <input type="text" name="client_name" id="field-new-name" placeholder="Ej. Juan Carlos" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="block font-semibold text-[#1E392A] mb-1">Apellidos</label>
                                <input type="text" name="client_last_name" id="field-new-lastname" placeholder="Ej. Pérez Gómez" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="block font-semibold text-[#1E392A] mb-1">Teléfono / Celular *</label>
                                <input type="text" name="client_phone" id="field-new-phone" placeholder="Ej. 0989059188" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="block font-semibold text-[#1E392A] mb-1">Correo Electrónico</label>
                                <input type="email" name="client_email" id="field-new-email" placeholder="Ej. correo@ejemplo.com" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ASESOR ASIGNADO -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Asesor Asignado *</label>
                    <select name="user_id" id="field-user" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[#2C3E35]" required>
                        <option value="">Seleccione un asesor...</option>
                        @foreach($asesores as $asesor)
                            <option value="{{ $asesor->id }}">{{ $asesor->name }} {{ $asesor->last_name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PROPIEDAD DEL CATÁLOGO -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Propiedad del Catálogo</label>
                    <select name="property_id" id="field-property" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[#2C3E35]">
                        <option value="">Seleccione una propiedad...</option>
                        @isset($propiedades)
                            @foreach($propiedades as $propiedad)
                                <option value="{{ $propiedad->id }}">
                                    {{ $propiedad->code ?? 'PROP-'.$propiedad->id }} | {{ Str::limit($propiedad->title ?? 'Sin título', 30) }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <!-- CANAL DE CAPTACIÓN -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Canal de Captación</label>
                    <input type="text" name="source_channel" id="field-source-channel" placeholder="Ej. Web, Redes Sociales, WhatsApp" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                </div>

                <!-- LUGAR / UBICACIÓN -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Lugar de Cita / Ubicación *</label>
                    <input type="text" name="location_reference" id="field-location" placeholder="Ej. Oficina Quito / Riobamba / Guano" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2" required>
                </div>

                <!-- FECHA Y HORA -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Fecha y Hora de Cita</label>
                    <input type="datetime-local" name="appointment_date" id="field-date" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                </div>

                <!-- TIPO DE GESTIÓN -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Tipo de Gestión *</label>
                    <select name="type" id="field-type" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2" required>
                        <option value="visita">Visita</option>
                        <option value="reunion">Reunión</option>
                        <option value="tarea">Tarea</option>
                    </select>
                </div>

                <!-- PRIORIDAD -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Prioridad *</label>
                    <select name="priority" id="field-priority" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2" required>
                        <option value="normal">Normal</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>

                <!-- ESTADO -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Estado *</label>
                    <select name="status" id="field-status" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Agendado">Agendado</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Realizado">Realizado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
            </div>

            <!-- OBSERVACIONES / NOTAS -->
            <div class="mt-2">
                <label class="font-semibold text-[#1E392A] block mb-1">Observaciones / Notas</label>
                <textarea name="notes" id="field-notes" rows="2" placeholder="Escriba las notas u observaciones de la cita..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#D8D3C8]">
                <button type="button" onclick="closeModal('appointment-modal')" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-2 rounded-lg font-medium transition">Cancelar</button>
                <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-6 py-2 rounded-lg font-medium transition shadow">Guardar Cita</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CAMBIAR ESTADO / CANCELACIÓN -->
<div id="status-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col">
        <div class="bg-[#2C5E43] text-white px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-base"><i class="fa-solid fa-arrows-rotate mr-2"></i> Cambiar Estado de Cita</h3>
            <button type="button" onclick="closeModal('status-modal')" class="text-white hover:text-gray-200 text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="status-form" method="POST" action="" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <label class="font-semibold text-[#1E392A] block mb-1">Nuevo Estado *</label>
                <select name="status" id="status-select" onchange="toggleCancellationFields()" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[#2C3E35]" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Agendado">Agendado</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Realizado">Realizado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>

            <!-- Campos Condicionales para Cancelado -->
            <div id="cancellation-fields" class="hidden space-y-3 bg-[#F9F7F2] p-3 rounded-xl border border-[#D8D3C8]">
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Motivo de Cancelación *</label>
                    <textarea name="cancellation_reason" id="status-reason" rows="3" placeholder="Explique por qué se canceló la cita..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="rescue_to_portfolio" id="status-rescue" value="1" class="rounded border-[#D8D3C8] text-[#2C5E43] focus:ring-[#2C5E43]">
                    <label for="status-rescue" class="font-medium text-[#1E392A] text-xs">Rescatar cliente a Cartera General</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#D8D3C8]">
                <button type="button" onclick="closeModal('status-modal')" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-2 rounded-lg font-medium transition">Cancelar</button>
                <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-5 py-2 rounded-lg font-medium transition shadow">Actualizar Estado</button>
            </div>
        </form>
    </div>
</div>
{{-- MODAL PASAR A CARTERA --}}
<div
    id="portfolio-modal"
    class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
>
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">

        {{-- CABECERA --}}
        <div class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center">

            <h3 class="font-bold text-base">
                <i class="fa-solid fa-folder-plus mr-2"></i>
                Pasar a Cartera
            </h3>

            <button
                type="button"
                onclick="closeModal('portfolio-modal')"
                class="text-white hover:text-gray-200 text-lg"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <form
            id="portfolio-form"
            method="POST"
            action=""
            class="p-5 space-y-4 text-xs"
        >
            @csrf

            {{-- INFORMACIÓN AUTOMÁTICA --}}
            <div class="bg-white border border-[#D8D3C8] rounded-xl p-3 space-y-2">

                <div>
                    <span class="text-[10px] text-gray-500 font-bold uppercase">
                        Cliente
                    </span>

                    <p id="portfolio-client-name" class="font-bold text-[#1E392A]">
                        -
                    </p>
                </div>

                <div>
                    <span class="text-[10px] text-gray-500 font-bold uppercase">
                        Propiedad de interés
                    </span>

                    <p id="portfolio-property-name" class="font-semibold text-[#2C5E43]">
                        -
                    </p>
                </div>

                <div>
                    <span class="text-[10px] text-gray-500 font-bold uppercase">
                        Asesor
                    </span>

                    <p id="portfolio-advisor-name" class="font-semibold">
                        -
                    </p>
                </div>

            </div>


            {{-- MOTIVO --}}
            <div>
                <label class="font-semibold text-[#1E392A] block mb-1">
                    Motivo de ingreso a Cartera *
                </label>

                <textarea
                    name="entry_reason"
                    id="portfolio-reason"
                    rows="3"
                    required
                    placeholder="Ej. Cliente interesado, pero requiere seguimiento y nuevas opciones..."
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"
                ></textarea>
            </div>


            {{-- CANAL --}}
            <div>
                <label class="font-semibold text-[#1E392A] block mb-1">
                    Canal de contacto
                </label>

                <select
                    name="contact_channel"
                    id="portfolio-channel"
                    onchange="togglePortfolioSocialFields()"
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"
                >
                    <option value="">Seleccione...</option>
                    <option value="Sitio Web">Sitio Web</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Instagram">Instagram</option>
                    <option value="TikTok">TikTok</option>
                    <option value="Teléfono">Teléfono</option>
                    <option value="Correo">Correo</option>
                    <option value="Referido">Referido</option>
                    <option value="Presencial">Presencial</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>


            {{-- DATOS RED SOCIAL --}}
            <div
                id="portfolio-social-fields"
                class="hidden bg-white border border-blue-200 rounded-xl p-3 space-y-3"
            >

                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">
                        Red Social
                    </label>

                    <select
                        name="social_platform"
                        id="portfolio-social-platform"
                        class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"
                    >
                        <option value="">Seleccione...</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Instagram">Instagram</option>
                        <option value="TikTok">TikTok</option>
                        <option value="Otra">Otra</option>
                    </select>
                </div>


                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">
                        Link del perfil / cuenta
                    </label>

                    <input
                        type="url"
                        name="social_profile_url"
                        id="portfolio-social-url"
                        placeholder="https://instagram.com/usuario"
                        class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"
                    >
                </div>

            </div>


            {{-- ESTADO INICIAL --}}
            <div>
                <label class="font-semibold text-[#1E392A] block mb-1">
                    Estado inicial en Cartera *
                </label>

                <select
                    name="portfolio_status"
                    id="portfolio-status"
                    required
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"
                >
                    <option value="Nuevo">Nuevo</option>
                    <option value="Contactado">Contactado</option>
                    <option value="Seguimiento">Seguimiento</option>
                    <option value="Interesado">Interesado</option>
                    <option value="Negociación">Negociación</option>
                </select>
            </div>


            {{-- NOTAS --}}
            <div>
                <label class="font-semibold text-[#1E392A] block mb-1">
                    Observaciones adicionales
                </label>

                <textarea
                    name="notes"
                    id="portfolio-notes"
                    rows="2"
                    placeholder="Información adicional para el seguimiento..."
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"
                ></textarea>
            </div>


            <div class="flex justify-end gap-3 pt-3 border-t border-[#D8D3C8]">

                <button
                    type="button"
                    onclick="closeModal('portfolio-modal')"
                    class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-2 rounded-lg font-medium transition"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-medium shadow transition"
                >
                    <i class="fa-solid fa-folder-plus mr-1"></i>
                    Pasar a Cartera
                </button>

            </div>

        </form>

    </div>
</div>
{{-- MODAL CONFIRMAR ELIMINACIÓN --}}
<div
    id="delete-modal"
    class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
>

    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">

        {{-- ENCABEZADO --}}
        <div class="bg-red-700 text-white px-6 py-4 flex items-center justify-between">

            <h3 class="font-bold text-base flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Eliminar registro
            </h3>

            <button
                type="button"
                onclick="closeDeleteModal()"
                class="text-white hover:text-red-100 text-lg"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        {{-- CONTENIDO --}}
        <div class="p-6 text-center">

            <div class="w-14 h-14 mx-auto mb-4 bg-red-100 text-red-700 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-trash-can text-xl"></i>
            </div>

            <h4 class="font-bold text-[#1E392A] text-lg mb-2">
                ¿Desea eliminar esta cita?
            </h4>

            <p class="text-sm text-gray-600 leading-relaxed">
                Esta acción eliminará el registro permanentemente.
            </p>

            <p class="text-xs text-red-600 font-semibold mt-2">
                Esta acción no se puede deshacer.
            </p>

        </div>


        {{-- BOTONES --}}
        <div class="px-6 pb-6 flex justify-center gap-3">

            <button
                type="button"
                onclick="closeDeleteModal()"
                class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-5 py-2 rounded-lg font-semibold transition"
            >
                <i class="fa-solid fa-xmark mr-1"></i>
                No, regresar
            </button>

            <button
                type="button"
                onclick="confirmDeleteAppointment()"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition"
            >
                <i class="fa-solid fa-trash mr-1"></i>
                Sí, eliminar
            </button>

        </div>

    </div>
</div>

<script>

console.log('SCRIPT DE CITAS CARGADO CORRECTAMENTE');

let isNewClientMode = false;


/* =========================================================
   CLIENTE EXISTENTE / CLIENTE NUEVO
========================================================= */

function toggleClientMode() {

    const existingContainer =
        document.getElementById('existing-client-container');

    const newContainer =
        document.getElementById('new-client-container');

    const toggleText =
        document.getElementById('toggle-client-text');

    if (!existingContainer || !newContainer || !toggleText) {
        console.error('No se encontraron los campos de cliente.');
        return;
    }

    isNewClientMode = !isNewClientMode;

    if (isNewClientMode) {

        newContainer.classList.remove('hidden');
        existingContainer.classList.add('hidden');

        toggleText.textContent =
            '← Volver a seleccionar cliente existente';

        document.getElementById('field-client-id').value = '';

    } else {

        newContainer.classList.add('hidden');
        existingContainer.classList.remove('hidden');

        toggleText.textContent =
            'Registrar nuevo cliente en lugar de seleccionar';

        document.getElementById('field-new-name').value = '';
        document.getElementById('field-new-lastname').value = '';
        document.getElementById('field-new-phone').value = '';
        document.getElementById('field-new-email').value = '';
    }
}


/* =========================================================
   CREAR CITA
========================================================= */

function openCreateModal() {

    console.log('Abriendo modal crear cita');

    const modal =
        document.getElementById('appointment-modal');

    const form =
        document.getElementById('appointment-form');

    const title =
        document.getElementById('modal-title');

    const methodField =
        document.getElementById('method-field');

    if (!modal || !form || !title || !methodField) {
        console.error('No se encontró el modal de citas.');
        return;
    }

    if (isNewClientMode) {
        toggleClientMode();
    }

    form.reset();

    title.innerHTML =
        '<i class="fa-solid fa-calendar-plus mr-2"></i> Ingresar Nueva Cita Manual';

    form.action = "{{ route('gestion.citas.store') }}";

    methodField.innerHTML = '';

    const defaultUser = "{{ auth()->id() }}";

    if (defaultUser) {

        const userField =
            document.getElementById('field-user');

        if (userField) {
            userField.value = defaultUser;
        }
    }

    modal.classList.remove('hidden');
}


/* =========================================================
   EDITAR CITA
========================================================= */

function openEditModal(button) {

    console.log('Abriendo modal editar cita');

    const rawData =
        button.getAttribute('data-appointment');

    let data;

    try {

        data = JSON.parse(rawData);

    } catch (error) {

        console.error(
            'Error al leer los datos de la cita:',
            error,
            rawData
        );

        return;
    }

    const modal =
        document.getElementById('appointment-modal');

    const form =
        document.getElementById('appointment-form');

    const title =
        document.getElementById('modal-title');

    const methodField =
        document.getElementById('method-field');


    if (isNewClientMode) {
        toggleClientMode();
    }


    title.innerHTML =
        '<i class="fa-solid fa-user-pen mr-2"></i> Modificar Cita #' + data.id;

   form.action = "{{ url('/admin/citas') }}/" + data.id;

    methodField.innerHTML =
        '<input type="hidden" name="_method" value="PUT">';


    document.getElementById('field-client-id').value =
        data.client_id || '';

    /*
     * CORREGIDO:
     * antes decía field-[#field-user]
     */
    document.getElementById('field-user').value =
        data.user_id || '';

    document.getElementById('field-property').value =
        data.property_id || '';

    document.getElementById('field-source-channel').value =
        data.source_channel || data.channel || '';

    document.getElementById('field-location').value =
        data.location_reference || '';


    if (data.appointment_date) {

        document.getElementById('field-date').value =
            data.appointment_date
                .replace(' ', 'T')
                .substring(0, 16);

    } else {

        document.getElementById('field-date').value = '';
    }


    document.getElementById('field-type').value =
        data.type || 'visita';

    document.getElementById('field-priority').value =
        data.priority || 'normal';

    document.getElementById('field-status').value =
        data.status || 'Pendiente';

    document.getElementById('field-notes').value =
        data.notes || '';

    modal.classList.remove('hidden');
}


/* =========================================================
   CAMBIAR ESTADO
========================================================= */

function openStatusModal(
    id,
    currentStatus,
    reason = '',
    rescued = false
) {

    console.log('Abriendo estado de cita:', id);

    const modal =
        document.getElementById('status-modal');

    const form =
        document.getElementById('status-form');

    if (!modal || !form) {

        console.error(
            'No se encontró el modal de estado.'
        );

        return;
    }

    form.action = "{{ url('/intranet/citas') }}/" + id + "/estado";

    document.getElementById('status-select').value =
        currentStatus;

    document.getElementById('status-reason').value =
        reason;

    document.getElementById('status-rescue').checked =
        rescued;

    toggleCancellationFields();

    modal.classList.remove('hidden');
}


function toggleCancellationFields() {

    const statusSelect =
        document.getElementById('status-select');

    const cancellationFields =
        document.getElementById('cancellation-fields');

    const reasonInput =
        document.getElementById('status-reason');

    if (!statusSelect || !cancellationFields || !reasonInput) {
        return;
    }

    if (statusSelect.value === 'Cancelado') {

        cancellationFields.classList.remove('hidden');

        reasonInput.setAttribute(
            'required',
            'required'
        );

    } else {

        cancellationFields.classList.add('hidden');

        reasonInput.removeAttribute('required');
    }
}


/* =========================================================
   PASAR A CARTERA
========================================================= */

function openPortfolioModal(button) {

    console.log('Abriendo modal cartera');

    const appointmentId =
        button.dataset.appointmentId;

    const clientName =
        button.dataset.clientName || 'Cliente';

    const propertyName =
        button.dataset.propertyName ||
        'Sin propiedad específica';

    const advisorName =
        button.dataset.advisorName ||
        'Sin asesor';

    const channel =
        button.dataset.channel || '';


    const modal =
        document.getElementById('portfolio-modal');

    const form =
        document.getElementById('portfolio-form');


    if (!modal || !form) {

        console.error(
            'No se encontró el modal de cartera.'
        );

        return;
    }


    form.action =
        '/intranet/citas/' +
        appointmentId +
        '/cartera';


    document.getElementById(
        'portfolio-client-name'
    ).textContent = clientName;


    document.getElementById(
        'portfolio-property-name'
    ).textContent = propertyName;


    document.getElementById(
        'portfolio-advisor-name'
    ).textContent = advisorName;


    document.getElementById(
        'portfolio-reason'
    ).value = '';


    document.getElementById(
        'portfolio-notes'
    ).value = '';


    document.getElementById(
        'portfolio-status'
    ).value = 'Nuevo';


    const channelSelect =
        document.getElementById(
            'portfolio-channel'
        );


    const availableValues =
        Array.from(channelSelect.options)
            .map(option => option.value);


    if (availableValues.includes(channel)) {

        channelSelect.value = channel;

    } else {

        channelSelect.value = '';
    }


    document.getElementById(
        'portfolio-social-platform'
    ).value = '';


    document.getElementById(
        'portfolio-social-url'
    ).value = '';


    togglePortfolioSocialFields();

    modal.classList.remove('hidden');
}


function togglePortfolioSocialFields() {

    const channel =
        document.getElementById(
            'portfolio-channel'
        ).value;

    const socialFields =
        document.getElementById(
            'portfolio-social-fields'
        );

    const platform =
        document.getElementById(
            'portfolio-social-platform'
        );

    const profileUrl =
        document.getElementById(
            'portfolio-social-url'
        );


    const socialNetworks = [
        'Facebook',
        'Instagram',
        'TikTok'
    ];


    if (socialNetworks.includes(channel)) {

        socialFields.classList.remove('hidden');

        platform.value = channel;

    } else {

        socialFields.classList.add('hidden');

        platform.value = '';

        profileUrl.value = '';
    }
}
let deleteAppointmentId = null;


function openDeleteModal(id) {

    deleteAppointmentId = id;

    const modal = document.getElementById('delete-modal');

    if (modal) {
        modal.classList.remove('hidden');
    }
}


function closeDeleteModal() {

    const modal = document.getElementById('delete-modal');

    if (modal) {
        modal.classList.add('hidden');
    }

    deleteAppointmentId = null;
}


function confirmDeleteAppointment() {

    if (!deleteAppointmentId) {
        console.error('No se encontró el ID de la cita.');
        return;
    }

    const form = document.getElementById(
        'delete-form-' + deleteAppointmentId
    );

    if (!form) {
        console.error('No se encontró el formulario de eliminación.');
        return;
    }

    form.submit();
}

/* =========================================================
   CERRAR MODALES
========================================================= */

function closeModal(modalId) {

    const modal =
        document.getElementById(modalId);

    if (modal) {

        modal.classList.add('hidden');

    } else {

        console.error(
            'No existe el modal:',
            modalId
        );
    }
}

</script>
@endsection