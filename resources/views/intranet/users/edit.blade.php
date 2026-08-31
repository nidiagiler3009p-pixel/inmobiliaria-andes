@extends('layouts.admin')

@section('admin_content')

<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- ENCABEZADO --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800">
                Modificar Asesor
            </h1>

            <p class="text-gray-500 mt-1">
                Actualiza la información del miembro del equipo.
            </p>
        </div>

        <a href="{{ route('users.index') }}"
           class="inline-flex items-center justify-center gap-2
                  px-5 py-3 rounded-xl bg-gray-100 text-gray-700
                  font-bold hover:bg-gray-200 transition">

            <i class="fa-solid fa-arrow-left"></i>
            Regresar
        </a>
    </div>


    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">

            <div class="flex items-center gap-2 font-bold text-red-700 mb-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                Revisa la información ingresada
            </div>

            <ul class="list-disc pl-6 text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- TARJETA PRINCIPAL --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- CABECERA DEL ASESOR --}}
        <div class="px-7 py-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-2xl bg-emerald-600
                            text-white flex items-center justify-center
                            text-xl font-black shadow-sm">

                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}

                </div>

                <div>
                    <h2 class="text-xl font-black text-gray-800">
                        {{ $user->name }} {{ $user->last_name }}
                    </h2>

                    <p class="text-sm text-gray-500">
                        {{ $user->email }}
                    </p>
                </div>

            </div>

        </div>


        <form
            action="{{ route('users.update', $user->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-7"
        >

            @csrf
            @method('PUT')


            {{-- INFORMACIÓN PERSONAL --}}
            <div class="mb-8">

                <h3 class="text-lg font-black text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-user text-emerald-600"></i>
                    Información Personal
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- NOMBRE --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nombre *
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- APELLIDO --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Apellido *
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name', $user->last_name) }}"
                            required
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- EMAIL --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Correo Electrónico *
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- TELÉFONO --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- CIUDAD --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Ciudad
                        </label>

                        <input
                            type="text"
                            name="city"
                            value="{{ old('city', $user->city) }}"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- SUCURSAL --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Sucursal
                        </label>

                        <input
                            type="text"
                            name="branch"
                            value="{{ old('branch', $user->branch) }}"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>

                </div>

            </div>


            {{-- INFORMACIÓN LABORAL --}}
            <div class="mb-8">

                <h3 class="text-lg font-black text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-emerald-600"></i>
                    Información Laboral
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- PROFESIÓN --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Profesión
                        </label>

                        <input
                            type="text"
                            name="profession"
                            value="{{ old('profession', $user->profession) }}"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- EXPERIENCIA --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Experiencia
                        </label>

                        <input
                            type="text"
                            name="experience_years"
                            value="{{ old('experience_years', $user->experience_years) }}"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >
                    </div>


                    {{-- ROL --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Rol *
                        </label>

                        <select
                            name="role"
                            required
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                        >

                            <option value="Asesor"
                                @selected(old('role', $user->role) === 'Asesor')>
                                Asesor
                            </option>

                            <option value="Trámites"
                                @selected(old('role', $user->role) === 'Trámites')>
                                Trámites
                            </option>

                            <option value="Contador"
                                @selected(old('role', $user->role) === 'Contador')>
                                Contador
                            </option>

                            <option value="Publicista"
                                @selected(old('role', $user->role) === 'Publicista')>
                                Publicista
                            </option>

                            <option value="Administrador/Gerente"
                                @selected(old('role', $user->role) === 'Administrador/Gerente')>
                                Administrador / Gerente
                            </option>

                        </select>
                    </div>


                    {{-- META ECONÓMICA --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Meta $ *
                        </label>

                        <div class="relative">

                            <span class="absolute left-4 top-1/2 -translate-y-1/2
                                         text-gray-500 font-bold">
                                $
                            </span>

                            <input
                                type="number"
                                name="monthly_goal"
                                min="0"
                                value="{{ old('monthly_goal', $user->monthly_goal ?? 0) }}"
                                required
                                class="w-full pl-8 rounded-xl border-gray-300
                                       focus:border-emerald-500
                                       focus:ring-emerald-500"
                            >

                        </div>

                        <p class="text-xs text-gray-400 mt-1">
                            Objetivo económico mensual.
                        </p>
                    </div>

                </div>

            </div>


            {{-- SEGURIDAD --}}
            <div class="mb-8">

                <h3 class="text-lg font-black text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-emerald-600"></i>
                    Seguridad
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- CONTRASEÑA --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nueva Contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            minlength="6"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-emerald-500
                                   focus:ring-emerald-500"
                            placeholder="Dejar vacío para conservar la actual"
                        >

                        <p class="text-xs text-gray-400 mt-1">
                            Solo completa este campo si deseas cambiarla.
                        </p>
                    </div>


                    {{-- CV --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Actualizar CV
                        </label>

                        <input
                            type="file"
                            name="cv_file"
                            accept=".pdf"
                            class="w-full rounded-xl border border-gray-300
                                   bg-white px-3 py-2 text-sm"
                        >

                        @if($user->cv_file_path)

                            <p class="text-xs text-emerald-600 mt-2">
                                <i class="fa-solid fa-file-pdf"></i>
                                El asesor tiene un CV registrado.
                            </p>

                        @endif

                    </div>

                </div>

            </div>


            {{-- ESTADO INFORMATIVO --}}
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-2xl p-5">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>
                        <p class="font-black text-gray-800">
                            Estado del usuario
                        </p>

                        <p class="text-sm text-gray-500">
                            El estado se administra desde la tabla principal de asesores.
                        </p>
                    </div>

                    @php
                        $estado = strtolower((string) ($user->status ?? 'activo'));
                    @endphp

                    @if($estado === 'activo')

                        <span class="inline-flex items-center gap-2
                                     px-4 py-2 rounded-full
                                     bg-emerald-100 text-emerald-700
                                     text-sm font-black">

                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Activo

                        </span>

                    @else

                        <span class="inline-flex items-center gap-2
                                     px-4 py-2 rounded-full
                                     bg-gray-200 text-gray-600
                                     text-sm font-black">

                            <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                            Inactivo

                        </span>

                    @endif

                </div>

            </div>


            {{-- BOTONES --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3
                        pt-6 border-t border-gray-100">

                <a
                    href="{{ route('users.index') }}"
                    class="px-6 py-3 rounded-xl
                           bg-gray-100 text-gray-700
                           font-black text-center
                           hover:bg-gray-200 transition"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="px-7 py-3 rounded-xl
                           bg-emerald-600 text-white
                           font-black
                           hover:bg-emerald-700
                           shadow-sm transition"
                >
                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                    Guardar Cambios
                </button>

            </div>

        </form>

    </div>

</div>

@endsection