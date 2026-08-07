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
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Asesor Asignado:</label>
                <select name="user_id" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-sm text-[#2C3E35]">
                    <option value="">Todos los asesores</option>
                    @foreach($asesores as $asesor)
                        <option value="{{ $asesor->id }}" {{ request('user_id') == $asesor->id ? 'selected' : '' }}>
                            {{ $asesor->name }} {{ $asesor->last_name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Desde:</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-1.5 text-sm">
            </div>
            <div class="flex flex-col text-xs">
                <label class="font-semibold text-[#1E392A] mb-1">Hasta:</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-1.5 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow transition h-[34px]">
                    <i class="fa-solid fa-filter mr-1"></i> Filtrar
                </button>
            </div>
        </div>

        <div class="w-full lg:w-auto flex justify-end">
            <button type="button" onclick="openModal('new')" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Ingresar Nueva Cita Manual
            </button>
        </div>
    </form>

    <!-- TABLA DE CITAS -->
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden mb-8">
        <div class="flex justify-between items-center mb-4 px-2">
            <h2 class="text-lg font-bold text-[#1E392A]"><i class="fa-solid fa-list-check text-[#2C5E43] mr-2"></i> Listado General de Citas y Prospectos</h2>
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
                            <td class="p-2.5 font-mono font-bold text-[#2C5E43]">
                                PROP-{{ $appointment->property_id ?? 'N/A' }}
                            </td>
                            <td class="p-2.5">
                                {{ $appointment->user ? $appointment->user->name . ' ' . ($appointment->user->last_name ?? '') : 'Sin Asesor' }}
                            </td>
                            <td class="p-2.5 font-medium">
                                {{ $appointment->client ? $appointment->client->name . ' ' . ($appointment->client->last_name ?? '') : 'Sin Cliente' }}
                            </td>
                            <td class="p-2.5">
                                {{ $appointment->client ? $appointment->client->phone : 'N/A' }}
                            </td>
                            <td class="p-2.5">
                                <span class="bg-blue-50 text-blue-800 px-1.5 py-0.5 rounded text-[10px] font-semibold">
                                    {{ $appointment->source_channel ?? 'Web' }}
                                </span>
                            </td>
                            <td class="p-2.5">
                                {{ $appointment->location_reference ?? 'Matriz / Oficina' }}
                            </td>
                            <td class="p-2.5 font-semibold text-[#1E392A]">
                                {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y H:i') : 'Sin fecha fija' }}
                            </td>
                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded font-semibold text-[10px]
                                    {{ $appointment->priority == 'urgente' ? 'bg-red-100 text-red-800' : ($appointment->priority == 'alta' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($appointment->priority ?? 'normal') }}
                                </span>
                            </td>
                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded font-semibold text-[10px] 
                                    {{ $appointment->status == 'Confirmada' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td class="p-2.5 text-gray-600 max-w-xs truncate" title="{{ $appointment->notes }}">
                                {{ $appointment->notes ?: 'Sin observaciones' }}
                            </td>
                            <td class="p-2.5 text-center flex items-center justify-center gap-1.5">
                                <button type="button" onclick="openModal('edit', {
                                    id: '{{ $appointment->id }}',
                                    client_id: '{{ $appointment->client_id }}',
                                    user_id: '{{ $appointment->user_id }}',
                                    property_id: '{{ $appointment->property_id }}',
                                    appointment_date: '{{ $appointment->appointment_date }}',
                                    location_reference: '{{ $appointment->location_reference }}',
                                    source_channel: '{{ $appointment->source_channel ?? '' }}',
                                    type: '{{ $appointment->type }}',
                                    priority: '{{ $appointment->priority }}',
                                    status: '{{ $appointment->status }}',
                                    notes: `{{ addslashes($appointment->notes) }}`
                                })" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white p-1.5 rounded-lg shadow transition" title="Modificar Datos">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                
                                <form action="{{ route('gestion.citas.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta cita?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-lg shadow transition" title="Eliminar Cita">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-4 text-center text-gray-500">No hay citas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="appointment-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        
        <div class="bg-[#2C5E43] text-white px-6 py-4 flex justify-between items-center">
            <h3 id="modal-title" class="font-bold text-base"><i class="fa-solid fa-calendar-plus mr-2"></i> Ingresar Nueva Cita</h3>
            <button type="button" onclick="closeModal()" class="text-white hover:text-gray-200 text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="appointment-form" method="POST" action="{{ route('gestion.citas.store') }}" class="p-6 overflow-y-auto space-y-4 text-xs">
            @csrf
            <div id="method-field"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- SECCIÓN DE CLIENTE -->
                <div class="md:col-span-2 bg-[#F9F7F2] p-4 rounded-xl border border-[#D8D3C8]">
                    <div class="flex justify-between items-center mb-3">
                        <label class="font-bold text-[#1E392A] text-sm"><i class="fa-solid fa-user-tag mr-1 text-[#2C5E43]"></i> Datos del Cliente / Prospecto</label>
                        <button type="button" onclick="toggleClientMode()" class="text-[#2C5E43] hover:underline font-semibold text-xs bg-white border border-[#2C5E43] px-3 py-1 rounded-lg shadow-sm">
                            <span id="toggle-client-text">Registrar nuevo cliente en lugar de seleccionar</span>
                        </button>
                    </div>

                    <!-- Opción A: Seleccionar existente -->
                    <div id="existing-client-container">
                        <label class="text-[#556B5D] block mb-1 font-medium">Seleccionar de la lista de clientes registrados:</label>
                        <select name="client_id" id="field-client-id" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[#2C3E35]">
                            <option value="">Seleccione un cliente...</option>
                            @isset($clients)
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} {{ $client->last_name ?? '' }} - Tel: {{ $client->phone }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <!-- Opción B: Crear nuevo cliente con todos sus datos -->
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
                    <label class="font-semibold text-[#1E392A] block mb-1">Asesor Asignado</label>
                    <select name="user_id" id="field-user" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[#2C3E35]" required>
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
                        @isset($properties)
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">PROP-{{ $property->id }} | {{ $property->title ?? 'Sin título' }}</option>
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
                    <label class="font-semibold text-[#1E392A] block mb-1">Lugar de Cita / Ubicación</label>
                    <input type="text" name="location_reference" id="field-location" placeholder="Ej. Oficina Quito / Riobamba / Guano" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                </div>

                <!-- FECHA Y HORA -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Fecha y Hora de Cita</label>
                    <input type="datetime-local" name="appointment_date" id="field-date" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                </div>

                <!-- TIPO DE GESTIÓN -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Tipo de Gestión</label>
                    <select name="type" id="field-type" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                        <option value="visita">Visita</option>
                        <option value="reunion">Reunión</option>
                        <option value="tarea">Tarea</option>
                    </select>
                </div>

                <!-- PRIORIDAD -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Prioridad</label>
                    <select name="priority" id="field-priority" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                        <option value="normal">Normal</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>

                <!-- ESTADO -->
                <div>
                    <label class="font-semibold text-[#1E392A] block mb-1">Estado</label>
                    <select name="status" id="field-status" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Atendida">Atendida</option>
                    </select>
                </div>
            </div>

            <!-- OBSERVACIONES / NOTAS -->
            <div class="mt-2">
                <label class="font-semibold text-[#1E392A] block mb-1">Observaciones / Notas</label>
                <textarea name="notes" id="field-notes" rows="2" placeholder="Escriba las notas u observaciones de la cita..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#D8D3C8]">
                <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-2 rounded-lg font-medium transition">Cancelar</button>
                <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-6 py-2 rounded-lg font-medium transition shadow">Guardar Cita</button>
            </div>
        </form>
    </div>
</div>

<script>
    let isNewClientMode = false;

    function toggleClientMode() {
        const existingContainer = document.getElementById('existing-client-container');
        const newContainer = document.getElementById('new-client-container');
        const toggleText = document.getElementById('toggle-client-text');
        
        isNewClientMode = !isNewClientMode;

        if (isNewClientMode) {
            newContainer.classList.remove('hidden');
            existingContainer.classList.add('hidden');
            toggleText.textContent = '← Volver a seleccionar cliente existente';
            document.getElementById('field-client-id').value = ''; // limpiar select
        } else {
            newContainer.classList.add('hidden');
            existingContainer.classList.remove('hidden');
            toggleText.textContent = 'Registrar nuevo cliente en lugar de seleccionar';
            // Limpiar inputs de nuevo cliente
            document.getElementById('field-new-name').value = '';
            document.getElementById('field-new-lastname').value = '';
            document.getElementById('field-new-phone').value = '';
            document.getElementById('field-new-email').value = '';
        }
    }

    function openModal(mode, data = {}) {
        const modal = document.getElementById('appointment-modal');
        const form = document.getElementById('appointment-form');
        const title = document.getElementById('modal-title');
        const methodField = document.getElementById('method-field');

        modal.classList.remove('hidden');

        // Resetear modo cliente al abrir
        if (isNewClientMode) {
            toggleClientMode();
        }

        if (mode === 'new') {
            title.innerHTML = '<i class="fa-solid fa-calendar-plus mr-2"></i> Ingresar Nueva Cita';
            form.action = "{{ route('gestion.citas.store') }}";
            methodField.innerHTML = '';
            form.reset();
        } else if (mode === 'edit') {
            title.innerHTML = '<i class="fa-solid fa-user-pen mr-2"></i> Modificar Cita';
            form.action = "/intranet/citas/" + data.id; 
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('field-client-id').value = data.client_id || '';
            document.getElementById('field-user').value = data.user_id || '';
            document.getElementById('field-property').value = data.property_id || '';
            document.getElementById('field-source-channel').value = data.source_channel || '';
            document.getElementById('field-location').value = data.location_reference || '';
            document.getElementById('field-date').value = data.appointment_date ? data.appointment_date.replace(' ', 'T').slice(0, 16) : '';
            document.getElementById('field-type').value = data.type || 'visita';
            document.getElementById('field-priority').value = data.priority || 'normal';
            document.getElementById('field-status').value = data.status || 'Pendiente';
            document.getElementById('field-notes').value = data.notes || '';
        }
    }

    function closeModal() {
        document.getElementById('appointment-modal').classList.add('hidden');
    }
</script>
@endsection