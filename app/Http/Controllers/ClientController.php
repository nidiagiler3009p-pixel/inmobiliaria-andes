<?php

namespace App\Http\Controllers;
use App\Models\Tramite;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Property;
use App\Models\AppointmentTracking;
use Carbon\Carbon;

class ClientController extends Controller
{
    // Mostrar la lista de clientes en la Intranet
    public function index()
    {
        $clients = Client::with('user')->latest()->paginate(10);
        return view('intranet.clients.index', compact('clients'));
    }

    // Mostrar el formulario para registrar un nuevo cliente internamente
    public function create()
    {
        $asesores = User::where('role', 'Asesor')->get();
        return view('intranet.clients.create', compact('asesores'));
    }

    // Guardar el nuevo cliente en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
        ]);

        Client::create($request->all());

        return redirect()->route('clients.index')->with('success', '¡Cliente registrado con éxito en la Intranet!');
    }

    // Mostrar los detalles o perfil de un cliente específico
    public function show(Client $client)
    {
        $client->load(
    'appointmentTrackings',
    'schedules',
    'user',
    'prospect'
);
        return view('intranet.clients.show', compact('client'));
    }

    // Actualizar la información del cliente
public function update(Request $request, Client $client)
{
    $request->validate([
        'name' => 'required|string|max:255',

        'last_name' => 'required|string|max:255',

        'identification_card' =>
            'nullable|string|max:100',

        'phone' =>
            'required|string|max:20',

        'email' =>
            'nullable|email|max:255',

        'status' =>
            'required|in:Confirmada,Interesado,En Proceso,Cerrado Exitoso,Seguimiento Pendiente,Negociación,Vendida',

        'social_media_source' =>
            'nullable|string|max:255',

        'observations' =>
            'nullable|string|max:3000',
    ]);

    /*
    |--------------------------------------------------------------------------
    | DATOS TEMPORALES SI FALTAN CAMPOS OBLIGATORIOS
    |--------------------------------------------------------------------------
    */

    $email = trim((string) $request->email);

    if ($email === '') {
        $email = 'prospecto-' . ($client->prospect_id ?? $client->id) . '@pendiente.local';
    }

    $identification = trim(
        (string) $request->identification_card
    );

    if ($identification === '') {
        $identification =
            'PEND-' .
            str_pad(
                (string) ($client->prospect_id ?? $client->id),
                6,
                '0',
                STR_PAD_LEFT
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR CLIENTE
    |--------------------------------------------------------------------------
    */

    $client->update([
        'name' =>
            $request->name,

        'last_name' =>
            $request->last_name,

        'identification_card' =>
            $identification,

        'phone' =>
            $request->phone,

        'email' =>
            $email,

        'status' =>
            $request->status,

        'social_media_source' =>
            $request->social_media_source,

        'observations' =>
            $request->observations,
    ]);




    /*
    |--------------------------------------------------------------------------
    | VOLVER A LA FICHA
    |--------------------------------------------------------------------------
    */

$sourceType = $request->input('source_type');
$sourceId = $request->input('source_id');

$url = route('clients.show', [
    'client' => $client->id,
]);

if ($sourceType && $sourceId) {
    $url .= '?' . http_build_query([
        'source_type' => $sourceType,
        'source_id' => $sourceId,
    ]);
}

return redirect()
    ->to($url)
    ->with(
        'success',
        'Datos del cliente actualizados correctamente.'
    
    );
}    // Eliminar un cliente de la base de datos
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado de la base de datos.');
    }

public function edit(Client $client)
{
    return view(
        'intranet.clients.edit',
        compact('client')
    );
}
public function confirmReview(Request $request, Client $client)
{
    /*
    |--------------------------------------------------------------------------
    | 1. CONFIRMAR CLIENTE
    |--------------------------------------------------------------------------
    */

    if ($client->review_status !== 'Confirmado') {
        $client->review_status = 'Confirmado';
        $client->save();
    }

    /*
    |--------------------------------------------------------------------------
    | 2. RECIBIR ORIGEN EXACTO
    |--------------------------------------------------------------------------
    */

    $sourceType = $request->input('source_type');
    $sourceId = $request->input('source_id');

    /*
    |--------------------------------------------------------------------------
    | 3. SI VIENE DE UNA CITA
    |--------------------------------------------------------------------------
    */

    if ($sourceType === 'appointment' && $sourceId) {

        $cita = AppointmentTracking::where('id', $sourceId)
            ->where('client_id', $client->id)
            ->first();

        if ($cita) {

            if ($cita->status === 'Realizado') {
                $cita->status = 'Transferido';
                $cita->save();
            }

            return redirect()
                ->route('gestion.citas')
                ->with(
                    'success',
                    'Cliente confirmado correctamente. La cita fue transmutada a Clientes / Trámites.'
                );
        }
    }


/*
|--------------------------------------------------------------------------
| 4. TRÁMITE EXACTO DESDE CITAS INTEGRALES
|--------------------------------------------------------------------------
*/

if ($sourceType === 'tramite' && $sourceId) {

    $tramite = Tramite::where('id', $sourceId)
        ->where('prospect_id', $client->prospect_id)
        ->first();

    if ($tramite) {

        if ($tramite->status === 'Completado') {
            $tramite->status = 'Transferido';
            $tramite->save();
        }

        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'success',
                'Cliente confirmado correctamente. El trámite fue transmutado a Clientes.'
            );
    }
}

    /*
    |--------------------------------------------------------------------------
    | 4. CITAS INTEGRALES
    |--------------------------------------------------------------------------
    */

    $origin = strtolower(
        trim((string) $client->origin_module)
    );

    if (str_contains($origin, 'integral')) {
        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'success',
                'Cliente confirmado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | 5. OTROS ORÍGENES
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('clients.show', $client->id)
        ->with(
            'success',
            'Cliente confirmado correctamente.'
        );
}
    // Procesar el formulario público del catálogo, registrar el cliente, la cita y heredar el asesor de la propiedad
    public function sendMessageAndCreateAppointment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'property_id' => 'required|exists:properties,id',
            'channel' => 'nullable|string',
            'location_reference' => 'required|string',
            'appointment_date' => 'nullable|date',
        ]);

        // 1. Buscar la propiedad para heredar automáticamente su asesor asignado
        $property = Property::findOrFail($request->property_id);

        // 2. Buscar si el cliente ya existe por su teléfono, o crearlo con los enums exactos de la BD
        $client = Client::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => $request->name,
                'last_name' => '', // Valor por defecto si viene vacío del formulario rápido
                'email' => $request->email ?? ($request->phone . '@sin-correo.com'),
                'origin_module' => 'Contacto', // Coincide con los módulos permitidos en tu migración
                'status' => 'Interesado', // Coincide exactamente con el enum de la base de datos
            ]
        );

        // Si el cliente ya existía pero actualizó su nombre, lo sincronizamos
        if ($client->name !== $request->name) {
            $client->update(['name' => $request->name]);
        }
        /*
|--------------------------------------------------------------------------
| 3. BUSCAR O CREAR PROSPECTO
|--------------------------------------------------------------------------
*/

