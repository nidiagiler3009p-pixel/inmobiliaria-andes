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

        // 1. Filtro por Barra de Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filtro por Asesor Asignado
        if ($request->filled('agent_id') && $request->agent_id !== 'all') {
            $query->where('user_id', $request->agent_id);
        }

        // 3. Filtro por Tipo de Operación
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // 4. Filtro por Tipo de Inmueble
        if ($request->filled('property_type')) {
            $tipo = trim($request->property_type);
            $query->where('property_type', 'LIKE', "%{$tipo}%");
        }

        // 5. Filtros de Ubicación adaptados al campo 'location'
        if ($request->filled('province')) {
            $query->where('location', 'LIKE', "%{$request->province}%");
        }
        if ($request->filled('city')) {
            $query->where('location', 'LIKE', "%{$request->city}%");
        }
        if ($request->filled('sector')) {
            $query->where('location', 'LIKE', "%{$request->sector}%");
        }

        // 6. Filtro por Rango de Precios
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 7. Filtro por Características utilizando las columnas booleanas directas
        if ($request->filled('features') && is_array($request->features)) {
            foreach ($request->features as $feature) {
                if ($feature === 'parqueadero') {
                    $query->where('garages', '>', 0);
                } elseif ($feature === 'jardin') {
                    $query->where('has_jardin', true);
                } elseif ($feature === 'balcon') {
                    $query->where('has_balcon', true);
                } elseif ($feature === 'seguridad') {
                    $query->where('has_seguridad', true);
                } elseif ($feature === 'agua') {
                    $query->where('has_agua', true);
                } elseif ($feature === 'luz') {
                    $query->where('has_luz', true);
                } elseif ($feature === 'alcantarillado') {
                    $query->where('has_alcantarillado', true);
                } elseif ($feature === 'internet') {
                    $query->where('has_internet', true);
                } elseif ($feature === 'piscina') {
                    $query->where('has_piscina', true);
                } elseif ($feature === 'bbq') {
                    $query->where('has_bbq', true);
                } elseif ($feature === 'amoblado') {
                    $query->where('has_amoblado', true);
                } elseif ($feature === 'mascotas') {
                    $query->where('has_mascotas', true);
                }
            }
        }

        // Resultados paginados para la vista de búsqueda/filtros
        $properties = $query->latest()->paginate(12)->withQueryString();
        $agents = User::all();

        // ==========================================
        // CONTEOS REALES Y DINÁMICOS PARA LA BARRA LATERAL
        // ==========================================
        
        $baseCountQuery = Property::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $baseCountQuery->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('agent_id') && $request->agent_id !== 'all') {
            $baseCountQuery->where('user_id', $request->agent_id);
        }
        if ($request->filled('service_type')) {
            $baseCountQuery->where('service_type', $request->service_type);
        }
        if ($request->filled('property_type')) {
            $baseCountQuery->where('property_type', 'LIKE', '%' . trim($request->property_type) . '%');
        }
        if ($request->filled('province')) {
            $baseCountQuery->where('location', 'LIKE', "%{$request->province}%");
        }
        if ($request->filled('city')) {
            $baseCountQuery->where('location', 'LIKE', "%{$request->city}%");
        }
        if ($request->filled('sector')) {
            $baseCountQuery->where('location', 'LIKE', "%{$request->sector}%");
        }
        if ($request->filled('min_price')) {
            $baseCountQuery->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $baseCountQuery->where('price', '<=', $request->max_price);
        }

        // Tipos de Inmueble dinámicos basados en tu enum
        $countCasas         = (clone $baseCountQuery)->where('property_type', 'Casa')->count();
        $countTerrenos      = (clone $baseCountQuery)->where('property_type', 'Terrenos')->count();
        $countComerciales   = (clone $baseCountQuery)->where('property_type', 'Comerciales')->count();
        $countProyectos     = (clone $baseCountQuery)->where('property_type', 'Oficinas')->count();

        // Conteos dinámicos utilizando directamente las columnas booleanas
        $countParq      = (clone $baseCountQuery)->where('garages', '>', 0)->count();
        $countJardin    = (clone $baseCountQuery)->where('has_jardin', true)->count();
        $countBalcon    = (clone $baseCountQuery)->where('has_balcon', true)->count();
        $countSeguridad = (clone $baseCountQuery)->where('has_seguridad', true)->count();
        $countAgua      = (clone $baseCountQuery)->where('has_agua', true)->count();
        $countLuz       = (clone $baseCountQuery)->where('has_luz', true)->count();
        $countAlcantarillado = (clone $baseCountQuery)->where('has_alcantarillado', true)->count();

        // ==========================================
        // COLECCIONES PARA LOS CARRUSELES TIPO NETFLIX
        // ==========================================
        $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->take(6)->get();
        $terrenos      = Property::with('images')->where('property_type', 'Terrenos')->take(6)->get();
        $casas         = Property::with('images')->where('property_type', 'Casa')->take(6)->get();
        $comerciales   = Property::with('images')->where('property_type', 'Comerciales')->take(6)->get();
        $proyectos     = Property::with('images')->where('property_type', 'Oficinas')->take(6)->get();
       
        return view('intranet.properties.index', compact(
            'properties', 
            'agents', 
            'bajaronPrecio', 
            'terrenos', 
            'casas', 
            'comerciales', 
            'proyectos',
            'countCasas',
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

        // Asegurar que todos los checkboxes y características se guarden como 0 o 1
        $data['price_dropped'] = (int) $request->input('price_dropped', 0);
        $data['has_jardin']         = $request->has('has_jardin') ? 1 : 0;
        $data['has_balcon']         = $request->has('has_balcon') ? 1 : 0;
        $data['has_seguridad']      = $request->has('has_seguridad') ? 1 : 0;
        $data['has_agua']           = $request->has('has_agua') ? 1 : 0;
        $data['has_luz']            = $request->has('has_luz') ? 1 : 0;
        $data['has_alcantarillado'] = $request->has('has_alcantarillado') ? 1 : 0;
        $data['has_internet']       = $request->has('has_internet') ? 1 : 0;
        $data['has_piscina']        = $request->has('has_piscina') ? 1 : 0;
        $data['has_bbq']            = $request->has('has_bbq') ? 1 : 0;
        $data['has_amoblado']       = $request->has('has_amoblado') ? 1 : 0;
        $data['has_mascotas']       = $request->has('has_mascotas') ? 1 : 0;

        if (isset($data['basic_services']) && is_array($data['basic_services'])) {
            $data['basic_services'] = json_encode($data['basic_services']);
        }

        // Al crear, comprobamos si de una vez se llenaron los campos de asesor
        $data['social_info_completed'] = (
            !empty($data['url_youtube']) &&
            !empty($data['url_instagram']) &&
            !empty($data['url_tiktok']) &&
            !empty($data['url_facebook']) &&
            !empty($data['whatsapp_phone']) &&
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
        
        $property->load('images', 'user');
        return view('intranet.properties.show', compact('property'));
    }

    // Editar
    public function edit(Property $property)
    {
        $asesores = User::all();
        
        // Cargar todas las colecciones que la vista utiliza en su diseño
        $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->take(6)->get();
        $terrenos      = Property::with('images')->where('property_type', 'Terrenos')->take(6)->get();
        $casas         = Property::with('images')->where('property_type', 'Casa')->take(6)->get();
        $comerciales   = Property::with('images')->where('property_type', 'Comerciales')->take(6)->get();
        $proyectos     = Property::with('images')->where('property_type', 'Oficinas')->take(6)->get();

        return view('intranet.properties.edit', compact(
            'property', 
            'asesores', 
            'bajaronPrecio', 
            'terrenos', 
            'casas', 
            'comerciales', 
            'proyectos'
        ));
    }

    // Actualizar
    public function update(Request $request, Property $property)
    {
        $user = auth()->user();

        // Verificamos si el usuario autenticado tiene el rol de asesor
        if ($user && $user->hasRole('asesor')) {
            // El asesor solo valida sus campos correspondientes
            $request->validate([
                'url_youtube'    => 'nullable|string|max:255',
                'url_instagram'  => 'nullable|string|max:255',
                'url_tiktok'     => 'nullable|string|max:255',
                'url_facebook'   => 'nullable|string|max:255',
                'whatsapp_phone' => 'nullable|string|max:20',
                'contact_email'  => 'nullable|email|max:255',
            ]);

            $data = $request->only([
                'url_youtube', 
                'url_instagram', 
                'url_tiktok', 
                'url_facebook', 
                'whatsapp_phone', 
                'contact_email'
            ]);

            // Comprobamos si TODOS los campos del asesor están llenos para activar el visto
            $advisorFieldsCompleted = !empty($data['url_youtube']) &&
                                      !empty($data['url_instagram']) &&
                                      !empty($data['url_tiktok']) &&
                                      !empty($data['url_facebook']) &&
                                      !empty($data['whatsapp_phone']) &&
                                      !empty($data['contact_email']);

            $data['social_info_completed'] = $advisorFieldsCompleted ? 1 : 0;

        } else {
            // El administrador gestiona y valida todo el registro completo
            $request->validate([
                'title'        => 'required|string|max:255',
                'price'        => 'required|numeric',
                'service_type' => 'required|string',
            ]);

            $data = $request->all();

            // Asegurar que todos los checkboxes y características se actualicen como 0 o 1
            $data['price_dropped'] = (int) $request->input('price_dropped', 0);
            $data['has_jardin']         = $request->has('has_jardin') ? 1 : 0;
            $data['has_balcon']         = $request->has('has_balcon') ? 1 : 0;
            $data['has_seguridad']      = $request->has('has_seguridad') ? 1 : 0;
            $data['has_agua']           = $request->has('has_agua') ? 1 : 0;
            $data['has_luz']            = $request->has('has_luz') ? 1 : 0;
            $data['has_alcantarillado'] = $request->has('has_alcantarillado') ? 1 : 0;
            $data['has_internet']       = $request->has('has_internet') ? 1 : 0;
            $data['has_piscina']        = $request->has('has_piscina') ? 1 : 0;
            $data['has_bbq']            = $request->has('has_bbq') ? 1 : 0;
            $data['has_amoblado']       = $request->has('has_amoblado') ? 1 : 0;
            $data['has_mascotas']       = $request->has('has_mascotas') ? 1 : 0;

            if (isset($data['basic_services']) && is_array($data['basic_services'])) {
                $data['basic_services'] = json_encode($data['basic_services']);
            }

            // Calculamos el estatus de completado también si el admin actualiza dichos campos
            $data['social_info_completed'] = (
                !empty($data['url_youtube']) &&
                !empty($data['url_instagram']) &&
                !empty($data['url_tiktok']) &&
                !empty($data['url_facebook']) &&
                !empty($data['whatsapp_phone']) &&
                !empty($data['contact_email'])
            ) ? 1 : 0;
        }

        // Actualizamos la propiedad
        $property->update($data);

        // Gestión de imágenes
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