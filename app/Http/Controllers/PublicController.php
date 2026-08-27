<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Contact;
use App\Models\AdvisoryRequest;
use App\Services\ProspectService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
  public function index()
{
    return redirect()->route('conocenos');
}

    public function conocenos() { return view('public-pages.conocenos'); }
    public function asesorias() { return view('public-pages.asesorias'); }
    public function tramites() { return view('public-pages.tramites'); }
    public function catalogo() { return view('public-pages.catalogo'); }
    public function contact() { return view('public-pages.contactanos'); }
    public function unete() { return view('public-pages.unete'); }

    public function storeContact(Request $request, ProspectService $prospectService) {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'general_address' => 'required|string|max:255',
            'requirements_message' => 'required|string',
        ]);

        $contact = DB::transaction(function () use ($request, $prospectService) {
            $prospect = $prospectService->findOrCreate(
                $request->name,
                $request->last_name,
                $request->phone,
                null,
                null,
                'Contáctanos'
            );
            $contact = Contact::create([
                'prospect_id' => $prospect->id,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'general_address' => $request->general_address,
                'requirements_message' => $request->requirements_message,
                'status' => 'Nuevo',
            ]);
            $prospectService->addHistory(
                $prospect,
                'Contacto recibido',
                'contact',
                $contact->id,
                null,
                'Nuevo',
                'El prospecto ingresó mediante el formulario público Contáctanos. Mensaje: ' . $request->requirements_message,
                null
            );
            return $contact;
        });

        try {
            $contenidoMensaje = "Has recibido un nuevo mensaje de contacto:\n\nNombre: {$contact->name} {$contact->last_name}\nTeléfono: {$contact->phone}\nDirección: {$contact->general_address}\nMensaje: {$contact->requirements_message}";
            Mail::raw($contenidoMensaje, function ($message) use ($contact) {
                $message->to('inmobiliarialosandesecuador@gmail.com')
                        ->subject('Nuevo mensaje de Contacto Web de: ' . $contact->name . ' ' . $contact->last_name);
            });
        } catch (\Exception $e) {
            // No detener el formulario si falla el correo.
        }

        return back()->with('success', '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.');
    }

public function storeAdvisory(Request $request, ProspectService $prospectService) {
    $request->validate(['name'=>'required|string|max:255','last_name'=>'required|string|max:255','phone'=>'required|string|max:20','email'=>'required|email|max:255','advisory_type'=>'required|string','message'=>'required|string']);
    DB::transaction(function () use ($request, $prospectService) {
        $prospect = $prospectService->findOrCreate($request->name, $request->last_name, $request->phone, $request->email, null, 'Asesoría Web');
        $advisory = AdvisoryRequest::create([
            'prospect_id' => $prospect->id,
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'advisory_type' => $request->advisory_type,
            'message' => $request->message,
            'status' => 'Nuevo'
        ]);
        $prospectService->addHistory(
            $prospect,
            'Solicitud de asesoría',
            'advisory',
            $advisory->id,
            null,
            'Nuevo',
            'El prospecto solicitó una asesoría desde la web. Tipo: ' . $request->advisory_type . '. Mensaje: ' . $request->message,
            null
        );
    });
    return back()->with('success', '¡Solicitud de asesoría registrada con éxito!');
}
}