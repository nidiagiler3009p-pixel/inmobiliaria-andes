@extends('layouts.admin')
@section('admin_content')

<div class="flex-grow p-6 max-w-7xl mx-auto w-full text-[#2C3E35] font-sans antialiased">

    {{-- ENCABEZADO --}}
    <header class="relative bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-6 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="absolute left-1/2 -translate-x-1/2 -top-5 bg-[#2C5E43] text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md border-4 border-[#F9F7F2]">
            <i class="fa-solid fa-inbox text-lg"></i>
        </div>
        <div class="text-center w-full pt-2">
            <h1 class="text-2xl font-bold text-[#1E392A]">Citas e Ingresos Integrales</h1>
            <p class="text-sm text-[#556B5D] mt-1">Gestión centralizada de prospectos provenientes de Contáctanos, Asesorías y Trámites.</p>
        </div>
    </header>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="mb-5 bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-sm">
            <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="font-black text-lg">×</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between text-sm">
            <span><i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="font-black text-lg">×</button>
        </div>
    @endif

    {{-- PANEL DE FILTROS --}}
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-xl p-4 mb-6 shadow-sm">
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-[#556B5D] mr-1"><i class="fa-solid fa-filter mr-1"></i>Origen:</span>
                <a href="{{ route('admin.citas-totales', ['status' => request('status')]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('filtro') || request('filtro') == 'todos' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Todos</a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'contacto', 'status' => request('status')]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filtro') == 'contacto' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Contáctanos</a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'asesoria', 'status' => request('status')]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filtro') == 'asesoria' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Asesorías</a>
                <a href="{{ route('admin.citas-totales', ['filtro' => 'tramite', 'status' => request('status')]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filtro') == 'tramite' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Trámites</a>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-[#556B5D] mr-1"><i class="fa-solid fa-circle-check mr-1"></i>Estado:</span>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro')]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Todos</a>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro'), 'status' => 'Pendiente']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') == 'Pendiente' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Pendiente</a>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro'), 'status' => 'En Proceso']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') == 'En Proceso' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">En Proceso</a>
                <a href="{{ route('admin.citas-totales', ['filtro' => request('filtro'), 'status' => 'Completado']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') == 'Completado' ? 'bg-[#2C5E43] text-white shadow' : 'bg-white border border-[#D8D3C8] text-[#2C3E35] hover:bg-gray-50' }}">Completado</a>
            </div>
        </div>
<div class="mt-4 flex flex-wrap items-center gap-2">

    <a href="{{ route('admin.citas.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#2C5E43] hover:bg-[#1E392A] text-white rounded-lg font-semibold text-xs shadow transition">
        <i class="fa-solid fa-circle-plus"></i>
        Ingresar Nuevo Registro
    </a>

    <a href="{{ route('admin.cartera') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-xs shadow transition">
        <i class="fa-solid fa-folder-open"></i>
        Ir a Cartera
    </a>

