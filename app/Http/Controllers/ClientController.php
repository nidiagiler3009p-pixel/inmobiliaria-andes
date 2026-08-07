<?php

namespace App\Http\Controllers;

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
        $client->load('appointmentTrackings', 'schedules', 'user');
        return view('intranet.clients.show', compact('client'));
    }

    // Actualizar la información del cliente
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|string',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', '¡Datos del cliente actualizados correctamente!');
    }

    // Eliminar un cliente de la base de datos
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado de la base de datos.');
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
        
        // 3. Evaluar automáticamente si hay fecha y hora (Confirmada o Pendiente)
        $hasDate = !empty($request->appointment_date);
        $appointmentStatus = $hasDate ? 'Confirmada' : 'Pendiente';

        // 4. Registrar la cita/seguimiento en la base de datos
        AppointmentTracking::create([
            'client_id' => $client->id,
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