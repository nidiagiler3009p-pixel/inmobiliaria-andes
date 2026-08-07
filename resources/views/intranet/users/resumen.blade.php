@extends('layouts.admin')

@section('admin_content')
<div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 items-start justify-center pb-12">
    
    <!-- Tarjeta Central: Resumen y Bienvenida -->
    <div class="flex-1 bg-white/90 backdrop-blur-md rounded-3xl border border-emerald-100 shadow-sm p-8 max-w-2xl mx-auto">
        
        <!-- Encabezado de Bienvenida -->
        <div class="text-center mb-6">
            <div class="inline-flex p-3 bg-emerald-800 text-white rounded-full mb-2 shadow-sm">
                <i class="fa-solid fa-handshake text-xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-[#2C4A3E]">Inmobiliaria Los Andes</h2>
            <p class="text-xs font-bold text-gray-600 mt-1">¡Bienvenido, Administrador David Lema!</p>
        </div>

        <!-- Resumen Rápido -->
        <div class="mb-6">
            <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase tracking-wider mb-3">Resumen Rápido</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="bg-[#FDFBF7] p-3 rounded-2xl border border-emerald-100">
                    <span class="text-[10px] text-gray-500 font-bold block">Propiedades en Cartera</span>
                    <span class="text-sm font-extrabold text-[#2C4A3E]">15</span>
                </div>
                <div class="bg-[#FDFBF7] p-3 rounded-2xl border border-emerald-100">
                    <span class="text-[10px] text-gray-500 font-bold block">Trámites en Proceso</span>
                    <span class="text-sm font-extrabold text-[#2C4A3E]">7</span>
                </div>
                <div class="bg-[#FDFBF7] p-3 rounded-2xl border border-emerald-100">
                    <span class="text-[10px] text-gray-500 font-bold block">Citas Hoy</span>
                    <span class="text-sm font-extrabold text-[#2C4A3E]">3</span>
                </div>
                <div class="bg-[#FDFBF7] p-3 rounded-2xl border border-emerald-100">
                    <span class="text-[10px] text-gray-500 font-bold block">Asesores Activos</span>
                    <span class="text-sm font-extrabold text-[#2C4A3E]">5</span>
                </div>
                <div class="bg-[#FDFBF7] p-3 rounded-2xl border border-emerald-100 bg-red-50/50">
                    <span class="text-[10px] text-red-600 font-bold block">Citas No Atendidas</span>
                    <span class="text-sm font-extrabold text-red-700">1</span>
                </div>
                <div class="bg-[#FDFBF7] p-3 rounded-2xl border border-emerald-100">
                    <span class="text-[10px] text-gray-500 font-bold block">Agenda Semanal</span>
                    <span class="text-sm font-extrabold text-[#2C4A3E]">7</span>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="mb-6">
            <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase tracking-wider mb-3">Acciones Rápidas</h3>
            <div class="flex flex-wrap gap-2">
                <a href="#" class="px-3 py-2 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Nueva Propiedad</a>
                <a href="#" class="px-3 py-2 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Nuevo Trámite</a>
                <a href="{{ route('users.index') }}" class="px-3 py-2 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Nuevo Asesor</a>
                <a href="#" class="px-3 py-2 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Ver Agenda Global</a>
            </div>
        </div>

        <!-- Notificaciones Recientes -->
        <div class="mb-6">
            <h3 class="text-xs font-extrabold text-[#2C4A3E] uppercase tracking-wider mb-3">Notificaciones Recientes</h3>
            <div class="space-y-2 text-xs text-gray-700 bg-[#FDFBF7] p-4 rounded-2xl border border-emerald-100">
                <div class="flex items-center gap-2 font-bold text-emerald-800">
                    <i class="fa-solid fa-chart-line"></i> Metas: Cumplimiento Mensual 85%
                </div>
                <div class="grid grid-cols-2 gap-2 pt-1 text-[11px]">
                    <div>🔑 Prospectos Vendedores: <strong>4 Nuevos</strong></div>
                    <div>📞 Contactos: <strong>6 Nuevos</strong></div>
                </div>
                <div>👤 Prospectos Clientes: <strong>12 Nuevos</strong></div>
                <div class="text-amber-700">⚠️ Citas No Atendidas: 1 (Asesor Cueva)</div>
                <div class="text-gray-500">⚙️ Mensaje de Sistema: Actualización de Base de Datos</div>
            </div>
        </div>

        <!-- Enlaces Inferiores del Panel -->
<!-- Enlaces Inferiores del Panel -->
        <div class="flex flex-wrap justify-between items-center text-xs font-bold text-[#2C4A3E] pt-4 border-t border-emerald-100">
            <a href="#" class="hover:underline">Link de web clientes</a>
            <a href="#" class="hover:underline">Perfil</a>
            <a href="#" class="hover:underline">Configuración</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-red-600 hover:underline cursor-pointer">Cerrar Sesión</button>
            </form>
        </div>

    </div>

    <!-- Tarjeta Lateral Derecha: Configuración de Enlaces y Redes -->
    <div class="w-full lg:w-80 bg-white/90 backdrop-blur-md rounded-3xl border border-emerald-100 shadow-sm p-6">
        <div class="text-center mb-4">
            <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider block mb-2">Visítanos en Redes</span>
            <div class="flex justify-center gap-3 text-base text-[#2C4A3E] mb-4">
                <i class="fab fa-instagram"></i>
                <i class="fab fa-facebook"></i>
                <i class="fab fa-tiktok"></i>
                <i class="fab fa-youtube"></i>
                <i class="fab fa-whatsapp"></i>
            </div>
            <h4 class="text-xs font-extrabold text-[#2C4A3E] uppercase tracking-wider">Configuración de Enlaces</h4>
        </div>

        <div class="space-y-3">
            <div class="relative flex items-center">
                <span class="absolute left-3 text-gray-400 text-xs"><i class="fab fa-instagram"></i></span>
                <input type="text" placeholder="Ingresa tu link de Instagram" class="w-full pl-8 pr-3 py-2 rounded-xl border border-emerald-200 text-[11px] bg-[#FDFBF7]">
            </div>
            <div class="relative flex items-center">
                <span class="absolute left-3 text-gray-400 text-xs"><i class="fab fa-facebook"></i></span>
                <input type="text" placeholder="Ingresa tu link de Facebook" class="w-full pl-8 pr-3 py-2 rounded-xl border border-emerald-200 text-[11px] bg-[#FDFBF7]">
            </div>
            <div class="relative flex items-center">
                <span class="absolute left-3 text-gray-400 text-xs"><i class="fab fa-tiktok"></i></span>
                <input type="text" placeholder="Ingresa tu link de TikTok" class="w-full pl-8 pr-3 py-2 rounded-xl border border-emerald-200 text-[11px] bg-[#FDFBF7]">
            </div>
            <div class="relative flex items-center">
                <span class="absolute left-3 text-gray-400 text-xs"><i class="fab fa-youtube"></i></span>
                <input type="text" placeholder="Ingresa tu link de YouTube" class="w-full pl-8 pr-3 py-2 rounded-xl border border-emerald-200 text-[11px] bg-[#FDFBF7]">
            </div>
            <div class="relative flex items-center">
                <span class="absolute left-3 text-gray-400 text-xs"><i class="fab fa-whatsapp"></i></span>
                <input type="text" placeholder="Ingresa tu link de WhatsApp" class="w-full pl-8 pr-3 py-2 rounded-xl border border-emerald-200 text-[11px] bg-[#FDFBF7]">
            </div>
            <button type="button" class="w-full py-2.5 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm mt-2">
                Guardar Enlaces
            </button>
        </div>
    </div>

</div>
@endsection 