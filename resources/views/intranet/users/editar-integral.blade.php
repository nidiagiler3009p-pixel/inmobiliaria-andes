@extends('layouts.admin')

@section('admin_content')
@php
    // Normalizar la variable $tipo para evitar fallos por tildes o prefijos
    $tipoNorm = strtolower(str_replace(['á', 'é', 'í', 'ó', 'ú', '_'], ['a', 'e', 'i', 'o', 'u', ''], $tipo));
    if (str_contains($tipoNorm, 'tramite')) {
        $tipoClean = 'tramite';
    } elseif (str_contains($tipoNorm, 'asesor')) {
        $tipoClean = 'asesoria';
    } else {
        $tipoClean = 'contacto';
    }

    // Armar el ID compuesto si no lo trae
    $routeId = str_contains((string)$item->id, '_') ? $item->id : ($tipoClean . '_' . $item->id);
@endphp

<div class="flex-grow p-6 max-w-4xl mx-auto w-full text-[#2C3E35] font-sans antialiased">
    <header class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-6 shadow-sm mb-6">
        <h1 class="text-2xl font-bold text-[#1E392A]">Editar Registro: {{ ucfirst($tipoClean) }}</h1>
        <p class="text-sm text-[#556B5D] mt-1">Actualiza los datos del prospecto seleccionado o transmuta su tipo de registro.</p>
    </header>

    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-6 shadow-sm">
        <form action="{{ route('admin.citas.update', $routeId) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Identificadores del registro original -->
            <input type="hidden" name="tipo_origen" value="{{ $tipoClean }}">
            <input type="hidden" name="id" value="{{ $item->id }}">

            <!-- Selector para Cambiar de Tipo (Transmutación) -->
            <div class="mb-6 pb-4 border-b border-[#D8D3C8]">
                <label for="nuevo_tipo" class="block text-xs font-bold mb-1 text-[#1E392A]">Tipo de Registro / Origen</label>
                <select name="nuevo_tipo" id="nuevo_tipo" class="w-full p-2.5 border border-[#D8D3C8] rounded-xl text-xs bg-white font-semibold shadow-sm focus:ring-2 focus:ring-[#2C5E43] focus:outline-none">
                    <option value="contacto" {{ old('nuevo_tipo', $tipoClean) === 'contacto' ? 'selected' : '' }}>Contacto</option>
                    <option value="asesoria" {{ old('nuevo_tipo', $tipoClean) === 'asesoria' ? 'selected' : '' }}>Asesoría</option>
                    <option value="tramite" {{ old('nuevo_tipo', $tipoClean) === 'tramite' ? 'selected' : '' }}>Trámite</option>
                </select>
                <p class="text-[10px] text-[#556B5D] mt-1">Al cambiar la opción, se mostrarán los campos requeridos para el nuevo tipo y el registro se migrará automáticamente.</p>
            </div>

            <!-- SECCIÓN 1: CAMPOS DE CONTACTO -->
            <div id="seccion-contacto" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name', trim(($item->name ?? $item->first_name ?? $item->full_name ?? '') . ' ' . ($item->last_name ?? ''))) }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $item->phone) }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1">Dirección / Ubicación</label>
                    <input type="text" name="general_address" value="{{ old('general_address', $item->general_address ?? $item->ubicacion ?? $item->ciudad ?? '') }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1">Mensaje / Requerimientos</label>
                    <textarea name="requirements_message" rows="3" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">{{ old('requirements_message', $item->requirements_message ?? $item->message ?? $item->property_details ?? '') }}</textarea>
                </div>
            </div>

            <!-- SECCIÓN 2: CAMPOS DE ASESORÍA -->
            <div id="seccion-asesoria" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">Nombre Completo</label>
                    <input type="text" name="full_name" value="{{ old('full_name', trim(($item->full_name ?? $item->name ?? $item->first_name ?? '') . ' ' . ($item->last_name ?? ''))) }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $item->phone) }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Tipo de Plan / Asesoría</label>
                    <input type="text" name="plan_type" value="{{ old('plan_type', $item->plan_type ?? '') }}" placeholder="Ej: Plan Básico, Asesoría Legal" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Ciudad</label>
                    <input type="text" name="ciudad" value="{{ old('ciudad', $item->ciudad ?? $item->general_address ?? $item->ubicacion ?? '') }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1">Detalles de Propiedad</label>
                    <textarea name="property_details" rows="3" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">{{ old('property_details', $item->property_details ?? $item->requirements_message ?? $item->message ?? '') }}</textarea>
                </div>
            </div>

            <!-- SECCIÓN 3: CAMPOS DE TRÁMITE -->
            <div id="seccion-tramite" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1">Nombres</label>
                    <input type="text" name="first_name" value="{{ old('first_name', trim(($item->first_name ?? $item->name ?? $item->full_name ?? '') . ' ' . ($item->last_name ?? ''))) }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $item->phone) }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Cédula / Identificación</label>
                    <input type="text" name="identification_card" value="{{ old('identification_card', $item->identification_card ?? '') }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Tipo de Trámite</label>
                    <input type="text" name="tramite_type" value="{{ old('tramite_type', $item->tramite_type ?? '') }}" placeholder="Ej: Municipal, Escrituración" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Asunto</label>
                    <input type="text" name="subject" value="{{ old('subject', $item->subject ?? '') }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion', $item->ubicacion ?? $item->ciudad ?? $item->general_address ?? '') }}" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1">Mensaje</label>
                    <textarea name="message" rows="3" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">{{ old('message', $item->message ?? $item->requirements_message ?? $item->property_details ?? '') }}</textarea>
                </div>
            </div>

            <!-- ESTADO GENERAL -->
            <div class="mt-4 pt-4 border-t border-[#D8D3C8]">
                <label class="block text-xs font-semibold mb-1">Estado del Registro</label>
                <select name="status" class="w-full p-2 border border-[#D8D3C8] rounded-xl text-xs bg-white">
                    <option value="Pendiente" {{ old('status', $item->status ?? '') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="En Proceso" {{ old('status', $item->status ?? '') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="Completado" {{ old('status', $item->status ?? '') == 'Completado' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.citas-totales') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-xl text-xs font-semibold transition">Cancelar</a>
                <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow transition">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipo = document.getElementById('nuevo_tipo');
    const seccionContacto = document.getElementById('seccion-contacto');
    const seccionAsesoria = document.getElementById('seccion-asesoria');
    const seccionTramite = document.getElementById('seccion-tramite');

    function obtenerNombreActual() {
        const inputNombre = document.querySelector('input[name="name"]:not([disabled]), input[name="full_name"]:not([disabled]), input[name="first_name"]:not([disabled])');
        return inputNombre ? inputNombre.value : '';
    }

    function alternarFormularios() {
        const nombrePrevio = obtenerNombreActual();
        const tipoSeleccionado = selectTipo.value;

        seccionContacto.style.display = (tipoSeleccionado === 'contacto') ? 'grid' : 'none';
        seccionAsesoria.style.display = (tipoSeleccionado === 'asesoria') ? 'grid' : 'none';
        seccionTramite.style.display = (tipoSeleccionado === 'tramite') ? 'grid' : 'none';

        toggleInputs(seccionContacto, tipoSeleccionado === 'contacto');
        toggleInputs(seccionAsesoria, tipoSeleccionado === 'asesoria');
        toggleInputs(seccionTramite, tipoSeleccionado === 'tramite');

        if (nombrePrevio.trim() !== '') {
            const nuevoInputNombre = document.querySelector('input[name="name"]:not([disabled]), input[name="full_name"]:not([disabled]), input[name="first_name"]:not([disabled])');
            if (nuevoInputNombre) {
                nuevoInputNombre.value = nombrePrevio;
            }
        }
    }

    function toggleInputs(container, enable) {
        const inputs = container.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.disabled = !enable;
        });
    }

    selectTipo.addEventListener('change', alternarFormularios);
    alternarFormularios();
});
</script>
@endsection