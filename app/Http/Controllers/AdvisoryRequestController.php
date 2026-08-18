<?php

namespace App\Http\Controllers;

use App\Models\AdvisoryRequest;
use App\Models\AppointmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdvisoryRequestController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar los datos del formulario de asesorías
        $validated = $request->validate([
            'plan_type'         => 'required|in:Gratis,Estándar,Total',
            'advisor_id'        => 'nullable|exists:users,id',
            'full_name'         => 'required|string|max:255',
            'email'             => 'nullable|email|max:255', // Ahora es opcional
            'phone'             => 'required|string|max:50',
            'ciudad'            => 'required|string|max:100',
            'discovery_channel' => 'nullable|string|max:255',
            'property_type'     => 'nullable|string|max:100',
            'property_location' => 'nullable|string|max:255',
            'estimated_price'   => 'nullable|numeric',
            'property_details'  => 'nullable|string',
            'preferences_notes' => 'nullable|string',
            'accepted_terms'    => 'required|accepted',
        ]);

        // 2. Guardar en la Base de Datos
        $advisory = AdvisoryRequest::create([
            'plan_type'         => $validated['plan_type'],
            'advisor_id'        => $validated['advisor_id'] ?? null,
            'full_name'         => $validated['full_name'],
            'email'             => $validated['email'] ?? null,
            'phone'             => $validated['phone'],
            'ciudad'            => $validated['ciudad'],
            'discovery_channel' => $validated['discovery_channel'] ?? null,
            'property_type'     => $validated['property_type'] ?? null,
            'property_location' => $validated['property_location'] ?? null,
            'estimated_price'   => $validated['estimated_price'] ?? null,
            'property_details'  => $validated['property_details'] ?? null,
            'preferences_notes' => $validated['preferences_notes'] ?? null,
            'accepted_terms'    => true,
            'status'            => 'Pendiente',
        ]);

        // 3. Crear el registro espejo en la tabla centralizadora
        AppointmentTracking::create([
            'user_id'            => $advisory->advisor_id ?? 1, // Asignación por defecto o al asesor seleccionado
            'type'               => 'asesoria',
            'source_channel'     => 'Web - Asesorías (Plan ' . $advisory->plan_type . ')',
            'location_reference' => $advisory->property_location ?? $advisory->ciudad,
            'registration_date'  => now(),
            'appointment_date'   => now(),
            'status'             => 'Pendiente',
            'priority'           => 'normal',
            'notes'              => 'Cliente: ' . $advisory->full_name . ' | Tel: ' . $advisory->phone . 
                                    ' | Detalles: ' . ($advisory->property_details ?? 'Sin detalles'),
        ]);

        // 4. Enviar el correo de notificación a la inmobiliaria de manera segura
        try {
            $correosDestino = [
                'inmobiliarialosandesecuador@gmail.com'
            ];

            $mensajeTexto = "Se ha recibido una nueva solicitud de asesoría desde la web:\n\n" .
                "• Plan Elegido: {$advisory->plan_type}\n" .
                "• Cliente: {$advisory->full_name}\n" .
                "• Correo: " . ($advisory->email ?? 'No proporcionado') . "\n" .
                "• Teléfono: {$advisory->phone}\n" .
                "• Ciudad: {$advisory->ciudad}\n" .
                "• Tipo de Propiedad: " . ($advisory->property_type ?? 'No especificado') . "\n" .
                "• Ubicación: " . ($advisory->property_location ?? 'No especificada') . "\n" .
                "• Precio Estimado: $" . ($advisory->estimated_price ?? 'No especificado') . "\n" .
                "• Detalles: " . ($advisory->property_details ?? 'Ninguno') . "\n" .
                "• Notas adicionales: " . ($advisory->preferences_notes ?? 'Ninguna');

            Mail::raw($mensajeTexto, function ($message) use ($correosDestino, $advisory) {
                $message->to($correosDestino)
                        ->subject('Nueva Solicitud de Asesoría - Plan ' . $advisory->plan_type);
            });
        } catch (\Throwable $e) {
            // Registrar error de envío en los logs de Laravel sin detener la experiencia del usuario
            Log::error('Error al enviar correo de asesoría: ' . $e->getMessage());
        }

        // 5. Redireccionar con mensaje de éxito
        return back()->with('success', '¡Tu solicitud de asesoría ha sido registrada correctamente!');
    }
}