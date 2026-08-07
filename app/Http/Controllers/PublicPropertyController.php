<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PublicPropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['images', 'user']);

        // Filtro por búsqueda de texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de operación (Venta / Arriendo)
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filtro por tipo de inmueble
        if ($request->filled('property_type')) {
            $tipo = trim($request->property_type);
            $query->where('property_type', 'LIKE', "%{$tipo}%");
        }

        // Filtros de ubicación
        if ($request->filled('province')) {
            $query->where('location', 'LIKE', "%{$request->province}%");
        }
        if ($request->filled('city')) {
            $query->where('location', 'LIKE', "%{$request->city}%");
        }
        if ($request->filled('sector')) {
            $query->where('location', 'LIKE', "%{$request->sector}%");
        }
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Filtro por precio mínimo y máximo
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtro por características (Checkboxes)
        if ($request->filled('features') && is_array($request->features)) {
            foreach ($request->features as $feature) {
                if ($feature === 'parqueadero') {
                    $query->where('garages', '>', 0);
                } elseif (in_array($feature, ['jardin', 'balcon', 'seguridad', 'agua', 'luz', 'alcantarillado', 'internet', 'piscina', 'bbq', 'amoblado', 'mascotas'])) {
                    $query->where("has_{$feature}", true);
                }
            }
        }

        // Resultados paginados con los filtros aplicados
        $properties = $query->latest()->paginate(12)->withQueryString();

        // Secciones tipo Netflix
        $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->take(8)->get();
        $terrenos = Property::with('images')->where('property_type', 'Terrenos')->take(8)->get();
        $casas = Property::with('images')->where('property_type', 'Casa')->take(8)->get();
        $comerciales = Property::with('images')->where('property_type', 'Comerciales')->take(8)->get();
        $proyectos = Property::with('images')->where('property_type', 'Oficinas')->take(8)->get();

        // Contadores para el menú lateral
        $countCasas = Property::where('property_type', 'Casa')->count();
        $countTerrenos = Property::where('property_type', 'Terrenos')->count();
        $countComerciales = Property::where('property_type', 'Comerciales')->count();
        $countProyectos = Property::where('property_type', 'Oficinas')->count();

        return view('public-pages.catalogo', compact(
            'properties',
            'bajaronPrecio', 
            'terrenos', 
            'casas', 
            'comerciales', 
            'proyectos',
            'countCasas', 
            'countTerrenos', 
            'countComerciales', 
            'countProyectos'
        ));
    }

    public function show($id)
    {
        $property = Property::with(['images', 'user'])->findOrFail($id);
        return view('public-pages.show', compact('property'));
    }
}