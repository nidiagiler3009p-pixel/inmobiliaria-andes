@extends('layouts.admin')

@section('admin_content')
<div class="p-6 bg-[#f4f1ea] min-h-screen">
    <!-- Cabecera y Botón para Nueva Cita -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#2d4a3e]">Gestión de Agenda del Asesor</h1>
            <p class="text-sm text-gray-600">Agenda del Asesor: {{ auth()->name ?? 'Administrador' }}</p>
        </div>
        <button onclick="document.getElementById('modal-nueva-cita').classList.remove('hidden')" class="bg-[#2d4a3e] text-white px-4 py-2 rounded-lg shadow hover:bg-[#3e6353] transition">
            + Nueva Cita / Tarea
        </button>
    </div>

    <!-- Mensaje de Éxito -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Columnas de Citas y Asignaciones -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Próximas Citas -->
        <div class="bg-white p-5 rounded-xl shadow-md border border-[#e2dcc8]">
            <h3 class="font-bold text-lg mb-4 text-[#2d4a3e]">Próximas Citas y Tareas</h3>
            <div class="space-y-3 overflow-y-auto max-h-96 pr-2">
                @forelse($appointments as $appointment)
                    <div class="p-3 bg-[#f9f8f6] border-l-4 {{ $appointment->priority == 'urgente' || $appointment->type == 'urgente' ? 'border-red-500' : 'border-[#2d4a3e]' }} rounded flex justify-between items-center shadow-sm">
                        <div>
                            <span class="font-semibold text-xs text-gray-500 block">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y H:i') }}
                            </span>
                            <p class="text-sm text-[#2d4a3e]">
                                <strong>{{ ucfirst($appointment->type) }}:</strong> {{ $appointment->location_reference ?? 'Sin referencia' }}
                            </p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded font-medium {{ $appointment->status == 'Confirmada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $appointment->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-6">No hay citas registradas en la agenda.</p>
                @endforelse
            </div>
        </div>

        <!-- Asignaciones Pendientes -->
        <div class="bg-white p-5 rounded-xl shadow-md border border-[#e2dcc8]">
            <h3 class="font-bold text-lg mb-4 text-[#2d4a3e]">Asignaciones Pendientes</h3>
            <div class="space-y-3 overflow-y-auto max-h-96 pr-2">
                @forelse($appointments->where('status', 'Pendiente') as $pending)
                    <div class="p-3 bg-[#f9f8f6] rounded border border-[#e2dcc8] flex justify-between items-center shadow-sm">
                        <span class="text-sm text-[#2d4a3e]"><strong>{{ ucfirst($pending->type) }}:</strong> {{ $pending->location_reference }}</span>
                        <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded font-medium">Pendiente</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-6">No hay tareas pendientes por realizar.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nueva Cita / Tarea -->
<div id="modal-nueva-cita" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-[#e2dcc8]">
        <h2 class="text-xl font-bold text-[#2d4a3e] mb-4">Registrar Nueva Cita o Tarea</h2>
        
        <form action="{{ route('admin.agenda.store') }}" method="POST" class="space-y-4">
            @csrf
            <!-- ID del asesor logueado -->
            <input type="hidden" name="user_id" value="{{ auth()->id() ?? 1 }}">

            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de Actividad</label>
                <select name="type" class="w-full mt-1 border rounded-lg p-2 border-[#e2dcc8] focus:ring-[#2d4a3e] focus:border-[#2d4a3e]">
                    <option value="visita">Visita</option>
                    <option value="reunion">Reunión</option>
                    <option value="tarea">Tarea</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                <select name="priority" class="w-full mt-1 border rounded-lg p-2 border-[#e2dcc8] focus:ring-[#2d4a3e] focus:border-[#2d4a3e]">
                    <option value="normal">Normal</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente (Activa Alerta y Correo)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
                <input type="datetime-local" name="appointment_date" class="w-full mt-1 border rounded-lg p-2 border-[#e2dcc8]" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Referencia / Ubicación</label>
                <input type="text" name="location_reference" placeholder="Ej: Prop. 12 - Calle Falsa 123" class="w-full mt-1 border rounded-lg p-2 border-[#e2dcc8]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="status" class="w-full mt-1 border rounded-lg p-2 border-[#e2dcc8]">
                    <option value="Confirmada">Confirmada</option>
                    <option value="Pendiente">Pendiente</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notas</label>
                <textarea name="notes" rows="2" class="w-full mt-1 border rounded-lg p-2 border-[#e2dcc8]"></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-nueva-cita').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 hover:bg-gray-300 transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#2d4a3e] text-white rounded-lg hover:bg-[#3e6353] transition">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection