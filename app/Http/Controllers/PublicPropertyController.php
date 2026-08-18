<?php

namespace App\Http\Controllers;

use App\Models\AppointmentTracking;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublicPropertyController extends Controller
{
    /**
     * Mostrar lista de propiedades en el catálogo público / intranet con filtros y conteos.
     */
   public function index(Request $request)
{
    $query = Property::with(['user', 'images']);

    // 1. Filtro por Barra de Búsqueda
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
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

// Capturar cualquier parámetro de ubicación que envíe el formulario público
$ubicacion = $request->input('location') 
          ?? $request->input('city') 
          ?? $request->input('province') 
          ?? $request->input('sector');

if (!empty($ubicacion) && trim($ubicacion) !== '') {
    $term = trim($ubicacion);
    $query->where(function ($q) use ($term) {
        $q->where('location', 'LIKE', "%{$term}%")
          ->orWhere('address', 'LIKE', "%{$term}%");
          });
}

// 6. Rangos de Precio
if ($request->filled('min_price') && (float)$request->min_price > 0) {
    $query->where('price', '>=', (float) $request->min_price);
}

if ($request->filled('max_price') && (float)$request->max_price > 0) {
    $max = (float) $request->max_price;
    // Si el frontend envía 300000 por defecto pero no se movió el slider, no limitar
    if ($max != 300000) { 
        $query->where('price', '<=', $max);
    }
}

    // 7. Filtro por Características booleanas
    if ($request->filled('features') && is_array($request->features)) {
        $featureMap = [
            'parqueadero'    => fn($q) => $q->where('garages', '>', 0),
            'jardin'         => fn($q) => $q->where('has_jardin', true),
            'balcon'         => fn($q) => $q->where('has_balcon', true),
            'seguridad'      => fn($q) => $q->where('has_seguridad', true),
            'agua'           => fn($q) => $q->where('has_agua', true),
            'luz'            => fn($q) => $q->where('has_luz', true),
            'alcantarillado' => fn($q) => $q->where('has_alcantarillado', true),
            'internet'       => fn($q) => $q->where('has_internet', true),
            'piscina'        => fn($q) => $q->where('has_piscina', true),
            'bbq'            => fn($q) => $q->where('has_bbq', true),
            'amoblado'       => fn($q) => $q->where('has_amoblado', true),
            'mascotas'       => fn($q) => $q->where('has_mascotas', true),
        ];

        foreach ($request->features as $feature) {
            if (isset($featureMap[$feature])) {
                $featureMap[$feature]($query);
            }
        }
    }

    // Clonar la consulta después de aplicar TODOS los filtros principales para los conteos
    $baseCountQuery = (clone $query);

    // Resultados paginados
    $properties = $query->latest()->paginate(12)->withQueryString();
    $agents = User::all();

    // Conteos dinámicos por tipo de inmueble
    $countCasas         = (clone $baseCountQuery)->where('property_type', 'Casa')->count();
    $countDepartamentos = (clone $baseCountQuery)->where('property_type', 'Departamentos')->count();
    $countTerrenos      = (clone $baseCountQuery)->whereIn('property_type', ['Terrenos', 'Terrenos Grandes'])->count();
    $countComerciales   = (clone $baseCountQuery)->where('property_type', 'Comerciales')->count();
    $countProyectos     = (clone $baseCountQuery)->where('property_type', 'Proyectos')->count();

    // Conteos dinámicos por características
    $countParq          = (clone $baseCountQuery)->where('garages', '>', 0)->count();
    $countJardin        = (clone $baseCountQuery)->where('has_jardin', true)->count();
    $countBalcon        = (clone $baseCountQuery)->where('has_balcon', true)->count();
    $countSeguridad     = (clone $baseCountQuery)->where('has_seguridad', true)->count();
    $countAgua          = (clone $baseCountQuery)->where('has_agua', true)->count();
    $countLuz           = (clone $baseCountQuery)->where('has_luz', true)->count();
    $countAlcantarillado = (clone $baseCountQuery)->where('has_alcantarillado', true)->count();
    $countInternet      = (clone $baseCountQuery)->where('has_internet', true)->count();
    $countPiscina       = (clone $baseCountQuery)->where('has_piscina', true)->count();
    $countBbq           = (clone $baseCountQuery)->where('has_bbq', true)->count();
    $countAmoblado      = (clone $baseCountQuery)->where('has_amoblado', true)->count();
    $countMascotas      = (clone $baseCountQuery)->where('has_mascotas', true)->count();

    // Carruseles para la vista pública
    $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->latest()->take(6)->get();
    $terrenos      = Property::with('images')->whereIn('property_type', ['Terrenos', 'Terrenos Grandes'])->latest()->take(6)->get();
    $casas         = Property::with('images')->where('property_type', 'Casa')->latest()->take(6)->get();
    $departamentos = Property::with('images')->where('property_type', 'Departamentos')->latest()->take(6)->get();
    $comerciales   = Property::with('images')->where('property_type', 'Comerciales')->latest()->take(6)->get();
    $proyectos     = Property::with('images')->where('property_type', 'Proyectos')->latest()->take(6)->get();

    return view('public-pages.catalogo', compact(
        'properties', 'agents', 'bajaronPrecio', 'terrenos', 'casas',
        'departamentos', 'comerciales', 'proyectos', 'countCasas',
        'countDepartamentos', 'countTerrenos', 'countComerciales',
        'countProyectos', 'countParq', 'countJardin', 'countBalcon',
        'countSeguridad', 'countAgua', 'countLuz', 'countAlcantarillado',
        'countInternet', 'countPiscina', 'countBbq', 'countAmoblado', 'countMascotas'
    ));
}

    public function create()
    {
        $asesores = User::all();
        $clientes = Client::all();
        return view('intranet.properties.create', compact('asesores', 'clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'property_type' => 'required|string',
            'service_type'  => 'required|string',
            'price'         => 'required|numeric',
            'user_id'       => 'required|exists:users,id',
            'owner_name'    => 'nullable|string|max:255',
            'owner_phone'   => 'nullable|string|max:50',
            'owner_dni'     => 'nullable|string|max:20',
            'owner_email'   => 'nullable|email|max:255',
        ]);

        $data = $request->all();
        $data['status'] = $data['status'] ?? 'En Venta';

        $booleanFields = [
            'has_jardin', 'has_balcon', 'has_seguridad', 'has_agua',
            'has_luz', 'has_alcantarillado', 'has_internet',
            'has_piscina', 'has_bbq', 'has_amoblado', 'has_mascotas'
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field) ? 1 : 0;
        }

        $data['price_dropped'] = (int) $request->input('price_dropped', 0);

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

    public function show(Property $property)
    {
        $property->load([
            'images' => fn($query) => $query->orderBy('position', 'asc'),
            'user'
        ]);

        return view('public-pages.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $asesores = User::all();
        $clientes = Client::all();

        $bajaronPrecio = Property::with('images')->where('price_dropped', 1)->take(6)->get();
        $terrenos      = Property::with('images')->whereIn('property_type', ['Terrenos', 'Terrenos Grandes'])->take(6)->get();
        $casas         = Property::with('images')->where('property_type', 'Casa')->take(6)->get();
        $departamentos = Property::with('images')->where('property_type', 'Departamentos')->take(6)->get();
        $comerciales   = Property::with('images')->where('property_type', 'Comerciales')->take(6)->get();
        $proyectos     = Property::with('images')->where('property_type', 'Proyectos')->take(6)->get();

        return view('intranet.properties.edit', compact(
            'property',
            'asesores',
            'clientes',
            'bajaronPrecio',
            'terrenos',
            'casas',
            'departamentos',
            'comerciales',
            'proyectos'
        ));
    }

    public function update(Request $request, Property $property)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('asesor')) {
            $request->validate([
                'url_youtube'    => 'nullable|string|max:255',
                'url_instagram'  => 'nullable|string|max:255',
                'url_tiktok'     => 'nullable|string|max:255',
                'url_facebook'   => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'contact_email'  => 'nullable|email|max:255',
            ]);

            $data = $request->only([
                'url_youtube', 'url_instagram', 'url_tiktok',
                'url_facebook', 'contact_phone', 'contact_email'
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
                'owner_name'   => 'nullable|string|max:255',
                'owner_phone'  => 'nullable|string|max:50',
                'owner_dni'    => 'nullable|string|max:20',
                'owner_email'  => 'nullable|email|max:255',
            ]);

            $data = $request->all();
            $data['price_dropped'] = (int) $request->input('price_dropped', 0);

            $booleanFields = [
                'has_jardin', 'has_balcon', 'has_seguridad', 'has_agua',
                'has_luz', 'has_alcantarillado', 'has_internet',
                'has_piscina', 'has_bbq', 'has_amoblado', 'has_mascotas'
            ];

            foreach ($booleanFields as $field) {
                $data[$field] = $request->has($field) ? 1 : 0;
            }

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

    public function destroy(Property $property)
    {
        foreach ($property->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        $property->delete();

        return redirect('/intranet/properties')->with('success', 'Propiedad eliminada correctamente.');
    }

    public function sendPublicMessage(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:50',
            'message'          => 'nullable|string',
            'property_id'      => 'nullable|exists:properties,id',
            'appointment_id'   => 'nullable|exists:appointments_tracking,id',
            'want_appointment' => 'nullable|boolean',
            'appointment_date' => 'nullable',
            'appointment_time' => 'nullable|string',
            'meeting_place'    => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $agentId = null;
            $agentName = 'un asesor comercial';
            $propertyTitle = '';

            if ($request->filled('property_id')) {
                $property = Property::with('user')->find($request->property_id);
                if ($property) {
                    $propertyTitle = $property->title;
                    if ($property->user) {
                        $agentName = $property->user->name;
                        $agentId   = $property->user->id;
                    }
                }
            }

            $fullName   = trim($request->name);
            $nameParts   = explode(' ', $fullName, 2);
            $firstName   = $nameParts[0];
            $lastName    = $nameParts[1] ?? '';
            $clientEmail = $request->email ?: ($request->phone . '@sinemail.com');

            $client = Client::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'name'                => $firstName,
                    'last_name'           => $lastName,
                    'email'               => $clientEmail,
                    'origin_module'       => 'Catálogo Público',
                    'social_media_source' => 'Sitio Web',
                    'status'              => 'Interesado',
                    'user_id'             => $agentId,
                ]
            );

            if (!$client->wasRecentlyCreated) {
                $client->update([
                    'name'      => $firstName,
                    'last_name' => $lastName,
                ]);
            }

            $isAppointment = $request->filled('want_appointment') && $request->filled('appointment_date');

            if ($isAppointment) {
                $dateOnly = $request->appointment_date;
                $timeOnly = $request->appointment_time ?: '09:00';
                $appointmentDatetime = $dateOnly . ' ' . $timeOnly;
                $type = 'Visita Guiada';
            } else {
                $appointmentDatetime = null;
                $type = 'Consulta Directa';
            }

            $locationRef = $request->filled('meeting_place') ? $request->meeting_place : 'Por confirmar';

            $dataToSave = [
                'client_id'          => $client->id,
                'user_id'            => $agentId,
                'property_id'        => $request->property_id,
                'registration_date'  => now(),
                'appointment_date'   => $appointmentDatetime,
                'location_reference' => $locationRef,
                'status'             => 'Pendiente',
                'notes'              => $request->message ?: 'Mensaje sin observaciones',
                'type'               => $type,
                'priority'           => 'Media',
                'source_channel'     => 'Sitio Web',
                'is_notified'        => false,
            ];

            $tracking = $request->filled('appointment_id')
                ? AppointmentTracking::find($request->appointment_id)
                : null;

            if ($tracking) {
                $dataToSave['status'] = 'agendada';
                $tracking->update($dataToSave);
            } else {
                $tracking = AppointmentTracking::create($dataToSave);
            }

            if ($isAppointment) {
                Carbon::setLocale('es');
                $formattedDate = Carbon::parse($dateOnly)->translatedFormat('j \d\e F');

                return redirect()->back()->with('appointment_confirmed', [
                    'appointment_id'   => $tracking->id,
                    'client_name'      => $fullName,
                    'name'             => $fullName,
                    'agent_name'       => $agentName,
                    'date'             => $formattedDate,
                    'time'             => $timeOnly,
                    'property_title'   => $propertyTitle,
                    'phone'            => $request->phone,
                    'appointment_date' => $dateOnly,
                    'appointment_time' => $timeOnly,
                    'meeting_place'    => $request->meeting_place,
                    'message'          => $request->message,
                    'want_appointment' => true,
                ]);
            }

            return redirect()->back()->with('success', 'Tu mensaje ha sido enviado con éxito, pronto un asesor se contactará contigo.');
        });
    }

    public function confirmAppointment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments_tracking,id',
        ]);

        $appointment = AppointmentTracking::find($request->appointment_id);

        if ($appointment) {
            $appointment->update([
                'status' => 'agendada',
            ]);
        }

        session()->forget('appointment_confirmed');

        return redirect()->back()
            ->with('success', '¡Tu cita ha sido confirmada con éxito! Pronto un asesor se pondrá en contacto contigo.');
    }

    public function conocenos()
    {
        $categorias = [
            'Casa',
            'Terrenos',
            'Terrenos Grandes',
            'Proyectos',      
            'Comerciales'
        ];

        $propiedades = Property::with('images')->get();

        return view('public-pages.conocenos', compact('categorias', 'propiedades'));
    }
}