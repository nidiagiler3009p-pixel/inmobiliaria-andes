@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 flex justify-center items-center min-h-[75vh]">
    <div class="relative w-full max-w-md bg-white p-8 rounded-3xl border border-emerald-100 shadow-md" id="login-container">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-extrabold text-[#2C4A3E]">Inmobiliaria Los Andes</h2>
            <p class="text-xs text-gray-500 mt-1">Acceso al Sistema</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-bold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
            </div>

            <div>
                <label class="block text-xs font-extrabold text-[#2C4A3E] mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-800 bg-[#FDFBF7]">
            </div>

            <button type="submit" class="w-full py-3 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-md mt-2">
                Accede
            </button>
        </form>

        <div class="text-center mt-4">
            <button type="button" onclick="toggleRecovery()" class="text-xs text-[#2C4A3E] hover:underline font-semibold focus:outline-none">
                ¿Olvidaste tu clave?
            </button>
        </div>
    </div>

    <!-- Panel de Recuperación -->
    <div class="hidden absolute w-full max-w-md bg-white p-8 rounded-3xl border border-emerald-100 shadow-md" id="recovery-container">
        
        <div class="text-center mb-6">
            <h2 class="text-xl font-extrabold text-[#2C4A3E]">Recuperación de Contraseña</h2>
            <p class="text-[11px] text-gray-500 mt-1">Valida tus datos para proceder con el cambio de clave.</p>
        </div>

        <form method="POST" action="{{ route('password.recover') }}" class="space-y-3">
            @csrf

            <div>
                <input type="text" name="cedula" placeholder="Cédula de Identidad" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
            </div>

            <div>
                <input type="email" name="email" placeholder="Correo Electrónico Registrado" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
            </div>

            <div>
                <input type="date" name="hire_date" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs text-gray-500 bg-[#FDFBF7]">
            </div>

            <div>
                <input type="password" name="password" placeholder="Nueva Clave de Acceso" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
            </div>

            <div>
                <input type="password" name="password_confirmation" placeholder="Confirmar Nueva Clave" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-md mt-2">
                Validar Datos y Cambiar Clave
            </button>
        </form>

        <div class="text-center mt-3">
            <button type="button" onclick="toggleRecovery()" class="text-[11px] text-[#2C4A3E] hover:underline font-semibold focus:outline-none">
                Volver al inicio de sesión
            </button>
        </div>
    </div>
</div>

<script>
    function toggleRecovery() {
        document.getElementById('login-container').classList.toggle('hidden');
        document.getElementById('recovery-container').classList.toggle('hidden');
    }
</script>
@endsection