<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
    /**
     * Carga la vista de la galería de imágenes de un inmueble.
     */
    public function index(Property $property)
    {
        $property->load(['images' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        return view('properties.images', compact('property'));
    }

    /**
     * Procesa y almacena las imágenes en el disco local y BD.
     */
    public function store(Request $request, Property $property)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $lastPosition = $property->images()->max('position') ?? 0;
        $hasPrimary = $property->images()->where('is_primary', true)->exists();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $lastPosition++;
                $path = $file->store('properties/' . $property->id, 'public');

                $isPrimary = !$hasPrimary;

                $property->images()->create([
                    'image_path' => $path,
                    'position' => $lastPosition,
                    'is_primary' => $isPrimary,
                ]);

                $hasPrimary = true;
            }
        }

        return redirect()->back()->with('success', 'Imágenes subidas correctamente.');
    }

    /**
     * Elimina una imagen físicamente del storage y de la base de datos.
     */
    public function destroy(PropertyImage $image)
    {
        $propertyId = $image->property_id;
        $wasPrimary = $image->is_primary;

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        if ($wasPrimary) {
            $nextPrimary = PropertyImage::where('property_id', $propertyId)
                ->orderBy('position', 'asc')
                ->first();

            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
            }
        }

        // Reindexar posiciones consecutivas
        $remainingImages = PropertyImage::where('property_id', $propertyId)
            ->orderBy('position', 'asc')
            ->get();

        foreach ($remainingImages as $index => $img) {
            $img->update(['position' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fotografía eliminada correctamente.'
        ]);
    }

    /**
     * Actualiza la posición de las imágenes y asigna como principal la del slot 1.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:property_images,id',
        ]);

        foreach ($request->order as $index => $id) {
            PropertyImage::where('id', $id)->update([
                'position' => $index + 1,
                'is_primary' => ($index === 0),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden de imágenes e insignia principal actualizados.'
        ]);
    }

    /**
     * Marca una imagen como la portada principal y la traslada al slot 1.
     */
    public function setPrimary(PropertyImage $image)
    {
        $propertyId = $image->property_id;

        // 1. Desmarcar principal en todas las fotos
        PropertyImage::where('property_id', $propertyId)
            ->update(['is_primary' => false]);

        // 2. Asignar como principal y mover a la primera posición
        $image->update([
            'is_primary' => true,
            'position' => 1,
        ]);

        // 3. Desplazar las demás imágenes desde la posición 2 en adelante
        $otherImages = PropertyImage::where('property_id', $propertyId)
            ->where('id', '!=', $image->id)
            ->orderBy('position', 'asc')
            ->get();

        foreach ($otherImages as $index => $other) {
            $other->update(['position' => $index + 2]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen establecida como principal y trasladada al primer lugar.'
        ]);
    }
}