</div>

    {{-- TABLA PRINCIPAL --}}
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-2 mb-3">
            <h2 class="font-bold text-[#1E392A]"><i class="fa-solid fa-list-check mr-2 text-[#2C5E43]"></i>Listado General de Citas e Ingresos Integrales</h2>
            <span class="text-xs text-[#556B5D] font-medium">Total de registros: {{ $appointments->count() }}</span>
        </div>
        <div class="max-h-[65vh] overflow-y-auto overflow-x-auto relative">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="sticky top-0 z-20 bg-[#2C5E43] text-white shadow-sm">
                    <tr>
                        <th class="p-2.5 font-semibold">Origen / Canal</th>
                        <th class="p-2.5 font-semibold">Cliente / Solicitante</th>
                        <th class="p-2.5 font-semibold">Teléfono</th>
                        <th class="p-2.5 font-semibold">Referencia / Ubicación</th>
                        <th class="p-2.5 font-semibold">Detalles</th>
                        <th class="p-2.5 font-semibold">Estado</th>
                        <th class="p-2.5 font-semibold">Fecha</th>
                        <th class="p-2.5 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D8D3C8]">
                @forelse($appointments as $item)
                    @php
                        $estadoActual = $item->status ?? 'Pendiente';
                        if($estadoActual == 'Nuevo' || empty($estadoActual)) $estadoActual = 'Pendiente';
                        $badgeClass = $estadoActual == 'En Proceso' || $estadoActual == 'Proces' ? 'bg-amber-100 text-amber-800 border border-amber-200' : ($estadoActual == 'Completado' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200');
                        $sourceType = $item->source_type ?? null;
                        $sourceRecordId = $item->source_record_id ?? null;
                        if(!$sourceType){
                            if(str_starts_with($item->id, 'contact_')){ $sourceType = 'contact'; $sourceRecordId = preg_replace('/[^0-9]/','',$item->id); }
                            elseif(str_starts_with($item->id, 'advisory_')){ $sourceType = 'advisory'; $sourceRecordId = preg_replace('/[^0-9]/','',$item->id); }
                            elseif(str_starts_with($item->id, 'tramite_')){ $sourceType = 'tramite'; $sourceRecordId = preg_replace('/[^0-9]/','',$item->id); }
                        }
                        $origenTexto = $item->origen_canal ?? 'Canal';
                        $origenClass = str_contains($origenTexto,'Asesoría') ? 'bg-purple-100 text-purple-800' : (str_contains($origenTexto,'Trámite') ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800');
                    @endphp
                    <tr class="hover:bg-[#F4F1EA] transition">
                        <td class="p-2.5 font-semibold"><span class="px-2 py-1 rounded text-[10px] {{ $origenClass }}">{{ $origenTexto }}</span></td>
                        <td class="p-2.5 font-medium text-[#1E392A]">{{ trim($item->nombre_cliente ?? '') !== '' ? $item->nombre_cliente : 'Prospecto Web' }}</td>
                        <td class="p-2.5">{{ $item->telefono_cliente ?? 'N/A' }}</td>
                        <td class="p-2.5 max-w-xs truncate" title="{{ $item->detalle_ubicacion ?? 'N/A' }}">{{ $item->detalle_ubicacion ?? 'N/A' }}</td>
                        <td class="p-2.5 max-w-xs truncate text-gray-600" title="{{ $item->detalle_nota ?? 'Sin detalles' }}">{{ Str::limit($item->detalle_nota ?? 'Sin detalles', 45) }}</td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded font-semibold text-[10px] {{ $badgeClass }}">{{ $estadoActual }}</span></td>
                        <td class="p-2.5 text-[#1E392A]">{{ $item->fecha_registro ? \Carbon\Carbon::parse($item->fecha_registro)->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td class="p-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <form action="{{ route('admin.citas.gestionar', $item->id) }}" method="POST" class="inline">@csrf @method('PATCH')<button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white w-7 h-7 inline-flex items-center justify-center rounded-lg shadow transition" title="Avanzar Estado"><i class="fa-solid fa-arrows-rotate text-xs"></i></button></form>
                                <a href="{{ route('admin.citas.edit', $item->id) }}" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white w-7 h-7 inline-flex items-center justify-center rounded-lg shadow transition" title="Modificar Datos"><i class="fa-solid fa-pen text-xs"></i></a>
                               {{-- EXPORTAR A CLIENTES --}}
@if(($item->source_type ?? '') === 'tramite' && ($item->status ?? '') === 'Completado')
    {{-- TRÁMITE COMPLETADO: EXPORTACIÓN HABILITADA --}}
    <a href="{{ route('admin.citas.exportar', $item->id) }}" class="bg-sky-600 hover:bg-sky-700 text-white w-7 h-7 inline-flex items-center justify-center rounded-lg shadow transition" title="Exportar trámite completado a Clientes">
        <i class="fa-solid fa-file-export text-xs"></i>
    </a>
@elseif(($item->source_type ?? '') === 'tramite')
    {{-- ES TRÁMITE, PERO AÚN NO ESTÁ COMPLETADO --}}
    <span class="bg-sky-100 text-sky-400 border border-sky-200 w-7 h-7 inline-flex items-center justify-center rounded-lg cursor-not-allowed" title="Complete el trámite antes de exportarlo a Clientes">
        <i class="fa-solid fa-file-export text-xs"></i>
    </span>
@else
    {{-- CONTACTO / ASESORÍA: PRIMERO DEBE TRANSMUTAR A TRÁMITE --}}
    <span class="bg-gray-200 text-gray-400 border border-gray-300 w-7 h-7 inline-flex items-center justify-center rounded-lg cursor-not-allowed" title="Primero debe convertir este registro a Trámite">
        <i class="fa-solid fa-file-export text-xs"></i>
    </span>
@endif
                                <button type="button" onclick="openIntegralPortfolioModal(this)" data-source-type="{{ $sourceType }}" data-source-record-id="{{ $sourceRecordId }}" data-client-name="{{ $item->nombre_cliente ?? 'Prospecto Web' }}" data-client-phone="{{ $item->telefono_cliente ?? '' }}" data-origin="{{ $item->origen_canal ?? 'Citas Integrales' }}" data-location="{{ $item->detalle_ubicacion ?? '' }}" data-notes="{{ $item->detalle_nota ?? '' }}" class="bg-blue-600 hover:bg-blue-700 text-white w-7 h-7 inline-flex items-center justify-center rounded-lg shadow transition" title="Pasar a Cartera"><i class="fa-solid fa-folder-plus text-xs"></i></button>
                                <button type="button" onclick="openRecycleModal('{{ $item->id }}')" class="bg-red-600 hover:bg-red-700 text-white w-7 h-7 inline-flex items-center justify-center rounded-lg shadow transition" title="Enviar a Papelera"><i class="fa-solid fa-trash text-xs"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-8 text-center text-gray-500"><i class="fa-solid fa-inbox text-3xl block mb-2 text-[#556B5D]"></i>No hay registros centralizados todavía.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PASAR A CARTERA --}}
    <div id="integral-portfolio-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-base"><i class="fa-solid fa-folder-plus mr-2"></i>Pasar a Cartera General</h3>
                <button type="button" onclick="closeIntegralPortfolioModal()" class="text-white hover:text-gray-200 text-lg"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="integral-portfolio-form" method="POST" action="" class="p-5 space-y-4 text-xs overflow-y-auto">
                @csrf
                <div class="bg-white border border-[#D8D3C8] rounded-xl p-3 space-y-3">
                    <div><span class="text-[10px] text-gray-500 font-bold uppercase">Cliente / Prospecto</span><p id="integral-portfolio-client" class="font-bold text-[#1E392A] text-sm">-</p></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="text-[10px] text-gray-500 font-bold uppercase">Origen</span><p id="integral-portfolio-origin" class="font-semibold">-</p></div>
                        <div><span class="text-[10px] text-gray-500 font-bold uppercase">Teléfono</span><p id="integral-portfolio-phone" class="font-semibold">-</p></div>
                    </div>
                    <div><span class="text-[10px] text-gray-500 font-bold uppercase">Ubicación / Referencia</span><p id="integral-portfolio-location" class="font-semibold text-gray-700">-</p></div>
                </div>
                <div><label class="font-semibold text-[#1E392A] block mb-1">Motivo de ingreso a Cartera *</label><textarea name="entry_reason" id="integral-portfolio-reason" rows="3" required placeholder="Ej. Cliente interesado que requiere seguimiento comercial..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"></textarea></div>
                <div><label class="font-semibold text-[#1E392A] block mb-1">Canal de Contacto</label>
                    <select name="contact_channel" id="integral-portfolio-channel" onchange="toggleIntegralSocialFields()" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                        <option value="">Seleccione...</option>
                        <option value="Sitio Web">Sitio Web</option><option value="WhatsApp">WhatsApp</option><option value="Facebook">Facebook</option><option value="Instagram">Instagram</option><option value="TikTok">TikTok</option><option value="Teléfono">Teléfono</option><option value="Correo">Correo</option><option value="Referido">Referido</option><option value="Presencial">Presencial</option><option value="Otro">Otro</option>
                    </select>
                </div>
                <div id="integral-social-fields" class="hidden bg-white border border-blue-200 rounded-xl p-3 space-y-3">
                    <div><label class="font-semibold text-[#1E392A] block mb-1">Red Social</label>
                        <select name="social_platform" id="integral-social-platform" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                            <option value="">Seleccione...</option>
                            <option value="Facebook">Facebook</option><option value="Instagram">Instagram</option><option value="TikTok">TikTok</option><option value="Otra">Otra</option>
                        </select>
                    </div>
                    <div><label class="font-semibold text-[#1E392A] block mb-1">Link del perfil / cuenta</label>
                        <input type="url" name="social_profile_url" id="integral-social-url" placeholder="https://facebook.com/usuario" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                        <p class="text-[10px] text-gray-500 mt-1">Permite acceder directamente al perfil para enviar información o catálogos.</p>
                    </div>
                </div>
                <div><label class="font-semibold text-[#1E392A] block mb-1">Estado Inicial en Cartera *</label>
                    <select name="portfolio_status" id="integral-portfolio-status" required class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                        <option value="Nuevo">Nuevo</option><option value="Contactado">Contactado</option><option value="Seguimiento">Seguimiento</option><option value="Interesado">Interesado</option><option value="Negociación">Negociación</option>
                    </select>
                </div>
                <div><label class="font-semibold text-[#1E392A] block mb-1">Observaciones</label><textarea name="notes" id="integral-portfolio-notes" rows="3" placeholder="Observaciones para el seguimiento..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2"></textarea></div>
                <div class="flex justify-end gap-3 pt-3 border-t border-[#D8D3C8]">
                    <button type="button" onclick="closeIntegralPortfolioModal()" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-2 rounded-lg font-medium">No, regresar</button>
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold shadow"><i class="fa-solid fa-folder-plus mr-1"></i>Pasar a Cartera</button>
                </div>
            </form>
        </div>
    </div>

    {{-- FORMULARIO OCULTO RECICLAJE --}}
    <form id="integral-recycle-form" method="POST" action="" class="hidden">@csrf @method('DELETE')</form>

    {{-- MODAL RECICLAR --}}
    <div id="recycle-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-red-700 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold"><i class="fa-solid fa-trash mr-2"></i>Enviar a Papelera</h3>
                <button type="button" onclick="closeRecycleModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 bg-red-100 text-red-700 rounded-full flex items-center justify-center"><i class="fa-solid fa-trash-can text-xl"></i></div>
                <h4 class="font-bold text-[#1E392A] text-lg mb-2">¿Desea enviar este registro a la papelera?</h4>
                <p class="text-sm text-gray-600">El registro será retirado de la bandeja, pero podrá restaurarse posteriormente.</p>
            </div>
            <div class="px-6 pb-6 flex justify-center gap-3">
                <button type="button" onclick="closeRecycleModal()" class="bg-gray-300 hover:bg-gray-400 px-5 py-2 rounded-lg font-semibold"><i class="fa-solid fa-xmark mr-1"></i>No, regresar</button>
                <button type="button" onclick="confirmRecycleIntegral()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold shadow"><i class="fa-solid fa-trash mr-1"></i>Sí, reciclar</button>
            </div>
        </div>
    </div>

    {{-- HISTORIAL DE RECICLADOS --}}
    <div class="mt-12 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-[#1E392A]"><i class="fa-solid fa-clock-rotate-left mr-2 text-[#2C5E43]"></i>Historial de Registros Reciclados</h3>
                <p class="text-xs text-[#556B5D] mt-1">Puedes restaurar un registro o eliminarlo permanentemente.</p>
            </div>
        </div>
    </div>
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-4 shadow-sm overflow-hidden mb-12">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-[#2C5E43] text-white">
                    <tr>
                        <th class="p-3 font-semibold">Origen / Canal</th>
                        <th class="p-3 font-semibold">Cliente / Solicitante</th>
                        <th class="p-3 font-semibold">Teléfono</th>
                        <th class="p-3 font-semibold">Referencia / Ubicación</th>
                        <th class="p-3 font-semibold">Fecha de Eliminación</th>
                        <th class="p-3 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D8D3C8]">
                @forelse($citasRecicladas ?? [] as $item)
                    <tr class="hover:bg-[#F4F1EA] transition">
                        <td class="p-3"><span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-[10px] font-semibold">{{ $item->origen ?? 'Canal' }}</span></td>
                        <td class="p-3 font-semibold text-[#1E392A]">{{ $item->cliente }}</td>
                        <td class="p-3">{{ $item->telefono }}</td>
                        <td class="p-3">{{ $item->referencia }}</td>
                        <td class="p-3 text-gray-600">{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td class="p-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <form action="{{ route('admin.citas.restaurar', $item->id) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white w-8 h-8 inline-flex items-center justify-center rounded-lg shadow transition" title="Restaurar Registro"><i class="fa-solid fa-rotate-left text-xs"></i></button></form>
                                <button type="button" onclick="openPermanentDeleteModal('{{ $item->id }}')" class="bg-red-600 hover:bg-red-700 text-white w-8 h-8 inline-flex items-center justify-center rounded-lg shadow transition" title="Eliminar Permanentemente"><i class="fa-solid fa-trash-can text-xs"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">No hay elementos en el historial de reciclaje.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FORMULARIO ELIMINACIÓN PERMANENTE --}}
    <form id="permanent-delete-form" method="POST" action="" class="hidden">@csrf @method('DELETE')</form>

    {{-- MODAL ELIMINACIÓN PERMANENTE --}}
    <div id="permanent-delete-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-red-800 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Eliminar Permanentemente</h3>
                <button type="button" onclick="closePermanentDeleteModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 bg-red-100 text-red-700 rounded-full flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation text-xl"></i></div>
                <h4 class="font-bold text-[#1E392A] text-lg mb-2">¿Desea eliminar este registro permanentemente?</h4>
                <p class="text-sm text-gray-600">Los datos serán eliminados del sistema y esta acción no podrá deshacerse.</p>
            </div>
            <div class="px-6 pb-6 flex justify-center gap-3">
                <button type="button" onclick="closePermanentDeleteModal()" class="bg-gray-300 hover:bg-gray-400 px-5 py-2 rounded-lg font-semibold">No, regresar</button>
                <button type="button" onclick="confirmPermanentDelete()" class="bg-red-700 hover:bg-red-800 text-white px-5 py-2 rounded-lg font-semibold shadow"><i class="fa-solid fa-trash-can mr-1"></i>Sí, eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openIntegralPortfolioModal(button) {
        const sourceType = button.dataset.sourceType;
        const sourceRecordId = button.dataset.sourceRecordId;
        const clientName = button.dataset.clientName || 'Prospecto';
        const phone = button.dataset.clientPhone || 'N/A';
        const origin = button.dataset.origin || 'Citas Integrales';
        const location = button.dataset.location || 'N/A';
        const notes = button.dataset.notes || '';
        const modal = document.getElementById('integral-portfolio-modal');
        const form = document.getElementById('integral-portfolio-form');
        form.action = "{{ url('/intranet/integrales') }}" + "/" + sourceType + "/" + sourceRecordId + "/cartera";
        document.getElementById('integral-portfolio-client').textContent = clientName;
        document.getElementById('integral-portfolio-phone').textContent = phone;
        document.getElementById('integral-portfolio-origin').textContent = origin;
        document.getElementById('integral-portfolio-location').textContent = location;
        document.getElementById('integral-portfolio-reason').value = '';
        document.getElementById('integral-portfolio-notes').value = notes;
        document.getElementById('integral-portfolio-status').value = 'Nuevo';
        document.getElementById('integral-portfolio-channel').value = 'Sitio Web';
        document.getElementById('integral-social-platform').value = '';
        document.getElementById('integral-social-url').value = '';
        toggleIntegralSocialFields();
        modal.classList.remove('hidden');
    }
    function closeIntegralPortfolioModal() { document.getElementById('integral-portfolio-modal').classList.add('hidden'); }
    function toggleIntegralSocialFields() {
        const channel = document.getElementById('integral-portfolio-channel').value;
        const fields = document.getElementById('integral-social-fields');
        const platform = document.getElementById('integral-social-platform');
        const socialUrl = document.getElementById('integral-social-url');
        const socialNetworks = ['Facebook','Instagram','TikTok'];
        if(socialNetworks.includes(channel)){
            fields.classList.remove('hidden');
            platform.value = channel;
        }else{
            fields.classList.add('hidden');
            platform.value = '';
            socialUrl.value = '';
        }
    }
    let integralRecycleId = null;
    function openRecycleModal(id){ integralRecycleId = id; document.getElementById('recycle-modal').classList.remove('hidden'); }
    function closeRecycleModal(){ document.getElementById('recycle-modal').classList.add('hidden'); integralRecycleId = null; }
    function confirmRecycleIntegral(){
        if(!integralRecycleId) return;
        const form = document.getElementById('integral-recycle-form');
        form.action = "{{ url('/admin/integrales') }}" + "/" + integralRecycleId;
        form.submit();
    }
    let permanentDeleteId = null;
    function openPermanentDeleteModal(id){ permanentDeleteId = id; document.getElementById('permanent-delete-modal').classList.remove('hidden'); }
    function closePermanentDeleteModal(){ document.getElementById('permanent-delete-modal').classList.add('hidden'); permanentDeleteId = null; }
    function confirmPermanentDelete(){
        if(!permanentDeleteId) return;
        const form = document.getElementById('permanent-delete-form');
        form.action = "{{ url('/admin/citas') }}" + "/" + permanentDeleteId + "/forzar-eliminar";
        form.submit();
    }
</script>

@endsection