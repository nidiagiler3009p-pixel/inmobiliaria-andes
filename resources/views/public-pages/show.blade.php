@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6 font-sans" 
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
    
    <!-- BOTÓN DE RETORNO -->
    <div class="flex justify-between items-center bg-white px-4 py-2.5 rounded-2xl border shadow-sm">
        <a href="{{ route('public.catalogo.index') }}" class="text-xs font-bold text-gray-700 hover:text-emerald-700 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-chevron-left text-[10px]"></i> Volver al Catálogo
        </a>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- COLUMNA IZQUIERDA: LLAMANDO AL COMPONENTE COMPARTIDO -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border shadow-sm space-y-4">
            
            <!-- AQUÍ SE INCRUSTA TODO EL BLOQUE DE LA PROPIEDAD AUTOMÁTICAMENTE -->
            <x-property-details :property="$property" />

            <!-- BOTÓN DE WHATSAPP (Exclusivo o adaptado para el público) -->
            @if(!empty($property->whatsapp_phone))
            <div class="pt-4 border-t">
                <a href="https://wa.me/{{ $property->whatsapp_phone }}?text=Hola,%20estoy%20interesado%20en%20la%20propiedad:%20{{ urlencode($property->title) }}" 
                   target="_blank" 
                   class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-2xl flex items-center justify-center gap-2 transition shadow-md text-sm">
                    <i class="fa-brands fa-whatsapp text-lg"></i> Consultar por WhatsApp
                </a>
            </div>
            @endif
        </div>

        <!-- COLUMNA DERECHA: IMÁGENES (Galería) -->
        <div class="lg:col-span-8 space-y-3">
            <div class="bg-white p-3 rounded-3xl border shadow-sm space-y-3">
                <!-- Visor principal y miniaturas de imágenes -->
                <div class="relative w-full bg-black rounded-2xl overflow-hidden flex items-center justify-center shadow-inner">
                    <template x-if="images.length > 0">
                        <div class="w-full flex items-center justify-center relative">
                            <img :src="activeImage" class="w-full h-auto max-h-[580px] object-cover transition-all duration-300">
                            <button @click="prevImage()" class="absolute left-3 bg-black/50 hover:bg-black/80 text-white p-3 rounded-full backdrop-blur-md transition flex items-center justify-center shadow-lg"><i class="fa-solid fa-chevron-left text-sm"></i></button>
                            <button @click="nextImage()" class="absolute right-3 bg-black/50 hover:bg-black/80 text-white p-3 rounded-full backdrop-blur-md transition flex items-center justify-center shadow-lg"><i class="fa-solid fa-chevron-right text-sm"></i></button>
                            <div class="absolute bottom-3 right-3 bg-black/70 text-white text-xs font-bold px-3 py-1 rounded-xl backdrop-blur-md">
                                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="images.length === 0">
                        <div class="h-[400px] flex flex-col items-center justify-center text-gray-400 space-y-2">
                            <i class="fa-solid fa-house-chimney-crack text-4xl"></i>
                            <span class="text-xs font-bold uppercase">Sin fotografías disponibles</span>
                        </div>
                    </template>
                </div>

                <!-- Miniaturas -->
                <template x-if="images.length > 0">
                    <div class="relative flex items-center px-6">
                        <button @click="prevThumb()" class="absolute left-0 z-10 bg-white hover:bg-gray-100 text-gray-800 shadow-md border p-2.5 rounded-full transition flex items-center justify-center"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                        <div id="thumbnail-container" class="flex gap-2.5 overflow-x-auto scroll-smooth py-1 px-1 w-full">
                            <template x-for="(img, index) in images" :key="index">
                                <div @click="currentIndex = index" 
                                     class="h-20 w-28 flex-shrink-0 rounded-xl overflow-hidden border-2 cursor-pointer transition transform hover:scale-105"
                                     :class="currentIndex === index ? 'border-emerald-600 shadow-md ring-2 ring-emerald-600/30' : 'border-transparent opacity-60 hover:opacity-100'">
                                    <img :src="img" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                        <button @click="nextThumb()" class="absolute right-0 z-10 bg-white hover:bg-gray-100 text-gray-800 shadow-md border p-2.5 rounded-full transition flex items-center justify-center"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>
@endsection