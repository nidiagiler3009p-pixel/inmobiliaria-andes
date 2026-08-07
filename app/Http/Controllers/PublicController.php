<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Contact;
use App\Models\AdvisoryRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Mail; 

class PublicController extends Controller
{
    public function index()
    {
        $properties = Property::where('status', 'Disponible')->latest()->take(6)->get();
        return view('welcome', compact('properties'));
    }

    // --- VISTAS PÚBLICAS ---

    public function conocenos()
    {
        return view('public-pages.conocenos');
    }

    public function asesorias()
    {
        return view('public-pages.asesorias');
    }

    public function tramites()
    {
        return view('public-pages.tramites');
    }

    public function catalogo()
    {
        return view('public-pages.catalogo');
    }

    public function contact()
    {
        return view('public-pages.contactanos');
    }

    public function unete()
    {
        return view('public-pages.unete');
    }

    // --- MÉTODOS PARA GUARDAR DATOS ---

    // Guardar el mensaje del formulario de contacto público
    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'general_address' => 'required|string|max:255',
            'requirements_message' => 'required|string',
        ]);

        // 1. Guardar en la base de datos
        $contact = Contact::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'general_address' => $request->general_address,
            'requirements_message' => $request->requirements_message,
            'status' => 'Nuevo',
        ]);
\App\Models\AppointmentTracking::create([
    'user_id'           => 1,
    'type'              => 'contacto',
    'source_channel'    => 'Web - Contáctanos',
    'location_reference'=> $request->general_address,
    'status'            => 'Pendiente',
    'priority'          => 'normal',
    'notes'             => 'Mensaje: ' . $request->requirements_message,
]);


        // 2. Enviar el correo directamente usando la configuración del .env
        try {
            $contenidoMensaje = "Has recibido un nuevo mensaje de contacto:\n\n" .
                                "Nombre: {$contact->name} {$contact->last_name}\n" .
                                "Teléfono: {$contact->phone}\n" .
                                "Dirección: {$contact->general_address}\n" .
                                "Mensaje: {$contact->requirements_message}";

            Mail::raw($contenidoMensaje, function ($message) use ($contact) {
                $message->to('inmobiliarialosandesecuador@gmail.com')
                        ->subject('Nuevo mensaje de Contacto Web de: ' . $contact->name . ' ' . $contact->last_name);
            });
        } catch (\Exception $e) {
            // Si falla el servidor de correo, no detiene la página pero puedes depurarlo si lo necesitas
        }

        return back()->with('success', '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.');
    }

    // Guardar la solicitud de asesoría
    public function storeAdvisory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'advisory_type' => 'required|string',
            'message' => 'required|string',
        ]);

        AdvisoryRequest::create($request->all());

        Client::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'origin_module' => 'Asesoría Web',
            'status' => 'Interesado',
            'observations' => $request->message,
        ]);

        return back()->with('success', '¡Solicitud de asesoría registrada con éxito!');
    }
}