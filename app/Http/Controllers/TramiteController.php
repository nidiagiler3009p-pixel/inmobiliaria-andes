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
use App\Models\ClientPortfolioEntry;
use App\Models\AccountingTransaction;

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

    public function iniciar(Tramite $tramite)
{
    if ($tramite->status !== 'Pendiente') {
        return redirect()
            ->route('clients.index')
            ->with('error', 'Solo los trámites pendientes pueden iniciarse.');
    }

    $tramite->status = 'En Proceso';
    $tramite->save();

    return redirect()
        ->route('clients.index')
        ->with('success', 'El trámite #' . $tramite->id . ' fue iniciado correctamente.');
}
public function finalizarConExito(
    Tramite $tramite,
    ProspectService $prospectService
) {
    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESTADO DEL TRÁMITE
    |--------------------------------------------------------------------------
    */

    if ($tramite->status !== 'En Proceso') {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'Solo los trámites que están En Proceso pueden finalizarse con éxito.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR PROSPECTO
    |--------------------------------------------------------------------------
    */

    if (empty($tramite->prospect_id)) {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'Este trámite no está relacionado con un prospecto.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EVITAR DUPLICAR LA OPERACIÓN CONTABLE
    |--------------------------------------------------------------------------
    */

    $alreadyExists = AccountingTransaction::where(
        'tramite_id',
        $tramite->id
    )->exists();

    if ($alreadyExists) {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'Este trámite ya fue enviado anteriormente a Contabilidad.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR TRÁMITE Y ENVIAR A CONTABILIDAD
    |--------------------------------------------------------------------------
    */

    return DB::transaction(function () use (
        $tramite,
        $prospectService
    ) {
        $previousStatus = $tramite->status;

        /*
        |--------------------------------------------------------------------------
        | LOCALIZAR CLIENTE FORMAL
        |--------------------------------------------------------------------------
        */

        $client = Client::where(
            'prospect_id',
            $tramite->prospect_id
        )->first();

        /*
        |--------------------------------------------------------------------------
        | CREAR OPERACIÓN CONTABLE
        |--------------------------------------------------------------------------
        |
        | Los valores económicos negociables NO se calculan aquí.
        | Contabilidad los establecerá posteriormente.
        |
        */

        $accountingTransaction = AccountingTransaction::create([
            'client_id' => $client?->id,
            'prospect_id' => $tramite->prospect_id,
            'tramite_id' => $tramite->id,
            'property_id' => null,

            'operation_type' => 'Trámite / Servicio',

            'description' =>
                'Operación generada desde el trámite #' . $tramite->id,

            'published_price' => null,
            'closing_price' => null,
            'brokerage_percentage' => null,
            'brokerage_amount' => null,
            'service_amount' => null,

            'status' => 'Pendiente',

            'origin_module' => 'Clientes / Trámites',
            'source_type' => 'tramite',
            'source_id' => $tramite->id,

            'notes' =>
                'Pendiente de revisión y valoración económica por Contabilidad.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CERRAR TRÁMITE
        |--------------------------------------------------------------------------
        */

        $tramite->status = 'Completado';
        $tramite->save();

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CLIENTE
        |--------------------------------------------------------------------------
        */

        if ($client) {
            $client->status = 'Cerrado Exitoso';
            $client->save();
        }

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR HISTORIAL DEL PROSPECTO
        |--------------------------------------------------------------------------
        */

        $prospect = $tramite->prospect;

        if ($prospect) {
            $prospectService->addHistory(
                $prospect,
                'Trámite finalizado con éxito',
                'tramite',
                $tramite->id,
                $previousStatus,
                'Completado',
                'El trámite fue completado con éxito y enviado a Contabilidad como operación pendiente.',
                auth()->id()
            );
        }

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'El trámite fue completado con éxito y enviado correctamente a Contabilidad.'
            );
    });
}
public function finalizarSinExito(
    Request $request,
    Tramite $tramite,
    ProspectService $prospectService
) {
    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESTADO
    |--------------------------------------------------------------------------
    */

    if ($tramite->status !== 'En Proceso') {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'Solo los trámites que están En Proceso pueden finalizarse sin éxito.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR PROSPECTO
    |--------------------------------------------------------------------------
    */

    if (empty($tramite->prospect_id)) {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'Este trámite no está relacionado con un prospecto.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR MOTIVO
    |--------------------------------------------------------------------------
    */

    $request->validate([
        'entry_reason' => 'required|string|max:2000',
    ]);


    /*
    |--------------------------------------------------------------------------
    | FINALIZAR SIN ÉXITO Y ENVIAR A CARTERA
    |--------------------------------------------------------------------------
    */

    return DB::transaction(function () use (
        $request,
        $tramite,
        $prospectService
    ) {

        $previousStatus = $tramite->status;


        /*
        |--------------------------------------------------------------------------
        | LOCALIZAR CLIENTE
        |--------------------------------------------------------------------------
        */

        $client = Client::where(
            'prospect_id',
            $tramite->prospect_id
        )->first();


        /*
        |--------------------------------------------------------------------------
        | EVITAR DUPLICAR EN CARTERA
        |--------------------------------------------------------------------------
        */

        $portfolioEntry = ClientPortfolioEntry::where(
                'prospect_id',
                $tramite->prospect_id
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | CREAR REGISTRO EN CARTERA
        |--------------------------------------------------------------------------
        */

        if (!$portfolioEntry) {

            ClientPortfolioEntry::create([

                'client_id' =>
                    $client?->id,

                'prospect_id' =>
                    $tramite->prospect_id,

                'appointment_id' =>
                    null,

                'source_type' =>
                    'tramite',

                'source_record_id' =>
                    $tramite->id,

                'previous_status' =>
                    $previousStatus,

                'prospect_name' =>
                    $tramite->first_name
                    ?? $client?->name
                    ?? '',

                'prospect_last_name' =>
                    $tramite->last_name
                    ?? $client?->last_name
                    ?? '',

                'prospect_phone' =>
                    $tramite->phone
                    ?? $client?->phone
                    ?? '',

                'prospect_email' =>
                    $tramite->email
                    ?? $client?->email,

                'property_id' =>
                    null,

                'advisor_id' =>
                    auth()->id(),

                'entry_source' =>
                    'Clientes / Trámites - Sin Éxito',

                'contact_channel' =>
                    $tramite->contact_preference,

                'social_platform' =>
                    null,

                'social_profile_url' =>
                    null,

                'entry_reason' =>
                    $request->entry_reason,

                'portfolio_status' =>
                    'Seguimiento',

                'notes' =>
                    'Trámite finalizado sin éxito y enviado a Cartera.',

                'entered_at' =>
                    now(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | MARCAR TRÁMITE SIN ÉXITO
        |--------------------------------------------------------------------------
        */

        $tramite->status = 'Sin Éxito';
        $tramite->save();


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CLIENTE
        |--------------------------------------------------------------------------
        */

        if ($client) {
            $client->status = 'Seguimiento Pendiente';
            $client->save();
        }


        /*
        |--------------------------------------------------------------------------
        | REGISTRAR HISTORIAL
        |--------------------------------------------------------------------------
        */

        $prospect = $tramite->prospect;

        if ($prospect) {

            $prospectService->addHistory(
                $prospect,
                'Trámite finalizado sin éxito',
                'tramite',
                $tramite->id,
                $previousStatus,
                'Sin Éxito',
                'El trámite fue finalizado sin éxito y enviado a Cartera. Motivo: '
                    . $request->entry_reason,
                auth()->id()
            );
        }


        /*
        |--------------------------------------------------------------------------
        | IR A CARTERA
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.cartera')
            ->with(
                'success',
                'El trámite finalizó sin éxito y el cliente fue enviado correctamente a Cartera.'
            );
    });
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