$prospectService = app(\App\Services\ProspectService::class);

$prospect = $prospectService->findOrCreate(
    $request->name,
    $client->last_name ?? '',
    $request->phone,
    $client->email,
    null,
    'Catálogo Público'
);

/*
|--------------------------------------------------------------------------
| VINCULAR CLIENTE CON PROSPECTO
|--------------------------------------------------------------------------
*/

if ($client->prospect_id !== $prospect->id) {
    $client->prospect_id = $prospect->id;
    $client->save();
}
        // 3. Evaluar automáticamente si hay fecha y hora (Confirmada o Pendiente)
        $hasDate = !empty($request->appointment_date);
        $appointmentStatus = $hasDate ? 'Confirmada' : 'Pendiente';

        // 4. Registrar la cita/seguimiento en la base de datos
        AppointmentTracking::create([
            'client_id' => $client->id,
            'prospect_id' => $prospect->id,
            'user_id' => $property->user_id ?? null, // Hereda el asesor encargado de esta propiedad
            'property_id' => $property->id,
            'client_name' => $client->name . ' ' . ($client->last_name ?? ''),
            'client_phone' => $client->phone ?? 'Sin teléfono',
            'channel' => $request->channel ?? 'Web',
            'location_reference' => $request->location_reference,
            'appointment_date' => $request->filled('appointment_date') ? $request->appointment_date : null,
            'status' => $appointmentStatus,
            'type' => 'visita', 
            'priority' => 'normal', 
            'registration_date' => now(),
            'is_notified' => false,
        ]);

// 5. Obtener el nombre del asesor asignado (si la propiedad tiene uno vinculado)
        $asesorNombre = ($property->user) ? $property->user->name . ' ' . $property->user->last_name : 'un asesor';

// 6. Construir los mensajes personalizados según tu requerimiento exacto
    if ($hasDate) {
        $formattedDate = Carbon::parse($request->appointment_date)->format('d/m/Y H:i');
        $mensajeExito = "¡Tu cita ha sido confirmada para el {$formattedDate}! Si necesitas modificarla, el asesor {$asesorNombre} se pondrá en contacto contigo. ✨ Tu sueño de tener casa propia en Inmobiliaria Los Andes está cada vez más cerca.";
    } else {
        $mensajeExito = "¡Gracias por elegirnos! Pronto un asesor se contactará contigo.";
    }

    // Redireccionamos de vuelta con el mensaje correspondiente
    return back()->with('success', $mensajeExito);
    }
}