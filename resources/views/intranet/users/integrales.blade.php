@extends('layouts.admin')

@section('admin_content')
<div class="flex-grow p-6 max-w-7xl mx-auto w-full text-[#2C3E35] font-sans antialiased">
    
    <header class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-6 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1E392A]">Bandeja de Citas e Ingresos Integrales</h1>
            <p class="text-sm text-[#556B5D] mt-1">Monitoreo centralizado de prospectos desde la web (Contáctanos, Asesorías y Trámites).</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <!-- Botón para Añadir Manualmente -->
            <a href="{{ route('admin.citas.create') }}" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Registro
            </a>

            <!-- Filtros Rápidos por Canal / Tipo -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.citas-totales') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('filtro') ? 'bg-[#2C5E43] text-white' : 'bg-white text-[#2C3E35] border border-[#D8D3C8] hover:bg-gray-100' }}">
                    Todos
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'contacto']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filtro') == 'contacto' ? 'bg-[#2C5E43] text-white' : 'bg-white text-[#2C3E35] border border-[#D8D3C8] hover:bg-gray-100' }}">
                    Contáctanos
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'asesoria']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filtro') == 'asesoria' ? 'bg-[#2C5E43] text-white' : 'bg-white text-[#2C3E35] border border-[#D8D3C8] hover:bg-gray-100' }}">
                    Asesorías
                </a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'tramite']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filtro') == 'tramite' ? 'bg-[#2C5E43] text-white' : 'bg-white text-[#2C3E35] border border-[#D8D3C8] hover:bg-gray-100' }}">
                    Trámites
                </a>
            </div>
        </div>
    </header>

    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#2C5E43] text-white">
                        <th class="p-2.5 font-semibold">Origen / Canal</th>
                        <th class="p-2.5 font-semibold">Cliente / Solicitante</th>
                        <th class="p-2.5 font-semibold">Teléfono</th>
                        <th class="p-2.5 font-semibold">Referencia / Ubicación</th>
                        <th class="p-2.5 font-semibold">Estado</th>
                        <th class="p-2.5 font-semibold">Fecha de Registro</th>
                        <th class="p-2.5 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D8D3C8]">
                    @forelse($appointments as $item)
                        <tr class="hover:bg-[#F4F1EA] transition">
                            <!-- Origen / Canal -->
                            <td class="p-2.5 font-semibold">
                                <span class="px-2 py-1 rounded text-[10px] 
                                    {{ $item->origen_canal == 'Asesorías' ? 'bg-purple-100 text-purple-800' : ($item->origen_canal == 'Trámites' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $item->origen_canal }}
                                </span>
                                
                                @if(!empty($item->detalle_nota))
                                    <div class="text-[10px] text-gray-500 mt-1 font-normal">
                                        <span class="font-medium text-gray-700">Nota:</span> {{ Str::limit($item->detalle_nota, 30) }}
                                    </div>
                                @endif
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

                            <!-- Estado -->
                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded font-semibold text-[10px] 
                                    {{ in_array($item->status, ['Atendido', 'Confirmada', 'Gestionado']) ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $item->status ?? 'Pendiente' }}
                                </span>
                            </td>

                            <!-- Fecha de Registro -->
                            <td class="p-2.5 text-[#1E392A]">
                                {{ $item->fecha_registro ? \Carbon\Carbon::parse($item->fecha_registro)->format('d/m/Y H:i') : 'N/A' }}
                            </td>

                            <!-- Acciones -->
                            <td class="p-2.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    
                                    <!-- Botón Gestionar -->
                                    <form action="{{ route('admin.citas.gestionar', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Marcar como Gestionado" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-2.5 py-1 rounded-lg text-[11px] shadow transition flex items-center gap-1 font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Gestionar
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

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('admin.citas.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar" class="p-1 bg-red-100 hover:bg-red-200 text-red-800 rounded-md transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500">No hay registros centralizados todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection