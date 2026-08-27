@extends('layouts.admin')
@section('admin_content')

@php
    $nombreProspecto = trim(($prospect->name ?? '') . ' ' . ($prospect->last_name ?? ''));
    $telefonoProspecto = $prospect->phone ?? 'N/A';
    $telefonoAlternativo = $prospect->contacts->firstWhere('type', 'phone');
    $instagramContact = $prospect->contacts->firstWhere('type', 'instagram');
    $facebookContact = $prospect->contacts->firstWhere('type', 'facebook');
    $tiktokContact = $prospect->contacts->firstWhere('type', 'tiktok');
    $aliasesPerfil = $prospect->aliases->take(2)->values();
    $alias1 = $aliasesPerfil->get(0);
    $alias2 = $aliasesPerfil->get(1);
@endphp

<div class="flex-grow px-3 py-4 max-w-5xl mx-auto w-full text-[#2C3E35] font-sans antialiased">

    {{-- ENCABEZADO --}}
    <header class="relative bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-4 shadow-sm mb-3">
        <div class="absolute left-1/2 -translate-x-1/2 -top-4 bg-[#2C5E43] text-white w-10 h-10 rounded-full flex items-center justify-center shadow border-4 border-[#F9F7F2]">
            <i class="fa-solid fa-clock-rotate-left text-sm"></i>
        </div>
        <div class="text-center pt-2">
            <h1 class="text-xl font-bold text-[#1E392A]">Historial del Prospecto</h1>
            <p class="text-[11px] text-[#556B5D] mt-1">Seguimiento cronológico de interacciones y movimientos.</p>
        </div>
    </header>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="mb-3 bg-emerald-50 border border-emerald-300 text-emerald-800 px-3 py-2 rounded-lg text-[10px] font-semibold">
            <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-3 bg-red-50 border border-red-300 text-red-700 px-3 py-2 rounded-lg text-[10px] font-semibold">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- NAVEGACIÓN --}}
    <div class="flex flex-wrap justify-end gap-2 mb-3">
        <a href="{{ route('admin.cartera') }}" class="inline-flex items-center gap-1.5 bg-[#2C5E43] hover:bg-[#1E392A] text-white px-3 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm transition" title="Volver a Cartera"><i class="fa-solid fa-arrow-left"></i> Cartera</a>
        <a href="{{ route('gestion.citas') }}" class="inline-flex items-center gap-1.5 bg-[#2C5E43] hover:bg-[#1E392A] text-white px-3 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm transition" title="Ir a Gestión de Citas"><i class="fa-solid fa-calendar-check"></i> Gestión de Citas</a>
        <a href="{{ route('admin.citas-totales') }}" class="inline-flex items-center gap-1.5 bg-[#556B5D] hover:bg-[#2C5E43] text-white px-3 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm transition" title="Ir a Citas Integrales"><i class="fa-solid fa-list-check"></i> Citas Integrales</a>
    </div>

    {{-- FICHA PRINCIPAL DEL PROSPECTO --}}
    <section class="bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-3 shadow-sm mb-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
            <div class="relative md:col-span-1">
                @if($esPotencial)
                    <div class="absolute top-0 right-0 inline-flex items-center gap-1 text-[8px] font-semibold text-amber-600 opacity-60" title="Cliente Potencial"><i class="fa-solid fa-star"></i> Potencial</div>
                @endif
                <div class="text-base font-bold text-[#1E392A] pr-14">{{ $nombreProspecto ?: 'Sin nombre' }}</div>
                <div class="text-[10px] text-[#2C5E43] font-semibold mt-1"><i class="fa-solid fa-clock-rotate-left mr-1"></i>{{ $totalMovimientos }} {{ $totalMovimientos === 1 ? 'movimiento' : 'movimientos' }}</div>
            </div>
            <div>
                <p class="text-[8px] uppercase font-bold text-gray-500">Contacto</p>
                <div class="text-[11px] font-semibold text-[#1E392A] mt-1"><i class="fa-solid fa-phone text-[#2C5E43] mr-1"></i> {{ $telefonoProspecto }}</div>
                @if($telefonoAlternativo)
                    <div class="text-[9px] text-[#556B5D] mt-1"><i class="fa-solid fa-mobile-screen mr-1"></i> {{ $telefonoAlternativo->value }} <span class="text-[7px]">(alternativo)</span></div>
                @endif
                @if($socialUrl)
                    <a href="{{ $socialUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 mt-1 text-[9px] font-semibold text-[#2C5E43] hover:underline" title="Abrir {{ $socialPlatform ?? 'perfil social' }}">
                        @if(strtolower($socialPlatform ?? '') === 'instagram')<i class="fa-brands fa-instagram"></i>@elseif(strtolower($socialPlatform ?? '') === 'facebook')<i class="fa-brands fa-facebook"></i>@elseif(strtolower($socialPlatform ?? '') === 'tiktok')<i class="fa-brands fa-tiktok"></i>@else<i class="fa-solid fa-link"></i>@endif
                        {{ $socialPlatform ?? 'Ver perfil' }}
                    </a>
                @endif
            </div>
            <div>
                <p class="text-[8px] uppercase font-bold text-gray-500">Estado actual</p>
                <div class="mt-1">
                    <span class="inline-flex px-2 py-1 rounded text-[9px] font-semibold {{ $estadoActual === 'Cliente Potencial' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($estadoActual === 'Negociación' ? 'bg-[#E4EFE8] text-[#1E392A] border border-[#8CB49A]' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">{{ $estadoActual }}</span>
                </div>
            </div>
            <div>
                <p class="text-[8px] uppercase font-bold text-gray-500">Ingresos a Cartera</p>
                <div class="text-lg font-bold text-[#2C5E43] mt-1">{{ $portfolioEntries->count() }}</div>
            </div>
        </div>
    </section>

    {{-- PERFIL COMPLETO --}}
    <section class="bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-3 shadow-sm mb-3">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <div>
                <h2 class="text-sm font-bold text-[#1E392A]"><i class="fa-solid fa-user-pen text-[#2C5E43] mr-1"></i> Perfil del Prospecto</h2>
                <p class="text-[9px] text-[#556B5D] mt-0.5">Información interna complementaria del prospecto.</p>
            </div>
            <button type="button" id="profile-toggle-button" class="inline-flex items-center gap-1.5 bg-[#2C5E43] hover:bg-[#1E392A] text-white px-3 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm transition"><i class="fa-solid fa-pen-to-square"></i> Editar / Completar Perfil</button>
        <button
    type="button"
    id="manual-movement-button"
    class="inline-flex items-center gap-1.5 bg-[#556B5D] hover:bg-[#2C5E43] text-white px-3 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm transition"
>
    <i class="fa-solid fa-plus"></i>
    Registrar Movimiento
</button>
        </div>

        {{-- RESUMEN --}}
        <div id="prospect-profile-summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <div class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                <p class="text-[8px] uppercase font-bold text-gray-500">Identificación</p>
                <p class="text-[10px] font-semibold text-[#1E392A] mt-1">{{ $prospect->identification ?: 'No registrada' }}</p>
            </div>
            <div class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                <p class="text-[8px] uppercase font-bold text-gray-500">Teléfono principal</p>
                <p class="text-[10px] font-semibold text-[#1E392A] mt-1">{{ $prospect->phone ?: 'No registrado' }}</p>
            </div>
            <div class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                <p class="text-[8px] uppercase font-bold text-gray-500">Teléfono alternativo</p>
                <p class="text-[10px] font-semibold text-[#1E392A] mt-1">{{ $telefonoAlternativo?->value ?? 'No registrado' }}</p>
            </div>
            <div class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2">
                <p class="text-[8px] uppercase font-bold text-gray-500">Correo</p>
                <p class="text-[10px] font-semibold text-[#1E392A] mt-1 break-all">{{ $prospect->email ?: 'No registrado' }}</p>
            </div>
        </div>

        {{-- ALIAS --}}
        @if($alias1 || $alias2)
            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                <span class="text-[8px] uppercase font-bold text-gray-500 mr-1">Otros nombres:</span>
                @if($alias1)
                    <span class="inline-flex items-center gap-1 bg-[#E4EFE8] text-[#2C5E43] border border-[#B8CFBF] rounded px-2 py-1 text-[8px] font-semibold" title="Nombre alternativo"><i class="fa-solid fa-user-tag"></i> {{ $alias1->alias_name }}</span>
                @endif
                @if($alias2)
                    <span class="inline-flex items-center gap-1 bg-[#E4EFE8] text-[#2C5E43] border border-[#B8CFBF] rounded px-2 py-1 text-[8px] font-semibold" title="Nombre alternativo"><i class="fa-solid fa-user-tag"></i> {{ $alias2->alias_name }}</span>
                @endif
            </div>
        @endif

        {{-- REDES SOCIALES --}}
        @if($instagramContact || $facebookContact || $tiktokContact)
            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                <span class="text-[8px] uppercase font-bold text-gray-500 mr-1">Redes:</span>
                @if($instagramContact)
                    <a href="{{ $instagramContact->value }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 bg-white border border-[#D8D3C8] text-[#2C5E43] rounded px-2 py-1 text-[8px] font-semibold hover:bg-[#E4EFE8] transition" title="Abrir Instagram"><i class="fa-brands fa-instagram"></i> Instagram</a>
                @endif
                @if($facebookContact)
                    <a href="{{ $facebookContact->value }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 bg-white border border-[#D8D3C8] text-[#2C5E43] rounded px-2 py-1 text-[8px] font-semibold hover:bg-[#E4EFE8] transition" title="Abrir Facebook"><i class="fa-brands fa-facebook"></i> Facebook</a>
                @endif
                @if($tiktokContact)
                    <a href="{{ $tiktokContact->value }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 bg-white border border-[#D8D3C8] text-[#2C5E43] rounded px-2 py-1 text-[8px] font-semibold hover:bg-[#E4EFE8] transition" title="Abrir TikTok"><i class="fa-brands fa-tiktok"></i> TikTok</a>
                @endif
            </div>
        @endif

        {{-- OBSERVACIONES --}}
        @if($prospect->notes)
            <div class="bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 mt-2">
                <p class="text-[8px] uppercase font-bold text-gray-500 mb-1">Observaciones generales</p>
                <p class="text-[9px] text-[#556B5D] leading-relaxed">{{ $prospect->notes }}</p>
            </div>
        @endif

        {{-- FORMULARIO EDITAR / COMPLETAR --}}
        <div id="prospect-profile-form-container" class="hidden mt-3 pt-3 border-t border-[#D8D3C8]">
            @if($errors->any())
                <div class="bg-red-50 border border-red-300 rounded-lg px-3 py-2 mb-3 text-[9px] text-red-700">
                    <div class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Revise los siguientes campos:</div>
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.prospectos.perfil.update', $prospect->id) }}">
                @csrf @method('PUT')

                {{-- DATOS PRINCIPALES --}}
                <div class="mb-3 pb-3 border-b border-[#D8D3C8]">
                    <h3 class="text-[10px] font-bold text-[#1E392A] mb-2"><i class="fa-solid fa-id-card text-[#2C5E43] mr-1"></i> Datos principales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Nombre *</label>
                            <input type="text" name="name" required value="{{ old('name', $prospect->name) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Apellido</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $prospect->last_name) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Identificación</label>
                            <input type="text" name="identification" value="{{ old('identification', $prospect->identification) }}" placeholder="Cédula / identificación" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Teléfono principal *</label>
                            <input type="text" name="phone" required value="{{ old('phone', $prospect->phone) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Teléfono alternativo</label>
                            <input type="text" name="alternate_phone" value="{{ old('alternate_phone', $telefonoAlternativo?->value) }}" placeholder="Máximo un número alternativo" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                            <p class="text-[7px] text-gray-500 mt-0.5">Solo si confirmó que pertenece a la misma persona.</p>
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Correo electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $prospect->email) }}" placeholder="correo@ejemplo.com" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                    </div>
                </div>

                {{-- NOMBRES ALTERNATIVOS --}}
                <div class="mb-3 pb-3 border-b border-[#D8D3C8]">
                    <h3 class="text-[10px] font-bold text-[#1E392A] mb-1"><i class="fa-solid fa-user-tag text-[#2C5E43] mr-1"></i> Nombres alternativos</h3>
                    <p class="text-[8px] text-[#556B5D] mb-2">Para la misma persona cuando anteriormente ingresó con otro nombre. Máximo dos.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Nombre alternativo 1</label>
                            <input type="text" name="alias_1" value="{{ old('alias_1', $alias1?->alias_name) }}" placeholder="Ej.: Juan P." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Nombre alternativo 2</label>
                            <input type="text" name="alias_2" value="{{ old('alias_2', $alias2?->alias_name) }}" placeholder="Ej.: Juan Carlos Pérez" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                    </div>
                </div>

                {{-- REDES SOCIALES --}}
                <div class="mb-3 pb-3 border-b border-[#D8D3C8]">
                    <h3 class="text-[10px] font-bold text-[#1E392A] mb-1"><i class="fa-solid fa-share-nodes text-[#2C5E43] mr-1"></i> Redes sociales</h3>
                    <p class="text-[8px] text-[#556B5D] mb-2">Ingrese el enlace completo del perfil.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1"><i class="fa-brands fa-instagram mr-1"></i> Instagram</label>
                            <input type="url" name="instagram" value="{{ old('instagram', $instagramContact?->value) }}" placeholder="https://instagram.com/..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1"><i class="fa-brands fa-facebook mr-1"></i> Facebook</label>
                            <input type="url" name="facebook" value="{{ old('facebook', $facebookContact?->value) }}" placeholder="https://facebook.com/..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1"><i class="fa-brands fa-tiktok mr-1"></i> TikTok</label>
                            <input type="url" name="tiktok" value="{{ old('tiktok', $tiktokContact?->value) }}" placeholder="https://tiktok.com/@..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px] focus:outline-none focus:border-[#2C5E43]">
                        </div>
                    </div>
                </div>

                {{-- OBSERVACIONES --}}
                <div>
                    <label class="block text-[9px] font-semibold text-[#1E392A] mb-1"><i class="fa-solid fa-note-sticky text-[#2C5E43] mr-1"></i> Observaciones generales</label>
                    <textarea name="notes" rows="3" placeholder="Información general útil para futuras gestiones..." class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-2 text-[10px] resize-y focus:outline-none focus:border-[#2C5E43]">{{ old('notes', $prospect->notes) }}</textarea>
                </div>

                {{-- BOTONES FORMULARIO --}}
                <div class="flex flex-wrap justify-end gap-2 mt-3 pt-3 border-t border-[#D8D3C8]">
                    <button type="button" id="profile-cancel-button" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-1.5 rounded-lg text-[10px] font-semibold transition"><i class="fa-solid fa-xmark mr-1"></i> Cancelar</button>
                    <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm transition"><i class="fa-solid fa-floppy-disk mr-1"></i> Guardar Perfil</button>
                </div>
            </form>
        </div>
    </section>

{{-- =========================================================
     REGISTRAR MOVIMIENTO MANUAL
========================================================== --}}

<section
    id="manual-movement-container"
    class="hidden bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-3 shadow-sm mb-3"
>

    <div class="flex justify-between items-center mb-3">

        <div>

            <h2 class="text-sm font-bold text-[#1E392A]">
                <i class="fa-solid fa-pen-to-square text-[#2C5E43] mr-1"></i>
                Registrar Movimiento Manual
            </h2>

            <p class="text-[9px] text-[#556B5D] mt-0.5">
                Registre interacciones realizadas fuera de la plataforma.
            </p>

        </div>

        <button
            type="button"
            id="manual-movement-close"
            class="text-[#556B5D] hover:text-[#1E392A]"
            title="Cerrar"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

    </div>


    <form
        method="POST"
        action="{{ route('admin.prospectos.movimientos.store', $prospect->id) }}"
    >

        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">


            {{-- TIPO --}}

            <div>

                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                    Tipo de interacción *
                </label>

                <select
                    name="interaction_type"
                    required
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px]"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    <option value="Llamada telefónica">
                        Llamada telefónica
                    </option>

                    <option value="WhatsApp">
                        WhatsApp
                    </option>

                    <option value="Visita presencial">
                        Visita presencial
                    </option>

                    <option value="Mensaje en red social">
                        Mensaje en red social
                    </option>

                    <option value="Referido">
                        Referido
                    </option>

                    <option value="Correo">
                        Correo
                    </option>

                    <option value="Publicidad">
                        Publicidad
                    </option>

                    <option value="Otro">
                        Otro
                    </option>

                </select>

            </div>


            {{-- CANAL --}}

            <div>

                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                    Canal *
                </label>

                <select
                    name="channel"
                    required
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px]"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    <option value="Teléfono">Teléfono</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Instagram">Instagram</option>
                    <option value="TikTok">TikTok</option>
                    <option value="Correo">Correo</option>
                    <option value="Presencial">Presencial</option>
                    <option value="Referido">Referido</option>
                    <option value="Radio">Radio</option>
                    <option value="Otro">Otro</option>

                </select>

            </div>


            {{-- FECHA --}}

            <div>

                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                    Fecha y hora *
                </label>

                <input
                    type="datetime-local"
                    name="interaction_date"
                    required
                    value="{{ old('interaction_date', now()->format('Y-m-d\TH:i')) }}"
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px]"
                >

            </div>

        </div>


        {{-- DESCRIPCIÓN --}}

        <div class="mt-2">

            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                Descripción *
            </label>

            <textarea
                name="description"
                rows="3"
                required
                placeholder="Ej.: Cliente llamó desde otro número y solicitó información sobre..."
                class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-2 text-[10px]"
            >{{ old('description') }}</textarea>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">


            {{-- RESULTADO --}}

            <div>

                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                    Resultado
                </label>

                <textarea
                    name="result"
                    rows="2"
                    placeholder="Resultado de la interacción..."
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-2 text-[10px]"
                >{{ old('result') }}</textarea>

            </div>


            {{-- NUEVO ESTADO --}}

            <div>

                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                    Nuevo estado
                </label>

                <select
                    name="new_status"
                    class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-1.5 text-[10px]"
                >

                    <option value="">
                        Mantener estado actual
                    </option>

                    <option value="Nuevo">Nuevo</option>
                    <option value="Contactado">Contactado</option>
                    <option value="Seguimiento">Seguimiento</option>
                    <option value="Interesado">Interesado</option>
                    <option value="Negociación">Negociación</option>
                    <option value="Cliente Potencial">Cliente Potencial</option>

                </select>

            </div>

        </div>


        {{-- NOTAS --}}

        <div class="mt-2">

            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">
                Notas adicionales
            </label>

            <textarea
                name="notes"
                rows="2"
                placeholder="Información complementaria..."
                class="w-full bg-white border border-[#D8D3C8] rounded-lg px-2.5 py-2 text-[10px]"
            >{{ old('notes') }}</textarea>

        </div>


        {{-- BOTONES --}}

        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-[#D8D3C8]">

            <button
                type="button"
                id="manual-movement-cancel"
                class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-1.5 rounded-lg text-[10px] font-semibold"
            >
                <i class="fa-solid fa-xmark mr-1"></i>
                Cancelar
            </button>

            <button
                type="submit"
                class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-1.5 rounded-lg text-[10px] font-semibold shadow-sm"
            >
                <i class="fa-solid fa-floppy-disk mr-1"></i>
                Guardar Movimiento
            </button>

        </div>

    </form>

</section>
    {{-- HISTORIAL --}}
    <section class="bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-3 shadow-sm">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-bold text-[#1E392A]"><i class="fa-solid fa-timeline text-[#2C5E43] mr-1"></i> Línea de tiempo</h2>
            <span class="text-[9px] text-[#556B5D] font-semibold">{{ $histories->count() }} eventos</span>
        </div>

        <div class="relative pl-6 max-h-[520px] overflow-y-auto pr-2">
            <div class="absolute left-[9px] top-0 bottom-0 w-px bg-[#BFC8C1]"></div>

            @forelse($histories as $history)
                @php
                    $origenTexto = match($history->source_type) {
                        'appointment' => 'Gestión de Citas',
                        'contact' => 'Contáctanos',
                        'advisory' => 'Asesoría',
                        'tramite' => 'Trámite',
                        'portfolio' => 'Cartera',
                        'prospect' => 'Perfil del Prospecto',
                        'manual' => 'Movimiento Manual',
                        default => ucfirst($history->source_type ?? 'Sistema')
                    };
                    $icono = match($history->source_type) {
                        'appointment' => 'fa-calendar-check',
                        'contact' => 'fa-envelope',
                        'advisory' => 'fa-handshake',
                        'tramite' => 'fa-file-signature',
                        'portfolio' => 'fa-folder-open',
                        'prospect' => 'fa-user-pen',
                        'manual' => 'fa-pen-to-square',
                        default => 'fa-circle'
                    };
                @endphp

                <article class="relative bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 mb-2 shadow-sm">
                    <div class="absolute -left-[23px] top-3 w-4 h-4 rounded-full bg-[#2C5E43] text-white flex items-center justify-center border-2 border-[#F9F7F2] shadow-sm">
                        <i class="fa-solid {{ $icono }} text-[6px]"></i>
                    </div>

                    <div class="flex flex-wrap justify-between items-start gap-2">
                        <div>
                            <div class="text-[11px] font-bold text-[#1E392A]">{{ $history->event_type }}</div>
                            <div class="inline-flex items-center gap-1 mt-0.5 text-[8px] text-[#2C5E43] font-semibold"><i class="fa-solid {{ $icono }}"></i> {{ $origenTexto }}</div>
                        </div>
                        <div class="text-right text-[8px] text-gray-500 whitespace-nowrap">
                            @if($history->created_at)<div>{{ $history->created_at->format('d/m/Y') }}</div><div>{{ $history->created_at->format('H:i') }}</div>@endif
                        </div>
                    </div>

                    @if($history->previous_status || $history->new_status)
                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                            @if($history->previous_status)<span class="inline-flex bg-gray-100 border border-gray-300 px-1.5 py-0.5 rounded text-[8px] font-semibold">{{ $history->previous_status }}</span>@endif
                            @if($history->previous_status && $history->new_status)<i class="fa-solid fa-arrow-right text-[8px] text-gray-400"></i>@endif
                            @if($history->new_status)<span class="inline-flex bg-emerald-50 border border-emerald-200 text-[#2C5E43] px-1.5 py-0.5 rounded text-[8px] font-semibold">{{ $history->new_status }}</span>@endif
                        </div>
                    @endif

                    @if($history->description)
                        <div class="text-[9px] text-[#556B5D] leading-relaxed mt-2">{{ $history->description }}</div>
                    @endif

                    @if($history->source_record_id)
                        <div class="mt-2 text-[7px] text-gray-400">Registro relacionado: #{{ $history->source_record_id }}</div>
                    @endif
                </article>

            @empty
                <div class="text-center text-gray-500 py-8">
                    <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 block text-[#556B5D]"></i>
                    Este prospecto todavía no tiene historial registrado.
                </div>
            @endforelse
        </div>
    </section>
</div>

{{-- JAVASCRIPT PERFIL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('profile-toggle-button');
    const cancelButton = document.getElementById('profile-cancel-button');
    const form = document.getElementById('prospect-profile-form-container');
    const summary = document.getElementById('prospect-profile-summary');

    function openProfileForm() {
        if (!form) return;
        form.classList.remove('hidden');
        if (summary) summary.classList.add('opacity-60');
        if (button) {
            button.innerHTML = '<i class="fa-solid fa-xmark"></i> Cerrar edición';
            button.classList.remove('bg-[#2C5E43]');
            button.classList.add('bg-[#556B5D]');
        }
    }

    function closeProfileForm() {
        if (!form) return;
        form.classList.add('hidden');
        if (summary) summary.classList.remove('opacity-60');
        if (button) {
            button.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Editar / Completar Perfil';
            button.classList.remove('bg-[#556B5D]');
            button.classList.add('bg-[#2C5E43]');
        }
    }

    if (button && form) {
        button.addEventListener('click', function () {
            if (form.classList.contains('hidden')) openProfileForm();
            else closeProfileForm();
        });
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', function () { closeProfileForm(); });
    }

    @if($errors->any())
        openProfileForm();
    @endif
});
const manualButton =
    document.getElementById('manual-movement-button');

const manualContainer =
    document.getElementById('manual-movement-container');

const manualClose =
    document.getElementById('manual-movement-close');

const manualCancel =
    document.getElementById('manual-movement-cancel');


function openManualMovement()
{
    if (manualContainer) {
        manualContainer.classList.remove('hidden');
        manualContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}


function closeManualMovement()
{
    if (manualContainer) {
        manualContainer.classList.add('hidden');
    }
}


if (manualButton) {
    manualButton.addEventListener(
        'click',
        openManualMovement
    );
}

if (manualClose) {
    manualClose.addEventListener(
        'click',
        closeManualMovement
    );
}

if (manualCancel) {
    manualCancel.addEventListener(
        'click',
        closeManualMovement
    );
}
</script>

@endsection