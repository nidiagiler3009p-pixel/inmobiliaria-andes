<?php

namespace App\Http\Controllers;

use App\Models\AdvisoryRequest;
use App\Services\ProspectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdvisoryRequestController extends Controller
{
    public function store(Request $request, ProspectService $prospectService) {
        $validated = $request->validate([
            'plan_type' => 'required|in:Gratis,Estándar,Total',
            'advisor_id' => 'nullable|exists:users,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'ciudad' => 'required|string|max:100',
            'discovery_channel' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:100',
            'property_location' => 'nullable|string|max:255',
            'estimated_price' => 'nullable|numeric',
            'property_details' => 'nullable|string',
            'preferences_notes' => 'nullable|string',
            'accepted_terms' => 'required|accepted',
        ]);

        $advisory = DB::transaction(function () use ($validated, $prospectService) {
            $fullName = trim($validated['full_name']);
            $parts = preg_split('/\s+/', $fullName, 2);
            $name = $parts[0] ?? 'Sin nombre';
            $lastName = $parts[1] ?? null;
            $prospect = $prospectService->findOrCreate($name, $lastName, $validated['phone'], $validated['email'] ?? null, null, 'Asesorías');
            $advisory = AdvisoryRequest::create([
                'prospect_id' => $prospect->id,
                'plan_type' => $validated['plan_type'],
                'advisor_id' => $validated['advisor_id'] ?? null,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'ciudad' => $validated['ciudad'],
                'discovery_channel' => $validated['discovery_channel'] ?? null,
                'property_type' => $validated['property_type'] ?? null,
                'property_location' => $validated['property_location'] ?? null,
                'estimated_price' => $validated['estimated_price'] ?? null,
                'property_details' => $validated['property_details'] ?? null,
                'preferences_notes' => $validated['preferences_notes'] ?? null,
                'accepted_terms' => true,
                'status' => 'Pendiente',
            ]);
            $descripcion = 'El prospecto realizó una solicitud de asesoría. Plan: ' . $advisory->plan_type . '.';
            if (!empty($advisory->property_type)) $descripcion .= ' Tipo de propiedad: ' . $advisory->property_type . '.';
            if (!empty($advisory->property_location)) $descripcion .= ' Ubicación: ' . $advisory->property_location . '.';
            if (!empty($advisory->property_details)) $descripcion .= ' Detalles: ' . $advisory->property_details;
            $prospectService->addHistory($prospect, 'Solicitud de asesoría', 'advisory', $advisory->id, null, 'Pendiente', $descripcion, $advisory->advisor_id);
            return $advisory;
        });

        try {
            $correosDestino = ['inmobiliarialosandesecuador@gmail.com'];
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
                $message->to($correosDestino)->subject('Nueva Solicitud de Asesoría - Plan ' . $advisory->plan_type);
            });
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de asesoría: ' . $e->getMessage());
        }

        return back()->with('success', '¡Tu solicitud de asesoría ha sido registrada correctamente!');
    }
}