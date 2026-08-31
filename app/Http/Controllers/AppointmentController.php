<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AppointmentTracking;
use App\Models\ClientPortfolioEntry;
use App\Models\User;
use App\Models\Client;
use App\Models\Contact;
use App\Models\AdvisoryRequest;
use App\Models\Tramite;
use App\Models\Property;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\ProspectService;
use Illuminate\Support\Facades\Log;
use App\Models\Prospect;
use App\Models\ProspectContact;
use App\Models\ProspectAlias;
use App\Models\ClientTramite;

class AppointmentController extends Controller
{
public function create()
{
    return view('intranet.users.create');
}



    public function index() {
        $appointments = AppointmentTracking::with(['client','user','property'])->where('user_id', auth()->id())->whereNotIn('status', ['eliminado','Transferido'])->latest()->get();
        $asesores = User::all(); $clientes = Client::all();
        return view('intranet.users.agenda', compact('appointments','asesores','clientes'));
    }

public function gestionCitas(Request $request) {
    $query = AppointmentTracking::with(['client','prospect','user','property','portfolioEntry'])
        ->whereNotIn('status', ['eliminado','Transferido'])
        ->whereNotIn('type', ['asesoria','tramite','contacto']);

    if ($request->filled('advisor_id')) $query->where('user_id', $request->advisor_id);
    if ($request->filled('property_id')) $query->where('property_id', $request->property_id);
    if ($request->filled('channel')) $query->where('source_channel', $request->channel);
    if ($request->filled('priority')) $query->where('priority', $request->priority);
    if ($request->filled('status')) $query->where('status', $request->status);
    if ($request->filled('desde')) $query->whereDate('appointment_date', '>=', $request->desde);
    if ($request->filled('hasta')) $query->whereDate('appointment_date', '<=', $request->hasta);

    $appointments = $query->latest()->get();
    $asesores = User::all();
    $clientes = Client::all();
    $propiedades = Property::select('id','title')->get();

    return view('intranet.users.gestion-citas', compact('appointments','asesores','clientes','propiedades'));
}

public function storeManual(
    Request $request,
    ProspectService $prospectService
) {
    Log::info('ENTRO A STORE MANUAL', $request->all());

    $request->validate([
        'client_name' => 'required|string|max:255',
        'client_last_name' => 'nullable|string|max:255',
        'client_phone' => 'required|string|max:255',
        'client_email' => 'nullable|email|max:255',
        'user_id' => 'required|exists:users,id',
        'property_id' => 'nullable|exists:properties,id',
        'type' => 'required|string|max:100',
        'location_reference' => 'required|string|max:255',
        'priority' => 'required|string|max:50',
        'status' => 'nullable|string|max:50',
        'appointment_date' => 'nullable|date',
        'notes' => 'nullable|string',
        'source_channel' => 'nullable|string|max:100',
    ]);

    return DB::transaction(function () use (
        $request,
        $prospectService
    ) {
        $prospect = $prospectService->findOrCreate(
            $request->client_name,
            $request->client_last_name,
            $request->client_phone,
            $request->client_email,
            null,
            'Gestión de Citas'
        );

        $cita = AppointmentTracking::create([
            'client_id' => null,
            'prospect_id' => $prospect->id,
            'user_id' => $request->user_id,
            'property_id' => $request->property_id,
            'registration_date' => now(),
            'appointment_date' => $request->appointment_date,
            'is_notified' => false,
            'location_reference' => $request->location_reference,
            'status' => $request->status ?? 'Pendiente',
            'type' => $request->type,
            'priority' => $request->priority,
            'source_channel' => $request->source_channel ?? 'Directo',
            'notes' => $request->notes,
        ]);

        Log::info('CITA CREADA EN APPOINTMENTS_TRACKING', [
            'id' => $cita->id,
            'client_id' => $cita->client_id,
            'prospect_id' => $cita->prospect_id,
            'type' => $cita->type,
        ]);

        $descripcion = 'Se registró una nueva cita.';

        if ($cita->appointment_date) {
            $descripcion .=
                ' Fecha: '
                . $cita->appointment_date->format('d/m/Y H:i')
                . '.';
        }

        if ($cita->location_reference) {
            $descripcion .=
                ' Lugar: '
                . $cita->location_reference
                . '.';
        }

        if ($cita->notes) {
            $descripcion .=
                ' Observaciones: '
                . $cita->notes;
        }

        $prospectService->addHistory(
            $prospect,
            'Cita registrada',
            'appointment',
            $cita->id,
            null,
            $cita->status,
            $descripcion,
            $cita->user_id
        );

        return redirect()
            ->route('gestion.citas')
            ->with(
                'success',
                '¡Cita registrada exitosamente!'
            );
    });

}


public function cambiarEstado(
    Request $request,
    $id,
    ProspectService $prospectService
) {
    $cita = AppointmentTracking::with([
        'client',
        'prospect',
        'property',
        'user',
        'portfolioEntry'
    ])->find($id);

    if (!$cita) {
        return redirect()
            ->route('gestion.citas')
            ->with(
                'error',
                'La cita solicitada no existe.'
            );
    }


    $request->validate([
        'status' => 'required|string|in:Pendiente,Agendado,Confirmada,Cancelado,Realizado',

        'cancellation_reason' =>
            'required_if:status,Cancelado|nullable|string|max:500',

        'rescue_to_portfolio' =>
            'nullable|boolean',
    ]);


    $estadoAnterior = $cita->status;


    /*
    |--------------------------------------------------------------------------
    | VALIDACIONES DE FLUJO
    |--------------------------------------------------------------------------
    */

    if (
        in_array(
            $request->status,
            ['Agendado', 'Confirmada', 'Realizado']
        ) &&
        empty($cita->appointment_date)
    ) {
        return redirect()
            ->route('gestion.citas')
            ->with(
                'error',
                'Para cambiar la cita a este estado primero debe asignar una fecha y hora.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCELACIÓN
    |--------------------------------------------------------------------------
    */

    if ($request->status === 'Cancelado') {

        return DB::transaction(function () use (
            $request,
            $cita,
            $estadoAnterior,
            $prospectService
        ) {

            $rescatarCartera =
                $request->boolean(
                    'rescue_to_portfolio'
                );


            /*
            |--------------------------------------------------------------------------
            | DATOS DE LA PERSONA
            |--------------------------------------------------------------------------
            */

            $persona =
                $cita->prospect ??
                $cita->client;


            /*
            |--------------------------------------------------------------------------
            | SI DEBE IR A CARTERA
            |--------------------------------------------------------------------------
            */

            if ($rescatarCartera) {

                if (!$persona) {
                    return redirect()
                        ->route('gestion.citas')
                        ->with(
                            'error',
                            'La cita no tiene información suficiente del prospecto para enviarlo a Cartera.'
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | EVITAR DUPLICADOS EN CARTERA
                |--------------------------------------------------------------------------
                */

                $yaExisteEnCartera =
                    ClientPortfolioEntry::where(
                        'source_type',
                        'appointment'
                    )
                    ->where(
                        'source_record_id',
                        $cita->id
                    )
                    ->exists();


                if (!$yaExisteEnCartera) {

                    $portfolioEntry =
                        ClientPortfolioEntry::create([

                            'client_id' =>
                                $cita->client_id,

                            'prospect_id' =>
                                $cita->prospect_id,

                            'appointment_id' =>
                                $cita->id,

                            'source_type' =>
                                'appointment',

                            'source_record_id' =>
                                $cita->id,

                            /*
                            |--------------------------------------------------------------------------
                            | IMPORTANTE
                            |--------------------------------------------------------------------------
                            | Guardamos Cancelado porque ese es el estado desde
                            | el cual está entrando a Cartera.
                            */

                            'previous_status' =>
                                'Cancelado',

                            'prospect_name' =>
                                $persona->first_name
                                ?? $persona->name
                                ?? '',

                            'prospect_last_name' =>
                                $persona->last_name
                                ?? '',

                            'prospect_phone' =>
                                $persona->phone
                                ?? '',

                            'prospect_email' =>
                                $persona->email
                                ?? null,

                            'property_id' =>
                                $cita->property_id,

                            'advisor_id' =>
                                $cita->user_id,

                            'entry_source' =>
                                'Gestión de Citas',

                            'contact_channel' =>
                                $cita->source_channel
                                ?? null,

                            'social_platform' =>
                                null,

                            'social_profile_url' =>
                                null,

                            'entry_reason' =>
                                $request->cancellation_reason,

                            'portfolio_status' =>
                                'Seguimiento',

                            'notes' =>
                                'Prospecto rescatado automáticamente desde una cita cancelada.',

                            'entered_at' =>
                                now(),
                        ]);


                    /*
                    |--------------------------------------------------------------------------
                    | HISTORIAL DEL ENVÍO A CARTERA
                    |--------------------------------------------------------------------------
                    */

                    if ($cita->prospect) {

                        $prospectService->addHistory(
                            $cita->prospect,
                            'Transferido a Cartera',
                            'portfolio',
                            $portfolioEntry->id,
                            'Cancelado',
                            'Transferido',
                            'La cita cancelada fue rescatada y transferida a Cartera. Motivo: '
                                . $request->cancellation_reason,
                            auth()->id()
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | LA CITA QUEDA TRANSFERIDA
                |--------------------------------------------------------------------------
                */

                $cita->status =
                    'Transferido';

                $cita->cancellation_reason =
                    $request->cancellation_reason;

                $cita->cancelled_at =
                    now();

                $cita->rescued_to_portfolio =
                    true;

                $cita->save();


                /*
                |--------------------------------------------------------------------------
                | HISTORIAL DE CANCELACIÓN
                |--------------------------------------------------------------------------
                */

                if ($cita->prospect) {

                    $prospectService->addHistory(
                        $cita->prospect,
                        'Cita cancelada',
                        'appointment',
                        $cita->id,
                        $estadoAnterior,
                        'Cancelado',
                        'La cita fue cancelada. Motivo: '
                            . $request->cancellation_reason,
                        auth()->id()
                    );
                }


                return redirect()
                    ->route('admin.cartera')
                    ->with(
                        'success',
                        'La cita fue cancelada y el prospecto fue trasladado correctamente a Cartera.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | CANCELACIÓN SIN ENVIAR A CARTERA
            |--------------------------------------------------------------------------
            */

            $cita->status =
                'Cancelado';

            $cita->cancellation_reason =
                $request->cancellation_reason;

            $cita->cancelled_at =
                now();

            $cita->rescued_to_portfolio =
                false;

            $cita->save();


            /*
            |--------------------------------------------------------------------------
            | HISTORIAL
            |--------------------------------------------------------------------------
            */

            if ($cita->prospect) {

                $prospectService->addHistory(
                    $cita->prospect,
                    'Cita cancelada',
                    'appointment',
                    $cita->id,
                    $estadoAnterior,
                    'Cancelado',
                    'La cita fue cancelada. Motivo: '
                        . $request->cancellation_reason,
                    auth()->id()
                );
            }


            return redirect()
                ->route('gestion.citas')
                ->with(
                    'success',
                    'La cita fue cancelada correctamente.'
                );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIO NORMAL DE ESTADO
    |--------------------------------------------------------------------------
    */

    return DB::transaction(function () use (
        $request,
        $cita,
        $estadoAnterior,
        $prospectService
    ) {

        $cita->status =
            $request->status;

        $cita->cancellation_reason =
            null;

        $cita->cancelled_at =
            null;

        $cita->rescued_to_portfolio =
            false;

        $cita->save();


        /*
        |--------------------------------------------------------------------------
        | HISTORIAL
        |--------------------------------------------------------------------------
        */

        if ($cita->prospect) {

            $prospectService->addHistory(
                $cita->prospect,
                'Cambio de estado de cita',
                'appointment',
                $cita->id,
                $estadoAnterior,
                $cita->status,
                'La cita cambió de estado de '
                    . $estadoAnterior
                    . ' a '
                    . $cita->status
                    . '.',
                auth()->id()
            );
        }


        return redirect()
            ->route('gestion.citas')
            ->with(
                'success',
                'El estado de la cita ha sido actualizado correctamente.'
            );
    });
}    public function integrales(Request $request) {
        $filtro = $request->get('filtro');
        $statusFiltro = $request->get('status');
        $collection = collect();
        if (!$filtro || $filtro === 'todos' || $filtro === 'contacto') {
            $query = Contact::query()->whereNotIn('status', ['eliminado','Transferido']);
            if ($statusFiltro) $query->where('status', $statusFiltro);
            $contactos = $query->get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = 'contact_' . $item->id;
                $obj->source_type = 'contact';
                $obj->source_record_id = $item->id;
                $obj->origen_canal = 'Contáctanos';
                $obj->nombre_cliente = trim(($item->name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'Sin nombre';
                $obj->telefono_cliente = $item->phone ?? 'N/A';
                $obj->detalle_ubicacion = $item->general_address ?? 'N/A';
                $obj->detalle_nota = $item->requirements_message ?? 'Sin detalles';
                $obj->status = $item->status ?? 'Pendiente';
                $obj->fecha_registro = $item->created_at;
                return $obj;
            });
            $collection = $collection->concat($contactos);
        }
        if (!$filtro || $filtro === 'todos' || $filtro === 'asesoria') {
            $query = AdvisoryRequest::query()->whereNotIn('status', ['eliminado','Transferido']);
            if ($statusFiltro) $query->where('status', $statusFiltro);
            $asesorias = $query->get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = 'advisory_' . $item->id;
                $obj->source_type = 'advisory';
                $obj->source_record_id = $item->id;
                $obj->origen_canal = 'Asesoría (' . ($item->plan_type ?? 'General') . ')';
                $obj->nombre_cliente = $item->full_name ?? 'Sin nombre';
                $obj->telefono_cliente = $item->phone ?? 'N/A';
                $obj->detalle_ubicacion = $item->ciudad ?? 'N/A';
                $obj->detalle_nota = $item->property_details ?? ($item->preferences_notes ?? 'Sin detalles');
                $obj->status = $item->status ?? 'Pendiente';
                $obj->fecha_registro = $item->created_at;
                return $obj;
            });
            $collection = $collection->concat($asesorias);
        }
       if (!$filtro || $filtro === 'todos' || $filtro === 'tramite') {
    $query = Tramite::query()
        ->whereNotIn('status', ['eliminado','Transferido'])
        ->where(function ($q) {
            $q->whereNull('subject')
              ->orWhere('subject', 'not like', 'Trámite generado desde Cita #%');
        });
            if ($statusFiltro) $query->where('status', $statusFiltro);
            $tramites = $query->get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = 'tramite_' . $item->id;
                $obj->source_type = 'tramite';
                $obj->source_record_id = $item->id;
                $obj->origen_canal = 'Trámite (' . ($item->tramite_type ?? 'General') . ')';
                $obj->nombre_cliente = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'Sin nombre';
                $obj->telefono_cliente = $item->phone ?? 'N/A';
                $obj->detalle_ubicacion = $item->ubicacion ?? 'N/A';
                $obj->detalle_nota = $item->message ?? 'Sin detalles';
                $obj->status = $item->status ?? 'Pendiente';
                $obj->fecha_registro = $item->created_at;
                return $obj;
            });
            $collection = $collection->concat($tramites);
        }
        $appointments = $collection->sortByDesc('fecha_registro');
        $recicladosCollection = collect();
        if (Schema::hasColumn('contacts', 'status')) {
            $contactosTrash = Contact::where('status', 'eliminado')->get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = 'contact_' . $item->id;
                $obj->origen = 'Contáctanos';
                $obj->cliente = trim(($item->name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'Sin nombre';
                $obj->telefono = $item->phone ?? 'N/A';
                $obj->referencia = $item->general_address ?? 'N/A';
                $obj->deleted_at = $item->updated_at;
                return $obj;
            });
            $recicladosCollection = $recicladosCollection->concat($contactosTrash);
        }
        if (Schema::hasColumn('advisory_requests', 'status')) {
            $asesoriasTrash = AdvisoryRequest::where('status', 'eliminado')->get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = 'advisory_' . $item->id;
                $obj->origen = 'Asesorías';
                $obj->cliente = $item->full_name ?? 'Sin nombre';
                $obj->telefono = $item->phone ?? 'N/A';
                $obj->referencia = $item->ciudad ?? 'N/A';
                $obj->deleted_at = $item->updated_at;
                return $obj;
            });
            $recicladosCollection = $recicladosCollection->concat($asesoriasTrash);
        }
        if (Schema::hasColumn('tramites', 'status')) {
            $tramitesTrash = Tramite::where('status', 'eliminado')->get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = 'tramite_' . $item->id;
                $obj->origen = 'Trámite';
                $obj->cliente = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'Sin nombre';
                $obj->telefono = $item->phone ?? 'N/A';
                $obj->referencia = $item->ubicacion ?? 'N/A';
                $obj->deleted_at = $item->updated_at;
                return $obj;
            });
            $recicladosCollection = $recicladosCollection->concat($tramitesTrash);
        }
        $citasRecicladas = $recicladosCollection->sortByDesc('deleted_at');
        $asesores = User::all(); $clientes = Client::all();
        return view('intranet.users.integrales', compact('appointments','asesores','clientes','citasRecicladas'));
    }

    public function moveToPortfolio(Request $request, $id, ProspectService $prospectService) {
    $cita = AppointmentTracking::with(['client','prospect','user','property','portfolioEntry'])->find($id);
    if (!$cita) return redirect()->route('gestion.citas')->with('error', 'La cita seleccionada no existe.');

    $persona = $cita->prospect ?? $cita->client;
    if (!$persona) {
        return redirect()->route('gestion.citas')->with('error', 'La cita no tiene datos suficientes de la persona para enviarla a Cartera.');
    }

    $alreadyExists = ClientPortfolioEntry::where('source_type', 'appointment')->where('source_record_id', $cita->id)->exists();
    if ($alreadyExists) {
        return redirect()->route('gestion.citas')->with('error', 'Esta cita ya se encuentra registrada en Cartera.');
    }

    $request->validate([
        'entry_reason' => 'required|string|max:2000',
        'contact_channel' => 'nullable|string|max:100',
        'social_platform' => 'nullable|string|max:50',
        'social_profile_url' => 'nullable|url|max:500',
        'portfolio_status' => 'required|in:Nuevo,Contactado,Seguimiento,Interesado,Negociación,Cliente Potencial',
        'notes' => 'nullable|string|max:3000'
    ]);

    return DB::transaction(function () use ($request, $cita, $persona, $prospectService) {
        $previousStatus = $cita->status;

        $portfolioEntry = ClientPortfolioEntry::create([
            'client_id' => $cita->client_id,
            'prospect_id' => $cita->prospect_id,
            'appointment_id' => $cita->id,
            'source_type' => 'appointment',
            'source_record_id' => $cita->id,
            'previous_status' => $previousStatus,
            'prospect_name' => $persona->first_name ?? $persona->name ?? '',
            'prospect_last_name' => $persona->last_name ?? '',
            'prospect_phone' => $persona->phone ?? '',
            'prospect_email' => $persona->email ?? null,
            'property_id' => $cita->property_id,
            'advisor_id' => $cita->user_id,
            'entry_source' => 'Gestión de Citas',
            'contact_channel' => $request->contact_channel ?: $cita->source_channel,
            'social_platform' => $request->social_platform,
            'social_profile_url' => $request->social_profile_url,
            'entry_reason' => $request->entry_reason,
            'portfolio_status' => $request->portfolio_status,
            'notes' => $request->notes,
            'entered_at' => now()
        ]);

        $cita->update(['status' => 'Transferido', 'rescued_to_portfolio' => true]);

        if ($cita->prospect) {
            $prospectService->addHistory($cita->prospect, 'Transferido a Cartera', 'portfolio', $portfolioEntry->id, $previousStatus, 'Transferido', 'La cita fue transferida a Cartera. Motivo: ' . $request->entry_reason, auth()->id());
        }

        return redirect()->route('gestion.citas')->with('success', 'El registro fue transferido correctamente a Cartera.');
    });
}


public function cartera(Request $request) {
    $query = ClientPortfolioEntry::with(['client','prospect','property','advisor']);
    if ($request->filled('status')) $query->where('portfolio_status', $request->status);
    if ($request->filled('source_type')) $query->where('source_type', $request->source_type);
    if ($request->filled('advisor_id')) $query->where('advisor_id', $request->advisor_id);
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function ($q) use ($search) {
            $q->where('prospect_name', 'LIKE', "%{$search}%")
              ->orWhere('prospect_last_name', 'LIKE', "%{$search}%")
              ->orWhere('prospect_phone', 'LIKE', "%{$search}%")
              ->orWhere('prospect_email', 'LIKE', "%{$search}%");
        });
    }
    $portfolioEntries = $query->latest('entered_at')->get();
    $asesores = User::all();
    return view('intranet.users.cartera', compact('portfolioEntries','asesores'));
}
public function moveIntegralToPortfolio(Request $request, $tipo, $id, ProspectService $prospectService) {
    $tipo = strtolower(trim($tipo));
    if ($tipo === 'contact' || $tipo === 'contacto') { $item = Contact::find($id); $sourceType = 'contact'; }
    elseif ($tipo === 'advisory' || $tipo === 'asesoria') { $item = AdvisoryRequest::find($id); $sourceType = 'advisory'; }
    elseif ($tipo === 'tramite') { $item = Tramite::find($id); $sourceType = 'tramite'; }
    else { return redirect()->route('admin.citas-totales')->with('error', 'El tipo de registro no es válido.'); }
    if (!$item) return redirect()->route('admin.citas-totales')->with('error', 'El registro seleccionado no existe.');
    if (empty($item->prospect_id)) return redirect()->route('admin.citas-totales')->with('error', 'Este registro todavía no está relacionado con un prospecto.');
    $alreadyExists = ClientPortfolioEntry::where('source_type', $sourceType)->where('source_record_id', $item->id)->exists();
    if ($alreadyExists) return redirect()->route('admin.citas-totales')->with('error', 'Este registro ya fue enviado a Cartera.');
    $request->validate([
        'entry_reason' => 'required|string|max:2000',
        'contact_channel' => 'nullable|string|max:100',
        'social_platform' => 'nullable|string|max:50',
        'social_profile_url' => 'nullable|url|max:500',
        'portfolio_status' => 'required|in:Nuevo,Contactado,Seguimiento,Interesado,Negociación,Cliente Potencial',
        'notes' => 'nullable|string|max:3000',
    ]);
    return DB::transaction(function () use ($request, $item, $sourceType, $prospectService) {
        $previousStatus = $item->status ?? 'Pendiente';
        if ($sourceType === 'contact') { $nombre = $item->name ?? ''; $apellido = $item->last_name ?? ''; $telefono = $item->phone ?? ''; $email = null; $entrySource = 'Citas Integrales - Contáctanos'; }
        elseif ($sourceType === 'advisory') { $fullName = trim($item->full_name ?? ''); $parts = explode(' ', $fullName, 2); $nombre = $parts[0] ?? ''; $apellido = $parts[1] ?? ''; $telefono = $item->phone ?? ''; $email = $item->email ?? null; $entrySource = 'Citas Integrales - Asesoría'; }
        else { $nombre = $item->first_name ?? ''; $apellido = $item->last_name ?? ''; $telefono = $item->phone ?? ''; $email = $item->email ?? null; $entrySource = 'Citas Integrales - Trámite'; }
        $portfolioEntry = ClientPortfolioEntry::create([
            'client_id' => null,
            'prospect_id' => $item->prospect_id,
            'appointment_id' => null,
            'source_type' => $sourceType,
            'source_record_id' => $item->id,
            'previous_status' => $previousStatus,
            'prospect_name' => $nombre,
            'prospect_last_name' => $apellido,
            'prospect_phone' => $telefono,
            'prospect_email' => $email,
            'property_id' => null,
            'advisor_id' => auth()->id(),
            'entry_source' => $entrySource,
            'contact_channel' => $request->contact_channel,
            'social_platform' => $request->social_platform,
            'social_profile_url' => $request->social_profile_url,
            'entry_reason' => $request->entry_reason,
            'portfolio_status' => $request->portfolio_status,
            'notes' => $request->notes,
            'entered_at' => now(),
        ]);
        $item->status = 'Transferido'; $item->save();
        $prospect = \App\Models\Prospect::find($item->prospect_id);
        if ($prospect) { $prospectService->addHistory($prospect, 'Transferido a Cartera', $sourceType, $portfolioEntry->id, $previousStatus, 'Transferido', 'El registro fue transferido desde ' . $entrySource . ' a Cartera. Motivo: ' . $request->entry_reason, auth()->id()); }
        return redirect()->route('admin.citas-totales')->with('success', 'El registro fue transferido correctamente a Cartera.');
    });
}

public function historialProspecto($id) {
    $prospect = \App\Models\Prospect::with(['contacts', 'aliases'])->find($id);
    if (!$prospect) return redirect()->route('admin.cartera')->with('error', 'El prospecto seleccionado no existe.');

    /* HISTORIAL COMPLETO */
    $histories = \App\Models\ProspectHistory::where('prospect_id', $prospect->id)->latest('created_at')->get();

    /* MOVIMIENTOS EN CARTERA */
    $portfolioEntries = ClientPortfolioEntry::with(['property','advisor'])->where('prospect_id', $prospect->id)->latest('entered_at')->get();

    /* ESTADO ACTUAL */
    $latestPortfolio = $portfolioEntries->first();
    $estadoActual = $latestPortfolio->portfolio_status ?? $histories->first()?->new_status ?? $prospect->status ?? 'Sin estado';

    /* CLIENTE POTENCIAL */
    $esPotencial = $portfolioEntries->contains(fn($entry) => $entry->portfolio_status === 'Cliente Potencial');

    /* RED SOCIAL MÁS RECIENTE */
    $socialEntry = $portfolioEntries->first(fn($entry) => !empty($entry->social_profile_url));
    $socialUrl = $socialEntry?->social_profile_url;
    $socialPlatform = $socialEntry?->social_platform;

    /* TOTAL DE MOVIMIENTOS */
    $totalMovimientos = $histories->count();

    return view('intranet.users.historial', compact('prospect','histories','portfolioEntries','estadoActual','esPotencial','socialUrl','socialPlatform','totalMovimientos'));
}
public function storeProspectMovement(Request $request, $id, ProspectService $prospectService) {
    $prospect = \App\Models\Prospect::find($id);
    if (!$prospect) return redirect()->route('admin.cartera')->with('error', 'El prospecto seleccionado no existe.');

    $validated = $request->validate([
        'interaction_type' => 'required|string|max:100',
        'channel' => 'required|string|max:100',
        'interaction_date' => 'required|date',
        'description' => 'required|string|max:3000',
        'result' => 'nullable|string|max:1000',
        'new_status' => 'nullable|in:Nuevo,Contactado,Seguimiento,Interesado,Negociación,Cliente Potencial',
        'notes' => 'nullable|string|max:3000',
    ]);

    return DB::transaction(function () use ($validated, $prospect, $prospectService) {
        $estadoAnterior = $prospect->status;
        if (!empty($validated['new_status'])) {
    $prospect->status = $validated['new_status'];
    $prospect->save();

    $ultimaEntradaCartera = ClientPortfolioEntry::where('prospect_id', $prospect->id)
        ->latest('entered_at')
        ->first();

    if ($ultimaEntradaCartera) {
        $ultimaEntradaCartera->portfolio_status = $validated['new_status'];
        $ultimaEntradaCartera->save();
    }
}

        $descripcion = 'Tipo: ' . $validated['interaction_type'] . '. Canal: ' . $validated['channel'] . '. Descripción: ' . $validated['description'];
        if (!empty($validated['result'])) $descripcion .= '. Resultado: ' . $validated['result'];
        if (!empty($validated['notes'])) $descripcion .= '. Notas: ' . $validated['notes'];

        $prospectService->addHistory(
            $prospect,
            'Movimiento manual',
            'manual',
            null,
            $estadoAnterior,
            $validated['new_status'] ?? $estadoAnterior,
            $descripcion,
            auth()->id()
        );

        return redirect()->route('admin.prospectos.historial', $prospect->id)->with('success', 'El movimiento manual fue registrado correctamente.');
    });
}


public function convertProspectToClient(
    $prospect,
    ProspectService $prospectService
) {
    /*
    |--------------------------------------------------------------------------
    | BUSCAR PROSPECTO
    |--------------------------------------------------------------------------
    */

    $prospectModel = \App\Models\Prospect::with([
        'contacts',
        'aliases',
        'client'
    ])->find($prospect);

    if (!$prospectModel) {
        return redirect()
            ->route('admin.cartera')
            ->with(
                'error',
                'El prospecto seleccionado no existe.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | BUSCAR REGISTRO ACTUAL EN CARTERA
    |--------------------------------------------------------------------------
    */

    $portfolio = ClientPortfolioEntry::where(
        'prospect_id',
        $prospectModel->id
    )
        ->orderByDesc('entered_at')
        ->orderByDesc('id')
        ->first();

    if (!$portfolio) {
        return redirect()
            ->route('admin.cartera')
            ->with(
                'error',
                'El prospecto ya no se encuentra registrado en Cartera.'
            );
    }


    return DB::transaction(function () use (
        $prospectModel,
        $prospectService,
        $portfolio
    ) {

        /*
        |--------------------------------------------------------------------------
        | DETERMINAR ORIGEN REAL
        |--------------------------------------------------------------------------
        |
        | El nuevo ClientTramite NO debe guardar "cartera" como origen.
        | Debe conservar el módulo donde nació originalmente el prospecto:
        |
        | appointment
        | contact
        | advisory
        | tramite
        |
        */

        $allowedOrigins = [
            'appointment',
            'contact',
            'advisory',
            'tramite',
        ];

        $originType = strtolower(
            trim((string) $portfolio->source_type)
        );

        $originId = $portfolio->source_record_id;


        /*
        |--------------------------------------------------------------------------
        | SI CARTERA VIENE DE CLIENTES / TRÁMITES
        |--------------------------------------------------------------------------
        */

        if ($originType === 'client_tramite') {

            $previousClientTramite = ClientTramite::find(
                $portfolio->source_record_id
            );

            if ($previousClientTramite) {

                $previousOriginType = strtolower(
                    trim(
                        (string) $previousClientTramite->source_type
                    )
                );

                if (in_array(
                    $previousOriginType,
                    $allowedOrigins,
                    true
                )) {

                    $originType =
                        $previousOriginType;

                    $originId =
                        $previousClientTramite->source_id;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RESPALDO: BUSCAR ORIGEN EN EL HISTORIAL
        |--------------------------------------------------------------------------
        |
        | Esto cubre registros antiguos que fueron creados cuando
        | ClientTramite guardaba source_type = cartera.
        |
        */

        if (
            !in_array($originType, $allowedOrigins, true)
            || empty($originId)
        ) {

            $originalHistory = \App\Models\ProspectHistory::where(
                'prospect_id',
                $prospectModel->id
            )
                ->whereIn(
                    'source_type',
                    $allowedOrigins
                )
                ->whereNotNull(
                    'source_record_id'
                )
                ->orderBy('id')
                ->first();

            if ($originalHistory) {

                $originType = strtolower(
                    trim(
                        (string) $originalHistory->source_type
                    )
                );

                $originId =
                    $originalHistory->source_record_id;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR QUE EL ORIGEN HAYA SIDO ENCONTRADO
        |--------------------------------------------------------------------------
        */

        if (
            !in_array($originType, $allowedOrigins, true)
            || empty($originId)
        ) {
            return redirect()
                ->route('admin.cartera')
                ->with(
                    'error',
                    'No se pudo determinar el origen original del prospecto.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | BUSCAR CLIENTE EXISTENTE
        |--------------------------------------------------------------------------
        */

        $client = \App\Models\Client::where(
            'prospect_id',
            $prospectModel->id
        )->first();


        /*
        |--------------------------------------------------------------------------
        | CREAR CLIENTE SI TODAVÍA NO EXISTE
        |--------------------------------------------------------------------------
        */

        if (!$client) {

            $social = $prospectModel->contacts
                ->first(
                    fn($contact) =>
                    in_array(
                        strtolower(
                            (string) $contact->type
                        ),
                        [
                            'instagram',
                            'facebook',
                            'tiktok'
                        ]
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | CORREO PROVISIONAL
            |--------------------------------------------------------------------------
            */

            $email = !empty(
                trim(
                    (string) $prospectModel->email
                )
            )
                ? $prospectModel->email
                : 'sin-correo-prospecto-' .
                    $prospectModel->id .
                    '@pendiente.local';


            /*
            |--------------------------------------------------------------------------
            | CÉDULA PROVISIONAL
            |--------------------------------------------------------------------------
            */

            $identification = !empty(
                trim(
                    (string) $prospectModel->identification
                )
            )
                ? $prospectModel->identification
                : 'PEND-' .
                    str_pad(
                        $prospectModel->id,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );


            /*
            |--------------------------------------------------------------------------
            | TELÉFONO
            |--------------------------------------------------------------------------
            */

            $phone = !empty(
                trim(
                    (string) $prospectModel->phone
                )
            )
                ? $prospectModel->phone
                : '0000000000';


            /*
            |--------------------------------------------------------------------------
            | CREAR CLIENTE
            |--------------------------------------------------------------------------
            */

            $client = \App\Models\Client::create([

                'prospect_id' =>
                    $prospectModel->id,

                'user_id' =>
                    auth()->id(),

                'name' =>
                    $prospectModel->name ?: 'Sin nombre',

                'last_name' =>
                    $prospectModel->last_name ?: '',

                'identification_card' =>
                    $identification,

                'phone' =>
                    $phone,

                'email' =>
                    $email,

                'social_media_source' =>
                    $social?->type,

                'status' =>
                    'Interesado',

                'review_status' =>
                    'Confirmado',

                'origin_module' =>
                    'Cartera',

                'observations' =>
                    $prospectModel->notes,
            ]);
        } else {

            /*
            |--------------------------------------------------------------------------
            | CLIENTE YA EXISTENTE
            |--------------------------------------------------------------------------
            */

            $client->status =
                'Interesado';

            $client->review_status =
                'Confirmado';

            $client->origin_module =
                'Cartera';

            $client->save();
        }


        /*
        |--------------------------------------------------------------------------
        | EVITAR DOS TRÁMITES ACTIVOS
        |--------------------------------------------------------------------------
        */

        $activeClientTramite = ClientTramite::where(
            'client_id',
            $client->id
        )
            ->whereIn(
                'status',
                [
                    'Pendiente',
                    'En Proceso'
                ]
            )
            ->first();

        if ($activeClientTramite) {

            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'Este cliente ya tiene un trámite activo en Clientes / Trámites.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CREAR NUEVO CLIENT_TRAMITE
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | Conservamos el origen ORIGINAL.
        |
        */

        $clientTramite = ClientTramite::create([

            'client_id' =>
                $client->id,

            'prospect_id' =>
                $prospectModel->id,

            'source_type' =>
                $originType,

            'source_id' =>
                $originId,

            'status' =>
                'Pendiente',

            'started_at' =>
                null,

            'finished_at' =>
                null,

            'result' =>
                null,

            'notes' =>
                'Nuevo trámite generado desde Cartera.',

            'created_by' =>
                auth()->id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR PROSPECTO
        |--------------------------------------------------------------------------
        */

        $estadoAnterior =
            $prospectModel->status;

        $prospectModel->status =
            'Cliente';

        $prospectModel->save();


        /*
        |--------------------------------------------------------------------------
        | HISTORIAL
        |--------------------------------------------------------------------------
        */

        $prospectService->addHistory(
            $prospectModel,
            'Enviado a Clientes / Trámites',
            'client_tramite',
            $clientTramite->id,
            $estadoAnterior,
            'Pendiente',
            'El prospecto fue enviado desde Cartera a Clientes / Trámites para iniciar un nuevo proceso. Origen original conservado: ' .
                $originType .
                ' #' .
                $originId .
                '.',
            auth()->id()
        );


        /*
        |--------------------------------------------------------------------------
        | SACAR DE CARTERA
        |--------------------------------------------------------------------------
        */

        ClientPortfolioEntry::where(
            'prospect_id',
            $prospectModel->id
        )->delete();


        /*
        |--------------------------------------------------------------------------
        | IR A CLIENTES / TRÁMITES
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'El prospecto fue enviado correctamente a Clientes / Trámites.'
            );
    });
}
public function updateProspectProfile(Request $request, $id, ProspectService $prospectService) {
    $prospect = \App\Models\Prospect::with(['contacts','aliases'])->find($id);
    if (!$prospect) return redirect()->route('admin.cartera')->with('error', 'El prospecto seleccionado no existe.');

    $request->validate([
        'name' => 'required|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'phone' => 'required|string|max:30',
        'email' => 'nullable|email|max:255',
        'identification' => 'nullable|string|max:50',
        'notes' => 'nullable|string|max:2000',
        'alternate_phone' => 'nullable|string|max:30',
        'instagram' => 'nullable|url|max:500',
        'facebook' => 'nullable|url|max:500',
        'tiktok' => 'nullable|url|max:500',
        'alias_1' => 'nullable|string|max:255',
        'alias_2' => 'nullable|string|max:255',
    ]);

    return DB::transaction(function () use ($request, $prospect, $prospectService) {
        $datosAnteriores = [
            'name' => $prospect->name,
            'last_name' => $prospect->last_name,
            'phone' => $prospect->phone,
            'email' => $prospect->email,
            'identification' => $prospect->identification,
            'notes' => $prospect->notes,
        ];

        $prospect->name = trim($request->name);
        $prospect->last_name = $request->filled('last_name') ? trim($request->last_name) : null;
        $prospect->phone = trim($request->phone);
        $prospect->email = $request->filled('email') ? trim($request->email) : null;
        $prospect->identification = $request->filled('identification') ? trim($request->identification) : null;
        $prospect->notes = $request->filled('notes') ? trim($request->notes) : null;
        $prospect->save();

        // Teléfono alternativo
        \App\Models\ProspectContact::where('prospect_id', $prospect->id)->where('type', 'phone')->delete();
        if ($request->filled('alternate_phone')) {
            $telefonoAlternativo = trim($request->alternate_phone);
            if ($telefonoAlternativo !== trim($prospect->phone)) {
                \App\Models\ProspectContact::create([
                    'prospect_id' => $prospect->id,
                    'type' => 'phone',
                    'value' => $telefonoAlternativo,
                    'label' => 'Alternativo',
                    'is_primary' => false,
                    'notes' => null,
                ]);
            }
        }

        // Redes sociales
        $redes = ['instagram' => $request->instagram, 'facebook' => $request->facebook, 'tiktok' => $request->tiktok];
        foreach ($redes as $tipo => $valor) {
            \App\Models\ProspectContact::where('prospect_id', $prospect->id)->where('type', $tipo)->delete();
            if (!empty($valor)) {
                \App\Models\ProspectContact::create([
                    'prospect_id' => $prospect->id,
                    'type' => $tipo,
                    'value' => trim($valor),
                    'label' => 'Perfil',
                    'is_primary' => false,
                    'notes' => null,
                ]);
            }
        }

        // Alias
        \App\Models\ProspectAlias::where('prospect_id', $prospect->id)->delete();
        $aliases = array_values(array_unique(array_filter([trim($request->alias_1 ?? ''), trim($request->alias_2 ?? '')])));
        foreach (array_slice($aliases, 0, 2) as $alias) {
            \App\Models\ProspectAlias::create([
                'prospect_id' => $prospect->id,
                'alias_name' => $alias,
                'notes' => 'Nombre alternativo registrado desde la ficha del prospecto.',
            ]);
        }

        // Cambios detectados
        $cambios = [];
        if ($datosAnteriores['name'] !== $prospect->name) $cambios[] = 'Nombre actualizado';
        if ($datosAnteriores['last_name'] !== $prospect->last_name) $cambios[] = 'Apellido actualizado';
        if ($datosAnteriores['phone'] !== $prospect->phone) $cambios[] = 'Teléfono principal actualizado';
        if ($datosAnteriores['email'] !== $prospect->email) $cambios[] = 'Correo actualizado';
        if ($datosAnteriores['identification'] !== $prospect->identification) $cambios[] = 'Identificación actualizada';
        if ($datosAnteriores['notes'] !== $prospect->notes) $cambios[] = 'Observaciones generales actualizadas';
        if ($request->filled('alternate_phone')) $cambios[] = 'Teléfono alternativo registrado';
        if ($request->filled('instagram') || $request->filled('facebook') || $request->filled('tiktok')) $cambios[] = 'Redes sociales actualizadas';
        if (count($aliases) > 0) $cambios[] = 'Nombres alternativos actualizados';
        $descripcion = count($cambios) > 0 ? implode('. ', $cambios) . '.' : 'Se revisó la información del perfil sin cambios relevantes.';

        $prospectService->addHistory($prospect, 'Perfil actualizado', 'prospect', $prospect->id, $prospect->status, $prospect->status, $descripcion, auth()->id());

        return redirect()->route('admin.prospectos.historial', $prospect->id)->with('success', 'El perfil del prospecto fue actualizado correctamente.');
    });
}

    public function storeIntegral(Request $request, ProspectService $prospectService) {
    $tipo = $this->normalizarTipo($request->input('tipo_registro', $request->input('nuevo_tipo')));
    $direccionComun = $request->input('general_address') ?? $request->input('ciudad') ?? $request->input('ubicacion') ?? 'N/A';

    return DB::transaction(function () use ($request, $prospectService, $tipo, $direccionComun) {
        /* CONTACTO */
        if ($tipo === 'contacto') {
            $request->validate(['phone' => 'required|string']);
            $nombreEntrada = $request->name ?? $request->full_name ?? $request->first_name ?? 'Sin nombre';
            $parts = explode(' ', trim($nombreEntrada), 2);
            $nombre = $parts[0] ?? 'Sin nombre'; $apellido = $parts[1] ?? 'Sin apellido';
            $prospect = $prospectService->findOrCreate($nombre, $apellido, $request->phone, $request->email, null, 'Citas Integrales - Contacto');
            $contact = Contact::create([
                'prospect_id' => $prospect->id,
                'name' => $nombre,
                'last_name' => $apellido,
                'phone' => $request->phone,
                'general_address' => $direccionComun,
                'requirements_message' => $request->message ?? $request->requirements_message ?? $request->property_details ?? '',
                'status' => $request->status ?? 'Pendiente'
            ]);
            $prospectService->addHistory($prospect, 'Contacto registrado desde Citas Integrales', 'contact', $contact->id, null, $contact->status, $contact->requirements_message, auth()->id());
        }

        /* ASESORÍA */
        elseif ($tipo === 'asesoria') {
            $request->validate(['phone' => 'required|string']);
            $nombreCompleto = $request->full_name ?? $request->name ?? $request->first_name ?? 'Sin nombre';
            $parts = explode(' ', trim($nombreCompleto), 2);
            $nombre = $parts[0] ?? 'Sin nombre'; $apellido = $parts[1] ?? '';
            $prospect = $prospectService->findOrCreate($nombre, $apellido, $request->phone, $request->email, null, 'Citas Integrales - Asesoría');
            $advisory = AdvisoryRequest::create([
                'prospect_id' => $prospect->id,
                'full_name' => $nombreCompleto,
                'email' => $request->email,
                'phone' => $request->phone,
                'plan_type' => $request->plan_type ?? 'Gratis',
                'ciudad' => $direccionComun,
                'property_type' => $request->property_type ?? 'Casa',
                'estimated_price' => $request->estimated_price,
                'property_details' => $request->property_details ?? $request->requirements_message ?? $request->message ?? '',
                'accepted_terms' => 1,
                'status' => $request->status ?? 'Pendiente'
            ]);
            $prospectService->addHistory($prospect, 'Asesoría registrada desde Citas Integrales', 'advisory', $advisory->id, null, $advisory->status, $advisory->property_details, auth()->id());
        }

        /* TRÁMITE */
        elseif ($tipo === 'tramite') {
            $request->validate(['phone' => 'required|string']);
            $nombreEntrada = $request->first_name ?? $request->name ?? $request->full_name ?? 'Sin nombre';
            $parts = explode(' ', trim($nombreEntrada), 2);
            $nombre = $parts[0] ?? 'Sin nombre'; $apellido = $parts[1] ?? 'Sin apellido';
            $prospect = $prospectService->findOrCreate($nombre, $apellido, $request->phone, $request->email, $request->identification_card, 'Citas Integrales - Trámite');
            $tramite = Tramite::create([
                'prospect_id' => $prospect->id,
                'first_name' => $nombre,
                'last_name' => $apellido,
                'email' => $request->email,
                'phone' => $request->phone,
                'identification_card' => $request->identification_card ?? '0000000000',
                'subject' => $request->subject ?? 'Trámite General',
                'tramite_type' => $request->tramite_type ?? 'Otros trámites',
                'ubicacion' => $direccionComun,
                'message' => $request->message ?? $request->tramite_detalle ?? $request->requirements_message ?? '',
'contact_preference' => match (
    strtolower(trim(
        $cita->source_channel
        ?? $cita->channel
        ?? ''
    ))
) {
    'whatsapp' => 'WhatsApp',

    'telefono',
    'teléfono',
    'llamada',
    'llamada telefonica',
    'llamada telefónica' => 'Llamada telefónica',

    'correo',
    'email',
    'correo electronico',
    'correo electrónico' => 'Correo electrónico',

    default => 'WhatsApp',
},


                'accepted_privacy_policy' => 1,
                'status' => $request->status ?? 'Pendiente'
            ]);
            $prospectService->addHistory($prospect, 'Trámite registrado desde Citas Integrales', 'tramite', $tramite->id, null, $tramite->status, $tramite->message, auth()->id());
        } else {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'El tipo de registro seleccionado no es válido.'
                );
        }

        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'success',
                '¡Registro procesado exitosamente!'
            );

    });
}


public function edit($id)
{
    $cleanId = preg_replace('/[^0-9]/', '', $id);
    $item = null;
    $tipo = '';

    if (str_starts_with($id, 'contact_')) {

        $item = Contact::find($cleanId);
        $tipo = 'contacto';

    } elseif (str_starts_with($id, 'advisory_')) {

        $item = AdvisoryRequest::find($cleanId);
        $tipo = 'asesoria';

    } elseif (str_starts_with($id, 'tramite_')) {

        $item = Tramite::find($cleanId);
        $tipo = 'tramite';

    } else {

        $item = AppointmentTracking::find($cleanId);
        $tipo = 'cita';
    }

    if (!$item) {

        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'error',
                'El registro solicitado no existe o fue eliminado permanentemente.'
            );
    }

    $asesores = User::all();
    $clientes = Client::all();

    return view(
        'intranet.users.editar-integral',
        compact(
            'item',
            'tipo',
            'asesores',
            'clientes'
        )
    );
}


    public function update(Request $request, $id) { return $this->updateIntegral($request, $id); }

    public function updateIntegral(Request $request, $id) {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        $tipoOrigen = $this->normalizarTipo($request->input('tipo_origen'), $id);
        if (!$tipoOrigen && is_numeric($id)) $tipoOrigen = 'cita';
        $nuevoTipo = $this->normalizarTipo($request->input('nuevo_tipo', $request->input('tipo_registro')));
        if ($tipoOrigen && $nuevoTipo && $tipoOrigen !== $nuevoTipo && $tipoOrigen !== 'cita') {
            return DB::transaction(function () use ($request, $tipoOrigen, $nuevoTipo, $cleanId) {
                if ($tipoOrigen === 'contacto') Contact::where('id', $cleanId)->delete();
                elseif ($tipoOrigen === 'asesoria') AdvisoryRequest::where('id', $cleanId)->delete();
                elseif ($tipoOrigen === 'tramite') Tramite::where('id', $cleanId)->delete();
                $request->merge(['tipo_registro' => $nuevoTipo]);
                $this->storeIntegral($request,app(ProspectService::class));
                return redirect()->route('admin.citas-totales')->with('success', '¡Registro transmutado y actualizado correctamente!');
            });
        }
        if ($tipoOrigen === 'contacto' || str_starts_with($id, 'contact_')) {
            $item = Contact::find($cleanId);
            if ($item) {
                $nombreCompleto = $request->name ?? $request->full_name ?? $item->name;
                $parts = explode(' ', trim($nombreCompleto), 2);
                $item->update([
                    'name' => $parts[0] ?? '',
                    'last_name' => $parts[1] ?? $item->last_name ?? 'Sin apellido',
                    'phone' => $request->phone ?? $item->phone,
                    'general_address' => $request->general_address ?? $request->ciudad ?? $request->ubicacion ?? $item->general_address,
                    'requirements_message' => $request->requirements_message ?? $request->message ?? $request->property_details ?? $item->requirements_message,
                    'status' => $request->status ?? $item->status
                ]);
            }
        } elseif ($tipoOrigen === 'asesoria' || str_starts_with($id, 'advisory_')) {
            $item = AdvisoryRequest::find($cleanId);
            if ($item) {
                $item->update([
                    'full_name' => $request->full_name ?? $request->name ?? $item->full_name,
                    'phone' => $request->phone ?? $item->phone,
                    'plan_type' => $request->plan_type ?? $item->plan_type,
                    'ciudad' => $request->ciudad ?? $request->general_address ?? $request->ubicacion ?? $item->ciudad,
                    'property_type' => $request->property_type ?? $item->property_type,
                    'estimated_price' => $request->estimated_price ?? $item->estimated_price,
                    'property_details' => $request->property_details ?? $request->requirements_message ?? $request->message ?? $item->property_details,
                    'status' => $request->status ?? $item->status
                ]);
            }
        } elseif ($tipoOrigen === 'tramite' || str_starts_with($id, 'tramite_')) {
            $item = Tramite::find($cleanId);
            if ($item) {
                $nombreCompleto = $request->first_name ?? $request->name ?? $item->first_name;
                $parts = explode(' ', trim($nombreCompleto), 2);
                $item->update([
                    'first_name' => $parts[0] ?? '',
                    'last_name' => $parts[1] ?? $item->last_name ?? 'Sin apellido',
                    'phone' => $request->phone ?? $item->phone,
                    'identification_card' => $request->identification_card ?? $item->identification_card,
                    'subject' => $request->subject ?? $item->subject,
                    'tramite_type' => $request->tramite_type ?? $item->tramite_type,
                    'ubicacion' => $request->ubicacion ?? $request->general_address ?? $request->ciudad ?? $item->ubicacion,
                    'message' => $request->message ?? $request->requirements_message ?? $request->property_details ?? $item->message,
                    'status' => $request->status ?? $item->status
                ]);
            }
        } else {
            $item = AppointmentTracking::find($cleanId);
            if (!$item) return redirect()->route('gestion.citas')->with('error', 'La cita seleccionada no existe.');
            $item->update($request->only(['client_id','user_id','property_id','appointment_date','location_reference','status','type','priority','notes','source_channel']));
        }
        if ($tipoOrigen === 'cita' || is_numeric($id)) return redirect()->route('gestion.citas')->with('success', '¡Cita actualizada correctamente!');
        return redirect()->route('admin.citas-totales')->with('success', '¡Registro actualizado correctamente!');
    }

    public function gestionar($id) {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        if (str_starts_with($id, 'contact_')) $item = Contact::find($cleanId);
        elseif (str_starts_with($id, 'advisory_')) $item = AdvisoryRequest::find($cleanId);
        elseif (str_starts_with($id, 'tramite_')) $item = Tramite::find($cleanId);
        else $item = AppointmentTracking::find($cleanId);
        if (!$item) return redirect()->back()->with('error', 'No se encontró el registro para actualizar estado.');
        $estadoActual = (empty($item->status) || $item->status === 'Nuevo') ? 'Pendiente' : $item->status;
        $item->status = ($estadoActual === 'Pendiente') ? 'En Proceso' : (($estadoActual === 'En Proceso') ? 'Completado' : 'Pendiente');
        $item->save();
        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }

public function exportar($id, ProspectService $prospectService)
{
    $cleanId = preg_replace('/[^0-9]/', '', $id);

    /*
    |--------------------------------------------------------------------------
    | SOLO REGISTROS DE TIPO TRÁMITE
    |--------------------------------------------------------------------------
    */
    if (!str_starts_with($id, 'tramite_')) {
        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'error',
                'Solo los registros que ya fueron convertidos a Trámite pueden pasar a Clientes / Trámites.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR TRÁMITE DE CITAS INTEGRALES
    |--------------------------------------------------------------------------
    */
    $tramite = Tramite::find($cleanId);

    if (!$tramite) {
        return redirect()
            ->route('admin.citas-totales')
            ->with('error', 'El trámite seleccionado no existe.');
    }

    /*
    |--------------------------------------------------------------------------
    | SOLO PUEDE PASAR SI ESTÁ COMPLETADO
    |--------------------------------------------------------------------------
    */
    if (($tramite->status ?? '') !== 'Completado') {
        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'error',
                'Solo los trámites con estado Completado pueden pasar a Clientes / Trámites.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR PROSPECTO
    |--------------------------------------------------------------------------
    */
    if (empty($tramite->prospect_id)) {
        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'error',
                'Este trámite todavía no está relacionado con un prospecto.'
            );
    }

    $prospect = Prospect::with([
        'contacts',
        'aliases'
    ])->find($tramite->prospect_id);

    if (!$prospect) {
        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'error',
                'No se encontró el prospecto relacionado con este trámite.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSMUTACIÓN DIRECTA
    |--------------------------------------------------------------------------
    |
    | Citas Integrales
    | Trámite + Completado
    |
    |          ↓
    |
    | Clientes / Trámites
    | Estado: Pendiente
    |
    | Ya NO existe pantalla intermedia de confirmación.
    |
    */
    return DB::transaction(function () use (
        $tramite,
        $prospect,
        $prospectService
    ) {

        /*
        |--------------------------------------------------------------------------
        | BUSCAR RED SOCIAL PRINCIPAL
        |--------------------------------------------------------------------------
        */
        $social = $prospect->contacts->first(
            fn ($contact) =>
                in_array(
                    $contact->type,
                    ['instagram', 'facebook', 'tiktok']
                )
        );

        /*
        |--------------------------------------------------------------------------
        | BUSCAR O CREAR CLIENTE
        |--------------------------------------------------------------------------
        */
        $client = Client::where(
            'prospect_id',
            $prospect->id
        )->first();

        if (!$client) {

            $client = Client::create([
                'prospect_id' => $prospect->id,
                'user_id' => auth()->id(),

                'name' =>
                    $prospect->name
                    ?: ($tramite->first_name ?? 'Sin nombre'),

                'last_name' =>
                    $prospect->last_name
                    ?: ($tramite->last_name ?? ''),

                'identification_card' =>
                    $prospect->identification
                    ?: ($tramite->identification_card ?? null),

                'phone' =>
                    $prospect->phone
                    ?: ($tramite->phone ?? ''),

                'email' =>
                    !empty($prospect->email)
                        ? $prospect->email
                        : 'sin-correo-prospecto-'
                            . $prospect->id
                            . '@pendiente.local',

                'social_media_source' =>
                    $social?->type,

                'status' =>
                    'Interesado',

                /*
                | Ya no necesita revisión manual.
                */
                'review_status' =>
                    'Confirmado',

                'origin_module' =>
                    'Citas Integrales - Trámite',

                'observations' =>
                    $prospect->notes
                    ?: ($tramite->message ?? null),
            ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | SI EL CLIENTE YA EXISTE
            |--------------------------------------------------------------------------
            |
            | No lo duplicamos.
            | Simplemente queda habilitado en Clientes / Trámites.
            |
            */
            $client->review_status = 'Confirmado';

            if (empty($client->origin_module)) {
                $client->origin_module =
                    'Citas Integrales - Trámite';
            }

            $client->save();
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR PROCESO PROPIO DE CLIENTES / TRÁMITES
        |--------------------------------------------------------------------------
        |
        | MUY IMPORTANTE:
        | Esto ya NO modifica el trámite para manejar ▶ ✓ ✕.
        |
        | Ese proceso pertenece exclusivamente a client_tramites.
        |
        */
        $clientTramite = ClientTramite::firstOrCreate(
            [
                'client_id' => $client->id,
                'source_type' => 'tramite',
                'source_id' => $tramite->id,
            ],
            [
                'prospect_id' => $prospect->id,
                'status' => 'Pendiente',
                'created_by' => auth()->id(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | MARCAR ORIGEN COMO TRANSFERIDO
        |--------------------------------------------------------------------------
        |
        | El registro de Citas Integrales termina aquí.
        | Su proceso posterior se realizará en client_tramites.
        |
        */
        $tramite->status = 'Transferido';
        $tramite->save();

        /*
        |--------------------------------------------------------------------------
        | HISTORIAL DEL PROSPECTO
        |--------------------------------------------------------------------------
        */
        $prospectService->addHistory(
            $prospect,
            'Transferido a Clientes / Trámites',
            'tramite',
            $tramite->id,
            $prospect->status,
            $prospect->status,
            'El trámite completado fue transferido directamente desde Citas Integrales hacia Clientes / Trámites.',
            auth()->id()
        );

        /*
        |--------------------------------------------------------------------------
        | IR DIRECTAMENTE A CLIENTES / TRÁMITES
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'El cliente fue transferido correctamente a Clientes / Trámites.'
            );
    });
}
    public function destroyIntegral($id) {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        try {
            if (str_starts_with($id, 'contact_')) $model = Contact::find($cleanId);
            elseif (str_starts_with($id, 'advisory_')) $model = AdvisoryRequest::find($cleanId);
            elseif (str_starts_with($id, 'tramite_')) $model = Tramite::find($cleanId);
            else $model = AppointmentTracking::find($cleanId);
            if (!$model) return redirect()->back()->with('error', 'El registro ya no existe.');
            $model->status = 'eliminado'; $model->save();
            if (is_numeric($id)) return redirect()->route('gestion.citas')->with('success', 'El registro ha sido enviado a la papelera correctamente.');
            return redirect()->back()->with('success', 'El registro ha sido reciclado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar reciclar el dato: ' . $e->getMessage());
        }
    }

        public function restaurar($id) {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        try {
            if (str_starts_with($id, 'contact_')) $model = Contact::find($cleanId);
            elseif (str_starts_with($id, 'advisory_')) $model = AdvisoryRequest::find($cleanId);
            elseif (str_starts_with($id, 'tramite_')) $model = Tramite::find($cleanId);
            else $model = AppointmentTracking::find($cleanId);
            if (!$model) return redirect()->back()->with('error', 'El registro no se encuentra para restaurar.');
            $model->status = 'Pendiente'; $model->save();
            return redirect()->back()->with('success', 'El registro ha sido restaurado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar restaurar el registro: ' . $e->getMessage());
        }
    }

    public function forzarEliminar($id) {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        try {
            if (str_starts_with($id, 'contact_')) $model = Contact::find($cleanId);
            elseif (str_starts_with($id, 'advisory_')) $model = AdvisoryRequest::find($cleanId);
            elseif (str_starts_with($id, 'tramite_')) $model = Tramite::find($cleanId);
            else $model = AppointmentTracking::find($cleanId);
            if (!$model) return redirect()->back()->with('error', 'El registro no existe o ya fue eliminado.');
            $model->delete();
            return redirect()->back()->with('success', 'El registro ha sido eliminado permanentemente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar permanentemente: ' . $e->getMessage());
        }
    }

    private function normalizarTipo($cadena, $id = '') {
        $cadena = strtolower(trim($cadena ?? ''));
        $cadena = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $cadena);
        if (str_contains($cadena, 'contact')) return 'contacto';
        if (str_contains($cadena, 'asesor')) return 'asesoria';
        if (str_contains($cadena, 'tramite')) return 'tramite';
        if (str_contains($cadena, 'cita')) return 'cita';
        if (!empty($id)) {
            if (str_starts_with($id, 'contact_')) return 'contacto';
            if (str_starts_with($id, 'advisory_')) return 'asesoria';
            if (str_starts_with($id, 'tramite_')) return 'tramite';
            if (is_numeric($id)) return 'cita';
        }
        return $cadena;
    }
public function exportAppointmentToClient(
    $id,
    ProspectService $prospectService
) {
    return DB::transaction(function () use ($id, $prospectService) {

        /*
        |--------------------------------------------------------------------------
        | 1. BUSCAR LA CITA
        |--------------------------------------------------------------------------
        */

        $cita = AppointmentTracking::with([
            'prospect',
            'client',
            'user',
            'property'
        ])->find($id);

        if (!$cita) {
            return redirect()
                ->route('gestion.citas')
                ->with(
                    'error',
                    'La cita seleccionada no existe.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. SOLO CITAS REALIZADAS
        |--------------------------------------------------------------------------
        */

        if ($cita->status !== 'Realizado') {
            return redirect()
                ->route('gestion.citas')
                ->with(
                    'error',
                    'La cita debe estar en estado Realizado antes de pasar a Clientes / Trámites.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. VERIFICAR PROSPECTO
        |--------------------------------------------------------------------------
        */

        if (empty($cita->prospect_id)) {
            return redirect()
                ->route('gestion.citas')
                ->with(
                    'error',
                    'Esta cita no está relacionada con un prospecto.'
                );
        }

        $prospect = Prospect::with([
            'contacts',
            'aliases'
        ])->find($cita->prospect_id);

        if (!$prospect) {
            return redirect()
                ->route('gestion.citas')
                ->with(
                    'error',
                    'No se encontró el prospecto relacionado con esta cita.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. BUSCAR RED SOCIAL PRINCIPAL
        |--------------------------------------------------------------------------
        */

        $social = $prospect->contacts
            ->first(
                fn ($contact) =>
                    in_array(
                        strtolower($contact->type ?? ''),
                        [
                            'instagram',
                            'facebook',
                            'tiktok'
                        ]
                    )
            );

        /*
        |--------------------------------------------------------------------------
        | 5. BUSCAR CLIENTE
        |--------------------------------------------------------------------------
        */

        $client = Client::where(
            'prospect_id',
            $prospect->id
        )->first();

        /*
        |--------------------------------------------------------------------------
        | 6. CREAR CLIENTE SI TODAVÍA NO EXISTE
        |--------------------------------------------------------------------------
        */

        if (!$client) {

            $clientEmail = trim(
                (string) ($prospect->email ?? '')
            );

            if ($clientEmail === '') {
                $clientEmail =
                    'prospecto-' .
                    $prospect->id .
                    '@pendiente.local';
            }

            $clientIdentification = trim(
                (string) ($prospect->identification ?? '')
            );

            if ($clientIdentification === '') {
                $clientIdentification =
                    'PEND-' .
                    str_pad(
                        (string) $prospect->id,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );
            }

            $clientPhone = trim(
                (string) ($prospect->phone ?? '')
            );

            if ($clientPhone === '') {
                $clientPhone = '0000000000';
            }

            $client = Client::create([

                'prospect_id' =>
                    $prospect->id,

                'user_id' =>
                    $cita->user_id
                    ?? auth()->id(),

                'name' =>
                    $prospect->name
                    ?? 'Sin nombre',

                'last_name' =>
                    $prospect->last_name
                    ?? 'Sin apellido',

                'identification_card' =>
                    $clientIdentification,

                'phone' =>
                    $clientPhone,

                'email' =>
                    $clientEmail,

                'social_media_source' =>
                    $social?->type,

                'status' =>
                    'Interesado',

                /*
                 * Ya no existe revisión manual.
                 */
                'review_status' =>
                    'Confirmado',

                'origin_module' =>
                    'Gestión de Citas - Trámite',

                'observations' =>
                    $cita->notes
                    ?? $prospect->notes,
            ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | CLIENTE YA EXISTENTE
            |--------------------------------------------------------------------------
            |
            | No duplicamos la persona.
            | Tampoco abrimos nuevamente una revisión.
            |
            */

            $client->review_status = 'Confirmado';

            if (empty($client->origin_module)) {
                $client->origin_module =
                    'Gestión de Citas - Trámite';
            }

            $client->save();
        }

        /*
        |--------------------------------------------------------------------------
        | 7. CREAR EL PROCESO EN CLIENTES / TRÁMITES
        |--------------------------------------------------------------------------
        |
        | Ya NO creamos un registro en la tabla "tramites".
        |
        | La cita pertenece a Gestión de Citas.
        | El nuevo proceso pertenece a "client_tramites".
        |
        */

        $clientTramite = ClientTramite::firstOrCreate(
            [
                'client_id' =>
                    $client->id,

                'source_type' =>
                    'appointment',

                'source_id' =>
                    $cita->id,
            ],
            [
                'prospect_id' =>
                    $prospect->id,

                'status' =>
                    'Pendiente',

                'created_by' =>
                    auth()->id(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 8. MARCAR LA CITA COMO TRANSFERIDA
        |--------------------------------------------------------------------------
        |
        | Conservamos el registro original para historial,
        | pero sale de la bandeja activa de Gestión de Citas.
        |
        */

        $cita->status = 'Transferido';
        $cita->save();

        /*
        |--------------------------------------------------------------------------
        | 9. HISTORIAL
        |--------------------------------------------------------------------------
        */

        $prospectService->addHistory(
            $prospect,
            'Transferido a Clientes / Trámites',
            'appointment',
            $cita->id,
            'Realizado',
            'Pendiente',
            'La cita realizada fue transferida directamente desde Gestión de Citas hacia Clientes / Trámites.',
            auth()->id()
        );

        /*
        |--------------------------------------------------------------------------
        | 10. REDIRECCIÓN DIRECTA
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'La cita fue transferida correctamente a Clientes / Trámites.'
            );
    });
}
   
public function returnPortfolioToSource(
    $id,
    ProspectService $prospectService
) {
    $portfolio = ClientPortfolioEntry::find($id);

    if (!$portfolio) {
        return redirect()
            ->route('admin.cartera')
            ->with(
                'error',
                'El registro seleccionado ya no existe en Cartera.'
            );
    }

    return DB::transaction(function () use (
        $portfolio,
        $prospectService
    ) {

        $previousStatus =
            $portfolio->previous_status ?: 'Pendiente';

        $sourceType =
            strtolower(trim((string) $portfolio->source_type));

        /*
        |--------------------------------------------------------------------------
        | REGISTRO QUE LLEGÓ A CARTERA DESDE CLIENTES / TRÁMITES
        |--------------------------------------------------------------------------
        |
        | En este caso "client_tramite" no es el origen inicial.
        | Buscamos dentro del ClientTramite de dónde venía realmente.
        |
        */

        if ($sourceType === 'client_tramite') {

            $clientTramite = ClientTramite::find(
                $portfolio->source_record_id
            );

            if (!$clientTramite) {
                return redirect()
                    ->route('admin.cartera')
                    ->with(
                        'error',
                        'No se encontró el trámite relacionado con este registro.'
                    );
            }

      $originalType =
    strtolower(
        trim((string) $clientTramite->source_type)
    );

$originalId =
    $clientTramite->source_id;


/*
|--------------------------------------------------------------------------
| RECUPERAR ORIGEN DE REGISTROS ANTIGUOS
|--------------------------------------------------------------------------
|
| Algunos ClientTramite antiguos fueron creados con:
|
| source_type = cartera
|
| Como la entrada anterior de Cartera ya fue eliminada,
| recuperamos el origen real desde ProspectHistory.
|
*/

if ($originalType === 'cartera') {

    $originalHistory = \App\Models\ProspectHistory::where(
        'prospect_id',
        $portfolio->prospect_id
    )
        ->whereIn(
            'source_type',
            [
                'appointment',
                'contact',
                'advisory',
                'tramite'
            ]
        )
        ->whereNotNull('source_record_id')
        ->orderBy('id')
        ->first();

    if ($originalHistory) {

        $originalType =
            strtolower(
                trim(
                    (string) $originalHistory->source_type
                )
            );

        $originalId =
            $originalHistory->source_record_id;
    }
}

            /*
            |--------------------------------------------------------------------------
            | ORIGEN REAL: GESTIÓN DE CITAS
            |--------------------------------------------------------------------------
            */

            if ($originalType === 'appointment') {

                $cita = AppointmentTracking::find(
                    $originalId
                );

                if (!$cita) {
                    return redirect()
                        ->route('admin.cartera')
                        ->with(
                            'error',
                            'No se encontró la cita original.'
                        );
                }

                $cita->status = 'Realizado';
                $cita->rescued_to_portfolio = false;
                $cita->save();

                /*
                 * El trámite fallido de Clientes / Trámites
                 * permanece cerrado.
                 */
                $clientTramite->status = 'Sin Éxito';

                $clientTramite->finished_at =
                    $clientTramite->finished_at ?: now();

                $clientTramite->result = 'Sin Éxito';

                $clientTramite->save();

                $prospect = Prospect::find(
                    $portfolio->prospect_id
                );

                if ($prospect) {

                    $prospectService->addHistory(
                        $prospect,
                        'Regresado desde Cartera',
                        'appointment',
                        $cita->id,
                        'Seguimiento Pendiente',
                        'Realizado',
                        'El prospecto regresó desde Cartera hacia Gestión de Citas.',
                        auth()->id()
                    );
                }

                $portfolio->delete();

                return redirect()
                    ->route('gestion.citas')
                    ->with(
                        'success',
                        'El registro regresó correctamente a Gestión de Citas.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | ORIGEN REAL: CITAS INTEGRALES - TRÁMITE
            |--------------------------------------------------------------------------
            */

            if ($originalType === 'tramite') {

                $tramite = Tramite::find(
                    $originalId
                );

                if (!$tramite) {
                    return redirect()
                        ->route('admin.cartera')
                        ->with(
                            'error',
                            'No se encontró el trámite original de Citas Integrales.'
                        );
                }

                $tramite->status = 'Completado';
                $tramite->save();

                $clientTramite->status = 'Sin Éxito';

                $clientTramite->finished_at =
                    $clientTramite->finished_at ?: now();

                $clientTramite->result = 'Sin Éxito';

                $clientTramite->save();

                $prospect = Prospect::find(
                    $portfolio->prospect_id
                );

                if ($prospect) {

                    $prospectService->addHistory(
                        $prospect,
                        'Regresado desde Cartera',
                        'tramite',
                        $tramite->id,
                        'Seguimiento Pendiente',
                        'Completado',
                        'El prospecto regresó desde Cartera hacia Citas Integrales.',
                        auth()->id()
                    );
                }

                $portfolio->delete();

                return redirect()
                    ->route('admin.citas-totales')
                    ->with(
                        'success',
                        'El registro regresó correctamente a Citas Integrales.'
                    );
            }

            return redirect()
                ->route('admin.cartera')
                ->with(
                    'error',
                    'No se pudo determinar el origen original del prospecto.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ORIGEN DIRECTO: GESTIÓN DE CITAS
        |--------------------------------------------------------------------------
        */

        if ($sourceType === 'appointment') {

            $cita = AppointmentTracking::find(
                $portfolio->source_record_id
            );

            if (!$cita) {
                return redirect()
                    ->route('admin.cartera')
                    ->with(
                        'error',
                        'No se encontró la cita original.'
                    );
            }

            $cita->status = $previousStatus;
            $cita->rescued_to_portfolio = false;
            $cita->save();

            $prospect = Prospect::find(
                $portfolio->prospect_id
            );

            if ($prospect) {

                $prospectService->addHistory(
                    $prospect,
                    'Regresado desde Cartera',
                    'appointment',
                    $cita->id,
                    'Transferido',
                    $previousStatus,
                    'El registro fue regresado desde Cartera hacia Gestión de Citas.',
                    auth()->id()
                );
            }

            $portfolio->delete();

            return redirect()
                ->route('gestion.citas')
                ->with(
                    'success',
                    'El registro regresó correctamente a Gestión de Citas.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ORIGEN DIRECTO: CITAS INTEGRALES
        |--------------------------------------------------------------------------
        */

        if ($sourceType === 'contact') {

            $item = Contact::find(
                $portfolio->source_record_id
            );

            $historyType = 'contact';
        }

        elseif ($sourceType === 'advisory') {

            $item = AdvisoryRequest::find(
                $portfolio->source_record_id
            );

            $historyType = 'advisory';
        }

        elseif ($sourceType === 'tramite') {

            $item = Tramite::find(
                $portfolio->source_record_id
            );

            $historyType = 'tramite';
        }

        else {

            return redirect()
                ->route('admin.cartera')
                ->with(
                    'error',
                    'No se pudo determinar el módulo de origen.'
                );
        }

        if (!$item) {

            return redirect()
                ->route('admin.cartera')
                ->with(
                    'error',
                    'No se encontró el registro original.'
                );
        }

        $item->status = $previousStatus;
        $item->save();

        $prospect = Prospect::find(
            $portfolio->prospect_id
        );

        if ($prospect) {

            $prospectService->addHistory(
                $prospect,
                'Regresado desde Cartera',
                $historyType,
                $item->id,
                'Transferido',
                $previousStatus,
                'El registro fue regresado desde Cartera hacia Citas Integrales.',
                auth()->id()
            );
        }

        $portfolio->delete();

        return redirect()
            ->route('admin.citas-totales')
            ->with(
                'success',
                'El registro regresó correctamente a Citas Integrales.'
            );
    });
}
}