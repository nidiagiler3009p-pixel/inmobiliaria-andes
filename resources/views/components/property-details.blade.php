@props([
    'property',
    'showContact' => true
])

@php
    $isPublic = $showContact && !request()->is('intranet*');
    $propId = $property->id ?? 'default';
    $info = session('appointment_confirmed') ?? [];
    $isTargetProperty = isset($info['property_id']) ? ($info['property_id'] == $propId) : true;
@endphp

<div class="space-y-4 text-gray-800">
    {{-- 1. ALERTA DE ÉXITO VISIBLE EN LA PÁGINA PRINCIPAL (FUERA DEL MODAL) --}}
    @if (session('success'))
        <div class="p-4 text-sm text-emerald-800 bg-emerald-100 border border-emerald-300 rounded-xl shadow-sm flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold ml-4">&times;</button>
        </div>
    @endif

    <!-- ENCABEZADO Y TÍTULO -->
    <div>
        <span class="text-[10px] tracking-widest text-emerald-600 uppercase font-bold block">
            Inmobiliaria Los Andes del Ecuador
        </span>
        <h1 class="text-xl font-extrabold text-gray-900 mt-1">
            {{ $property->title ?? 'Propiedad Sin Título' }}
        </h1>
    </div>

    <!-- INSIGNIAS (BADGES) -->
    @if(!empty($property->badge_left) || !empty($property->badge_right))
        <div class="flex flex-wrap gap-1">
            @if(!empty($property->badge_left))
                <span class="bg-emerald-100 text-emerald-900 text-[10px] px-2 py-0.5 rounded-md font-bold">
                    {{ $property->badge_left }}
                </span>
            @endif
            @if(!empty($property->badge_right))
                <span class="bg-emerald-50 text-emerald-800 text-[10px] px-2 py-0.5 rounded-md font-bold border border-emerald-200">
                    {{ $property->badge_right }}
                </span>
            @endif
        </div>
    @endif

    <!-- UBICACIÓN Y DIRECCIÓN -->
    @if(!empty($property->address) || !empty($property->location))
        <div class="flex items-start gap-2 text-sm text-gray-600">
            <i class="fa-solid fa-location-dot text-emerald-600 mt-1"></i>
            <div>
                @if(!empty($property->address))
                    <span class="block font-medium">{{ $property->address }}</span>
                @endif
                @if(!empty($property->location))
                    <span class="text-xs text-gray-400">Ubicación: {{ $property->location }}</span>
                @endif
            </div>
        </div>
    @endif

    <!-- PRECIO Y ESTADO -->
    <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-between">
        <div>
            <span class="text-[10px] text-emerald-800 uppercase font-bold block">Precio</span>
            <span class="text-lg font-black text-emerald-900">
                @if(!empty($property->price) && is_numeric($property->price))
                    ${{ number_format($property->price, 2) }}
                @else
                    Consultar Precio
                @endif
            </span>
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

    <!-- CARACTERÍSTICAS DETALLADAS -->
    <div class="space-y-2 text-xs font-bold pt-2 border-t border-gray-100">
        @if(!empty($property->bedrooms) && $property->bedrooms > 0)
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bed text-emerald-600 w-4 text-center"></i>
                <span>{{ $property->bedrooms }} Dormitorios</span>
            </div>
        @endif

        @if(!empty($property->bathrooms_full) || !empty($property->bathrooms_half))
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bath text-emerald-600 w-4 text-center"></i>
                <span>
                    @if(!empty($property->bathrooms_full)) {{ $property->bathrooms_full }} Baño(s) Completo(s) @endif
                    @if(!empty($property->bathrooms_half)) / {{ $property->bathrooms_half }} Medio(s) Baño(s) @endif
                </span>
            </div>
        @endif

        @if(!empty($property->garages) && $property->garages > 0)
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-warehouse text-emerald-600 w-4 text-center"></i>
                <span>{{ $property->garages }} Garajes</span>
            </div>
        @endif
    </div>

    <!-- ÁREAS EN M2 -->
    @if(!empty($property->land_area_m2) || !empty($property->construction_area_m2))
        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs font-bold">
            @if(!empty($property->land_area_m2))
                <div class="p-2 bg-gray-50 rounded-xl border flex items-center gap-2">
                    <i class="fa-solid fa-ruler-combined text-emerald-600"></i>
                    <div>
                        <span class="block text-[9px] text-gray-400 uppercase">Terreno</span>
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

    <!-- BOTÓN DE CONTACTO (SÓLO VISTA PÚBLICA) -->
    @if($isPublic)
        <div class="pt-4 border-t border-gray-100 mt-4">
            <button type="button" 
                    onclick="toggleClientModal('{{ $propId }}', true)" 
                    class="w-full bg-[#2d4a3e] text-white text-xs font-bold py-2.5 px-4 rounded-xl hover:bg-opacity-90 transition flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Enviar Mensaje / Agendar Cita
            </button>
        </div>
    @endif
