<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentTracking;
use App\Models\User;
use App\Models\Client;
use App\Notifications\UrgentAppointmentNotification;
use App\Models\Contact;
use App\Models\AdvisoryRequest;
use App\Models\Tramite;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = AppointmentTracking::with(['client', 'user', 'property'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        
        $asesores = User::all();
        $clientes = Client::all();
        
        return view('intranet.users.agenda', compact('appointments', 'asesores', 'clientes'));
    }

    public function gestionCitas()
    {
        $appointments = AppointmentTracking::with(['client', 'user', 'property'])
            ->latest()
            ->get();
        
        $asesores = User::all();
        $clientes = Client::all();
        
        return view('intranet.users.gestion-citas', compact('appointments', 'asesores', 'clientes'));
    }

    // Bandeja Integral unificada que recopila Citas, Contáctanos, Asesorías y Trámites
    public function integrales(Request $request)
    {
        $filtro = $request->get('filtro');
        $collection = collect();

        // 1. Citas / Tracking interno
        if (!$filtro || $filtro === 'todos' || $filtro === 'cita') {
            $citas = AppointmentTracking::with(['client', 'user', 'property'])->get()->map(function($item) {
                $item->origen_canal = ucfirst($item->type ?? 'General');
                $item->nombre_cliente = optional($item->client)->name ?? 'Prospecto Web';
                $item->telefono_cliente = optional($item->client)->phone ?? 'N/A';
                $item->detalle_ubicacion = $item->location_reference ?? 'N/A';
                $item->detalle_nota = $item->notes ?? '';
                $item->status = $item->status ?? 'Pendiente';
                $item->fecha_registro = $item->registration_date ?? $item->created_at;
                $item->is_external = false; // Es de AppointmentTracking
                return $item;
            });
            $collection = $collection->concat($citas);
        }

        // 2. Contáctanos (Contact)
        if (!$filtro || $filtro === 'todos' || $filtro === 'contacto') {
            $contactos = Contact::all()->map(function($item) {
                $obj = new \stdClass();
                $obj->id = $item->id;
                $obj->origen_canal = 'Contáctanos';
                $obj->nombre_cliente = $item->name ?? $item->nombre ?? 'Prospecto Web';
                $obj->telefono_cliente = $item->phone ?? $item->telefono ?? 'N/A';
                $obj->detalle_ubicacion = $item->subject ?? $item->ubicacion ?? 'N/A';
                $obj->detalle_nota = $item->message ?? $item->mensaje ?? '';
                $obj->status = $item->status ?? 'Pendiente';
                $obj->fecha_registro = $item->created_at;
                $obj->is_external = true;
                return $obj;
            });
            $collection = $collection->concat($contactos);
        }

        // 3. Asesorías (AdvisoryRequest)
        if (!$filtro || $filtro === 'todos' || $filtro === 'asesoria') {
            $asesorias = AdvisoryRequest::all()->map(function($item) {
                $obj = new \stdClass();
                $obj->id = $item->id;
                $obj->origen_canal = 'Asesorías';
                $obj->nombre_cliente = $item->name ?? $item->nombre ?? 'Prospecto Web';
                $obj->telefono_cliente = $item->phone ?? $item->telefono ?? 'N/A';
                $obj->detalle_ubicacion = $item->location ?? $item->ubicacion ?? 'N/A';
                $obj->detalle_nota = $item->notes ?? $item->detalles ?? '';
                $obj->status = $item->status ?? 'Pendiente';
                $obj->fecha_registro = $item->created_at;
                $obj->is_external = true;
                return $obj;
            });
            $collection = $collection->concat($asesorias);
        }

        // 4. Trámites (Tramite)
        if (!$filtro || $filtro === 'todos' || $filtro === 'tramite') {
            $tramites = Tramite::all()->map(function($item) {
                $obj = new \stdClass();
                $obj->id = $item->id;
                $obj->origen_canal = 'Trámites';
                $obj->nombre_cliente = $item->name ?? $item->nombre ?? 'Prospecto Web';
                $obj->telefono_cliente = $item->phone ?? $item->telefono ?? 'N/A';
                $obj->detalle_ubicacion = $item->location ?? $item->ubicacion ?? 'N/A';
                $obj->detalle_nota = $item->notes ?? $item->detalles ?? '';
                $obj->status = $item->status ?? 'Pendiente';
                $obj->fecha_registro = $item->created_at;
                $obj->is_external = true;
                return $obj;
            });
            $collection = $collection->concat($tramites);
        }

        // Ordenar del más reciente al más antiguo
        $appointments = $collection->sortByDesc('fecha_registro');

        $asesores = User::all();
        $clientes = Client::all();
        
        return view('intranet.users.integrales', compact('appointments', 'asesores', 'clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'user_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'appointment_date' => 'nullable|date',
            'location_reference' => 'nullable|string|max:255',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'type' => 'required|string',      
            'priority' => 'required|string',  
        ]);

        $validated['registration_date'] = now();
        $validated['is_notified'] = false;

        $appointment = AppointmentTracking::create($validated);

        if ($appointment->priority === 'urgente' || $appointment->type === 'urgente') {
            $advisor = $appointment->user;
            if ($advisor) {
                $advisor->notify(new UrgentAppointmentNotification($appointment));
            }
        }

        return redirect()->back()->with('success', '¡Cita registrada con éxito!');
    }

    public function update(Request $request, $id)
    {
        $appointment = AppointmentTracking::findOrFail($id);

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'user_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'appointment_date' => 'nullable|date',
            'location_reference' => 'nullable|string|max:255',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'type' => 'required|string',
            'priority' => 'required|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('admin.citas-totales')->with('success', '¡Registro actualizado correctamente!');
    }

    // Marcar como gestionado rápidamente
    public function gestionar($id)
    {
        $cita = AppointmentTracking::findOrFail($id);
        $cita->update(['status' => 'Gestionado']);
        
        return redirect()->route('admin.citas-totales')->with('success', 'Registro marcado como gestionado.');
    }

    // Vista de edición
    public function edit($id)
    {
        $cita = AppointmentTracking::findOrFail($id);
        $asesores = User::all();
        $clientes = Client::all();
        
        return view('intranet.users.editar-cita', compact('cita', 'asesores', 'clientes'));
    }

    // Exportar / Ver ficha
    public function exportar($id)
    {
        $cita = AppointmentTracking::with(['client', 'user', 'property'])->findOrFail($id);
        
        return view('intranet.users.ficha-cita', compact('cita'));
    }

    // Eliminar registro integral
    public function destroyIntegral($id)
    {
        $cita = AppointmentTracking::findOrFail($id);
        $cita->delete();

        return redirect()->route('admin.citas-totales')->with('success', 'Registro eliminado correctamente.');
    }

    // Vista para mostrar el formulario de creación manual
    public function create()
    {
        $asesores = User::all();
        $clientes = Client::all();
        
        return view('intranet.users.crear-cita', compact('asesores', 'clientes'));
    }

    // Guardar el registro manual (del botón Nuevo Registro)
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'type'               => 'required|string',
            'location_reference' => 'required|string',
            'priority'           => 'required|string',
            'notes'              => 'nullable|string',
        ]);

        AppointmentTracking::create([
            'client_id'          => $request->client_id ?? null,
            'user_id'            => auth()->id() ?? 1,
            'property_id'        => $request->property_id ?? null,
            'registration_date'  => now(),
            'appointment_date'   => $request->appointment_date ?? now(),
            'is_notified'        => false,
            'location_reference' => $request->location_reference,
            'status'             => 'Pendiente',
            'type'               => $request->type,
            'priority'           => $request->priority,    
            'notes'              => $request->notes,
        ]);

        return redirect()->route('admin.citas-totales')->with('success', '¡Registro creado exitosamente!');
    }
}