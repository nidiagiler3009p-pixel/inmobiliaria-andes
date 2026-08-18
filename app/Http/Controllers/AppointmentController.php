<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentTracking;
use App\Models\User;
use App\Models\Client;
use App\Models\Contact;
use App\Models\AdvisoryRequest;
use App\Models\Tramite;
use App\Models\Property;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Muestra la agenda personal del asesor autenticado.
     */
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

    /**
     * Muestra el panel general de Gestión de Citas con filtros aplicados.
     */
    public function gestionCitas(Request $request)
    {
        $query = AppointmentTracking::with(['client', 'user', 'property']);

        // Filtro por Asesor / Usuario
        if ($request->filled('advisor_id')) {
            $query->where('user_id', $request->advisor_id);
        }

        // Filtro por Propiedad
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filtro por Canal de Captación
        if ($request->filled('channel')) {
            $query->where('source_channel', $request->channel);
        }

        // Filtro por Prioridad
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtro por Estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por Rango de Fechas
        if ($request->filled('desde')) {
            $query->whereDate('appointment_date', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('appointment_date', '<=', $request->hasta);
        }

        $appointments = $query->latest()->get();

        $asesores = User::all();
        $clientes = Client::all();
        
        // Propiedades para selector de filtro
        $propiedades = Property::select('id', 'title')->get();

        return view('intranet.users.gestion-citas', compact('appointments', 'asesores', 'clientes', 'propiedades'));
    }

    /**
     * Actualiza el estado de una cita y procesa la cancelación / rescate a cartera.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $cita = AppointmentTracking::with('client')->find($id);

        if (!$cita) {
            return redirect()->back()->with('error', 'La cita solicitada no existe.');
        }
        
        $request->validate([
            'status' => 'required|string|in:Pendiente,Agendado,Confirmada,Cancelado,Realizado',
            'cancellation_reason' => 'required_if:status,Cancelado|nullable|string|max:500',
            'rescue_to_portfolio' => 'nullable|boolean',
        ]);

        $cita->status = $request->status;

        if ($request->status === 'Cancelado') {
            $cita->cancellation_reason = $request->cancellation_reason;
            $cita->rescued_to_portfolio = $request->has('rescue_to_portfolio') && $request->rescue_to_portfolio;
            $cita->cancelled_at = now();

            // Rescate a Cartera General
            if ($cita->rescued_to_portfolio) {
                if (!$cita->client_id) {
                    $cliente = Client::create([
                        'first_name' => $cita->client_name ?? 'Prospecto Cita',
                        'last_name'  => 'Cita Cancelada #' . $cita->id,
                        'phone'      => $cita->phone ?? 'N/A',
                        'email'      => $cita->email ?? null,
                        'address'    => $cita->location_reference ?? 'Riobamba / Guano / Quito',
                        'status'     => 'Cartera / Prospecto',
                        'notes'      => 'Rescatado desde Cita Cancelada. Motivo: ' . $request->cancellation_reason,
                    ]);
                    $cita->client_id = $cliente->id;
                } else {
                    if ($cita->client) {
                        $cita->client->update([
                            'notes' => trim(($cita->client->notes ? $cita->client->notes . ' | ' : '') . 
                                       'Cita Cancelada: ' . $request->cancellation_reason)
                        ]);
                    }
                }
            }
        } else {
            // Limpiar datos si cambia de Cancelado a otro estado
            $cita->cancellation_reason = null;
            $cita->rescued_to_portfolio = false;
            $cita->cancelled_at = null;
        }

        $cita->save();

        return redirect()->back()->with('success', 'El estado de la cita ha sido actualizado correctamente.');
    }

    /**
     * Bandeja Integral unificada (Contacts, AdvisoryRequests y Tramites).
     */
    public function integrales(Request $request)
    {
        $filtro = $request->get('filtro');
        $statusFiltro = $request->get('status');
        $collection = collect();

        // 1. Contáctanos
        if (!$filtro || $filtro === 'todos' || $filtro === 'contacto') {
            $query = Contact::query();
            if ($statusFiltro) {
                $query->where('status', $statusFiltro);
            } else {
                $query->where('status', '!=', 'eliminado');
            }
        
            $contactos = $query->get()->map(function($item) {
                $obj = new \stdClass();
                $obj->id = 'contact_' . $item->id;
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

        // 2. Asesorías
        if (!$filtro || $filtro === 'todos' || $filtro === 'asesoria') {
            $query = AdvisoryRequest::query();
            if ($statusFiltro) {
                $query->where('status', $statusFiltro);
            } else {
                $query->where('status', '!=', 'eliminado');
            }

            $asesorias = $query->get()->map(function($item) {
                $obj = new \stdClass();
                $obj->id = 'advisory_' . $item->id;
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

        // 3. Trámites
        if (!$filtro || $filtro === 'todos' || $filtro === 'tramite') {
            $query = Tramite::query();
            if ($statusFiltro) {
                $query->where('status', $statusFiltro);
            } else {
                $query->where('status', '!=', 'eliminado');
            }

            $tramites = $query->get()->map(function($item) {
                $obj = new \stdClass();
                $obj->id = 'tramite_' . $item->id;
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

        // Historial / Papelera
        $recicladosCollection = collect();

        if (Schema::hasColumn('contacts', 'status')) {
            $contactosTrash = Contact::where('status', 'eliminado')->get()->map(function($item) {
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
            $asesoriasTrash = AdvisoryRequest::where('status', 'eliminado')->get()->map(function($item) {
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
            $tramitesTrash = Tramite::where('status', 'eliminado')->get()->map(function($item) {
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
        $asesores = User::all();
        $clientes = Client::all();

        return view('intranet.users.integrales', compact('appointments', 'asesores', 'clientes', 'citasRecicladas'));
    }

    /**
     * Guarda manualmente una cita en AppointmentTracking.
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'type'               => 'required|string',
            'location_reference' => 'required|string',
            'priority'           => 'required|string',
            'notes'              => 'nullable|string',
            'source_channel'     => 'nullable|string',
        ]);

        AppointmentTracking::create([
            'client_id'          => $request->client_id ?? null,
            'user_id'            => $request->user_id ?? auth()->id() ?? 1,
            'property_id'        => $request->property_id ?? null,
            'registration_date'  => now(),
            'appointment_date'   => $request->appointment_date ?? now(),
            'is_notified'        => false,
            'location_reference' => $request->location_reference,
            'status'             => 'Pendiente',
            'type'               => $request->type,
            'priority'           => $request->priority,
            'source_channel'     => $request->source_channel ?? 'Directo',
            'notes'              => $request->notes,
        ]);

        return redirect()->back()->with('success', '¡Cita registrada exitosamente!');
    }

    /**
     * Guarda registros provenientes de la Bandeja Unificada.
     */
    public function storeIntegral(Request $request)
    {
        $tipo = $this->normalizarTipo($request->input('tipo_registro', $request->input('nuevo_tipo')));
        $direccionComun = $request->input('general_address')
                       ?? $request->input('ciudad')
                       ?? $request->input('ubicacion')
                       ?? 'N/A';

        if ($tipo === 'contacto') {
            $request->validate(['phone' => 'required|string']);
            $nombreEntrada = $request->name ?? $request->full_name ?? $request->first_name ?? 'Sin nombre';
            $parts = explode(' ', trim($nombreEntrada), 2);
            $firstName = $parts[0] ?? 'Sin nombre';
            $lastName = $parts[1] ?? 'Sin apellido';
            
            Contact::create([
                'name'                 => $firstName,
                'last_name'            => $lastName,
                'phone'                => $request->phone,
                'general_address'      => $direccionComun,
                'requirements_message' => $request->message ?? $request->requirements_message ?? $request->property_details ?? '',
                'status'               => $request->status ?? 'Pendiente',
            ]);
        } elseif ($tipo === 'asesoria') {
            $request->validate(['phone' => 'required|string']);
            $nombreEntrada = $request->full_name ?? $request->name ?? $request->first_name ?? 'Sin nombre';
            
            AdvisoryRequest::create([
                'full_name'        => $nombreEntrada,
                'email'            => $request->email ?? null,
                'phone'            => $request->phone,
                'plan_type'        => $request->plan_type ?? 'Gratis',
                'ciudad'           => $direccionComun,
                'property_type'    => $request->property_type ?? 'Casa',
                'estimated_price'  => $request->estimated_price ?? null,
                'property_details' => $request->property_details ?? $request->requirements_message ?? $request->message ?? '',
                'accepted_terms'   => 1,
                'status'           => $request->status ?? 'Pendiente',
            ]);
        } elseif ($tipo === 'tramite') {
            $request->validate(['phone' => 'required|string']);
            $nombreEntrada = $request->first_name ?? $request->name ?? $request->full_name ?? 'Sin nombre';
            $parts = explode(' ', trim($nombreEntrada), 2);
            $firstName = $parts[0] ?? 'Sin nombre';
            $lastName = $parts[1] ?? 'Sin apellido';

            Tramite::create([
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'email'               => $request->email ?? 'sin-correo@inmobiliarialosandes.com',
                'phone'               => $request->phone,
                'identification_card' => $request->identification_card ?? '0000000000',
                'subject'             => $request->subject ?? 'Trámite General',
                'tramite_type'        => $request->tramite_type ?? 'Otros trámites',
                'ubicacion'           => $direccionComun,
                'message'             => $request->message ?? $request->tramite_detalle ?? $request->requirements_message ?? '',
                'status'              => $request->status ?? 'Pendiente',
            ]);
        }

        return redirect()->back()->with('success', '¡Registro procesado exitosamente!');
    }

    /**
     * Muestra la vista de edición con fallback seguro en lugar de pantalla 404.
     */
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

        // Si el registro no se encuentra en la base de datos
        if (!$item) {
            return redirect()->route('admin.citas-totales')
                ->with('error', 'El registro solicitado no existe o fue eliminado permanentemente.');
        }

        $asesores = User::all();
        $clientes = Client::all();
        
        return view('intranet.users.editar-integral', compact('item', 'tipo', 'asesores', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        return $this->updateIntegral($request, $id);
    }

    public function updateIntegral(Request $request, $id)
    {
        // 1. Extraer ID numérico
        $cleanId = preg_replace('/[^0-9]/', '', $id);

        // 2. Normalizar tipo de origen y nuevo tipo (elimina tildes, mayúsculas y espacios)
        $tipoOrigen = $this->normalizarTipo($request->input('tipo_origen'), $id);
        $nuevoTipo  = $this->normalizarTipo($request->input('nuevo_tipo', $request->input('tipo_registro')));

        // 3. PROCESO DE TRANSMUTACIÓN (si cambió de tabla)
        if ($tipoOrigen && $nuevoTipo && $tipoOrigen !== $nuevoTipo && $tipoOrigen !== 'cita') {
            
            return DB::transaction(function () use ($request, $tipoOrigen, $nuevoTipo, $cleanId) {
                // A) Eliminar físicamente el registro original de su tabla de origen
                if ($tipoOrigen === 'contacto') {
                    Contact::where('id', $cleanId)->delete();
                    DB::table('contacts')->where('id', $cleanId)->delete();
                } elseif ($tipoOrigen === 'asesoria') {
                    AdvisoryRequest::where('id', $cleanId)->delete();
                    DB::table('advisory_requests')->where('id', $cleanId)->delete();
                } elseif ($tipoOrigen === 'tramite') {
                    Tramite::where('id', $cleanId)->delete();
                    DB::table('tramites')->where('id', $cleanId)->delete();
                }

                // B) Crear el registro en la nueva tabla destino
                $request->merge(['tipo_registro' => $nuevoTipo]);
                $this->storeIntegral($request);

                return redirect()->route('admin.citas-totales')
                    ->with('success', '¡Registro transmutado y actualizado correctamente!');
            });
        }

        // 4. ACTUALIZACIÓN REGULAR (si se mantiene en el mismo tipo)
        if ($tipoOrigen === 'contacto' || str_starts_with($id, 'contact_')) {
            $item = Contact::find($cleanId);
            if ($item) {
                $nombreCompleto = $request->name ?? $request->full_name ?? $item->name;
                $parts = explode(' ', trim($nombreCompleto), 2);

                $item->update([
                    'name'                 => $parts[0] ?? '',
                    'last_name'            => $parts[1] ?? ($item->last_name ?? 'Sin apellido'),
                    'phone'                => $request->phone ?? $item->phone,
                    'general_address'      => $request->general_address ?? $request->ciudad ?? $request->ubicacion ?? $item->general_address,
                    'requirements_message' => $request->requirements_message ?? $request->message ?? $request->property_details ?? $item->requirements_message,
                    'status'               => $request->status ?? $item->status,
                ]);
            }
        } elseif ($tipoOrigen === 'asesoria' || str_starts_with($id, 'advisory_')) {
            $item = AdvisoryRequest::find($cleanId);
            if ($item) {
                $item->update([
                    'full_name'        => $request->full_name ?? $request->name ?? $item->full_name,
                    'phone'            => $request->phone ?? $item->phone,
                    'plan_type'        => $request->plan_type ?? $item->plan_type,
                    'ciudad'           => $request->ciudad ?? $request->general_address ?? $request->ubicacion ?? $item->ciudad,
                    'property_type'    => $request->property_type ?? $item->property_type,
                    'estimated_price'  => $request->estimated_price ?? $item->estimated_price,
                    'property_details' => $request->property_details ?? $request->requirements_message ?? $request->message ?? $item->property_details,
                    'status'           => $request->status ?? $item->status,
                ]);
            }
        } elseif ($tipoOrigen === 'tramite' || str_starts_with($id, 'tramite_')) {
            $item = Tramite::find($cleanId);
            if ($item) {
                $nombreCompleto = $request->first_name ?? $request->name ?? $item->first_name;
                $parts = explode(' ', trim($nombreCompleto), 2);

                $item->update([
                    'first_name'          => $parts[0] ?? '',
                    'last_name'           => $parts[1] ?? ($item->last_name ?? 'Sin apellido'),
                    'phone'               => $request->phone ?? $item->phone,
                    'identification_card' => $request->identification_card ?? $item->identification_card,
                    'subject'             => $request->subject ?? $item->subject,
                    'tramite_type'        => $request->tramite_type ?? $item->tramite_type,
                    'ubicacion'           => $request->ubicacion ?? $request->general_address ?? $request->ciudad ?? $item->ubicacion,
                    'message'             => $request->message ?? $request->requirements_message ?? $request->property_details ?? $item->message,
                    'status'              => $request->status ?? $item->status,
                ]);
            }
        } else {
            $item = AppointmentTracking::find($cleanId);
            if ($item) {
                $item->update($request->only([
                    'client_id', 'user_id', 'property_id', 'appointment_date', 
                    'location_reference', 'status', 'type', 'priority', 'notes',
                    'source_channel', 'cancellation_reason', 'rescued_to_portfolio'
                ]));
            }
        }

        return redirect()->route('admin.citas-totales')->with('success', '¡Registro actualizado correctamente!');
    }

    public function gestionar($id)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        $item = null;

        if (str_starts_with($id, 'contact_')) {
            $item = Contact::find($cleanId);
        } elseif (str_starts_with($id, 'advisory_')) {
            $item = AdvisoryRequest::find($cleanId);
        } elseif (str_starts_with($id, 'tramite_')) {
            $item = Tramite::find($cleanId);
        } else {
            $item = AppointmentTracking::find($cleanId);
        }

        if (!$item) {
            return redirect()->back()->with('error', 'No se encontró el registro para actualizar estado.');
        }

        $estadoActual = (empty($item->status) || $item->status == 'Nuevo') ? 'Pendiente' : $item->status;

        if ($estadoActual == 'Pendiente') {
            $item->status = 'En Proceso';
        } elseif ($estadoActual == 'En Proceso') {
            $item->status = 'Completado';
        } else {
            $item->status = 'Pendiente';
        }

        $item->save();

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }

    public function exportar($id)
    {
        $cita = AppointmentTracking::with(['client', 'user', 'property'])->find($id);
        
        if (!$cita) {
            return redirect()->back()->with('error', 'No se encontró la ficha de la cita.');
        }

        return view('intranet.users.ficha-cita', compact('cita'));
    }

    public function create()
    {
        $asesores = User::all();
        $clientes = Client::all();
        return view('intranet.users.create', compact('asesores', 'clientes'));
    }

    public function destroyIntegral($id)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);

        try {
            if (str_starts_with($id, 'contact_')) {
                $model = Contact::find($cleanId);
            } elseif (str_starts_with($id, 'advisory_')) {
                $model = AdvisoryRequest::find($cleanId);
            } elseif (str_starts_with($id, 'tramite_')) {
                $model = Tramite::find($cleanId);
            } else {
                $model = AppointmentTracking::find($cleanId);
            }

            if (!$model) {
                return redirect()->back()->with('error', 'El registro ya no existe.');
            }

            $model->status = 'eliminado';
            $model->save();

            return redirect()->back()->with('success', 'El registro ha sido reciclado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar reciclar el dato: ' . $e->getMessage());
        }
    }

    public function restaurar($id)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);

        try {
            if (str_starts_with($id, 'contact_')) {
                $model = Contact::find($cleanId);
            } elseif (str_starts_with($id, 'advisory_')) {
                $model = AdvisoryRequest::find($cleanId);
            } elseif (str_starts_with($id, 'tramite_')) {
                $model = Tramite::find($cleanId);
            } else {
                $model = AppointmentTracking::find($cleanId);
            }

            if (!$model) {
                return redirect()->back()->with('error', 'El registro no se encuentra para restaurar.');
            }

            $model->status = 'Pendiente';
            $model->save();

            return redirect()->back()->with('success', 'El registro ha sido restaurado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar restaurar el registro: ' . $e->getMessage());
        }
    }

    public function forzarEliminar($id)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);

        try {
            if (str_starts_with($id, 'contact_')) {
                $model = Contact::find($cleanId);
            } elseif (str_starts_with($id, 'advisory_')) {
                $model = AdvisoryRequest::find($cleanId);
            } elseif (str_starts_with($id, 'tramite_')) {
                $model = Tramite::find($cleanId);
            } else {
                $model = AppointmentTracking::find($cleanId);
            }

            if (!$model) {
                return redirect()->back()->with('error', 'El registro no existe o ya fue eliminado.');
            }

            $model->delete();

            return redirect()->back()->with('success', 'El registro ha sido eliminado permanentemente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar permanentemente: ' . $e->getMessage());
        }
    }

    /**
     * Función auxiliar para estandarizar los tipos de datos recibidos y prevenir errores.
     */
    private function normalizarTipo($cadena, $id = '')
    {
        $cadena = strtolower(trim($cadena ?? ''));
        $cadena = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $cadena);

        if (str_contains($cadena, 'contact')) return 'contacto';
        if (str_contains($cadena, 'asesor')) return 'asesoria';
        if (str_contains($cadena, 'tramite')) return 'tramite';
        if (str_contains($cadena, 'cita')) return 'cita';

        if (!empty($id)) {
            if (str_starts_with($id, 'contact_')) return 'contacto';
            if (str_starts_with($id, 'advisory_')) return 'asesoria';
            if (str_starts_with($id, 'tramite_')) return 'tramite';
        }

        return $cadena;
    }
}