</div>

<!-- MODAL DE CONTACTO -->
@if($isPublic)
    <div id="clientMessageModal-{{ $propId }}" class="fixed inset-0 overflow-hidden z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="toggleClientModal('{{ $propId }}', false)"></div>
        
        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl p-6 flex flex-col justify-between border-l border-[#e2dcc8] z-10">
                <div class="overflow-y-auto max-h-full pr-1">
                    <div class="flex items-center justify-between pb-4 border-b border-[#e2dcc8]">
                        <h2 id="modal-title" class="text-lg font-bold text-[#2d4a3e]">Contacto y Cita Inmobiliaria</h2>
                        <button type="button" onclick="toggleClientModal('{{ $propId }}', false)" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
                    </div>

                    {{-- TARJETA DE CONFIRMACIÓN DE CITA --}}
                    @if (session('appointment_confirmed') && $isTargetProperty)
                        <div id="confirmationCard-{{ $propId }}" class="my-4 bg-emerald-50 border-2 border-emerald-500 p-5 rounded-2xl shadow-md">
                            <h6 class="font-bold text-base text-emerald-900 mb-1">¡Cita Registrada Exitosamente!</h6>
                            <p class="text-sm text-gray-700 mb-4">
                                Hola <strong>{{ $info['name'] ?? $info['client_name'] ?? 'Cliente' }}</strong>, tu cita para ver la propiedad <strong>{{ $info['property_title'] ?? '' }}</strong> el día <strong>{{ $info['date'] ?? $info['appointment_date'] ?? '' }}</strong> a las <strong>{{ $info['time'] ?? $info['appointment_time'] ?? '' }}</strong> ha sido recibida.
                            </p>
                            <div class="flex gap-3 max-w-xs">
                                <form action="{{ route('public.appointments.confirm') }}" method="POST" class="w-1/2">
                                    @csrf
                                    <input type="hidden" name="appointment_id" value="{{ $info['appointment_id'] ?? '' }}">
                                    <button type="submit" class="w-full bg-emerald-600 text-white text-xs font-bold py-2.5 rounded-lg hover:bg-emerald-700 transition">
                                        SÍ, Confirmar
                                    </button>
                                </form>
                                <button type="button" onclick="modifyAppointment('{{ $propId }}', '{{ $info['appointment_id'] ?? '' }}')" class="w-1/2 bg-white text-gray-700 border border-gray-300 text-xs font-bold py-2.5 rounded-lg hover:bg-gray-100 transition">
                                    NO, Modificar
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- ERRORES DE VALIDACIÓN -->
                    @if($errors->any())
                        <div class="mt-4 p-3 bg-red-100 border border-red-300 text-red-900 text-xs rounded-xl">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- FORMULARIO PRINCIPAL -->
                    <form action="{{ route('public.messages.send') }}" method="POST" id="contactForm-{{ $propId }}" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id ?? '' }}">
                        <input type="hidden" name="appointment_id" id="appointment_id-{{ $propId }}" value="{{ $info['appointment_id'] ?? '' }}">

                        <div>
                            <label for="name-{{ $propId }}" class="block text-xs font-bold text-gray-700 mb-1">Nombres y Apellidos <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name-{{ $propId }}" class="w-full text-xs p-2.5 border rounded-xl" placeholder="Ingrese su nombre completo" value="{{ old('name', $info['name'] ?? '') }}" required>
                        </div>

                        <div>
                            <label for="phone-{{ $propId }}" class="block text-xs font-bold text-gray-700 mb-1">Teléfono / Celular <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone-{{ $propId }}" class="w-full text-xs p-2.5 border rounded-xl" placeholder="Ej. 0998887777" value="{{ old('phone', $info['phone'] ?? '') }}" required>
                        </div>

                        <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <input class="w-4 h-4 text-emerald-600 rounded border-gray-300" type="checkbox" id="toggleAppointment-{{ $propId }}" name="want_appointment" value="1" onchange="toggleAppointmentFields('{{ $propId }}')" {{ old('want_appointment') || !empty($info) ? 'checked' : '' }}>
                            <label class="text-xs font-bold text-gray-700 cursor-pointer" for="toggleAppointment-{{ $propId }}">
                                ¿Desea agendar una cita presencial?
                            </label>
                        </div>

                        <div id="appointmentSection-{{ $propId }}" style="{{ old('want_appointment') || !empty($info) ? 'display: block;' : 'display: none;' }}" class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="appointment_date-{{ $propId }}" class="block text-[11px] font-bold text-gray-700 mb-1">Fecha</label>
                                    <input type="date" name="appointment_date" id="appointment_date-{{ $propId }}" class="w-full text-xs p-2 border rounded-lg" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', $info['appointment_date'] ?? '') }}">
                                </div>
                                <div>
                                    <label for="appointment_time-{{ $propId }}" class="block text-[11px] font-bold text-gray-700 mb-1">Hora</label>
                                    <input type="time" name="appointment_time" id="appointment_time-{{ $propId }}" class="w-full text-xs p-2 border rounded-lg" value="{{ old('appointment_time', $info['appointment_time'] ?? '') }}">
                                </div>
                            </div>
                            <div>
                                <label for="meeting_place-{{ $propId }}" class="block text-[11px] font-bold text-gray-700 mb-1">Lugar de Encuentro</label>
                                <input type="text" name="meeting_place" id="meeting_place-{{ $propId }}" class="w-full text-xs p-2 border rounded-lg" placeholder="Ej: En el inmueble" value="{{ old('meeting_place', $info['meeting_place'] ?? '') }}">
                            </div>
                        </div>

                        <div>
                            <label for="message-{{ $propId }}" class="block text-xs font-bold text-gray-700 mb-1">Mensaje / Observación</label>
                            <textarea name="message" id="message-{{ $propId }}" class="w-full text-xs p-2.5 border rounded-xl" rows="3" placeholder="Escriba su consulta...">{{ old('message', $info['message'] ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-xl hover:bg-emerald-800 transition" id="submitBtn-{{ $propId }}">
                            {{ old('want_appointment') || !empty($info) ? 'Agendar Cita y Enviar Mensaje' : 'Enviar Mensaje' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (typeof toggleClientModal !== 'function') {
            window.toggleClientModal = function(id, show) {
                const modal = document.getElementById('clientMessageModal-' + (id || 'default'));
                if (modal) {
                    if (show) modal.classList.remove('hidden');
                    else modal.classList.add('hidden');
                }
            };
        }

        if (typeof toggleAppointmentFields !== 'function') {
            window.toggleAppointmentFields = function(propId) {
                const toggle = document.getElementById('toggleAppointment-' + propId);
                if (!toggle) return;
                const section = document.getElementById('appointmentSection-' + propId);
                const btn = document.getElementById('submitBtn-' + propId);

                if (section) section.style.display = toggle.checked ? 'block' : 'none';
                if (btn) btn.textContent = toggle.checked ? 'Agendar Cita y Enviar Mensaje' : 'Enviar Mensaje';
            };
        }

        if (typeof modifyAppointment !== 'function') {
            window.modifyAppointment = function(propId, appointmentId) {
                const card = document.getElementById('confirmationCard-' + propId);
                if (card) card.style.display = 'none';

                if (appointmentId) {
                    const inputId = document.getElementById('appointment_id-' + propId);
                    if (inputId) inputId.value = appointmentId;
                }

                const toggle = document.getElementById('toggleAppointment-' + propId);
                if (toggle) {
                    toggle.checked = true;
                    toggleAppointmentFields(propId);
                }
            };
        }

        @if(session('appointment_confirmed'))
            @php 
                $targetPropId = session('appointment_confirmed.property_id') ?? $propId; 
            @endphp
            @if($targetPropId == $propId)
                document.addEventListener("DOMContentLoaded", function() {
                    toggleClientModal('{{ $targetPropId }}', true);
                    toggleAppointmentFields('{{ $targetPropId }}');
                });
            @endif
        @elseif($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                toggleClientModal('{{ $propId }}', true);
                toggleAppointmentFields('{{ $propId }}');
            });
        @endif
    </script>
@endif