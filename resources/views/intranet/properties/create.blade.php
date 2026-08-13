@extends('layouts.admin')

@section('admin_content')
<div class="max-w-4xl mx-auto px-4 pb-12 font-sans">
    <div class="flex justify-between items-center mb-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl border border-emerald-100 shadow-sm">
        <h2 class="text-base font-extrabold text-[#2C4A3E] uppercase"><i class="fa-solid fa-plus-circle mr-2"></i> Registrar Nueva Propiedad</h2>
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

    <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data" class="bg-white/90 backdrop-blur-md p-8 rounded-3xl border border-emerald-100 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs font-bold text-[#2C4A3E]">
            
            <!-- 1. Clasificación y Gestión Interna -->
            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Asesor Responsable / Asignado</label>
                <select name="user_id" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="">Seleccione un asesor...</option>
                    @foreach($asesores as $asesor)
                        <option value="{{ $asesor->id }}" {{ old('user_id') == $asesor->id ? 'selected' : '' }}>{{ $asesor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Tipo de Inmueble</label>
                <select name="property_type" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="Casa" {{ old('property_type') == 'Casa' ? 'selected' : '' }}>Casa</option>
                    <option value="Terrenos" {{ old('property_type') == 'Terrenos' ? 'selected' : '' }}>Terrenos</option>
                    <option value="Comerciales" {{ old('property_type') == 'Comerciales' ? 'selected' : '' }}>Comerciales</option>
                    <option value="Oficinas" {{ old('property_type') == 'Oficinas' ? 'selected' : '' }}>Proyectos / Oficinas</option>
                    <option value="Locales" {{ old('property_type') == 'Locales' ? 'selected' : '' }}>Locales</option>
                    <option value="Terrenos Grandes" {{ old('property_type') == 'Terrenos Grandes' ? 'selected' : '' }}>Terrenos Grandes</option>
                    <option value="Departamentos" {{ old('property_type') == 'Departamentos' ? 'selected' : '' }}>Departamentos</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Tipo de Operación</label>
                <select name="service_type" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="Venta" {{ old('service_type') == 'Venta' ? 'selected' : '' }}>Venta</option>
                    <option value="Arriendo" {{ old('service_type') == 'Arriendo' ? 'selected' : '' }}>Arriendo</option>
                </select>
            </div>

            <!-- Datos del Propietario -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-4"><i class="fa-solid fa-user-shield mr-2"></i> Datos del Propietario</h3>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Nombre del Propietario</label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" placeholder="Ej: Juan Pérez" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Teléfono del Propietario</label>
                <input type="text" name="owner_phone" value="{{ old('owner_phone') }}" placeholder="Ej: 0991234567" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Cédula / DNI del Propietario</label>
                <input type="text" name="owner_dni" value="{{ old('owner_dni') }}" placeholder="Ej: 0601234567" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Correo del Propietario</label>
                <input type="email" name="owner_email" value="{{ old('owner_email') }}" placeholder="ejemplo@correo.com" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- 2. Información General y Ubicación -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-4"><i class="fa-solid fa-location-dot mr-2"></i> Información General y Ubicación</h3>
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Título de la Propiedad</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ej: CASA DE VENTA EN CONJUNTO" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Insignia Izquierda (Badge Left)</label>
                <input type="text" name="badge_left" value="{{ old('badge_left') }}" placeholder="Ej: Cerca de Plaza y la Villa" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Insignia Derecha (Badge Right)</label>
                <input type="text" name="badge_right" value="{{ old('badge_right') }}" placeholder="Ej: a 3 cuadras..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Ciudad / Ubicación (Provincia/Zona)</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Ej: Guano, Chimborazo" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Dirección / Sector</label>
                <input type="text" name="address" value="{{ old('address') }}" required placeholder="Ej: Barrio Central" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">URL Google Maps</label>
                <input type="text" name="google_maps_url" value="{{ old('google_maps_url') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- 3. Distribución y Características Internas -->
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Número de Dormitorios</label>
                <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Detalle de Dormitorios</label>
                <input type="text" name="bedrooms_detail" value="{{ old('bedrooms_detail') }}" placeholder="Ej: 1 Master con Baño" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Baños Completos</label>
                <input type="number" name="bathrooms_full" value="{{ old('bathrooms_full') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Baños Sociales / Medios</label>
                <input type="number" name="bathrooms_half" value="{{ old('bathrooms_half') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Garajes</label>
                <input type="number" name="garages" value="{{ old('garages') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Detalle de Garajes</label>
                <input type="text" name="garages_detail" value="{{ old('garages_detail') }}" placeholder="Ej: 1 Cubierto" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Áreas Sociales (Salas)</label>
                <input type="text" name="social_areas" value="{{ old('social_areas') }}" placeholder="Ej: 1 Amplia Sala y 1 de Estar" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Cocina</label>
                <input type="text" name="kitchen" value="{{ old('kitchen') }}" placeholder="Ej: Cocina Amoblada" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Exteriores / Patios</label>
                <input type="text" name="exteriors" value="{{ old('exteriors') }}" placeholder="Ej: Patio Frontal y Trasero" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Cuarto de Estudio</label>
                <input type="text" name="study_room" value="{{ old('study_room') }}" placeholder="Ej: 1 Cuarto de Estudio" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Características Clave y Servicios Básicos (Filtros de Barra Lateral Sincronizados) -->
            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 p-5 bg-[#FDFBF7] border border-emerald-200 rounded-2xl">
                <span class="col-span-full uppercase text-[10px] text-emerald-800 font-extrabold tracking-wider mb-1">
                    <i class="fa-solid fa-check-to-slot mr-1 text-emerald-700"></i> Características Especiales y Servicios (Filtros del Catálogo)
                </span>
                
                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_jardin" value="1" {{ old('has_jardin') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Tiene Jardín / Patio</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_balcon" value="1" {{ old('has_balcon') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Tiene Balcón / Terraza</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_seguridad" value="1" {{ old('has_seguridad') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Tiene Seguridad / Guardia</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_agua" value="1" {{ old('has_agua') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Agua Potable</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_luz" value="1" {{ old('has_luz') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Luz / Electricidad</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_alcantarillado" value="1" {{ old('has_alcantarillado') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Alcantarillado</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_internet" value="1" {{ old('has_internet') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Internet / Fibra</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_piscina" value="1" {{ old('has_piscina') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Piscina</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_bbq" value="1" {{ old('has_bbq') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Área BBQ / Asador</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_amoblado" value="1" {{ old('has_amoblado') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Amoblado</span>
                </label>

                <label class="flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="has_mascotas" value="1" {{ old('has_mascotas') ? 'checked' : '' }} class="rounded border-emerald-300 text-[#2C4A3E] focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-[#2C4A3E]">Acepta Mascotas</span>
                </label>
            </div>

            <!-- 4. Precio, Documentación y Medidas -->
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Precio ($)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" required class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Condición de Precio</label>
                <select name="price_condition" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="Negociable" {{ old('price_condition') == 'Negociable' ? 'selected' : '' }}>Negociable</option>
                    <option value="Fijo" {{ old('price_condition') == 'Fijo' ? 'selected' : '' }}>Fijo</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Estado de Documentación</label>
                <input type="text" name="documentation_status" value="{{ old('documentation_status') }}" placeholder="Ej: Escritura en Regla" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Antigüedad (Años)</label>
                <input type="number" name="antiquity_years" value="{{ old('antiquity_years') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Área de Terreno (m2)</label>
                <input type="number" step="0.01" name="land_area_m2" value="{{ old('land_area_m2') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Área de Construcción (m2)</label>
                <input type="number" step="0.01" name="construction_area_m2" value="{{ old('construction_area_m2') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- 5. Multimedia, Estado y Redes -->
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Estado de Publicación</label>
                <select name="status" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="En Venta" {{ old('status') == 'En Venta' ? 'selected' : '' }}>En Venta</option>
                    <option value="En Trámite" {{ old('status') == 'En Trámite' ? 'selected' : '' }}>En Trámite</option>
                    <option value="Vendida" {{ old('status') == 'Vendida' ? 'selected' : '' }}>Vendida</option>
                </select>
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">¿Bajó de Precio?</label>
                <select name="price_dropped" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    <option value="0" {{ old('price_dropped') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('price_dropped') == '1' ? 'selected' : '' }}>Sí (Mostrar en sección Bajaron de Precio)</option>
                </select>
            </div>
            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500">URL Tour Virtual 360°</label>
                <input type="text" name="virtual_tour_url" value="{{ old('virtual_tour_url') }}" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Descripción / Detalles Adicionales</label>
                <textarea name="description" rows="3" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
            </div>

            <!-- Fotografías para la Galería -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <label class="block mb-2 uppercase text-[10px] text-gray-500">Fotografías de la Propiedad (Galería Múltiple)</label>
                <input type="file" name="images[]" multiple class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#2C4A3E] file:text-white hover:file:bg-emerald-800 cursor-pointer">
            </div>

            <!-- 6. Redes Sociales y Difusión -->
            <div class="md:col-span-2 pt-4 border-t border-emerald-100">
                <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase mb-4"><i class="fa-solid fa-share-nodes mr-2"></i> Redes Sociales y Contacto de Difusión</h3>
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-youtube text-red-600 mr-1"></i> URL YouTube</label>
                <input type="text" name="url_youtube" value="{{ old('url_youtube') }}" placeholder="https://youtube.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-instagram text-pink-600 mr-1"></i> URL Instagram</label>
                <input type="text" name="url_instagram" value="{{ old('url_instagram') }}" placeholder="https://instagram.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-tiktok text-black mr-1"></i> URL TikTok</label>
                <input type="text" name="url_tiktok" value="{{ old('url_tiktok') }}" placeholder="https://tiktok.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-brands fa-facebook text-blue-600 mr-1"></i> URL Facebook</label>
                <input type="text" name="url_facebook" value="{{ old('url_facebook') }}" placeholder="https://facebook.com/..." class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-solid fa-phone text-emerald-700 mr-1"></i> Teléfono WhatsApp (Difusión)</label>
                <input type="text" name="whatsapp_phone" value="{{ old('whatsapp_phone') }}" placeholder="Ej: 0988059187" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block mb-2 uppercase text-[10px] text-gray-500"><i class="fa-solid fa-envelope text-emerald-700 mr-1"></i> Correo de Contacto</label>
                <input type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="ejemplo@inmobiliaria.com" class="w-full bg-[#FDFBF7] border border-emerald-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-emerald-100">
            <a href="{{ route('properties.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-300 transition">Cancelar</a>
            <button type="submit" class="px-6 py-2.5 bg-[#2C4A3E] text-white text-xs font-bold rounded-xl shadow-sm hover:bg-emerald-800 transition cursor-pointer">Guardar Propiedad Completa</button>
        </div>
    </form>
</div>
@endsection