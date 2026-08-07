@extends('layouts.admin')

@section('admin_content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">
    
    <!-- Encabezado de la Sección -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-[#2C4A3E]">Gestión Integral de Asesores</h2>
            <p class="text-xs text-gray-500 mt-1">Control de rendimiento, comisiones, metas y administración del personal.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="#agregar-asesor" class="px-4 py-2 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                <i class="fa-solid fa-user-plus mr-1"></i> Nuevo Asesor
            </a>
            <a href="#" class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-bold text-xs rounded-xl transition">
                <i class="fa-solid fa-file-excel mr-1"></i> Exportar Reporte
            </a>
        </div>
    </div>

    <!-- Alerta de éxito -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-check-circle text-base"></i> 
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Listado de Asesores -->
    <div class="bg-white/90 backdrop-blur-md rounded-3xl border border-emerald-100 shadow-sm overflow-hidden p-6">
        <h3 class="text-sm font-extrabold text-[#2C4A3E] mb-4 uppercase tracking-wider">Listado de Asesores</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-50 text-[#2C4A3E] text-[11px] font-extrabold uppercase tracking-wider border-b border-emerald-100">
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Cédula</th>
                        <th class="p-3">Sucursal</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Meta Mensual</th>
                        <th class="p-3">Comisiones</th>
                        <th class="p-3">Pagos Pendientes</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-50 text-xs text-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-emerald-50/50 transition">
                        <td class="p-3 font-extrabold text-[#2C4A3E]">{{ $user->name }} {{ $user->last_name }}</td>
                        <td class="p-3 text-gray-500">{{ $user->phone ?? 'N/D' }}</td>
                        <td class="p-3">{{ $user->city }}</td>
                        <td class="p-3">
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 rounded-full text-[10px] font-extrabold">Activo</span>
                        </td>
                        <td class="p-3 font-bold text-emerald-800">${{ number_format($user->monthly_goal, 0) }}</td>
                        <td class="p-3 font-bold">$12,500</td>
                        <td class="p-3 font-bold text-amber-700">$1,500</td>
                        <td class="p-3 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('users.show', $user->id) }}" class="p-1.5 bg-gray-100 hover:bg-emerald-100 text-[#2C4A3E] rounded-lg transition" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 bg-gray-100 hover:bg-emerald-100 text-[#2C4A3E] rounded-lg transition" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Eliminar asesor?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="p-6 text-center text-gray-400">No hay asesores registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Control de Metas, Rendimiento y Estadísticas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-sm">
                <span class="text-[11px] text-gray-400 font-bold uppercase">Total Ventas Mensuales</span>
                <h4 class="text-xl font-extrabold text-[#2C4A3E] mt-1">$48,500</h4>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-sm">
                <span class="text-[11px] text-gray-400 font-bold uppercase">Total Cieres</span>
                <h4 class="text-xl font-extrabold text-[#2C4A3E] mt-1">12</h4>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-sm">
                <span class="text-[11px] text-gray-400 font-bold uppercase">Eficiencia de Conversión</span>
                <h4 class="text-xl font-extrabold text-[#2C4A3E] mt-1">65%</h4>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-sm">
                <span class="text-[11px] text-gray-400 font-bold uppercase">Bono Alcanzado</span>
                <h4 class="text-xl font-extrabold text-emerald-800 mt-1">$5,000</h4>
            </div>
        </div>
    </div>

    <!-- 3. Comisiones y Pagos -->
    <div class="bg-white/90 backdrop-blur-md rounded-3xl border border-emerald-100 shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-[#2C4A3E] uppercase tracking-wider">Comisiones y Pagos</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 bg-emerald-800 text-white text-[11px] font-bold rounded-xl">Generar Pagos</button>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 text-[11px] font-bold rounded-xl">Auditoría de Cuentas</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-emerald-50 text-[#2C4A3E] font-extrabold uppercase text-[10px]">
                        <th class="p-3">Período</th>
                        <th class="p-3">Asesor</th>
                        <th class="p-3">Ventas Totales</th>
                        <th class="p-3">Tasa de Comisión (%)</th>
                        <th class="p-3">Monto de Comisión</th>
                        <th class="p-3">Estado de Pago</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-50 text-gray-700">
                    <tr>
                        <td class="p-3">Q3 2026</td>
                        <td class="p-3 font-bold">David Cueva</td>
                        <td class="p-3">$246,790</td>
                        <td class="p-3">15.0%</td>
                        <td class="p-3 font-bold text-emerald-800">$10,500</td>
                        <td class="p-3"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-900 rounded-full font-bold text-[10px]">Pagado</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Formularios de Gestión (Agregar Nuevo Asesor y Modificar) -->
    <div id="agregar-asesor" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white/90 backdrop-blur-md rounded-3xl border border-emerald-100 shadow-sm p-6">
        
        <!-- Formulario: Agregar Nuevo Asesor -->
        <div>
            <h3 class="text-sm font-extrabold text-[#2C4A3E] mb-4 uppercase tracking-wider">Agregar Nuevo Asesor</h3>
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Nombre</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Apellido</label>
                    <input type="text" name="last_name" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Contraseña Inicial</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Teléfono</label>
                        <input type="text" name="phone" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Ciudad / Sucursal</label>
                        <input type="text" name="city" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Profesión</label>
                        <input type="text" name="profession" value="Asesor" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Experiencia</label>
                        <input type="text" name="experience_years" value="1 año" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Meta Mensual ($)</label>
                        <input type="number" name="monthly_goal" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Rol</label>
                        <select name="role" required class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]">
                            <option value="Asesor">Asesor</option>
                            <option value="Administrador/Gerente">Administrador/Gerente</option>
                            <option value="Contador">Contador</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Hoja de Vida (PDF)</label>
                    <input type="file" name="cv_file" accept=".pdf" class="w-full text-xs text-gray-500">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-[#2C4A3E] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition">Guardar</button>
                    <button type="reset" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs rounded-xl transition">Limpiar</button>
                </div>
            </form>
        </div>

        <!-- Formulario: Modificar Datos del Asesor -->
        <div>
            <h3 class="text-sm font-extrabold text-[#2C4A3E] mb-4 uppercase tracking-wider">Modificar Datos del Asesor</h3>
            
            <form id="form-modificar" action="" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Seleccionar Asesor</label>
                    <select id="select-asesor" class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-[#FDFBF7]" onchange="cargarDatosAsesor(this)">
                        <option value="">Seleccione un asesor...</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" 
                                    data-email="{{ $u->email }}" 
                                    data-phone="{{ $u->phone }}" 
                                    data-goal="{{ $u->monthly_goal }}"
                                    data-updateurl="{{ route('users.update', $u->id) }}">
                                {{ $u->name }} {{ $u->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Email</label>
                    <input type="email" id="mod_email" disabled class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-gray-100" placeholder="Seleccione un asesor...">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Teléfono</label>
                    <input type="text" id="mod_phone" disabled class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-gray-100">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Meta Mensual</label>
                    <input type="number" id="mod_goal" disabled class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-gray-100">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#2C4A3E] mb-1">Estado</label>
                    <input type="text" id="mod_estado" value="Activo" disabled class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs bg-gray-100">
                </div>

                <div class="flex gap-2 pt-6">
                    <button type="submit" id="btn-actualizar" disabled class="flex-1 py-2.5 bg-gray-300 text-white font-bold text-xs rounded-xl cursor-not-allowed transition">Actualizar</button>
                    <button type="button" onclick="limpiarModificar()" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs rounded-xl transition">Limpiar</button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

<!-- Script dinámico para el autocompletado del formulario de modificación -->
<script>
function cargarDatosAsesor(select) {
    const option = select.options[select.selectedIndex];
    const email = option.getAttribute('data-email');
    const phone = option.getAttribute('data-phone');
    const goal = option.getAttribute('data-goal');
    const updateUrl = option.getAttribute('data-updateurl');

    const btnActualizar = document.getElementById('btn-actualizar');
    const formModificar = document.getElementById('form-modificar');

    if (select.value) {
        document.getElementById('mod_email').value = email || '';
        document.getElementById('mod_phone').value = phone || '';
        document.getElementById('mod_goal').value = goal || '';
        
        formModificar.action = updateUrl;
        
        btnActualizar.disabled = false;
        btnActualizar.classList.remove('bg-gray-300', 'cursor-not-allowed');
        btnActualizar.classList.add('bg-[#2C4A3E]', 'hover:bg-emerald-800');
    } else {
        limpiarModificar();
    }
}

function limpiarModificar() {
    document.getElementById('select-asesor').value = '';
    document.getElementById('mod_email').value = '';
    document.getElementById('mod_phone').value = '';
    document.getElementById('mod_goal').value = '';
    
    const btnActualizar = document.getElementById('btn-actualizar');
    btnActualizar.disabled = true;
    btnActualizar.classList.add('bg-gray-300', 'cursor-not-allowed');
    btnActualizar.classList.remove('bg-[#2C4A3E]', 'hover:bg-emerald-800');
}
</script>