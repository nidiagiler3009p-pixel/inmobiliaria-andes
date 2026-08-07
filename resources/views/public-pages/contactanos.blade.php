@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Columna Izquierda: Información, Beneficios y WhatsApp (Ocupa 4 columnas) -->
        <div class="lg:col-span-4 space-y-6 bg-white/90 backdrop-blur-md p-6 rounded-3xl border border-emerald-100 shadow-sm">
            
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-full mb-3 text-emerald-800">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-[#2C4A3E]">Contáctanos</h2>
                <p class="text-xs text-gray-600 mt-1">Estamos aquí para ayudarte. Responderemos a la brevedad.</p>
            </div>

            <hr class="border-emerald-100">

            <!-- Lista de Beneficios -->
            <div class="space-y-4 text-xs text-[#2C4A3E]">
                <div class="flex items-start gap-3">
                    <div class="bg-emerald-50 p-2.5 rounded-full text-emerald-800 shrink-0">
                        <i class="fa-regular fa-clock text-sm"></i>
                    </div>
                    <div>
                        <p class="font-extrabold">Respuesta rápida</p>
                        <p class="text-gray-500 text-[11px] mt-0.5">Te respondemos en menos de 24 horas.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="bg-emerald-50 p-2.5 rounded-full text-emerald-800 shrink-0">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                    <div>
                        <p class="font-extrabold">Atención segura</p>
                        <p class="text-gray-500 text-[11px] mt-0.5">Tu información está protegida con total confidencialidad.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="bg-emerald-50 p-2.5 rounded-full text-emerald-800 shrink-0">
                        <i class="fa-solid fa-headset text-sm"></i>
                    </div>
                    <div>
                        <p class="font-extrabold">Asesoría personalizada</p>
                        <p class="text-gray-500 text-[11px] mt-0.5">Te acompañamos en todo el proceso.</p>
                    </div>
                </div>
            </div>

            <!-- Widget de WhatsApp Directo -->
            <div class="bg-[#F4F7F5] p-4 rounded-2xl border border-emerald-100">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-brands fa-whatsapp text-emerald-700 text-sm"></i>
                    <span class="font-bold text-xs text-[#2C4A3E]">¿Prefieres hablar?</span>
                </div>
                <p class="text-[11px] text-gray-600 mb-3">Escríbenos por WhatsApp y te atenderemos de inmediato.</p>
                <a href="https://wa.me/593988059187" target="_blank" class="block text-center py-2 bg-white hover:bg-emerald-50 text-[#2C4A3E] font-extrabold text-xs rounded-xl border border-emerald-200 transition shadow-xs">
                    098 805 9187
                </a>
            </div>

        </div>

        <!-- Columna Derecha: Formulario y Footer de Canales (Ocupa 8 columnas) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Tarjeta del Formulario -->
            <div class="bg-white/90 backdrop-blur-md p-8 rounded-3xl border border-emerald-100 shadow-sm">
                <h3 class="text-xl font-bold text-[#2C4A3E] mb-1">Envíanos tu mensaje</h3>
                <p class="text-xs text-gray-600 mb-6">Completa el formulario y nos pondremos en contacto contigo.</p>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-xl text-xs font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ url('/contacto') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Nombre y Apellido -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Nombre <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fa-regular fa-user text-xs"></i>
                                </span>
                                <input type="text" name="name" required placeholder="Ingresa tu nombre" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Apellido <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fa-regular fa-user text-xs"></i>
                                </span>
                                <!-- Modificado a last_name -->
                                <input type="text" name="last_name" required placeholder="Ingresa tu apellido" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                            </div>
                        </div>
                    </div>

                    <!-- Celular -->
                    <div>
                        <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Celular <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </span>
                            <input type="text" name="phone" required placeholder="Ej: +593 99 123 4567" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                        </div>
                    </div>

                    <!-- Dirección general -->
                    <div>
                        <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Dirección general <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-location-dot text-xs"></i>
                            </span>
                            <!-- Modificado a general_address -->
                            <input type="text" name="general_address" required placeholder="Ingresa tu dirección completa" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
                        </div>
                    </div>

                    <!-- Mensaje / Requerimientos -->
                    <div>
                        <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Déjanos tus requerimientos <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute top-3 left-3 text-gray-400">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </span>
                            <!-- Modificado a requirements_message -->
                            <textarea name="requirements_message" rows="4" required placeholder="Cuéntanos en qué podemos ayudarte..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]"></textarea>
                        </div>
                    </div>

                    <!-- Pie del formulario con botón y nota de privacidad -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                        <div class="flex items-center gap-2 text-[11px] text-gray-500">
                            <i class="fa-solid fa-shield text-emerald-700"></i>
                            <span>Tu información está protegida y no será compartida con terceros.</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#1B4D3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-md">
                            <i class="fa-regular fa-paper-plane mr-1"></i> Enviar mensaje
                        </button>
                    </div>
                </form>
            </div>

            <!-- Barra Inferior: Otros Canales de Contacto -->
            <div class="bg-white/90 backdrop-blur-md p-4 px-6 rounded-2xl border border-emerald-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#2C4A3E]">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-100 p-2 rounded-full text-emerald-900">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </div>
                    <span class="font-extrabold">Otros canales de contacto</span>
                </div>
                <div class="text-gray-600 text-center sm:text-right text-[11px]">
                    Email: <span class="font-semibold text-[#2C4A3E]">info@losandesinmobiliaria.com</span> | Horario: Lunes a Viernes 8:00 - 18:00
                </div>
            </div>

        </div>

    </div>
</div>
@endsection 