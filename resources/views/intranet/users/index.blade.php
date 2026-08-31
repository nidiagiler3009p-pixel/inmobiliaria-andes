@extends('layouts.admin')

@section('admin_content')

@php

    /*
    |--------------------------------------------------------------------------
    | RESUMEN
    |--------------------------------------------------------------------------
    */

    $totalPersonal = method_exists($users, 'total')
        ? $users->total()
        : $users->count();

    $activos = \App\Models\User::where(function ($query) {
        $query->whereRaw('LOWER(status) = ?', ['activo'])
              ->orWhereNull('status');
    })->count();

    $inactivos = \App\Models\User::whereRaw('LOWER(status) = ?', ['inactivo'])
        ->count();

    $totalPostulantes = $jobApplications->count();

    $metaDinero = \App\Models\User::sum('monthly_goal');

@endphp


<div class="max-w-7xl mx-auto px-4 py-8">


    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">

        <div>

            <h1 class="text-3xl font-black text-gray-800">
                Gestión Integral de Asesores
            </h1>

            <p class="text-gray-500 mt-1">
                Administración del personal, metas y postulaciones.
            </p>

        </div>


        <a href="{{ route('users.create') }}"
           class="inline-flex items-center justify-center gap-2
                  bg-emerald-600 hover:bg-emerald-700
                  text-white font-black
                  px-6 py-3 rounded-xl
                  shadow-sm transition">

            <i class="fa-solid fa-user-plus"></i>

            Nuevo Asesor

        </a>

    </div>



    {{-- ========================================================= --}}
    {{-- MENSAJE DE ÉXITO --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="mb-7 bg-emerald-50 border border-emerald-200
                    text-emerald-700 rounded-2xl px-5 py-4
                    flex items-center gap-3">

            <i class="fa-solid fa-circle-check text-xl"></i>

            <span class="font-semibold">
                {{ session('success') }}
            </span>

        </div>

    @endif



    {{-- ========================================================= --}}
    {{-- CUADROS DE RESUMEN --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-8">


        {{-- TOTAL PERSONAL --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between mb-3">

                <div class="w-10 h-10 rounded-xl bg-blue-50
                            text-blue-600 flex items-center justify-center">

                    <i class="fa-solid fa-users"></i>

                </div>

            </div>

            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                Total Personal
            </p>

            <p class="text-2xl font-black text-gray-800 mt-1">
                {{ $totalPersonal }}
            </p>

        </div>



        {{-- ACTIVOS --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <div class="w-10 h-10 rounded-xl bg-emerald-50
                        text-emerald-600 flex items-center justify-center mb-3">

                <i class="fa-solid fa-user-check"></i>

            </div>

            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                Activos
            </p>

            <p class="text-2xl font-black text-emerald-600 mt-1">
                {{ $activos }}
            </p>

        </div>



        {{-- INACTIVOS --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <div class="w-10 h-10 rounded-xl bg-gray-100
                        text-gray-600 flex items-center justify-center mb-3">

                <i class="fa-solid fa-user-slash"></i>

            </div>

            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                Inactivos
            </p>

            <p class="text-2xl font-black text-gray-700 mt-1">
                {{ $inactivos }}
            </p>

        </div>



        {{-- POSTULANTES --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <div class="w-10 h-10 rounded-xl bg-amber-50
                        text-amber-600 flex items-center justify-center mb-3">

                <i class="fa-solid fa-file-signature"></i>

            </div>

            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                Postulantes
            </p>

            <p class="text-2xl font-black text-amber-600 mt-1">
                {{ $totalPostulantes }}
            </p>

        </div>



        {{-- META PROPIEDADES --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <div class="w-10 h-10 rounded-xl bg-violet-50
                        text-violet-600 flex items-center justify-center mb-3">

                <i class="fa-solid fa-house-circle-check"></i>

            </div>

            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                Meta Propiedades
            </p>

            <p class="text-2xl font-black text-gray-800 mt-1">
                —
            </p>

            <p class="text-[11px] text-gray-400 mt-1">
                Propiedades a vender
            </p>

        </div>



        {{-- META ECONÓMICA --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">

            <div class="w-10 h-10 rounded-xl bg-green-50
                        text-green-600 flex items-center justify-center mb-3">

                <i class="fa-solid fa-dollar-sign"></i>

            </div>

            <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                Meta $
            </p>

            <p class="text-xl font-black text-green-600 mt-1">

                ${{ number_format((float) $metaDinero, 2) }}

            </p>

            <p class="text-[11px] text-gray-400 mt-1">
                Meta económica mensual
            </p>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- TABLA PERSONAL / ASESORES --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-10">


        <div class="px-6 py-5 border-b border-gray-100
                    flex flex-col md:flex-row
                    md:items-center md:justify-between gap-2">

            <div>

                <h2 class="text-xl font-black text-gray-800">

                    <i class="fa-solid fa-user-tie text-emerald-600 mr-2"></i>

                    Personal Registrado

                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Asesores y miembros registrados en la intranet.
                </p>

            </div>

        </div>



        <div class="overflow-x-auto">

            <table class="w-full text-sm">


                <thead class="bg-gray-50 text-gray-500">

                    <tr>

                        <th class="px-5 py-4 text-left font-black">
                            Personal
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Contacto
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Sucursal
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Rol
                        </th>

                        <th class="px-5 py-4 text-center font-black">
                            Estado
                        </th>

                        <th class="px-5 py-4 text-right font-black">
                            Meta $
                        </th>

                        <th class="px-5 py-4 text-center font-black">
                            Acciones
                        </th>

                    </tr>

                </thead>



                <tbody class="divide-y divide-gray-100">


                    @forelse($users as $user)

                        @php

                            $estadoUsuario = strtolower(
                                trim((string) ($user->status ?? 'activo'))
                            );

                            $estaActivo = $estadoUsuario !== 'inactivo';

                            $nombreCompleto = trim(
                                ($user->name ?? '') . ' ' .
                                ($user->last_name ?? '')
                            );

                        @endphp


                        <tr class="hover:bg-gray-50/70 transition">


                            {{-- PERSONAL --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-xl
                                                bg-emerald-100
                                                text-emerald-700
                                                flex items-center justify-center
                                                font-black">

                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}

                                    </div>


                                    <div>

                                        <p class="font-black text-gray-800">
                                            {{ $nombreCompleto ?: 'Sin nombre' }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            {{ $user->profession ?: 'Sin profesión registrada' }}
                                        </p>

                                    </div>

                                </div>

                            </td>



                            {{-- CONTACTO --}}

                            <td class="px-5 py-4">

                                <p class="font-semibold text-gray-700">
                                    {{ $user->email }}
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $user->phone ?: 'Sin teléfono' }}
                                </p>

                            </td>



                            {{-- SUCURSAL --}}

                            <td class="px-5 py-4 text-gray-600">

                                {{ $user->branch ?: ($user->city ?: 'N/D') }}

                            </td>



                            {{-- ROL --}}

                            <td class="px-5 py-4">

                                <span class="inline-flex px-3 py-1 rounded-lg
                                             bg-blue-50 text-blue-700
                                             text-xs font-bold">

                                    {{ $user->role ?: 'Sin rol' }}

                                </span>

                            </td>



                            {{-- ESTADO --}}

                            <td class="px-5 py-4 text-center">

                                @if($estaActivo)

                                    <span class="inline-flex items-center gap-2
                                                 bg-emerald-50 text-emerald-700
                                                 px-3 py-1.5 rounded-full
                                                 text-xs font-black">

                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>

                                        Activo

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-2
                                                 bg-gray-100 text-gray-600
                                                 px-3 py-1.5 rounded-full
                                                 text-xs font-black">

                                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>

                                        Inactivo

                                    </span>

                                @endif

                            </td>



                            {{-- META DINERO --}}

                            <td class="px-5 py-4 text-right">

                                <span class="font-black text-gray-800">

                                    ${{ number_format(
                                        (float) ($user->monthly_goal ?? 0),
                                        2
                                    ) }}

                                </span>

                            </td>



                            {{-- ACCIONES --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-2">


                                    {{-- VER --}}

                                    <a href="{{ route('users.show', $user->id) }}"
                                       title="Ver perfil"
                                       class="w-9 h-9 rounded-xl
                                              bg-blue-50 text-blue-600
                                              hover:bg-blue-600 hover:text-white
                                              flex items-center justify-center
                                              transition">

                                        <i class="fa-solid fa-eye"></i>

                                    </a>



                                    {{-- EDITAR --}}

                                    <button
                                        type="button"

                                        onclick="abrirEditar(
                                            @js(route('users.edit', $user->id)),
                                            @js($nombreCompleto)
                                        )"

                                        title="Modificar"

                                        class="w-9 h-9 rounded-xl
                                               bg-amber-50 text-amber-600
                                               hover:bg-amber-500 hover:text-white
                                               flex items-center justify-center
                                               transition">

                                        <i class="fa-solid fa-pen"></i>

                                    </button>



                                    {{-- ACTIVAR / DESACTIVAR --}}

                                    <button
                                        type="button"

                                        onclick="abrirEstado(
                                            @js(route('users.destroy', $user->id)),
                                            @js($nombreCompleto),
                                            @js($estaActivo ? 'desactivar' : 'activar')
                                        )"

                                        title="{{ $estaActivo ? 'Desactivar' : 'Activar' }}"

                                        class="w-9 h-9 rounded-xl
                                               {{ $estaActivo
                                                    ? 'bg-red-50 text-red-600 hover:bg-red-600'
                                                    : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600'
                                               }}
                                               hover:text-white
                                               flex items-center justify-center
                                               transition">

                                        @if($estaActivo)

                                            <i class="fa-solid fa-user-slash"></i>

                                        @else

                                            <i class="fa-solid fa-user-check"></i>

                                        @endif

                                    </button>


                                </div>

                            </td>

                        </tr>


                    @empty


                        <tr>

                            <td colspan="7"
                                class="px-6 py-12 text-center text-gray-400">

                                <i class="fa-solid fa-users
                                          text-4xl mb-3 block"></i>

                                No existen miembros registrados.

                            </td>

                        </tr>


                    @endforelse


                </tbody>

            </table>

        </div>



        {{-- PAGINACIÓN --}}

        @if(method_exists($users, 'links'))

            <div class="px-6 py-4 border-t border-gray-100">

                {{ $users->links() }}

            </div>

        @endif


    </div>



    {{-- ========================================================= --}}
    {{-- TABLA POSTULANTES --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">


        <div class="px-6 py-5 border-b border-gray-100">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                <div>

                    <h2 class="text-xl font-black text-gray-800">

                        <i class="fa-solid fa-file-lines
                                  text-amber-500 mr-2"></i>

                        Postulantes

                    </h2>

                    <p class="text-sm text-gray-500 mt-1">

                        Solicitudes recibidas desde
                        <strong>Únete a nuestro equipo</strong>
                        de la página pública.

                    </p>

                </div>


                <div class="px-4 py-2
                            bg-amber-50 text-amber-700
                            rounded-xl font-black text-sm">

                    {{ $totalPostulantes }}
                    {{ $totalPostulantes == 1 ? 'postulante' : 'postulantes' }}

                </div>

            </div>

        </div>



        <div class="overflow-x-auto">

            <table class="w-full text-sm">


                <thead class="bg-gray-50 text-gray-500">

                    <tr>

                        <th class="px-5 py-4 text-left font-black">
                            Postulante
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Contacto
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Profesión
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Ciudad
                        </th>

                        <th class="px-5 py-4 text-left font-black">
                            Experiencia
                        </th>

                        <th class="px-5 py-4 text-center font-black">
                            CV
                        </th>

                        <th class="px-5 py-4 text-center font-black">
                            Fecha
                        </th>

                    </tr>

                </thead>



                <tbody class="divide-y divide-gray-100">


                    @forelse($jobApplications as $postulante)


                        <tr class="hover:bg-gray-50/70 transition">


                            {{-- NOMBRE --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10
                                                rounded-xl
                                                bg-amber-100
                                                text-amber-700
                                                flex items-center
                                                justify-center
                                                font-black">

                                        {{ strtoupper(
                                            substr(
                                                $postulante->nombres ?? 'P',
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>


                                    <div>

                                        <p class="font-black text-gray-800">

                                            {{ $postulante->nombres }}
                                            {{ $postulante->apellidos }}

                                        </p>

                                        <p class="text-xs text-gray-400">
                                            Postulación web
                                        </p>

                                    </div>

                                </div>

                            </td>



                            {{-- CONTACTO --}}

                            <td class="px-5 py-4">

                                <p class="font-semibold text-gray-700">
                                    {{ $postulante->correo }}
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $postulante->celular ?: 'Sin celular' }}
                                </p>

                            </td>



                            {{-- PROFESIÓN --}}

                            <td class="px-5 py-4 text-gray-700">

                                {{ $postulante->profesion ?: 'N/D' }}

                            </td>



                            {{-- CIUDAD --}}

                            <td class="px-5 py-4 text-gray-600">

                                {{ $postulante->ciudad ?: 'N/D' }}

                            </td>



                            {{-- EXPERIENCIA --}}

                            <td class="px-5 py-4 text-gray-600">

                                {{ $postulante->experiencia ?: 'N/D' }}

                            </td>



                            {{-- CV --}}

                            <td class="px-5 py-4 text-center">

                                @if($postulante->cv_path)

                                    <a
                                        href="{{ asset('storage/' . $postulante->cv_path) }}"
                                        target="_blank"

                                        class="inline-flex items-center gap-2
                                               bg-red-50 text-red-600
                                               hover:bg-red-600 hover:text-white
                                               px-3 py-2 rounded-xl
                                               text-xs font-black
                                               transition"
                                    >

                                        <i class="fa-solid fa-file-pdf"></i>

                                        Ver CV

                                    </a>

                                @else

                                    <span class="text-gray-400 text-xs">
                                        Sin CV
                                    </span>

                                @endif

                            </td>



                            {{-- FECHA --}}

                            <td class="px-5 py-4 text-center text-gray-500">

                                {{ optional($postulante->created_at)->format('d/m/Y') ?? 'N/D' }}

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td colspan="7"
                                class="px-6 py-12 text-center text-gray-400">

                                <i class="fa-solid fa-file-circle-xmark
                                          text-4xl mb-3 block"></i>

                                No existen postulaciones registradas.

                            </td>

                        </tr>


                    @endforelse


                </tbody>

            </table>

        </div>

    </div>


</div>



{{-- ============================================================= --}}
{{-- MODAL CONFIRMAR EDICIÓN --}}
{{-- ============================================================= --}}

<div id="modal-editar"
     class="fixed inset-0 z-50 hidden
            items-center justify-center
            bg-black/50 px-4">

    <div class="bg-white rounded-3xl
                shadow-2xl
                w-full max-w-md
                p-7">


        <div class="w-14 h-14
                    mx-auto mb-4
                    rounded-2xl
                    bg-amber-100
                    text-amber-600
                    flex items-center justify-center
                    text-2xl">

            <i class="fa-solid fa-pen-to-square"></i>

        </div>


        <h3 class="text-xl font-black text-gray-800 text-center">

            Modificar Asesor

        </h3>


        <p class="text-gray-500 text-center mt-3">

            ¿Deseas modificar la información de

            <strong id="nombre-editar"
                    class="text-gray-800"></strong>?

        </p>


        <div class="flex gap-3 mt-7">

            <button
                type="button"
                onclick="cerrarEditar()"

                class="flex-1 px-4 py-3
                       rounded-xl
                       bg-gray-100
                       text-gray-700
                       font-black
                       hover:bg-gray-200 transition"
            >

                No, regresar

            </button>


            <a
                id="enlace-editar"
                href="#"

                class="flex-1 px-4 py-3
                       rounded-xl
                       bg-amber-500
                       text-white
                       font-black
                       text-center
                       hover:bg-amber-600 transition"
            >

                Sí, modificar

            </a>

        </div>

    </div>

</div>



{{-- ============================================================= --}}
{{-- MODAL ACTIVAR / DESACTIVAR --}}
{{-- ============================================================= --}}

<div id="modal-estado"
     class="fixed inset-0 z-50 hidden
            items-center justify-center
            bg-black/50 px-4">

    <div class="bg-white rounded-3xl
                shadow-2xl
                w-full max-w-md
                p-7">


        <div id="icono-estado"
             class="w-14 h-14
                    mx-auto mb-4
                    rounded-2xl
                    bg-red-100
                    text-red-600
                    flex items-center justify-center
                    text-2xl">

            <i class="fa-solid fa-user-slash"></i>

        </div>


        <h3 id="titulo-estado"
            class="text-xl font-black
                   text-gray-800 text-center">

            Desactivar Usuario

        </h3>


        <p class="text-gray-500 text-center mt-3">

            ¿Deseas

            <strong id="accion-estado"></strong>

            a

            <strong id="nombre-estado"
                    class="text-gray-800"></strong>?

        </p>


        <p id="mensaje-estado"
           class="text-xs
                  text-gray-400
                  text-center mt-2">

            El usuario permanecerá registrado en el sistema.

        </p>


        <form id="form-estado"
              method="POST"
              class="mt-7">

            @csrf
            @method('DELETE')


            <div class="flex gap-3">

                <button
                    type="button"
                    onclick="cerrarEstado()"

                    class="flex-1 px-4 py-3
                           rounded-xl
                           bg-gray-100
                           text-gray-700
                           font-black
                           hover:bg-gray-200 transition"
                >

                    No, regresar

                </button>


                <button
                    id="boton-confirmar-estado"
                    type="submit"

                    class="flex-1 px-4 py-3
                           rounded-xl
                           bg-red-600
                           text-white
                           font-black
                           hover:bg-red-700 transition"
                >

                    Sí, desactivar

                </button>

            </div>

        </form>

    </div>

</div>



{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | MODAL EDITAR
    |--------------------------------------------------------------------------
    */

    function abrirEditar(url, nombre)
    {
        document.getElementById('nombre-editar').textContent = nombre;

        document.getElementById('enlace-editar').href = url;


        const modal =
            document.getElementById('modal-editar');


        modal.classList.remove('hidden');

        modal.classList.add('flex');
    }


    function cerrarEditar()
    {
        const modal =
            document.getElementById('modal-editar');


        modal.classList.add('hidden');

        modal.classList.remove('flex');
    }



    /*
    |--------------------------------------------------------------------------
    | MODAL ESTADO
    |--------------------------------------------------------------------------
    */

    function abrirEstado(url, nombre, accion)
    {
        const modal =
            document.getElementById('modal-estado');

        const form =
            document.getElementById('form-estado');

        const titulo =
            document.getElementById('titulo-estado');

        const accionTexto =
            document.getElementById('accion-estado');

        const nombreTexto =
            document.getElementById('nombre-estado');

        const boton =
            document.getElementById('boton-confirmar-estado');

        const icono =
            document.getElementById('icono-estado');


        form.action = url;

        nombreTexto.textContent = nombre;

        accionTexto.textContent = accion;


        if (accion === 'desactivar')
        {
            titulo.textContent =
                'Desactivar Usuario';

            boton.textContent =
                'Sí, desactivar';


            boton.className =
                'flex-1 px-4 py-3 rounded-xl ' +
                'bg-red-600 text-white font-black ' +
                'hover:bg-red-700 transition';


            icono.className =
                'w-14 h-14 mx-auto mb-4 rounded-2xl ' +
                'bg-red-100 text-red-600 ' +
                'flex items-center justify-center text-2xl';


            icono.innerHTML =
                '<i class="fa-solid fa-user-slash"></i>';
        }
        else
        {
            titulo.textContent =
                'Activar Usuario';

            boton.textContent =
                'Sí, activar';


            boton.className =
                'flex-1 px-4 py-3 rounded-xl ' +
                'bg-emerald-600 text-white font-black ' +
                'hover:bg-emerald-700 transition';


            icono.className =
                'w-14 h-14 mx-auto mb-4 rounded-2xl ' +
                'bg-emerald-100 text-emerald-600 ' +
                'flex items-center justify-center text-2xl';


            icono.innerHTML =
                '<i class="fa-solid fa-user-check"></i>';
        }


        modal.classList.remove('hidden');

        modal.classList.add('flex');
    }


    function cerrarEstado()
    {
        const modal =
            document.getElementById('modal-estado');


        modal.classList.add('hidden');

        modal.classList.remove('flex');
    }



    /*
    |--------------------------------------------------------------------------
    | CERRAR MODALES CON ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function(event)
    {
        if (event.key === 'Escape')
        {
            cerrarEditar();

            cerrarEstado();
        }
    });

</script>

@endsection