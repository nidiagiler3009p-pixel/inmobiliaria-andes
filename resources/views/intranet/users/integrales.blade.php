@extends('layouts.admin')

@section('admin_content')
<!-- Tu header actual -->
<header class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-0 shadow-sm mb-6 overflow-hidden">
    
    <!-- Eliminamos el p-6 del header y lo ponemos solo aquí para que no haya margen extra -->
    <div class="bg-white rounded-2xl shadow-sm border-0">
        
        <!-- Fila Superior: Título, Descripción y Botón Nuevo -->
        <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Bandeja de Citas e Ingresos Integrales</h1>
                <p class="text-sm text-gray-500 mt-0.5">Monitoreo centralizado de prospectos web (Contáctanos, Asesorías y Trámites).</p>
            </div>
            
            <div>
                <a href="{{ route('admin.citas.create') }}"class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1E392A] text-white text-sm font-semibold rounded-xl hover:bg-[#15281e] shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                    Nuevo Registro
                </a>
            </div>
        </div>

        <!-- Línea Divisoria -->
        <div class="border-t border-gray-100"></div>

        <!-- Fila Inferior: Panel de Filtros -->
        <div class="bg-gray-50/70 p-5 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
            
            <!-- Grupo de Filtros: Canal -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Canal:
                </span>
                <a href="{{ route('admin.citas-totales', ['status' => request('status')]) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ !request('filtro') || request('filtro') == 'todos' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Todos
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'contacto', 'status' => request('status')]) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('filtro') == 'contacto' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Contáctanos
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'asesoria', 'status' => request('status')]) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('filtro') == 'asesoria' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Asesorías
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'tramite', 'status' => request('status')]) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('filtro') == 'tramite' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Trámites
                </a>
            </div>

            <!-- Grupo de Filtros: Estado -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Estado:
                </span>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro')]) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ !request('status') ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Todos
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro'), 'status' => 'Pendiente']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') == 'Pendiente' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Pendientes
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro'), 'status' => 'En Proceso']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') == 'En Proceso' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   En Proceso
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro'), 'status' => 'Completado']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request('status') == 'Completado' ? 'bg-[#1E392A] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                   Completados
                </a>
            </div>

        </div>
    </div>
</header>

<div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden">
        <div class="max-h-[65vh] overflow-y-auto overflow-x-auto relative">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="sticky top-0 z-20 bg-[#2C5E43] text-white shadow-sm">
                    <span class="text-xs text-[#556B5D] font-medium">Total de registros: {{ $appointments->count() }}</span>
                    <tr>
                        <th class="p-2.5 font-semibold">Origen / Canal</th>
                        <th class="p-2.5 font-semibold">Cliente / Solicitante</th>
                        <th class="p-2.5 font-semibold">Teléfono</th>
                        <th class="p-2.5 font-semibold">Referencia / Ubicación</th>
                        <th class="p-2.5 font-semibold">Detalles</th>
                        <th class="p-2.5 font-semibold">Estado</th>
                        <th class="p-2.5 font-semibold">Fecha de Registro</th>
                        <th class="p-2.5 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D8D3C8]"></tbody>
    @forelse($appointments as $item)
        @php
            $estadoActual = $item->status ?? 'Pendiente';
            
            if($estadoActual == 'Nuevo' || empty($estadoActual)) {
                $estadoActual = 'Pendiente';
            }

            // Traducimos 'Proces' o 'En Proceso' a los colores y textos visuales
            if ($estadoActual == 'En Proceso' || $estadoActual == 'Proces') {
                $badgeClass = 'bg-amber-100 text-amber-800';
                $btnClass = 'bg-amber-600 hover:bg-amber-700 text-white';
                $btnText = 'En Proceso';
                $estadoActual = 'En Proceso';
            } elseif ($estadoActual == 'Completado') {
                $badgeClass = 'bg-emerald-100 text-emerald-800';
                $btnClass = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                $btnText = 'Completado';
            } else {
                $estadoActual = 'Pendiente';
                $badgeClass = 'bg-rose-100 text-rose-800';
                $btnClass = 'bg-rose-600 hover:bg-rose-700 text-white';
                $btnText = 'Gestionar';
            }
        @endphp
        <tr class="hover:bg-[#F4F1EA] transition">
            <!-- Origen / Canal -->
            <td class="p-2.5 font-semibold">
                <span class="px-2 py-1 rounded text-[10px] 
                    {{ $item->origen_canal == 'Asesorías' ? 'bg-purple-100 text-purple-800' : ($item->origen_canal == 'Trámites' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ $item->origen_canal }}
                </span>
            </td>

            <!-- Cliente / Solicitante -->
            <td class="p-2.5 font-medium">
                {{ trim($item->nombre_cliente) !== '' ? $item->nombre_cliente : 'Prospecto Web' }}
            </td>

            <!-- Teléfono -->
            <td class="p-2.5">
                {{ $item->telefono_cliente ?? 'N/A' }}
            </td>

            <!-- Referencia / Ubicación -->
            <td class="p-2.5 max-w-xs truncate" title="{{ $item->detalle_ubicacion }}">
                {{ $item->detalle_ubicacion ?? 'N/A' }}
            </td>

            <!-- Detalle / Notas (NUEVA COLUMNA) -->
            <td class="p-2.5 max-w-xs truncate text-gray-600" title="{{ $item->detalle_nota ?? 'Sin detalles' }}">
                {{ Str::limit($item->detalle_nota ?? 'Sin detalles', 40) }}
            </td>

            <!-- Estado -->
            <td class="p-2.5">
                <span class="px-2 py-0.5 rounded font-semibold text-[10px] {{ $badgeClass }}">
                    {{ $estadoActual }}
                </span>
            </td>

            <!-- Fecha de Registro -->
            <td class="p-2.5 text-[#1E392A]">
                {{ $item->fecha_registro ? \Carbon\Carbon::parse($item->fecha_registro)->format('d/m/Y H:i') : 'N/A' }}
            </td>

            <!-- Acciones -->
            <td class="p-2.5 text-center">
                <div class="flex items-center justify-center gap-1.5">
                    
                    <!-- Botón Gestionar con cambio cíclico de color y estado -->
                    <form action="{{ route('admin.citas.gestionar', $item->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Avanzar Estado" class="{{ $btnClass }} px-2.5 py-1 rounded-lg text-[11px] shadow transition flex items-center gap-1 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $btnText }}
                        </button>
                    </form>

                    <!-- Botón Editar -->
                    <a href="{{ route('admin.citas.edit', $item->id) }}" title="Editar registro" class="p-1 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>

                    <!-- Botón Exportar / Ficha -->
                    <a href="{{ route('admin.citas.exportar', $item->id) }}" title="Exportar / Ver Ficha" class="p-1 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>

<!-- Botón Reciclar / Papelera -->
                    <form action="{{ route('admin.integrales.destroy', $item->id) }}" method="POST" onsubmit="return confirmarReciclaje(this);"> 
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Reciclar" class="p-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-md transition">
                            <!-- Icono de Reciclar / Papelera / Archivar -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </form>

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="p-6 text-center text-gray-500">No hay registros centralizados todavía.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</div>
<!-- Separador visual del Historial -->
<div class="mt-12 mb-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Historial de Registros Reciclados</h3>
            <p class="text-xs text-gray-500">Listado de prospectos eliminados. Puedes restaurarlos o borrarlos permanentemente.</p>
        </div>
    </div>
</div>

<!-- Tarjeta de la Tabla de Historial Reciclado -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <!-- Cabecera de la tabla -->
            <thead>
                <tr class="bg-[#1E392A] text-white text-xs uppercase tracking-wider">
                    <th class="py-3.5 px-4 font-semibold">Origen / Canal</th>
                    <th class="py-3.5 px-4 font-semibold">Cliente / Solicitante</th>
                    <th class="py-3.5 px-4 font-semibold">Teléfono</th>
                    <th class="py-3.5 px-4 font-semibold">Referencia / Ubicación</th>
                    <th class="py-3.5 px-4 font-semibold">Fecha de Eliminación</th>
                    <th class="py-3.5 px-4 font-semibold text-center">Acciones</th>
                </tr>
            </thead>
            
            <!-- Cuerpo de la tabla -->
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @forelse($citasRecicladas ?? [] as $item)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-3.5 px-4">
                            <span class="inline-flex px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">
                                {{ $item->origen ?? 'Canal' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-gray-900">{{ $item->cliente }}</td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $item->telefono }}</td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $item->referencia }}</td>
                        <td class="py-3.5 px-4 text-gray-500 text-xs">
                            {{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="inline-flex items-center justify-center gap-2">
                                
                                <!-- Botón Restaurar -->
                                <form action="{{ route('admin.citas.restaurar', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            onclick="return confirm('¿Desea restaurar este registro? Los datos serán restaurados exitosamente.')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition" 
                                            title="Restaurar registro">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Restaurar
                                    </button>
                                </form>

                                <!-- Botón Borrar Permanentemente -->
<!-- Botón Borrar con confirmación personalizada Sí / No -->
<form action="{{ route('admin.citas.forzar-eliminar', $item->id) }}" method="POST" class="inline" id="delete-form-{{ $item->id }}">
    @csrf
    @method('DELETE')
    <button type="button" 
            onclick="confirmarBorrado('{{ $item->id }}')"
            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 rounded-lg text-xs font-semibold transition" 
            title="Eliminar permanentemente">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        Borrar
    </button>
</form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400 text-sm">
                            No hay elementos en el historial de reciclaje.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
function confirmarBorrado(id) {
    // Ventana de confirmación con texto personalizado
    let seguro = confirm("¡Advertencia! Los datos se borrarán permanentemente del sistema.\n\nPresione 'Aceptar' (Sí, borrar) o 'Cancelar' (No, cancelar).");
    
    if (seguro) {
        // Si el usuario acepta, envía el formulario correspondiente
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
<script>
    function confirmarReciclaje(form) {
        var confirmacion = confirm("¿Deseas reciclar el Dato?\n\n- Si Reciclar\n- No cancelar");
        return confirmacion; // Si le da a Aceptar envía el formulario, si es Cancelar lo detiene
    }
</script>
@endsection