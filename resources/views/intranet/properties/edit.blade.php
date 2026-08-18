@extends('layouts.admin')

@section('admin_content')
<div class="max-w-4xl mx-auto px-4 pb-12 font-sans">
    <div class="flex justify-between items-center mb-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl border border-emerald-100 shadow-sm">
        <h2 class="text-base font-extrabold text-[#2C4A3E] uppercase"><i class="fa-solid fa-pen-to-square mr-2"></i> Editar Propiedad #{{ $property->id }}</h2>
        <a href="{{ route('properties.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">‹ Volver al Catálogo</a>
    </div>

    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-2xl text-xs font-bold">
        <p class="font-extrabold mb-1">Por favor corrige los siguientes errores:</p>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" class="bg-white/90 backdrop-blur-md p-8 rounded-3xl border border-emerald-100 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs font-bold text-[#2C4A3E]">
            
            <!-- 1. Clasificación y Gestión Interna -->
            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Asesor Responsable / Asignado</label>
                <select name="user_id" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Seleccione un asesor...</option>
                    @foreach($asesores as $asesor)
                        <option value="{{ $asesor->id }}" {{ old('user_id', $property->user_id) == $asesor->id ? 'selected' : '' }}>{{ $asesor->name }}</option>
                    @endforeach
                </select>
            </div>

<div>
    <label class="block mb-2 uppercase text-[10px] text-gray-500">Tipo de Inmueble</label>
    <select name="property_type" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
        <option value="">Seleccione un tipo...</option>
        <option value="Casa" {{ old('property_type', $property->property_type ?? '') == 'Casa' ? 'selected' : '' }}>Casa</option>
        <option value="Terrenos" {{ old('property_type', $property->property_type ?? '') == 'Terrenos' ? 'selected' : '' }}>Terrenos</option>
        <option value="Terrenos Grandes" {{ old('property_type', $property->property_type ?? '') == 'Terrenos Grandes' ? 'selected' : '' }}>Terrenos Grandes</option>
        <option value="Proyectos" {{ old('property_type', $property->property_type ?? '') == 'Proyectos' ? 'selected' : '' }}>Proyectos</option>
        <option value="Departamentos" {{ old('property_type', $property->property_type ?? '') == 'Departamentos' ? 'selected' : '' }}>Departamentos</option>
        <option value="Comerciales" {{ old('property_type', $property->property_type ?? '') == 'Comerciales' ? 'selected' : '' }}>Comerciales</option>
    </select>
</div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Tipo de Operación</label>
                <select name="service_type" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="Venta" {{ old('service_type', $property->service_type) == 'Venta' ? 'selected' : '' }}>Venta</option>
                    <option value="Arriendo" {{ old('service_type', $property->service_type) == 'Arriendo' ? 'selected' : '' }}>Arriendo</option>
                </select>
            </div>

            <!-- Datos del Propietario -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-4"><i class="fa-solid fa-user-shield mr-2"></i> Datos del Propietario</h3>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Nombre del Propietario</label>
                <input type="text" name="owner_name" value="{{ old('owner_name', $property->owner_name) }}" placeholder="Ej: Juan Pérez" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Teléfono del Propietario</label>
                <input type="text" name="owner_phone" value="{{ old('owner_phone', $property->owner_phone) }}" placeholder="Ej: 0991234567" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Cédula / DNI del Propietario</label>
                <input type="text" name="owner_dni" value="{{ old('owner_dni', $property->owner_dni) }}" placeholder="Ej: 0601234567" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Correo del Propietario</label>
                <input type="email" name="owner_email" value="{{ old('owner_email', $property->owner_email) }}" placeholder="ejemplo@correo.com" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- 2. Información General y Ubicación -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-4"><i class="fa-solid fa-location-dot mr-2"></i> Información General y Ubicación</h3>
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Título de la Propiedad</label>
                <input type="text" name="title" value="{{ old('title', $property->title) }}" required placeholder="Ej: CASA DE VENTA EN CONJUNTO" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Insignia Izquierda (Badge Left)</label>
                <input type="text" name="badge_left" value="{{ old('badge_left', $property->badge_left) }}" placeholder="Ej: Cerca de Plaza y la Villa" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Insignia Derecha (Badge Right)</label>
                <input type="text" name="badge_right" value="{{ old('badge_right', $property->badge_right) }}" placeholder="Ej: a 3 cuadras..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Ciudad / Ubicación (Provincia/Zona)</label>
                <input type="text" name="location" value="{{ old('location', $property->location) }}" placeholder="Ej: Guano, Chimborazo" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Dirección / Sector</label>
                <input type="text" name="address" value="{{ old('address', $property->address) }}" required placeholder="Ej: Barrio Central" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">URL Google Maps</label>
                <input type="text" name="google_maps_url" value="{{ old('google_maps_url', $property->google_maps_url) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- 3. Distribución y Características Internas -->
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Número de Dormitorios</label>
                <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Detalle de Dormitorios</label>
                <input type="text" name="bedrooms_detail" value="{{ old('bedrooms_detail', $property->bedrooms_detail) }}" placeholder="Ej: 1 Master con Baño" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Baños Completos</label>
                <input type="number" name="bathrooms_full" value="{{ old('bathrooms_full', $property->bathrooms_full) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Baños Sociales / Medios</label>
                <input type="number" name="bathrooms_half" value="{{ old('bathrooms_half', $property->bathrooms_half) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Garajes</label>
                <input type="number" name="garages" value="{{ old('garages', $property->garages) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Detalle de Garajes</label>
                <input type="text" name="garages_detail" value="{{ old('garages_detail', $property->garages_detail) }}" placeholder="Ej: 1 Cubierto" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Áreas Sociales (Salas)</label>
                <input type="text" name="social_areas" value="{{ old('social_areas', $property->social_areas) }}" placeholder="Ej: 1 Amplia Sala y 1 de Estar" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Cocina</label>
                <input type="text" name="kitchen" value="{{ old('kitchen', $property->kitchen) }}" placeholder="Ej: Cocina Amoblada" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Exteriores / Patios</label>
                <input type="text" name="exteriors" value="{{ old('exteriors', $property->exteriors) }}" placeholder="Ej: Patio Frontal y Trasero" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Cuarto de Estudio</label>
                <input type="text" name="study_room" value="{{ old('study_room', $property->study_room) }}" placeholder="Ej: 1 Cuarto de Estudio" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Características Clave y Servicios Básicos -->
            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 p-5 bg-[#FDFBF7] border border-emerald-200 rounded-2xl">
                <span class="col-span-full uppercase text-[10px] text-emerald-800 font-extrabold tracking-wider mb-1">
                    <i class="fa-solid fa-check-to-slot mr-1 text-emerald-700"></i> Características Especiales y Servicios (Filtros del Catálogo)
                </span>
                
                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_jardin" value="1" {{ old('has_jardin', $property->has_jardin) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Tiene Jardín / Patio</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_balcon" value="1" {{ old('has_balcon', $property->has_balcon) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Tiene Balcón / Terraza</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_seguridad" value="1" {{ old('has_seguridad', $property->has_seguridad) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Tiene Seguridad / Guardia</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_agua" value="1" {{ old('has_agua', $property->has_agua) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Agua Potable</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_luz" value="1" {{ old('has_luz', $property->has_luz) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Luz / Electricidad</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_alcantarillado" value="1" {{ old('has_alcantarillado', $property->has_alcantarillado) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Alcantarillado</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_internet" value="1" {{ old('has_internet', $property->has_internet) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Internet / Fibra</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_piscina" value="1" {{ old('has_piscina', $property->has_piscina) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Piscina</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_bbq" value="1" {{ old('has_bbq', $property->has_bbq) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Área BBQ / Asador</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_amoblado" value="1" {{ old('has_amoblado', $property->has_amoblado) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Amoblado</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_mascotas" value="1" {{ old('has_mascotas', $property->has_mascotas) ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Acepta Mascotas</span>
                </label>
            </div>

            <!-- 4. Precio, Documentación y Medidas -->
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Precio ($)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $property->price) }}" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Condición de Precio</label>
                <select name="price_condition" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="Negociable" {{ old('price_condition', $property->price_condition) == 'Negociable' ? 'selected' : '' }}>Negociable</option>
                    <option value="Fijo" {{ old('price_condition', $property->price_condition) == 'Fijo' ? 'selected' : '' }}>Fijo</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Estado de Documentación</label>
                <input type="text" name="documentation_status" value="{{ old('documentation_status', $property->documentation_status) }}" placeholder="Ej: Escritura en Regla" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Antigüedad (Años)</label>
                <input type="number" name="antiquity_years" value="{{ old('antiquity_years', $property->antiquity_years) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Área de Terreno (m2)</label>
                <input type="number" step="0.01" name="land_area_m2" value="{{ old('land_area_m2', $property->land_area_m2) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Área de Construcción (m2)</label>
                <input type="number" step="0.01" name="construction_area_m2" value="{{ old('construction_area_m2', $property->construction_area_m2) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- 5. Multimedia, Estado y Redes -->
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Estado de Publicación</label>
                <select name="status" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="En Venta" {{ old('status', $property->status) == 'En Venta' ? 'selected' : '' }}>En Venta</option>
                    <option value="En Trámite" {{ old('status', $property->status) == 'En Trámite' ? 'selected' : '' }}>En Trámite</option>
                    <option value="Vendida" {{ old('status', $property->status) == 'Vendida' ? 'selected' : '' }}>Vendida</option>
                </select>
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">¿Bajó de Precio?</label>
                <select name="price_dropped" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="0" {{ old('price_dropped', $property->price_dropped) == '0' || old('price_dropped', $property->price_dropped) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('price_dropped', $property->price_dropped) == '1' || old('price_dropped', $property->price_dropped) == 1 ? 'selected' : '' }}>Sí (Mostrar en sección Bajaron de Precio)</option>
                </select>
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">URL Tour Virtual 360°</label>
                <input type="text" name="virtual_tour_url" value="{{ old('virtual_tour_url', $property->virtual_tour_url) }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Descripción / Detalles Adicionales</label>
                <textarea name="description" rows="3" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description', $property->description) }}</textarea>
            </div>

            <!-- GALERÍA INTERACTIVA DE FOTOGRAFÍAS -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-1">
                    <i class="fa-solid fa-images mr-2"></i> Galería de Fotografías Existentes
                </h3>
                <p class="text-[10px] text-gray-400 mb-3">Arrastra las fotos para cambiar su orden de aparición. Usa ⭐ para fijar la portada principal y 🗑️ para eliminar.</p>

                @if($property->images && $property->images->count() > 0)
                    <div id="sortable-gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                        @foreach($property->images->sortBy('position') as $image)
                            <div data-id="{{ $image->id }}" class="relative group bg-[#FDFBF7] border border-emerald-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition cursor-grab active:cursor-grabbing">
                                
                                <!-- Badge de Foto Principal -->
                                @if($image->is_primary)
                                    <span class="badge-primary absolute top-2 left-2 z-10 bg-[#2C4A3E] text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                        <i class="fa-solid fa-star text-amber-300"></i> Principal
                                    </span>
                                @endif

                                <!-- Previsualización de Imagen -->
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Foto propiedad" class="w-full h-32 object-cover">

                                <!-- Acciones Flotantes en Hover -->
                                <div class="absolute inset-0 bg-[#2C4A3E]/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[1px]">
                                    <button type="button" 
                                            onclick="setPrimaryImage({{ $image->id }})" 
                                            title="Marcar como Foto Principal"
                                            class="bg-amber-500 hover:bg-amber-600 text-white w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold transition shadow-sm">
                                        <i class="fa-solid fa-star"></i>
                                    </button>
                                    <button type="button" 
                                            onclick="deleteImage({{ $image->id }}, this)" 
                                            title="Eliminar Foto"
                                            class="bg-red-600 hover:bg-red-700 text-white w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold transition shadow-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-[#FDFBF7] border border-dashed border-emerald-200 rounded-2xl text-center text-gray-400 text-xs mb-4">
                        <i class="fa-solid fa-image text-2xl mb-1 text-emerald-300"></i>
                        <p>No hay fotografías registradas para esta propiedad.</p>
                    </div>
                @endif

                <!-- Campo para Subir Más Fotografías -->
<div class="mt-4">
    <label class="block mb-2 uppercase text-[10px] font-extrabold text-[#2C4A3E]">
        <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Agregar Nuevas Fotografías
    </label>
    
    <input type="file" 
           id="images-input" 
           name="images[]" 
           multiple 
           accept="image/*"
           onchange="previewNewImages(event)" 
           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#2C4A3E] file:text-white hover:file:bg-emerald-800 cursor-pointer border border-emerald-200 rounded-xl bg-[#FDFBF7] p-1 shadow-sm">
    
    <p class="text-[10px] text-gray-400 mt-1">Puedes seleccionar varios archivos simultáneamente. Se añadirán a las fotos existentes al guardar los cambios.</p>

    <!-- Contenedor donde se previsualizan las imágenes antes de guardar -->
    <div id="new-images-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-3"></div>

            <!-- 6. Redes Sociales y Difusión -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-4"><i class="fa-solid fa-share-nodes mr-2"></i> Redes Sociales y Contacto de Difusión</h3>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-youtube text-red-600 mr-1"></i> URL YouTube</label>
                <input type="text" name="url_youtube" value="{{ old('url_youtube', $property->url_youtube) }}" placeholder="https://youtube.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-instagram text-pink-600 mr-1"></i> URL Instagram</label>
                <input type="text" name="url_instagram" value="{{ old('url_instagram', $property->url_instagram) }}" placeholder="https://instagram.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-tiktok text-black mr-1"></i> URL TikTok</label>
                <input type="text" name="url_tiktok" value="{{ old('url_tiktok', $property->url_tiktok) }}" placeholder="https://tiktok.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-facebook text-blue-600 mr-1"></i> URL Facebook</label>
                <input type="text" name="url_facebook" value="{{ old('url_facebook', $property->url_facebook) }}" placeholder="https://facebook.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-solid fa-phone text-emerald-700 mr-1"></i> Teléfono de Contacto</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $property->contact_phone) }}" placeholder="Ej: 0987894025" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-solid fa-envelope text-emerald-700 mr-1"></i> Correo de Contacto</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $property->contact_email) }}" placeholder="ejemplo@inmobiliaria.com" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-emerald-100">
            <a href="{{ route('properties.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-300 transition">Cancelar</a>
            <button type="submit" class="px-6 py-2.5 bg-[#2C4A3E] text-white text-xs font-bold rounded-xl shadow-sm hover:bg-emerald-800 transition cursor-pointer">Actualizar Propiedad</button>
        </div>
    </form>
</div>

<!-- LIBRERÍA SORTABLE JS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
// Objeto global para acumular múltiples selecciones de imágenes nuevas
const selectedFilesDataTransfer = new DataTransfer();

document.addEventListener('DOMContentLoaded', function () {
    const gallery = document.getElementById('sortable-gallery');

    if (gallery) {
        new Sortable(gallery, {
            animation: 150,
            ghostClass: 'opacity-40',
            dragClass: 'shadow-2xl',
            onEnd: function () {
                const order = Array.from(gallery.children).map(item => item.getAttribute('data-id'));

                fetch("{{ route('properties.images.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Al reordenar, la primera imagen pasa a ser la principal automáticamente
                        if (gallery.children.length > 0) {
                            const newFirstId = gallery.children[0].getAttribute('data-id');
                            updatePrimaryBadgeUI(newFirstId);
                        }
                    } else {
                        alert(data.message || 'Error al guardar el orden.');
                    }
                })
                .catch(error => console.error('Error al guardar el orden:', error));
            }
        });
    }
});

// Función para actualizar visualmente la insignia de Foto Principal
function updatePrimaryBadgeUI(primaryId) {
    document.querySelectorAll('.badge-primary').forEach(badge => badge.remove());
    
    const selectedItem = document.querySelector(`[data-id="${primaryId}"]`);
    if (selectedItem) {
        const newBadge = document.createElement('span');
        newBadge.className = 'badge-primary absolute top-2 left-2 z-10 bg-[#2C4A3E] text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1';
        newBadge.innerHTML = '<i class="fa-solid fa-star text-amber-300"></i> Principal';
        selectedItem.prepend(newBadge);
    }
}

// Función para marcar una imagen como principal y moverla al inicio
function setPrimaryImage(imageId) {
    const url = "{{ route('properties.images.primary', ':id') }}".replace(':id', imageId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const gallery = document.getElementById('sortable-gallery');
            const targetCard = document.querySelector(`[data-id="${imageId}"]`);

            if (gallery && targetCard) {
                // Mover físicamente la tarjeta al primer lugar de la galería
                gallery.insertBefore(targetCard, gallery.firstChild);
            }
            updatePrimaryBadgeUI(imageId);
        } else {
            alert(data.message || 'Error al cambiar la foto principal.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Función para eliminar una foto existente de la galería
function deleteImage(imageId, buttonElement) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta fotografía?')) {
        return;
    }

    const url = "{{ route('properties.images.destroy', ':id') }}".replace(':id', imageId);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = buttonElement.closest('[data-id]');
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    card.remove();

                    const gallery = document.getElementById('sortable-gallery');
                    if (gallery && gallery.children.length === 0) {
                        gallery.parentElement.innerHTML = `
                            <div id="empty-gallery-msg" class="p-8 bg-[#FDFBF7] border border-dashed border-emerald-200 rounded-2xl text-center text-gray-400 text-sm">
                                <i class="fa-solid fa-image text-3xl mb-2 text-emerald-300"></i>
                                <p>No hay fotografías registradas para esta propiedad actualmente.</p>
                            </div>
                        `;
                    } else if (gallery && gallery.children.length > 0) {
                        // Si se eliminó la principal, reasignar insignia a la nueva primera foto
                        const newFirstId = gallery.children[0].getAttribute('data-id');
                        updatePrimaryBadgeUI(newFirstId);
                    }
                }, 300);
            }
        } else {
            alert(data.message || 'Error al eliminar la fotografía.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Previsualización acumulativa de imágenes nuevas
function previewNewImages(event) {
    const input = event.target;
    if (!input.files || input.files.length === 0) return;

    // Agregar archivos seleccionados al acumulador
    Array.from(input.files).forEach(file => {
        if (file.type.startsWith('image/')) {
            selectedFilesDataTransfer.items.add(file);
        }
    });

    // Sincronizar el input HTML
    input.files = selectedFilesDataTransfer.files;

    renderPendingPreviews();
}

// Renderizar tarjetas de previsualización para archivos pendientes
function renderPendingPreviews() {
    const previewContainer = document.getElementById('new-images-preview');
    if (!previewContainer) return;

    previewContainer.innerHTML = '';

    Array.from(selectedFilesDataTransfer.files).forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function (e) {
            const card = document.createElement('div');
            card.className = 'relative group bg-[#FDFBF7] border-2 border-dashed border-emerald-500 rounded-2xl overflow-hidden shadow-sm aspect-video flex items-center justify-center';

            card.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover">
                
                <span class="absolute top-2 left-2 z-10 bg-emerald-700 text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                    <i class="fa-solid fa-clock"></i> Pendiente
                </span>

                <button type="button" 
                        onclick="removePendingImage(${index})" 
                        title="Quitar de la selección" 
                        class="absolute top-2 right-2 z-10 bg-red-600 hover:bg-red-700 text-white w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition shadow-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            previewContainer.appendChild(card);
        };

        reader.readAsDataURL(file);
    });
}

// Eliminar una imagen individual de la selección pendiente antes de guardar
function removePendingImage(index) {
    const input = document.getElementById('images-input');
    const newDT = new DataTransfer();

    Array.from(selectedFilesDataTransfer.files).forEach((file, i) => {
        if (i !== index) {
            newDT.items.add(file);
        }
    });

    selectedFilesDataTransfer.items.clear();
    Array.from(newDT.files).forEach(file => selectedFilesDataTransfer.items.add(file));

    if (input) {
        input.files = selectedFilesDataTransfer.files;
    }

    renderPendingPreviews();
}
</script>
@endsection