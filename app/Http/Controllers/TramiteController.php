<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Client;
use App\Models\Property;
use App\Models\User;

class TramiteController extends Controller
{
    // ==========================================
    // 1. MÉTODOS PARA LA INTRANET (RESOURCE)
    // ==========================================

    // Listar todos los trámites activos
    public function indexPublic()
    {
        $tramites = Tramite::with(['client', 'user', 'property'])->latest()->paginate(10);
        return view('public-pages.tramites', compact('tramites'));
    }

    // Formulario para crear un nuevo trámite
    public function create()
    {
        $clients = Client::all();
        $properties = Property::all();
        $users = User::all();
        return view('intranet.tramites.create', compact('clients', 'properties', 'users'));
    }

    // Guardar trámite desde la intranet
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'process_type' => 'required|string|max:255',
            'status' => 'required|string',
            'estimated_completion_date' => 'nullable|date',
        ]);

        Tramite::create($request->all());

        return redirect()->route('tramites.index')->with('success', '¡Trámite registrado correctamente!');
    }

    // Ver detalles del trámite
    public function show(Tramite $tramite)
    {
        $tramite->load(['client', 'user', 'property']);
        return view('intranet.tramites.show', compact('tramite'));
    }

    // Actualizar estado del trámite
    public function update(Request $request, Tramite $tramite)
    {
        $request->validate([
            'status' => 'required|string',
            'process_type' => 'required|string|max:255',
        ]);

        $tramite->update($request->all());

        return redirect()->route('tramites.index')->with('success', '¡Trámite actualizado con éxito!');
    }

    // Eliminar trámite
    public function destroy(Tramite $tramite)
    {
        $tramite->delete();
        return redirect()->route('tramites.index')->with('success', 'Trámite eliminado del sistema.');
    }
public function storePublic(Request $request)
    {
        // 1. Validar los datos que envía el usuario desde la web pública
        $request->validate([
            'first_name'              => 'required|string|max:255',
            'last_name'               => 'required|string|max:255',
            'identification_card'     => 'required|string|max:20',
            'email'                   => 'required|email|max:255',
            'phone'                   => 'required|string|max:50',
            'location'                => 'required|string|max:255', // Coincide con el name="location" de tu vista
            'tramite_type'            => 'required|string',
            'message'                 => 'required|string|max:500',
            'contact_preference'      => 'required|string',
            'accepted_privacy_policy' => 'required',
        ]);

        // 2. Guardar en la Base de Datos mapeando 'location' a la columna 'ubicacion'
        $tramite = Tramite::create([
            'first_name'              => $request->first_name,
            'last_name'               => $request->last_name,
            'identification_card'     => $request->identification_card,
            'email'                   => $request->email,
            'phone'                   => $request->phone,
            'tramite_type'            => $request->tramite_type,
            'ubicacion'               => $request->location, // Aquí guardamos la ubicación ingresada
            'subject'                 => 'Solicitud web: ' . $request->tramite_type, // Valor automático para evitar errores de base de datos
            'message'                 => $request->message,
            'contact_preference'      => $request->contact_preference,
            'accepted_privacy_policy' => 1,
            'status'                  => 'Pendiente',
        ]);

        // 3. Crear el registro espejo en la tabla centralizadora
        \App\Models\AppointmentTracking::create([
            'user_id'           => 1,
            'type'              => 'tramite',
            'source_channel'    => 'Web - Trámites (' . $tramite->tramite_type . ')',
            'location_reference'=> 'Ubicación: ' . $tramite->ubicacion,
            'status'            => 'Pendiente',
            'priority'          => 'normal',
            'notes'             => $tramite->message,
        ]);

        // 4. Enviar el correo de notificación a la inmobiliaria
        $correosDestino = [
            'inmobilirialosandesecuador@gmail.com'
        ];

        try {
            Mail::raw("Se ha recibido una nueva solicitud de trámite desde la web:\n\n" .
                      "• Cliente: {$tramite->first_name} {$tramite->last_name}\n" .
                      "• Cédula: {$tramite->identification_card}\n" .
                      "• Correo: {$tramite->email}\n" .
                      "• Teléfono: {$tramite->phone}\n" .
                      "• Tipo de Trámite: {$tramite->tramite_type}\n" .
                      "• Ubicación: {$tramite->ubicacion}\n" .
                      "• Preferencia de contacto: {$tramite->contact_preference}\n\n" .
                      "Mensaje del cliente:\n{$tramite->message}", function ($message) use ($correosDestino, $tramite) {
                $message->to($correosDestino)
                        ->subject('Nueva solicitud de trámite: ' . $tramite->tramite_type);
            });
        } catch (\Exception $e) {
            // Evita que falle el flujo si hay un problema puntual con el servidor de correo
        }

        // 5. Redireccionar con mensaje de éxito
        return redirect()->back()->with('success', '¡Tu solicitud de trámite ha sido enviada con éxito!');
    }
}

