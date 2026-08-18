<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    // Mostrar lista de propiedades en la Intranet con filtros avanzados y conteos reales
    public function index(Request $request)
    {
        $query = Property::with(['user', 'images']);

        // Helper para aplicar exactamente los mismos filtros a $query y a $baseCountQuery
        $applyFilters = function ($q) use ($request) {
            // 1. Barra de Búsqueda General (Título, Ubicación o Dirección)
            if ($request->filled('search')) {
                $search = trim($request->search);
                $q->where(function($sub) use ($search) {
                    $sub->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('location', 'LIKE', "%{$search}%")
                        ->orWhere('address', 'LIKE', "%{$search}%");
                });
            }

            // 2. Asesor
            if ($request->filled('agent_id') && $request->agent_id !== 'all') {
                $q->where('user_id', $request->agent_id);
            }

            // 3. Tipo de Operación (Venta, Arriendo, etc.)
            if ($request->filled('service_type') && $request->service_type !== 'all' && $request->service_type !== '') {
                $q->where('service_type', $request->service_type);
            }

            // 4. Tipo de Inmueble (Casa, Departamentos, Comerciales, etc.)
            if ($request->filled('property_type') && $request->property_type !== 'all' && $request->property_type !== '') {
                $tipo = trim($request->property_type);
                if ($tipo === 'Terrenos') {
                    $q->where(function($sub) {
                        $sub->where('property_type', 'Terrenos')
                            ->orWhere('property_type', 'Terrenos Grandes');
                    });
                } else {
                    $q->where('property_type', 'LIKE', "%{$tipo}%");
                }
            }


//Ubicacion: Se busca en location, city, province o sector
$ubicacion = $request->input('location') 
          ?? $request->input('city') 
          ?? $request->input('province') 
          ?? $request->input('sector');

if (!empty($ubicacion) && trim($ubicacion) !== '') {
    $term = trim($ubicacion);
    $q->where('location', 'LIKE', "%{$term}%");
}

// 6. Rangos de Precio (Ignorar max_price si es el valor por defecto de 300,000)
if ($request->filled('min_price') && (float)$request->min_price > 0) {
    $q->where('price', '>=', (float) $request->min_price);
}

if ($request->filled('max_price') && (float)$request->max_price > 0) {
    $max = (float) $request->max_price;
    // Si el frontend envía 300000 por defecto pero no se movió el slider, no limitar
    if ($max != 300000) { 
        $q->where('price', '<=', $max);
    }
}
            // 7. Características
            if ($request->filled('features') && is_array($request->features)) {
                foreach ($request->features as $feature) {
                    match ($feature) {
                        'parqueadero'    => $q->where('garages', '>', 0),
                        'jardin'         => $q->where('has_jardin', true),
                        'balcon'         => $q->where('has_balcon', true),
                        'seguridad'      => $q->where('has_seguridad', true),
                        'agua'           => $q->where('has_agua', true),
                        'luz'            => $q->where('has_luz', true),
                        'alcantarillado' => $q->where('has_alcantarillado', true),
                        'internet'       => $q->where('has_internet', true),
                        'piscina'        => $q->where('has_piscina', true),
                        'bbq'            => $q->where('has_bbq', true),
                        'amoblado'       => $q->where('has_amoblado', true),
                        'mascotas'       => $q->where('has_mascotas', true),
                        default          => null,
                    };
                }
            }
        };

        // Aplicar los filtros a la consulta principal
        $applyFilters($query);

        // Resultados paginados
        $properties = $query->latest()->paginate(12)->withQueryString();
        $agents = User::all();

        // ==========================================
        // CONTEOS REALES Y DINÁMICOS PARA LA BARRA LATERAL
        // ==========================================
        $baseCountQuery = Property::query();
        $applyFilters($baseCountQuery);

        // Conteos por tipo de inmueble
        $countCasas         = (clone $baseCountQuery)->where('property_type', 'Casa')->count();
        $countDepartamentos = (clone $baseCountQuery)->where('property_type', 'Departamentos')->count();
        $countTerrenos      = (clone $baseCountQuery)->whereIn('property_type', ['Terrenos', 'Terrenos Grandes'])->count();
        $countComerciales   = (clone $baseCountQuery)->where('property_type', 'Comerciales')->count();
        $countProyectos     = (clone $baseCountQuery)->where('property_type', 'Proyectos')->count();

        // Conteos por características
        $countParq           = (clone $baseCountQuery)->where('garages', '>', 0)->count();
        $countJardin         = (clone $baseCountQuery)->where('has_jardin', true)->count();
        $countBalcon         = (clone $baseCountQuery)->where('has_balcon', true)->count();
        $countSeguridad      = (clone $baseCountQuery)->where('has_seguridad', true)->count();
        $countAgua           = (clone $baseCountQuery)->where('has_agua', true)->count();
        $countLuz            = (clone $baseCountQuery)->where('has_luz', true)->count();
        $countAlcantarillado = (clone $baseCountQuery)->where('has_alcantarillado', true)->count();

        // ==========================================
        // COLECCIONES PARA CARRUSELES POR CATEGORÍA
        // ==========================================
        $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->latest()->take(6)->get();
        $terrenos      = Property::with('images')->whereIn('property_type', ['Terrenos', 'Terrenos Grandes'])->latest()->take(6)->get();
        $casas         = Property::with('images')->where('property_type', 'Casa')->latest()->take(6)->get();
        $departamentos = Property::with('images')->where('property_type', 'Departamentos')->latest()->take(6)->get();
        $comerciales   = Property::with('images')->where('property_type', 'Comerciales')->latest()->take(6)->get();
        $proyectos     = Property::with('images')->where('property_type', 'Proyectos')->latest()->take(6)->get();

        return view('intranet.properties.index', compact(
            'properties', 
            'agents', 
            'bajaronPrecio', 
            'casas',
            'departamentos',
            'terrenos', 
            'comerciales', 
            'proyectos',
            'countCasas',
            'countDepartamentos',
            'countTerrenos',
            'countComerciales',
            'countProyectos',
            'countParq',
            'countJardin',
            'countBalcon',
            'countSeguridad',
            'countAgua',
            'countLuz',
            'countAlcantarillado'
        ));
    }

    // Formulario para crear
    public function create()
    {
        $asesores = User::all();
        return view('intranet.properties.create', compact('asesores'));
    }

    // Guardar la nueva propiedad
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'property_type' => 'required|string',
            'service_type'  => 'required|string',
            'price'         => 'required|numeric',
            'user_id'       => 'required|exists:users,id',
        ]);

        $data = $request->all();

        if (empty($data['status'])) {
            $data['status'] = 'En Venta';
        }

        // Checkboxes y características a valores booleanos (0 o 1)
        $data['price_dropped']       = (int) $request->input('price_dropped', 0);
        $data['has_jardin']        = $request->has('has_jardin') ? 1 : 0;
        $data['has_balcon']        = $request->has('has_balcon') ? 1 : 0;
        $data['has_seguridad']     = $request->has('has_seguridad') ? 1 : 0;
        $data['has_agua']          = $request->has('has_agua') ? 1 : 0;
        $data['has_luz']           = $request->has('has_luz') ? 1 : 0;
        $data['has_alcantarillado']= $request->has('has_alcantarillado') ? 1 : 0;
        $data['has_internet']      = $request->has('has_internet') ? 1 : 0;
        $data['has_piscina']       = $request->has('has_piscina') ? 1 : 0;
        $data['has_bbq']           = $request->has('has_bbq') ? 1 : 0;
        $data['has_amoblado']      = $request->has('has_amoblado') ? 1 : 0;
        $data['has_mascotas']      = $request->has('has_mascotas') ? 1 : 0;

        if (isset($data['basic_services']) && is_array($data['basic_services'])) {
            $data['basic_services'] = json_encode($data['basic_services']);
        }

        $data['social_info_completed'] = (
            !empty($data['url_youtube']) &&
            !empty($data['url_instagram']) &&
            !empty($data['url_tiktok']) &&
            !empty($data['url_facebook']) &&
            !empty($data['contact_phone']) &&
            !empty($data['contact_email'])
        ) ? 1 : 0;

        $property = Property::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path'  => $path,
                ]);
            }
        }

        return redirect('/intranet/properties')->with('success', '¡Propiedad guardada exitosamente!');
    }

    // Mostrar detalles
    public function show(Property $property)
    {
        $property->load([
            'images' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'user'
        ]);

        return view('intranet.properties.show', compact('property'));
    }

    // Editar
    public function edit(Property $property)
    {
        $asesores = User::all();
        
        $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->take(6)->get();
        $casas         = Property::with('images')->where('property_type', 'Casa')->take(6)->get();
        $departamentos = Property::with('images')->where('property_type', 'Departamentos')->take(6)->get();
        $terrenos      = Property::with('images')->whereIn('property_type', ['Terrenos', 'Terrenos Grandes'])->take(6)->get();
        $comerciales   = Property::with('images')->where('property_type', 'Comerciales')->take(6)->get();
        $proyectos     = Property::with('images')->where('property_type', 'Proyectos')->take(6)->get();

        return view('intranet.properties.edit', compact(
            'property', 
            'asesores', 
            'bajaronPrecio', 
            'casas',
            'departamentos',
            'terrenos', 
            'comerciales', 
            'proyectos'
        ));
    }

    // Actualizar
    public function update(Request $request, Property $property)
    {
        $user = auth()->user();

        if ($user && $user->role === 'Asesor') {
            $request->validate([
                'url_youtube'    => 'nullable|string|max:255',
                'url_instagram'  => 'nullable|string|max:255',
                'url_tiktok'     => 'nullable|string|max:255',
                'url_facebook'   => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'contact_email'  => 'nullable|email|max:255',
            ]);

            $data = $request->only([
                'url_youtube', 
                'url_instagram', 
                'url_tiktok', 
                'url_facebook', 
                'contact_phone', 
                'contact_email'
            ]);

            $advisorFieldsCompleted = !empty($data['url_youtube']) &&
                                      !empty($data['url_instagram']) &&
                                      !empty($data['url_tiktok']) &&
                                      !empty($data['url_facebook']) &&
                                      !empty($data['contact_phone']) &&
                                      !empty($data['contact_email']);

            $data['social_info_completed'] = $advisorFieldsCompleted ? 1 : 0;

        } else {
            $request->validate([
                'title'        => 'required|string|max:255',
                'price'        => 'required|numeric',
                'service_type' => 'required|string',
            ]);

            $data = $request->all();

            $data['price_dropped']       = (int) $request->input('price_dropped', 0);
            $data['has_jardin']        = $request->has('has_jardin') ? 1 : 0;
            $data['has_balcon']        = $request->has('has_balcon') ? 1 : 0;
            $data['has_seguridad']     = $request->has('has_seguridad') ? 1 : 0;
            $data['has_agua']          = $request->has('has_agua') ? 1 : 0;
            $data['has_luz']           = $request->has('has_luz') ? 1 : 0;
            $data['has_alcantarillado']= $request->has('has_alcantarillado') ? 1 : 0;
            $data['has_internet']      = $request->has('has_internet') ? 1 : 0;
            $data['has_piscina']       = $request->has('has_piscina') ? 1 : 0;
            $data['has_bbq']           = $request->has('has_bbq') ? 1 : 0;
            $data['has_amoblado']      = $request->has('has_amoblado') ? 1 : 0;
            $data['has_mascotas']      = $request->has('has_mascotas') ? 1 : 0;

            if (isset($data['basic_services']) && is_array($data['basic_services'])) {
                $data['basic_services'] = json_encode($data['basic_services']);
            }

            $data['social_info_completed'] = (
                !empty($data['url_youtube']) &&
                !empty($data['url_instagram']) &&
                !empty($data['url_tiktok']) &&
                !empty($data['url_facebook']) &&
                !empty($data['contact_phone']) &&
                !empty($data['contact_email'])
            ) ? 1 : 0;
        }

        $property->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path'  => $path,
                ]);
            }
        }

        return redirect('/intranet/properties')->with('success', '¡Propiedad actualizada correctamente!');
    }

    // Eliminar
    public function destroy(Property $property)
    {
        foreach ($property->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        $property->delete();

        return redirect('/intranet/properties')->with('success', 'Propiedad eliminada correctamente.');
    }
}