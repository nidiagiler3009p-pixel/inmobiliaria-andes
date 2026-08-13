@extends('layouts.admin')

@section('admin_content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-[#EFECE6] border border-[#D8D3C8] rounded-2xl p-8 shadow-sm" x-data="{ tipo: 'contacto' }">
        <h2 class="text-xl font-bold text-[#1E392A] mb-6">Nuevo Registro Centralizado</h2>

  <form action="{{ route('admin.citas.storeIntegral') }}" method="POST">
    @csrf

            <!-- Selector del tipo -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-[#1E392A] mb-2">Seleccione el tipo de registro:</label>
                <select name="tipo_registro" x-model="tipo" class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1E392A]">
                    <option value="contacto">Contáctanos</option>
                    <option value="asesoria">Asesoría</option>
                    <option value="tramite">Trámite</option>
                </select>
            </div>

            <!-- ================= CAMPOS COMUNES ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-[#1E392A] mb-1">Nombres / Razón Social *</label>
                    <input type="text" name="name" x-bind:name="tipo === 'asesoria' ? 'full_name' : (tipo === 'tramite' ? 'first_name' : 'name')" placeholder="Ej. Juan Pérez" required class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E392A] mb-1">Teléfono *</label>
                    <input type="text" name="phone" placeholder="Ej. 0991234567" required class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700">
                </div>
            </div>

            <!-- DIRECCIÓN / UBICACIÓN COMÚN (Se envía como 'general_address' para contacto, 'ciudad' para asesoría y 'ubicacion' para trámite) -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-[#1E392A] mb-1">Dirección / Ubicación / Referencia *</label>
                <input type="text" 
                       x-bind:name="tipo === 'contacto' ? 'general_address' : (tipo === 'asesoria' ? 'ciudad' : 'ubicacion')" 
                       placeholder="Ej. Av. Daniel León Borja, Riobamba" 
                       required 
                       class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700">
            </div>

            <!-- ================= CAMPOS ESPECÍFICOS ================= -->
            
            <!-- ASESORÍA -->
            <div x-show="tipo === 'asesoria'" class="space-y-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-[#1E392A] mb-1">Tipo de Asesoría</label>
                    <select name="plan_type" class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700">
                        <option value="General">General</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Legal">Legal</option>
                        <option value="Financiera">Financiera</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#1E392A] mb-1">Detalles de la Propiedad</label>
                    <textarea name="property_details" rows="3" placeholder="Detalles de lo que busca..." class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700"></textarea>
                </div>
            </div>

            <!-- TRÁMITE -->
            <div x-show="tipo === 'tramite'" class="space-y-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-[#1E392A] mb-1">Tipo de Trámite</label>
                    <select name="tramite_type" class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700">
                        <option value="Escrituras">Escrituras</option>
                        <option value="Avalúos">Avalúos</option>
                        <option value="Certificados">Certificados</option>
                        <option value="Municipal">Municipal</option>
                    </select>
                </div>
                 <div>
        <label class="block text-sm font-semibold text-[#1E392A] mb-1">Mensaje de requerimiento / Detalle</label>
        <textarea name="tramite_detalle" rows="3" class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700" placeholder="Escribe el detalle aquí..."></textarea>
    </div>
</div>

            <!-- CONTÁCTANOS -->
<div x-show="tipo === 'contacto'" class="mb-4">
    <label class="block text-sm font-semibold text-[#1E392A] mb-1">Mensaje de requerimiento</label>
    <textarea name="message" rows="3" placeholder="¿En qué podemos ayudarle?" class="w-full border border-[#D8D3C8] rounded-xl p-3 bg-white text-gray-700">{{ old('message', isset($item) ? $item->message : '') }}</textarea>
</div>

    <div class="flex items-center space-x-4 mt-6">
        <button type="submit" class="bg-[#1E392A] text-white px-6 py-3 rounded-xl font-semibold hover:bg-[#2c533e] transition">
            Guardar Registro
        </button>
        <a href="{{ route('admin.citas-totales') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-400 transition text-center">
            Cancelar
        </a>
    </div>
</form>
    </div>
</div>
@endsection