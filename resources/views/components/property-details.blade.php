@props(['property'])

<div class="space-y-4 text-gray-800">
    <div>
        <span class="text-[10px] tracking-widest text-emerald-600 uppercase font-bold block">Inmobiliaria Los Andes del Ecuador</span>
        <h1 class="text-xl font-extrabold text-gray-900 mt-1">{{ $property->title }}</h1>
    </div>

    <!-- Insignias -->
    @if(!empty($property->badge_left) || !empty($property->badge_right))
    <div class="flex flex-wrap gap-1">
        @if(!empty($property->badge_left))
            <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded-md font-bold">{{ $property->badge_left }}</span>
        @endif
        @if(!empty($property->badge_right))
            <span class="bg-emerald-50 text-emerald-800 text-[10px] px-2 py-0.5 rounded-md font-bold border border-emerald-200">{{ $property->badge_right }}</span>
        @endif
    </div>
    @endif

    @if(!empty($property->address) || !empty($property->location))
    <div class="flex items-start gap-2 text-sm text-gray-600">
        <i class="fa-solid fa-location-dot text-emerald-600 mt-1"></i>
        <div>
            @if(!empty($property->address))
                <span class="block font-medium">{{ $property->address }}</span>
            @endif
            @if(!empty($property->location))
                <span class="text-xs text-gray-400">Ciudad / Ubicación: {{ $property->location }}</span>
            @endif
        </div>
    </div>
    @endif

    <!-- PRECIO -->
    @if(!empty($property->price))
    <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-between">
        <div>
            <span class="text-[10px] text-emerald-800 uppercase font-bold block">Precio</span>
            <span class="text-lg font-black text-emerald-900">${{ number_format($property->price, 2) }}</span>
            @if(!empty($property->price_condition))
                <span class="block text-[10px] text-gray-500 font-normal">({{ $property->price_condition }})</span>
            @endif
        </div>
        @if(!empty($property->status))
            <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-xl uppercase">
                {{ $property->status }}
            </span>
        @endif
    </div>
    @endif

    <!-- CARACTERÍSTICAS DETALLADAS -->
    <div class="space-y-2 text-xs font-bold pt-2 border-t">
        @if(!empty($property->bedrooms) && $property->bedrooms > 0)
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-bed text-emerald-600 w-4 text-center"></i>
            <span>{{ $property->bedrooms }} Dormitorios {{ !empty($property->bedrooms_detail) ? '('.$property->bedrooms_detail.')' : '' }}</span>
        </div>
        @endif

        @if((!empty($property->bathrooms_full) && $property->bathrooms_full > 0) || (!empty($property->bathrooms_half) && $property->bathrooms_half > 0))
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-bath text-emerald-600 w-4 text-center"></i>
            <span>
                @if(!empty($property->bathrooms_full) && $property->bathrooms_full > 0)
                    {{ $property->bathrooms_full }} Completos
                @endif
                @if(!empty($property->bathrooms_half) && $property->bathrooms_half > 0)
                    / {{ $property->bathrooms_half }} Medios
                @endif
            </span>
        </div>
        @endif

        @if(!empty($property->garages) && $property->garages > 0)
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-warehouse text-emerald-600 w-4 text-center"></i>
            <span>{{ $property->garages }} Garajes {{ !empty($property->garages_detail) ? '('.$property->garages_detail.')' : '' }}</span>
        </div>
        @endif

        @if(!empty($property->social_areas))
        <div class="flex items-center gap-2"><i class="fa-solid fa-couch text-emerald-600 w-4 text-center"></i> <span>{{ $property->social_areas }}</span></div>
        @endif
        @if(!empty($property->kitchen))
        <div class="flex items-center gap-2"><i class="fa-solid fa-utensils text-emerald-600 w-4 text-center"></i> <span>{{ $property->kitchen }}</span></div>
        @endif
        @if(!empty($property->exteriors))
        <div class="flex items-center gap-2"><i class="fa-solid fa-tree text-emerald-600 w-4 text-center"></i> <span>{{ $property->exteriors }}</span></div>
        @endif
        @if(!empty($property->study_room))
        <div class="flex items-center gap-2"><i class="fa-solid fa-book text-emerald-600 w-4 text-center"></i> <span>{{ $property->study_room }}</span></div>
        @endif

        <div class="grid grid-cols-2 gap-2 pt-1 text-xs">
            @if(!empty($property->property_type))
            <div><span class="text-gray-400 text-[10px] block">Tipo:</span> {{ $property->property_type }}</div>
            @endif
            @if(!empty($property->service_type))
            <div><span class="text-gray-400 text-[10px] block">Operación:</span> {{ $property->service_type }}</div>
            @endif
        </div>

        @if(!empty($property->documentation_status))
        <div class="flex items-start gap-2 pt-1 border-t">
            <i class="fa-solid fa-file-shield text-emerald-600 w-4 text-center mt-0.5"></i>
            <span>Papeles: {{ $property->documentation_status }}</span>
        </div>
        @endif

        <!-- CARACTERÍSTICAS BOOLEANAS -->
        <div class="pt-2 flex flex-wrap gap-1 border-t">
            @if($property->has_jardin) <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded font-bold">Jardín/Patio</span> @endif
            @if($property->has_balcon) <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded font-bold">Balcón/Terraza</span> @endif
            @if($property->has_seguridad) <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded font-bold">Seguridad</span> @endif
            @if($property->has_agua) <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded font-bold">Agua</span> @endif
            @if($property->has_luz) <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded font-bold">Luz</span> @endif
            @if($property->has_alcantarillado) <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded font-bold">Alcantarillado</span> @endif
        </div>

        @if(!empty($property->antiquity_years))
        <div class="flex items-center gap-2 pt-1">
            <i class="fa-solid fa-calendar-days text-emerald-600 w-4 text-center"></i>
            <span>{{ $property->antiquity_years }} años de antigüedad</span>
        </div>
        @endif
    </div>

    <!-- ÁREAS M2 -->
    @if(!empty($property->land_area_m2) || !empty($property->construction_area_m2))
    <div class="grid grid-cols-2 gap-2 pt-2 border-t text-xs font-bold">
        @if(!empty($property->land_area_m2))
        <div class="p-2 bg-gray-50 rounded-xl border flex items-center gap-2">
            <i class="fa-solid fa-ruler-combined text-emerald-600"></i>
            <div>
                <span class="block text-[9px] text-gray-400 uppercase">Área Terreno</span>
                <span>{{ $property->land_area_m2 }} m²</span>
            </div>
        </div>
        @endif
        @if(!empty($property->construction_area_m2))
        <div class="p-2 bg-gray-50 rounded-xl border flex items-center gap-2">
            <i class="fa-solid fa-house text-emerald-600"></i>
            <div>
                <span class="block text-[9px] text-gray-400 uppercase">Construcción</span>
                <span>{{ $property->construction_area_m2 }} m²</span>
            </div>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($property->description))
    <div class="pt-3 border-t text-xs">
        <span class="block font-bold text-gray-700 uppercase mb-1">Descripción</span>
        <p class="text-gray-600 leading-relaxed">{{ $property->description }}</p>
    </div>
    @endif

    <!-- BOTÓN DE ENVÍO DE MENSAJE Y GESTIÓN DE CITAS -->
    <div class="pt-4 border-t mt-4">
        <button type="button" onclick="openClientMessageModal('{{ $property->id }}', '{{ $property->title }}')" class="w-full bg-[#2d4a3e] text-white text-xs font-bold py-2.5 px-4 rounded-xl hover:bg-opacity-90 transition flex items-center justify-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            Enviar Mensaje / Agendar Cita
        </button>
    </div>

    <!-- ENLACES, MAPAS Y REDES SOCIALES -->
    <div class="pt-3 border-t space-y-2 text-xs">
        <span class="block text-[10px] text-gray-400 uppercase font-extrabold">Enlaces y Recorridos</span>
        <div class="flex flex-wrap gap-2">
            @if(!empty($property->google_maps_url))
                <a href="{{ $property->google_maps_url }}" target="_blank" class="px-3 py-1.5 bg-emerald-50 text-emerald-800 font-bold rounded-xl border border-emerald-200 hover:bg-emerald-100 transition"><i class="fa-solid fa-map-location-dot mr-1"></i> Mapa</a>
            @endif
            @if(!empty($property->virtual_tour_url))
                <a href="{{ $property->virtual_tour_url }}" target="_blank" class="px-3 py-1.5 bg-emerald-50 text-emerald-800 font-bold rounded-xl border border-emerald-200 hover:bg-emerald-100 transition"><i class="fa-solid fa-vr-cardboard mr-1"></i> Tour 360°</a>
            @endif
            @if(!empty($property->url_youtube))
                <a href="{{ $property->url_youtube }}" target="_blank" class="p-2 bg-red-50 text-red-600 rounded-xl border border-red-200 hover:bg-red-100 transition"><i class="fa-brands fa-youtube text-sm"></i></a>
            @endif
            @if(!empty($property->url_instagram))
                <a href="{{ $property->url_instagram }}" target="_blank" class="p-2 bg-pink-50 text-pink-600 rounded-xl border border-pink-200 hover:bg-pink-100 transition"><i class="fa-brands fa-instagram text-sm"></i></a>
            @endif
            @if(!empty($property->url_tiktok))
                <a href="{{ $property->url_tiktok }}" target="_blank" class="p-2 bg-gray-100 text-black rounded-xl border border-gray-300 hover:bg-gray-200 transition"><i class="fa-brands fa-tiktok text-sm"></i></a>
            @endif
            @if(!empty($property->url_facebook))
                <a href="{{ $property->url_facebook }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 rounded-xl border border-blue-200 hover:bg-blue-100 transition"><i class="fa-brands fa-facebook text-sm"></i></a>
            @endif
        </div>
    </div>

    <!-- MODAL / FORMULARIO LATERAL PARA EL CLIENTE FINAL EN EL CATÁLOGO -->
    <div id="clientMessageModal" class="fixed inset-0 overflow-hidden z-50 hidden">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeClientMessageModal()"></div>
        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl p-6 flex flex-col justify-between border-l border-[#e2dcc8]">
                <div class="overflow-y-auto max-h-full pr-1">
                    <div class="flex items-center justify-between pb-4 border-b border-[#e2dcc8]">
                        <h2 class="text-lg font-bold text-[#2d4a3e]">Contacto y Cita Inmobiliaria</h2>
                        <button type="button" onclick="closeClientMessageModal()" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
                    </div>
                    
                    <!-- ALERTA DE ÉXITO DE LARAVEL -->
                    @if(session('success'))
                        <div class="mt-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs rounded-xl font-bold flex items-center justify-between shadow-sm">
                            <span>{{ session('success') }}</span>
                            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold ml-2">✕</button>
                        </div>
                    @endif

                    <form action="{{ route('intranet.clients.send-message-appointment') }}" method="POST" class="space-y-4 mt-4">
                        @csrf
                        <input type="hidden" name="channel" value="Web">
                        <input type="hidden" name="property_id" id="modal_property_id" value="{{ $property->id }}">

                        <div>
                            <label class="block text-xs font-semibold text-[#2d4a3e] mb-1">Nombres y Apellidos</label>
                            <input type="text" name="name" class="w-full text-xs border border-[#e2dcc8] rounded-xl p-2.5 bg-white text-[#2d4a3e]" placeholder="Ingrese su nombre completo" required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#2d4a3e] mb-1">Teléfono / Celular</label>
                            <input type="text" name="phone" class="w-full text-xs border border-[#e2dcc8] rounded-xl p-2.5 bg-white text-[#2d4a3e]" placeholder="Ej. 0998887777" required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#2d4a3e] mb-1">¿Desea agendar una cita?</label>
                            <p class="text-[10px] text-gray-500 mb-1">Despliegue y seleccione fecha y hora si desea concretar la cita de inmediato; de lo contrario, déjelo en blanco.</p>
                            <input type="datetime-local" name="appointment_date" class="w-full text-xs border border-[#e2dcc8] rounded-xl p-2.5 bg-white text-[#2d4a3e]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#2d4a3e] mb-1">Descripción / Observaciones</label>
                            <textarea name="location_reference" rows="3" class="w-full text-xs border border-[#e2dcc8] rounded-xl p-2.5 bg-[#f9f8f6]" placeholder="Escriba su consulta o detalles..." required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#2d4a3e] text-white text-xs font-bold py-3 rounded-xl hover:bg-opacity-90 transition">
                            Enviar Solicitud / Agendar Cita
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openClientMessageModal(propertyId, propertyTitle) {
            document.getElementById('clientMessageModal').classList.remove('hidden');
            if(propertyId) {
                document.getElementById('modal_property_id').value = propertyId;
            }
        }
        function closeClientMessageModal() {
            document.getElementById('clientMessageModal').classList.add('hidden');
        }

        // Si Laravel devuelve un mensaje de éxito, mantenemos el modal abierto automáticamente
        @if(session('success'))
            document.addEventListener("DOMContentLoaded", function() {
                openClientMessageModal();
            });
        @endif
    </script>
</div>