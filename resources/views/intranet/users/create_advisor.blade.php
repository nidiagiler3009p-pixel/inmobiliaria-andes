@extends('layouts.admin')

@section('admin_content')

<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-7">

        <div>
            <h1 class="text-3xl font-black text-gray-800">
                Nuevo Asesor
            </h1>

            <p class="text-gray-500 mt-1">
                Registra un nuevo miembro del equipo de Inmobiliaria Los Andes.
            </p>
        </div>

        <a href="{{ route('users.index') }}"
           class="inline-flex items-center justify-center gap-2
                  px-5 py-3 rounded-xl
                  bg-gray-100 text-gray-700
                  font-bold hover:bg-gray-200 transition">

            <i class="fa-solid fa-arrow-left"></i>
            Regresar

        </a>

    </div>


    {{-- ERRORES --}}
    @if ($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200
                    rounded-2xl p-5 text-red-700">

            <div class="font-black mb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                Revisa los siguientes datos
            </div>

            <ul class="list-disc pl-6 text-sm space-y-1">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORMULARIO --}}
    <form action="{{ route('users.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf


        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">


            {{-- CABECERA --}}
            <div class="px-7 py-6 border-b border-gray-100
                        bg-gradient-to-r from-emerald-50 to-white">

                <div class="flex items-center gap-4">

                    <div class="w-14 h-14 rounded-2xl
                                bg-emerald-600 text-white
                                flex items-center justify-center text-2xl">

                        <i class="fa-solid fa-user-plus"></i>

                    </div>

                    <div>
                        <h2 class="text-xl font-black text-gray-800">
                            Registro de Personal
                        </h2>

                        <p class="text-sm text-gray-500">
                            Complete la información del nuevo miembro.
                        </p>
                    </div>

                </div>

            </div>


            <div class="p-7">


                {{-- ===================================================== --}}
                {{-- INFORMACIÓN PERSONAL --}}
                {{-- ===================================================== --}}

                <div class="mb-9">

                    <h3 class="text-lg font-black text-gray-800 mb-5
                               flex items-center gap-2">

                        <i class="fa-solid fa-user text-emerald-600"></i>

                        Información Personal

                    </h3>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        {{-- NOMBRE --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Nombres *
                            </label>

                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="Ej. Juan Carlos"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- APELLIDO --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Apellidos *
                            </label>

                            <input type="text"
                                   name="last_name"
                                   value="{{ old('last_name') }}"
                                   required
                                   placeholder="Ej. Pérez López"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- EMAIL --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Correo Electrónico *
                            </label>

                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="asesor@correo.com"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- TELÉFONO --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Teléfono *
                            </label>

                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   required
                                   placeholder="0991234567"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- CIUDAD --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Ciudad *
                            </label>

                            <input type="text"
                                   name="city"
                                   value="{{ old('city', 'Riobamba') }}"
                                   required
                                   placeholder="Riobamba"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- SUCURSAL --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Sucursal
                            </label>

                            <input type="text"
                                   name="branch"
                                   value="{{ old('branch') }}"
                                   placeholder="Ej. Matriz Riobamba"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>

                    </div>

                </div>



                {{-- ===================================================== --}}
                {{-- INFORMACIÓN PROFESIONAL --}}
                {{-- ===================================================== --}}

                <div class="mb-9">

                    <h3 class="text-lg font-black text-gray-800 mb-5
                               flex items-center gap-2">

                        <i class="fa-solid fa-briefcase text-emerald-600"></i>

                        Información Profesional

                    </h3>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        {{-- PROFESIÓN --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Profesión *
                            </label>

                            <input type="text"
                                   name="profession"
                                   value="{{ old('profession') }}"
                                   required
                                   placeholder="Ej. Ing. Comercial"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- EXPERIENCIA --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Experiencia *
                            </label>

                            <input type="text"
                                   name="experience_years"
                                   value="{{ old('experience_years') }}"
                                   required
                                   placeholder="Ej. 3 años"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                        </div>


                        {{-- ROL --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Rol *
                            </label>

                            <select name="role"
                                    required
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-emerald-500
                                           focus:ring-emerald-500">

                                <option value="">
                                    Seleccione un rol
                                </option>

                                <option value="Asesor"
                                    @selected(old('role') === 'Asesor')>
                                    Asesor
                                </option>

                                <option value="Trámites"
                                    @selected(old('role') === 'Trámites')>
                                    Trámites
                                </option>

                                <option value="Contador"
                                    @selected(old('role') === 'Contador')>
                                    Contador
                                </option>

                                <option value="Publicista"
                                    @selected(old('role') === 'Publicista')>
                                    Publicista
                                </option>

                                <option value="Administrador/Gerente"
                                    @selected(old('role') === 'Administrador/Gerente')>
                                    Administrador / Gerente
                                </option>

                            </select>

                        </div>


                        {{-- META ECONÓMICA --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Meta $ Mensual *
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2
                                             -translate-y-1/2
                                             text-gray-500 font-bold">
                                    $
                                </span>

                                <input type="number"
                                       name="monthly_goal"
                                       value="{{ old('monthly_goal', 0) }}"
                                       required
                                       min="0"
                                       step="1"
                                       placeholder="0"
                                       class="w-full pl-8 rounded-xl
                                              border-gray-300
                                              focus:border-emerald-500
                                              focus:ring-emerald-500">

                            </div>

                            <p class="text-xs text-gray-400 mt-1">
                                Objetivo económico mensual del miembro.
                            </p>

                        </div>

                    </div>

                </div>



                {{-- ===================================================== --}}
                {{-- ACCESO AL SISTEMA --}}
                {{-- ===================================================== --}}

                <div class="mb-9">

                    <h3 class="text-lg font-black text-gray-800 mb-5
                               flex items-center gap-2">

                        <i class="fa-solid fa-lock text-emerald-600"></i>

                        Acceso al Sistema

                    </h3>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        {{-- CONTRASEÑA --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Contraseña *
                            </label>

                            <input type="password"
                                   name="password"
                                   required
                                   minlength="6"
                                   placeholder="Mínimo 6 caracteres"
                                   class="w-full rounded-xl border-gray-300
                                          focus:border-emerald-500
                                          focus:ring-emerald-500">

                            <p class="text-xs text-gray-400 mt-1">
                                Esta contraseña será utilizada para ingresar a la intranet.
                            </p>

                        </div>


                        {{-- CV --}}
                        <div>

                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Hoja de Vida / CV
                            </label>

                            <input type="file"
                                   name="cv_file"
                                   accept=".pdf"
                                   class="w-full rounded-xl
                                          border border-gray-300
                                          bg-white px-3 py-2 text-sm">

                            <p class="text-xs text-gray-400 mt-1">
                                Opcional. Solo archivo PDF, máximo 5 MB.
                            </p>

                        </div>

                    </div>

                </div>



                {{-- ===================================================== --}}
                {{-- ESTADO --}}
                {{-- ===================================================== --}}

                <div class="bg-emerald-50 border border-emerald-100
                            rounded-2xl p-5 mb-8">

                    <div class="flex items-center gap-4">

                        <div class="w-11 h-11 rounded-xl
                                    bg-emerald-100 text-emerald-600
                                    flex items-center justify-center">

                            <i class="fa-solid fa-circle-check"></i>

                        </div>


                        <div>

                            <p class="font-black text-gray-800">
                                Estado inicial: Activo
                            </p>

                            <p class="text-sm text-gray-500">
                                El nuevo miembro quedará activo al registrarse.
                                Después podrás desactivarlo desde Gestión de Asesores
                                sin eliminar su información.
                            </p>

                        </div>

                    </div>

                </div>



                {{-- ===================================================== --}}
                {{-- BOTONES --}}
                {{-- ===================================================== --}}

                <div class="flex flex-col-reverse sm:flex-row
                            justify-end gap-3
                            pt-6 border-t border-gray-100">

                    <a href="{{ route('users.index') }}"
                       class="px-6 py-3 rounded-xl
                              bg-gray-100 text-gray-700
                              font-black text-center
                              hover:bg-gray-200 transition">

                        Cancelar

                    </a>


                    <button type="submit"
                            class="px-7 py-3 rounded-xl
                                   bg-emerald-600 text-white
                                   font-black
                                   hover:bg-emerald-700
                                   shadow-sm transition">

                        <i class="fa-solid fa-user-plus mr-2"></i>

                        Registrar Asesor

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection