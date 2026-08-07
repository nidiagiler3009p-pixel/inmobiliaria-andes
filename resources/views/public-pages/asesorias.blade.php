@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    
    <!-- Título de la Sección -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-[#2C4A3E]">Elige la asesoría que mejor se adapte a ti</h2>
        <p class="text-sm text-gray-600 mt-2">Tenemos opciones flexibles para ayudarte a vender o arrendar tu propiedad con éxito.</p>
    </div>

    <!-- Alerta de éxito -->
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold text-center max-w-2xl mx-auto">
            {{ session('success') }}
        </div>
    @endif

    <!-- Contenedor general en Grid de 4 columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-16 items-start">

        <!-- Columna Lateral Izquierda -->
        <div class="flex flex-col gap-6">
            
            <!-- Bloque 1 -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm">
                <h3 class="text-base font-extrabold text-[#2C4A3E] mb-3">¿Cómo funcionan nuestras asesorías?</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-3">
                    En Inmobiliaria Los Andes del Ecuador te ofrecemos diferentes niveles de asesoría según tus necesidades.
                </p>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Tú eliges cuánto apoyo necesitas y nosotros ponemos a tu disposición nuestra experiencia, tecnología y equipo profesional.
                </p>
            </div>

            <!-- Bloque 2 -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 flex-shrink-0">
                        <i class="fa-solid fa-clover text-sm"></i>
                    </div>
                    <h4 class="text-sm font-extrabold text-[#2C4A3E]">¿No estás seguro de qué plan elegir?</h4>
                </div>
                <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                    Contáctanos y te ayudaremos a encontrar la opción ideal para tu propiedad.
                </p>
                <a href="https://wa.me/" target="_blank" class="w-full py-2.5 px-4 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 text-decoration-none shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Hablar con un asesor
                </a>
            </div>

            <!-- Bloque 3: Seleccionar Asesor de Preferencia -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 flex-shrink-0">
                        <i class="fa-solid fa-user-tie text-sm"></i>
                    </div>
                    <h4 class="text-sm font-extrabold text-[#2C4A3E]">Tienes un asesor de preferencia</h4>
                </div>
                <div class="mb-2">
                    <select name="advisor_id" form="asesoria-form" class="w-full px-3 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7] text-gray-700">
                        <option value="">Selecciona un asesor</option>
                        @if(isset($advisors))
                            @foreach($advisors as $advisor)
                                <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <p class="text-[11px] text-gray-500 leading-normal">
                    Si lo prefieres, puedes continuar sin seleccionar un asesor.
                </p>
            </div>

        </div>

        <!-- Tarjetas Comparativas de Planes -->
        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            
            <!-- Plan Gratis -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm flex flex-col justify-between h-full">
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block mb-1">ASESORÍA</span>
                    <h3 class="text-2xl font-extrabold text-[#2C4A3E]">Gratis</h3>
                    <div class="my-3">
                        <span class="text-3xl font-black text-[#2C4A3E]">0%</span>
                        <span class="text-xs text-gray-500 block mt-0.5">Por propiedad</span>
                    </div>
                    <div class="my-2 flex justify-center text-emerald-700">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-base">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 mb-4 text-center">Ideal si solo necesitas apoyo en la promoción de tu propiedad.</p>
                    
                    <span class="text-xs font-extrabold text-[#2C4A3E] block mb-2">Incluye:</span>
                    <ul class="space-y-1.5 text-[11px] text-gray-600 mb-4">
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Asesoría para manejo de redes y publicidad</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Asesoría para manejo de publicidad</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Diseño de banners publicitarios</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Consejos para mejorar la visibilidad de tu propiedad</span></li>
                    </ul>
                </div>
                <div>
                    <div class="bg-emerald-50/60 p-3 rounded-2xl border border-emerald-100 mb-4 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-800 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-[10px] text-gray-600 leading-tight">Este plan es 100% gratuito y está enfocado únicamente en asesoría para redes sociales y publicidad.</p>
                    </div>
                    <a href="#form-asesoria" onclick="document.getElementById('plan_type').value='Gratis';" class="w-full py-2.5 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-bold text-xs rounded-xl transition text-center block text-decoration-none shadow-sm">
                        Elegir plan gratuito
                    </a>
                </div>
            </div>

            <!-- Plan Estándar -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border-2 border-blue-600 shadow-md flex flex-col justify-between h-full relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white px-3 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    más elegido <i class="fa-solid fa-star text-[9px]"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-blue-800 uppercase tracking-wider block mb-1">ASESORÍA</span>
                    <h3 class="text-2xl font-extrabold text-[#2C4A3E]">Estándar</h3>
                    <div class="my-3">
                        <span class="text-2xl font-black text-[#2C4A3E]">3%</span>
                        <span class="text-xs text-gray-700 font-bold">de Comisión</span>
                        <span class="text-xs text-gray-500 block mt-0.5">Por propiedad</span>
                    </div>
                    <div class="my-2 flex justify-center text-blue-600">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-base">
                            <i class="fa-solid fa-house-laptop"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 mb-4 text-center">Te ayudamos a promover tu propiedad con estrategias profesionales.</p>
                    
                    <span class="text-xs font-extrabold text-[#2C4A3E] block mb-2">Incluye todo lo del plan Gratis, más:</span>
                    <ul class="space-y-1.5 text-[11px] text-gray-600 mb-4">
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Publicación en nuestra plataforma web</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Marketing digital y estrategias de venta</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Fotografía profesional</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Fotos y vídeos con dron</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Publicidad en portales inmobiliarios</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Publicidad en redes sociales</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Seguimiento y atención a clientes</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-blue-600 mt-0.5"></i> <span>Publicidad en radio local</span></li>
                    </ul>
                </div>
                <div>
                    <a href="#form-asesoria" onclick="document.getElementById('plan_type').value='Estándar';" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition text-center block shadow-md text-decoration-none">
                        Elegir plan estándar
                    </a>
                </div>
            </div>

            <!-- Plan Total -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm flex flex-col justify-between h-full relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-[#1B4D3E] text-white px-3 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    máxima cobertura <i class="fa-solid fa-shield-halved text-[9px]"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block mb-1">ASESORÍA</span>
                    <h3 class="text-2xl font-extrabold text-[#2C4A3E]">Total</h3>
                    <div class="my-3">
                        <span class="text-2xl font-black text-[#2C4A3E]">3%</span>
                        <span class="text-xs text-gray-700 font-bold">de Comisión</span>
                        <span class="text-[11px] text-emerald-800 font-bold block mt-0.5">+ Contrato de corretaje firmado obligatorio</span>
                    </div>
                    <div class="my-2 flex justify-center text-emerald-700">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-base">
                            <i class="fa-solid fa-house-chimney-user"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 mb-4 text-center">La solución completa para vender o arrendar sin preocupaciones.</p>
                    
                    <span class="text-xs font-extrabold text-[#2C4A3E] block mb-2">Incluye todo lo del plan Estándar, más:</span>
                    <ul class="space-y-1.5 text-[11px] text-gray-600 mb-4">
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Asesoría y gestión de trámites legales</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Elaboración y revisión de contratos</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Acompañamiento legal en todo el proceso</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Vídeo 3D y recorrido virtual</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Informe de gestión y resultados</span></li>
                        <li class="flex items-start gap-1.5"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> <span>Estrategias avanzadas de cierre de venta</span></li>
                    </ul>
                </div>
                <div>
                    <a href="#form-asesoria" onclick="document.getElementById('plan_type').value='Total';" class="w-full py-2.5 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-bold text-xs rounded-xl transition text-center block text-decoration-none shadow-sm">
                        Elegir plan total
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Formulario completo mapeado a la Base de Datos -->
    <div id="form-asesoria" class="max-w-2xl mx-auto bg-white/95 backdrop-blur-md p-8 rounded-3xl border border-emerald-100 shadow-sm">
        <h3 class="text-xl font-bold text-[#2C4A3E] mb-6 flex items-center gap-2">
            <i class="fa-solid fa-file-contract text-emerald-800"></i> Solicita tu Asesoría Personalizada
        </h3>
       <form id="asesoria-form" action="{{ route('asesorias.store') }}" method="POST" class="space-y-4"> 
        @csrf
    <!-- resto de tus campos -->
            
            <!-- Campo Oculto del Plan Seleccionado -->
            <input type="hidden" id="plan_type" name="plan_type" value="Estándar">

            <!-- 1. Datos Personales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Nombre Completo</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Correo Electrónico</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Teléfono / Celular</label>
                    <input type="text" name="phone" required class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Ciudad</label>
                    <input type="text" name="ciudad" required placeholder="Ej: Quito, Guayaquil..." class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">¿Cómo nos conoció?</label>
                    <input type="text" name="discovery_channel" placeholder="Redes sociales, recomendado..." class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
            </div>

            <hr class="border-emerald-100 my-4">

            <!-- 2. Datos de la Propiedad -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Tipo de Propiedad</label>
                    <select name="property_type" class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                        <option value="Casa">Casa</option>
                        <option value="Departamento">Departamento</option>
                        <option value="Terreno">Terreno</option>
                        <option value="Comercial">Local / Oficina</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Ubicación de la Propiedad</label>
                    <input type="text" name="property_location" placeholder="Sector o dirección aproximada" class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Precio Estimado ($)</label>
                    <input type="number" step="0.01" name="estimated_price" placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Detalles de la Propiedad</label>
                <textarea name="property_details" rows="2" placeholder="N° de habitaciones, baños, metros cuadrados..." class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]"></textarea>
            </div>

            <!-- 3. Preferencias Adicionales -->
            <div>
                <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Notas / Preferencias Adicionales</label>
                <textarea name="preferences_notes" rows="2" placeholder="Horarios de contacto, condiciones especiales..." class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]"></textarea>
            </div>

            <!-- Términos -->
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="accepted_terms" name="accepted_terms" value="1" required class="rounded border-emerald-300 text-emerald-800 focus:ring-emerald-800">
                <label for="accepted_terms" class="text-[11px] text-gray-600">Acepto los términos, condiciones y políticas de privacidad.</label>
            </div>

            <button type="submit" class="w-full py-3 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-bold text-xs rounded-xl transition shadow-md mt-4">
                Enviar Solicitud de Asesoría
            </button>
        </form>
    </div>

</div>
@endsection