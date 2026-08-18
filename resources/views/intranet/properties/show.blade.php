@extends('layouts.admin')

@section('admin_content')
<div class="max-w-[98rem] mx-auto px-4 pb-16 space-y-4 font-sans" 
     x-data="{ 
         images: [
             @foreach($property->images as $image)
                 @if(!empty($image->image_path))
                     '{{ asset('storage/' . $image->image_path) }}',
                 @endif
             @endforeach
         ],
         currentIndex: 0,
         get activeImage() {
             return this.images.length > 0 ? this.images[this.currentIndex] : '';
         },
         nextImage() {
             if (this.images.length === 0) return;
             this.currentIndex = (this.currentIndex + 1) % this.images.length;
         },
         prevImage() {
             if (this.images.length === 0) return;
             this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
         },
         nextThumb() {
             const container = document.getElementById('thumbnail-container');
             if (container) container.scrollBy({ left: 200, behavior: 'smooth' });
         },
         prevThumb() {
             const container = document.getElementById('thumbnail-container');
             if (container) container.scrollBy({ left: -200, behavior: 'smooth' });
         }
     }">
    
    <!-- BARRA SUPERIOR (VOLVER + EDITAR/ELIMINAR) -->
    <div class="flex justify-between items-center bg-white/90 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-emerald-100 shadow-sm">
        <a href="{{ route('properties.index') }}" class="text-xs font-bold text-[#2C4A3E] hover:text-emerald-700 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-chevron-left text-[10px]"></i> Volver al Catálogo
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('properties.edit', $property->id) }}" class="p-1.5 px-3.5 bg-[#2C4A3E] text-white text-xs font-bold rounded-xl hover:bg-emerald-800 transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-pen text-[10px]"></i> Editar
            </a>
            <form action="{{ route('properties.destroy', $property->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta propiedad? Esta acción no se puede deshacer.');" class="inline-flex">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-1.5 px-3.5 bg-red-600 text-white text-xs font-bold rounded-xl hover:bg-red-700 transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-[2px] items-start">
        
        <!-- COLUMNA IZQUIERDA: DETALLES (USANDO EL COMPONENTE COMPARTIDO) -->
        <div class="lg:col-span-4 lg:max-w-[370px] ml-auto bg-[#FDFBF7] p-2 rounded-3xl border border-emerald-200/80 shadow-sm">
            <x-property-details :property="$property" />
        </div>

        <!-- COLUMNA DERECHA: IMÁGENES Y CARRUSEL -->
        <div class="lg:col-span-8 space-y-3">
            <div class="bg-white p-3 rounded-3xl border border-emerald-100 shadow-sm space-y-3">
                
                <!-- Imagen Principal -->
                <div class="relative w-full bg-black rounded-2xl overflow-hidden flex items-center justify-center border border-emerald-100 shadow-inner group">
                    <template x-if="images.length > 0">
                        <div class="w-full flex items-center justify-center relative">
                            <img :src="activeImage" class="w-full h-auto max-h-[580px] object-cover transition-all duration-300">
                            
                            <button @click="prevImage()" class="absolute left-3 bg-black/50 hover:bg-black/80 text-white p-3 rounded-full backdrop-blur-md transition flex items-center justify-center shadow-lg cursor-pointer">
                                <i class="fa-solid fa-chevron-left text-sm"></i>
                            </button>

                            <button @click="nextImage()" class="absolute right-3 bg-black/50 hover:bg-black/80 text-white p-3 rounded-full backdrop-blur-md transition flex items-center justify-center shadow-lg cursor-pointer">
                                <i class="fa-solid fa-chevron-right text-sm"></i>
                            </button>

                            <div class="absolute bottom-3 right-3 bg-black/70 text-white text-[11px] font-bold px-3 py-1 rounded-xl backdrop-blur-md shadow">
                                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="images.length === 0">
                        <div class="h-[400px] flex flex-col items-center justify-center text-emerald-800/40 space-y-2">
                            <i class="fa-solid fa-house-chimney-crack text-4xl"></i>
                            <span class="text-xs font-bold uppercase">Sin fotografías registradas</span>
                        </div>
                    </template>
                </div>

                <!-- Miniaturas Inferiores -->
                <template x-if="images.length > 0">
                    <div class="relative flex items-center px-6">
                        <button @click="prevThumb()" class="absolute left-0 z-10 bg-white hover:bg-emerald-50 text-emerald-900 shadow-md border border-emerald-200 p-2.5 rounded-full transition flex items-center justify-center cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>

                        <div id="thumbnail-container" class="flex gap-2.5 overflow-x-auto scroll-smooth py-1 px-1 no-scrollbar w-full">
                            <template x-for="(img, index) in images" :key="index">
                                <div @click="currentIndex = index" 
                                     class="h-20 w-28 flex-shrink-0 rounded-xl overflow-hidden border-2 cursor-pointer transition transform hover:scale-105"
                                     :class="currentIndex === index ? 'border-emerald-700 shadow-md ring-2 ring-emerald-600/30' : 'border-transparent opacity-60 hover:opacity-100'">
                                    <img :src="img" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>

                        <button @click="nextThumb()" class="absolute right-0 z-10 bg-white hover:bg-emerald-50 text-emerald-900 shadow-md border border-emerald-200 p-2.5 rounded-full transition flex items-center justify-center cursor-pointer">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </template>

            </div>
        </div>

    </div>
</div>
@endsection