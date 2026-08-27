@extends('layouts.admin')
@section('admin_content')

<div class="flex-grow px-3 py-4 max-w-4xl mx-auto w-full text-[#2C3E35] font-sans antialiased">

    <header class="relative bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-4 shadow-sm mb-3">
        <div class="absolute left-1/2 -translate-x-1/2 -top-4 bg-[#2C5E43] text-white w-10 h-10 rounded-full flex items-center justify-center shadow border-4 border-[#F9F7F2]">
            <i class="fa-solid fa-user-pen text-sm"></i>
        </div>
        <div class="text-center pt-2">
            <h1 class="text-xl font-bold text-[#1E392A]">Editar Datos del Cliente</h1>
            <p class="text-[10px] text-[#556B5D] mt-1">Complete o corrija la información antes de confirmar el cliente.</p>
        </div>
    </header>

    @if($errors->any())
        <div class="mb-3 bg-red-50 border border-red-300 text-red-700 px-3 py-2 rounded-lg text-[10px]">
            <div class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Revise los siguientes campos:</div>
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('clients.update', $client->id) }}" class="bg-[#EFECE6] border border-[#D8D3C8] rounded-xl px-4 py-3 shadow-sm">
        @csrf @method('PUT')

        <h2 class="text-sm font-bold text-[#1E392A] mb-3"><i class="fa-solid fa-id-card text-[#2C5E43] mr-1"></i> Información del Cliente</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Nombre *</label>
                <input type="text" name="name" required value="{{ old('name', $client->name) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Apellido *</label>
                <input type="text" name="last_name" required value="{{ old('last_name', $client->last_name) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Identificación</label>
                <input type="text" name="identification_card" value="{{ old('identification_card', $client->identification_card) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Teléfono *</label>
                <input type="text" name="phone" required value="{{ old('phone', $client->phone) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Correo</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Estado comercial *</label>
                <select name="status" required class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
                    @foreach(['Confirmada','Interesado','En Proceso','Cerrado Exitoso','Seguimiento Pendiente','Negociación','Vendida'] as $estado)
                        <option value="{{ $estado }}" {{ old('status', $client->status) === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Origen</label>
                <input type="text" value="{{ $client->origin_module ?? 'No definido' }}" disabled class="w-full bg-gray-100 border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
            <div>
                <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Canal social</label>
                <input type="text" name="social_media_source" value="{{ old('social_media_source', $client->social_media_source) }}" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">
            </div>
        </div>

        <div class="mt-3">
            <label class="block text-[9px] font-semibold text-[#1E392A] mb-1">Observaciones</label>
            <textarea name="observations" rows="4" class="w-full bg-white border border-[#D8D3C8] rounded-lg px-3 py-2 text-[10px]">{{ old('observations', $client->observations) }}</textarea>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-[#D8D3C8]">
            <a href="{{ route('clients.show', $client->id) }}" class="bg-gray-300 hover:bg-gray-400 text-[#2C3E35] px-4 py-2 rounded-lg text-[10px] font-semibold"><i class="fa-solid fa-arrow-left mr-1"></i> Cancelar</a>
            <button type="submit" class="bg-[#2C5E43] hover:bg-[#1E392A] text-white px-4 py-2 rounded-lg text-[10px] font-semibold shadow-sm"><i class="fa-solid fa-floppy-disk mr-1"></i> Guardar cambios</button>
        </div>
    </form>
</div>

@endsection