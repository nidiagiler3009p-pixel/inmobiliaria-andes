<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\Client;
use App\Models\Property;
use App\Models\User;
use App\Services\ProspectService;

class TramiteController extends Controller
{
    // ==========================================
    // 1. MÉTODOS PARA LA INTRANET
    // ==========================================

public function indexPublic() { $tramites = Tramite::with('prospect')->latest()->paginate(10); return view('public-pages.tramites', compact('tramites')); }

    public function create() {
        $clients = Client::all(); $properties = Property::all(); $users = User::all();
        return view('intranet.tramites.create', compact('clients','properties','users'));
    }

    public function store(Request $request) {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'process_type' => 'required|string|max:255',
            'status' => 'required|string',
            'estimated_completion_date' => 'nullable|date',
        ]);
        Tramite::create($request->all());
        return redirect()->route('tramites.index')->with('success', '¡Trámite registrado correctamente!');
    }

    public function show(Tramite $tramite) {
        $tramite->load(['client','user','property']);
        return view('intranet.tramites.show', compact('tramite'));
    }

    public function update(Request $request, Tramite $tramite) {
        $request->validate(['status' => 'required|string', 'process_type' => 'required|string|max:255']);
        $tramite->update($request->all());
        return redirect()->route('tramites.index')->with('success', '¡Trámite actualizado con éxito!');
    }

    public function destroy(Tramite $tramite) {
        $tramite->delete();
        return redirect()->route('tramites.index')->with('success', 'Trámite eliminado del sistema.');
    }

    // ==========================================
    // 2. SOLICITUD DE TRÁMITE DESDE WEB PÚBLICA
    // ==========================================

   public function storePublic(Request $request, ProspectService $prospectService) {
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'identification_card' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:50',
        'location' => 'required|string|max:255',
        'tramite_type' => 'required|string',
        'message' => 'required|string|max:500',
        'contact_preference' => 'required|string',
        'accepted_privacy_policy' => 'required|accepted',
    ]);
    $tramite = DB::transaction(function () use ($validated, $prospectService) {
        $prospect = $prospectService->findOrCreate($validated['first_name'], $validated['last_name'], $validated['phone'], $validated['email'], $validated['identification_card'], 'Trámites');
        $tramite = Tramite::create([
            'prospect_id' => $prospect->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'identification_card' => $validated['identification_card'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'tramite_type' => $validated['tramite_type'],
            'ubicacion' => $validated['location'],
            'subject' => 'Solicitud web: ' . $validated['tramite_type'],
            'message' => $validated['message'],
            'contact_preference' => $validated['contact_preference'],
            'accepted_privacy_policy' => 1,
            'status' => 'Pendiente',
        ]);
        $prospectService->addHistory($prospect, 'Solicitud de trámite', 'tramite', $tramite->id, null, 'Pendiente', 'El prospecto realizó una solicitud de trámite desde la web. Tipo: ' . $tramite->tramite_type . '. Ubicación: ' . $tramite->ubicacion . '. Mensaje: ' . $tramite->message, null);
        return $tramite;
    });
    try {
        $correosDestino = ['inmobilirialosandesecuador@gmail.com'];
        Mail::raw("Se ha recibido una nueva solicitud de trámite desde la web:\n\n" . "• Cliente: {$tramite->first_name} {$tramite->last_name}\n" . "• Cédula: {$tramite->identification_card}\n" . "• Correo: {$tramite->email}\n" . "• Teléfono: {$tramite->phone}\n" . "• Tipo de Trámite: {$tramite->tramite_type}\n" . "• Ubicación: {$tramite->ubicacion}\n" . "• Preferencia de contacto: {$tramite->contact_preference}\n\n" . "Mensaje:\n{$tramite->message}", function ($message) use ($correosDestino, $tramite) { $message->to($correosDestino)->subject('Nueva solicitud de trámite: ' . $tramite->tramite_type); });
    } catch (\Throwable $e) { Log::error('Error al enviar correo de trámite: ' . $e->getMessage()); }
    return redirect()->back()->with('success', '¡Tu solicitud de trámite ha sido enviada con éxito!');
}
}