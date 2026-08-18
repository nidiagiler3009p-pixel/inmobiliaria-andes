@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    
    <!-- Título de la Sección -->
    <div class="text-center mb-12">
        <h2 class="text-4xl font-extrabold text-[#2C4A3E]">Elige la asesoría que mejor se adapte a ti</h2>
        <p class="text-base font-medium text-gray-600 mt-2">Tenemos opciones flexibles para ayudarte a vender o arrendar tu propiedad con éxito.</p>
    </div>

    <!-- Alerta de éxito -->
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-2xl text-sm font-bold text-center max-w-2xl mx-auto shadow-sm flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-700 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alerta de errores de validación -->
    @if($errors->any())
        <div class="mb-8 p-4 bg-red-100 border border-red-300 text-red-900 rounded-2xl text-sm font-bold max-w-2xl mx-auto shadow-sm">
            <div class="flex items-center gap-2 mb-2 justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-700 text-base"></i>
                <span>Por favor, corrige los siguientes errores:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-red-800 text-left font-normal max-w-md mx-auto">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Contenedor general en Grid de 4 columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-16 items-start">

        <!-- Columna Lateral Izquierda -->
        <div class="flex flex-col gap-6">
            
            <!-- Bloque 1: Información general -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm">
                <h3 class="text-lg font-extrabold text-[#2C4A3E] mb-3">¿Cómo funcionan nuestras asesorías?</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-3">
                    En Inmobiliaria Los Andes del Ecuador te ofrecemos diferentes niveles de asesoría según tus necesidades.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Tú eliges cuánto apoyo necesitas y nosotros ponemos a tu disposición nuestra experiencia, tecnología y equipo profesional.
                </p>
            </div>

            <!-- Bloque 2: Contacto directo WhatsApp -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 flex-shrink-0">
                        <i class="fa-solid fa-clover text-base"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-[#2C4A3E]">¿No estás seguro de qué plan elegir?</h4>
                </div>
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Contáctanos y te ayudaremos a encontrar la opción ideal para tu propiedad.
                </p>
                <a href="https://wa.me/" target="_blank" rel="noopener noreferrer" class="w-full py-3 px-4 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-extrabold text-sm rounded-xl transition flex items-center justify-center gap-2 text-decoration-none shadow-sm">
                    <i class="fa-brands fa-whatsapp text-lg"></i> Hablar con un asesor
                </a>
            </div>

        </div>

        <!-- Tarjetas Comparativas de Planes -->
        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            
            <!-- Plan Gratis -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm flex flex-col justify-between h-full">
                <div>
                    <span class="text-xs font-black text-emerald-800 uppercase tracking-wider block mb-1">ASESORÍA</span>
                    <h3 class="text-3xl font-extrabold text-[#2C4A3E]">Gratis</h3>
                    <div class="my-3">
                        <span class="text-4xl font-black text-[#2C4A3E]">0%</span>
                        <span class="text-sm text-gray-500 font-semibold block mt-0.5">Por propiedad</span>
                    </div>
                    <div class="my-3 flex justify-center text-emerald-700">
                        <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500 mb-4 text-center">Ideal si solo necesitas apoyo en la promoción de tu propiedad.</p>
                    
                    <span class="text-sm font-extrabold text-[#2C4A3E] block mb-2">Incluye:</span>
                    <ul class="space-y-2 text-xs sm:text-sm font-medium text-gray-700 mb-6">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Asesoría para manejo de redes sociales</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Estrategias básicas de publicidad digital</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Diseño de banners publicitarios</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Consejos para mejorar visibilidad</span></li>
                    </ul>
                </div>
                <div>
                    <div class="bg-emerald-50/70 p-3 rounded-2xl border border-emerald-100 mb-4 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-800 text-sm mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-gray-600 leading-snug">Este plan es 100% gratuito y está enfocado únicamente en publicidad.</p>
                    </div>
                    <button type="button" onclick="selectPlan('Gratis')" class="w-full py-3 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-bold text-sm rounded-xl transition text-center block text-decoration-none shadow-sm">
                        Elegir plan gratuito
                    </button>
                </div>
            </div>

            <!-- Plan Estándar -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border-2 border-blue-600 shadow-md flex flex-col justify-between h-full relative">
                <div class="absolute -top-3.5 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white px-4 py-1 rounded-full text-xs font-black uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    más elegido <i class="fa-solid fa-star text-xs"></i>
                </div>
                <div>
                    <span class="text-xs font-black text-blue-800 uppercase tracking-wider block mb-1">ASESORÍA</span>
                    <h3 class="text-3xl font-extrabold text-[#2C4A3E]">Estándar</h3>
                    <div class="my-3">
                        <span class="text-4xl font-black text-[#2C4A3E]">3%</span>
                        <span class="text-sm text-gray-700 font-bold">de Comisión</span>
                        <span class="text-sm text-gray-500 font-semibold block mt-0.5">Por propiedad</span>
                    </div>
                    <div class="my-3 flex justify-center text-blue-600">
                        <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-house-laptop"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500 mb-4 text-center">Te ayudamos a promover tu propiedad con estrategias profesionales.</p>
                    
                    <span class="text-sm font-extrabold text-[#2C4A3E] block mb-2">Incluye lo del plan Gratis, más:</span>
                    <ul class="space-y-2 text-xs sm:text-sm font-medium text-gray-700 mb-6">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Publicación en nuestra plataforma web</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Marketing digital y estrategias de venta</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Fotografía profesional</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Fotos y vídeos con dron</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Publicidad en portales e impresos</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Seguimiento y atención a clientes</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-blue-600 text-sm mt-0.5"></i> <span>Publicidad en radio local</span></li>
                    </ul>
                </div>
                <div>
                    <button type="button" onclick="selectPlan('Estándar')" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition text-center block shadow-md text-decoration-none">
                        Elegir plan estándar
                    </button>
                </div>
            </div>

            <!-- Plan Total -->
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm flex flex-col justify-between h-full relative">
                <div class="absolute -top-3.5 left-1/2 transform -translate-x-1/2 bg-[#1B4D3E] text-white px-4 py-1 rounded-full text-xs font-black uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    máxima cobertura <i class="fa-solid fa-shield-halved text-xs"></i>
                </div>
                <div>
                    <span class="text-xs font-black text-emerald-800 uppercase tracking-wider block mb-1">ASESORÍA</span>
                    <h3 class="text-3xl font-extrabold text-[#2C4A3E]">Total</h3>
                    <div class="my-3">
                        <span class="text-4xl font-black text-[#2C4A3E]">3%</span>
                        <span class="text-sm text-gray-700 font-bold">de Comisión</span>
                        <span class="text-xs text-emerald-800 font-bold block mt-0.5">+ Contrato de corretaje firmado obligatorio</span>
                    </div>
                    <div class="my-3 flex justify-center text-emerald-700">
                        <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-house-chimney-user"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500 mb-4 text-center">La solución completa para vender o arrendar sin preocupaciones.</p>
                    
                    <span class="text-sm font-extrabold text-[#2C4A3E] block mb-2">Incluye lo del plan Estándar, más:</span>
                    <ul class="space-y-2 text-xs sm:text-sm font-medium text-gray-700 mb-6">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Asesoría y gestión de trámites legales</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Elaboración y revisión de contratos</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Acompañamiento legal completo</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Vídeo 3D y recorrido virtual</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Informe de gestión y resultados</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <span>Estrategias avanzadas de cierre</span></li>
                    </ul>
                </div>
                <div>
                    <button type="button" onclick="selectPlan('Total')" class="w-full py-3 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-bold text-sm rounded-xl transition text-center block text-decoration-none shadow-sm">
                        Elegir plan total
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Formulario simplificado -->
    <div id="form-asesoria" class="max-w-3xl mx-auto bg-white/95 backdrop-blur-md p-8 md:p-10 rounded-3xl border border-emerald-100 shadow-md scroll-mt-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 border-b border-emerald-100 pb-4">
            <h3 class="text-2xl font-extrabold text-[#2C4A3E] flex items-center gap-2">
                <i class="fa-solid fa-file-contract text-emerald-800"></i> Solicita tu Asesoría Personalizada
            </h3>
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 w-fit">
                Plan: <span id="selected-plan-display" class="ml-1 uppercase">{{ old('plan_type', 'Estándar') }}</span>
            </span>
        </div>

        <form id="asesoria-form" action="{{ route('asesorias.store') }}" method="POST" class="space-y-5"> 
            @csrf
            
            <!-- Campo Oculto del Plan Seleccionado para el Backend -->
            <input type="hidden" id="plan_type" name="plan_type" value="{{ old('plan_type', 'Estándar') }}">

            <!-- 1. Información Principal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="full_name" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Nombre Completo</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="Ej: Juan Pérez" class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Teléfono / Celular</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Ej: 0991234567" class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
            </div>

            <!-- 2. Ubicación y Plan Seleccionado -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="ciudad" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Ciudad / Ubicación</label>
                    <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad') }}" required placeholder="Ej: Riobamba, Guano, Quito..." class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
                <div>
                    <label for="selected_plan_badge" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Asesoría Escogida</label>
                    <input type="text" id="selected_plan_badge" readonly class="w-full px-4 py-3 rounded-xl border text-sm font-black text-center transition-all duration-300 shadow-sm cursor-not-allowed uppercase" value="ESTÁNDAR">
                </div>
            </div>

            <!-- 3. Datos de la Propiedad -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="property_type" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Tipo de Propiedad</label>
                    <select id="property_type" name="property_type" class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                        <option value="Casa" {{ old('property_type') == 'Casa' ? 'selected' : '' }}>Casa</option>
                        <option value="Departamento" {{ old('property_type') == 'Departamento' ? 'selected' : '' }}>Departamento</option>
                        <option value="Terreno" {{ old('property_type') == 'Terreno' ? 'selected' : '' }}>Terreno</option>
                        <option value="Comercial" {{ old('property_type') == 'Comercial' ? 'selected' : '' }}>Local / Oficina</option>
                    </select>
                </div>
                <div>
                    <label for="estimated_price" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Precio Estimado ($)</label>
                    <input type="number" step="0.01" id="estimated_price" name="estimated_price" value="{{ old('estimated_price') }}" placeholder="0.00" class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                </div>
            </div>

            <div>
                <label for="property_details" class="block text-sm font-extrabold text-[#2C4A3E] mb-1.5">Observaciones / Detalles</label>
                <textarea id="property_details" name="property_details" rows="3" placeholder="Detalla aquí las características de la propiedad (N° de habitaciones, baños, metraje) o cualquier consulta adicional..." class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">{{ old('property_details') }}</textarea>
            </div>

            <!-- Términos -->
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="accepted_terms" name="accepted_terms" value="1" {{ old('accepted_terms') ? 'checked' : '' }} required class="w-4 h-4 rounded border-emerald-300 text-emerald-800 focus:ring-emerald-800 cursor-pointer">
                <label for="accepted_terms" class="text-xs sm:text-sm text-gray-700 font-medium cursor-pointer">Acepto los términos, condiciones y políticas de privacidad.</label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-[#1B4D3E] hover:bg-emerald-900 text-white font-extrabold text-sm rounded-xl transition shadow-md mt-4">
                Enviar Solicitud de Asesoría
            </button>
        </form>
    </div>

</div>

<!-- Script para la selección dinámica de plan y estilos -->
<script>
    function selectPlan(planName) {
        // Actualiza el campo oculto
        const planInput = document.getElementById('plan_type');
        if (planInput) {
            planInput.value = planName;
        }

        // Actualiza la etiqueta del título del formulario
        const planDisplay = document.getElementById('selected-plan-display');
        if (planDisplay) {
            planDisplay.textContent = planName;
        }

        // Actualiza el campo bloqueado y aplica el color representativo
        const lockedInput = document.getElementById('selected_plan_badge');
        if (lockedInput) {
            lockedInput.value = 'PLAN ' + planName.toUpperCase();
            
            // Definición de clases según el plan
            if (planName === 'Gratis') {
                lockedInput.className = 'w-full px-4 py-3 rounded-xl border text-sm font-black text-center transition-all duration-300 shadow-sm cursor-not-allowed bg-emerald-100 border-emerald-300 text-emerald-900';
            } else if (planName === 'Estándar') {
                lockedInput.className = 'w-full px-4 py-3 rounded-xl border text-sm font-black text-center transition-all duration-300 shadow-sm cursor-not-allowed bg-blue-100 border-blue-400 text-blue-900';
            } else if (planName === 'Total') {
                lockedInput.className = 'w-full px-4 py-3 rounded-xl border text-sm font-black text-center transition-all duration-300 shadow-sm cursor-not-allowed bg-[#1B4D3E] border-[#1B4D3E] text-white';
            }
        }

        // Desplazamiento suave hacia el formulario
        const formElement = document.getElementById('form-asesoria');
        if (formElement) {
            formElement.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Al cargar la página se sincroniza el campo con la opción guardada o por defecto
    document.addEventListener('DOMContentLoaded', function() {
        const initialPlan = document.getElementById('plan_type')?.value || 'Estándar';
        selectPlan(initialPlan);
    });
</script>
@endsection