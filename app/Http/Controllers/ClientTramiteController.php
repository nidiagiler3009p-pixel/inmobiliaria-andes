<?php

namespace App\Http\Controllers;

use App\Models\ClientTramite;
use App\Models\ClientPortfolioEntry;
use App\Models\Prospect;
use App\Services\ProspectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientTramiteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INICIAR TRÁMITE
    |--------------------------------------------------------------------------
    |
    | Pendiente → En Proceso
    |
    */
    public function iniciar(ClientTramite $clientTramite)
    {
        if ($clientTramite->status !== 'Pendiente') {
            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'Solo los trámites pendientes pueden iniciarse.'
                );
        }

        DB::transaction(function () use ($clientTramite) {

            $clientTramite->update([
                'status' => 'En Proceso',
                'started_at' => now(),
            ]);

            /*
             * También actualizamos el estado operativo
             * del cliente.
             */
            if ($clientTramite->client) {
                $clientTramite->client->update([
                    'status' => 'En Proceso',
                ]);
            }
        });

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'El trámite fue iniciado correctamente.'
            );
    }





    public function finalizarConExito(
    ClientTramite $clientTramite,
    ProspectService $prospectService
) {
    if ($clientTramite->status !== 'En Proceso') {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'Solo los trámites en proceso pueden finalizarse con éxito.'
            );
    }

    $client = $clientTramite->client;

    if (!$client) {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'No se encontró el cliente relacionado.'
            );
    }

    $prospect = null;

    if (!empty($clientTramite->prospect_id)) {
        $prospect = Prospect::find(
            $clientTramite->prospect_id
        );
    }

    if (!$prospect) {
        return redirect()
            ->route('clients.index')
            ->with(
                'error',
                'No se encontró el prospecto relacionado.'
            );
    }

    return DB::transaction(function () use (
        $clientTramite,
        $client,
        $prospect,
        $prospectService
    ) {

        /*
        |--------------------------------------------------------------------------
        | EVITAR ENVÍO DUPLICADO A CONTABILIDAD
        |--------------------------------------------------------------------------
        */

        $accountingExists =
            \App\Models\AccountingTransaction::where(
                'source_type',
                'client_tramite'
            )
            ->where(
                'source_id',
                $clientTramite->id
            )
            ->exists();

        if ($accountingExists) {
            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'Este trámite ya fue enviado a Contabilidad.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR TRANSACCIÓN CONTABLE
        |--------------------------------------------------------------------------
        |
        | tramite_id queda NULL porque esa FK pertenece exclusivamente
        | a la tabla antigua "tramites".
        |
        | El origen real se identifica mediante:
        |
        | source_type = client_tramite
        | source_id   = client_tramites.id
        |
        */

        \App\Models\AccountingTransaction::create([

            'client_id' =>
                $client->id,

            'prospect_id' =>
                $prospect->id,

            'tramite_id' =>
                null,

            'property_id' =>
                null,

            'operation_type' =>
                'Servicio / Trámite',

            'description' =>
                'Operación exitosa desde Clientes / Trámites',

            'gross_income' =>
                0,

            'direct_expenses_total' =>
                0,

            'advisor_commissions_total' =>
                0,

            'general_expenses_prorated' =>
                0,

            'net_profit' =>
                0,

            'status' =>
                'Pendiente',

            'origin_module' =>
                'Clientes / Trámites',

            'source_type' =>
                'client_tramite',

            'source_id' =>
                $clientTramite->id,

            'notes' =>
                'Trámite finalizado con éxito y enviado desde Clientes / Trámites.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CERRAR CLIENT_TRAMITE
        |--------------------------------------------------------------------------
        */

        $clientTramite->update([
            'status' =>
                'Exitoso',

            'finished_at' =>
                now(),

            'result' =>
                'Exitoso',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CERRAR CLIENTE EXITOSAMENTE
        |--------------------------------------------------------------------------
        */

        $client->update([
            'status' =>
                'Cerrado Exitoso',
        ]);

        /*
        |--------------------------------------------------------------------------
        | HISTORIAL
        |--------------------------------------------------------------------------
        */

        $prospectService->addHistory(
            $prospect,
            'Trámite finalizado con éxito',
            'client_tramite',
            $clientTramite->id,
            'En Proceso',
            'Cerrado Exitoso',
            'El trámite terminó con éxito y fue enviado a Contabilidad.',
            auth()->id()
        );

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'El trámite finalizó con éxito y fue enviado a Contabilidad.'
            );
    });
}

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR SIN ÉXITO
    |--------------------------------------------------------------------------
    |
    | En Proceso → Sin Éxito
    | Cliente → Cartera
    |
    */



    public function finalizarSinExito(
        Request $request,
        ClientTramite $clientTramite,
        ProspectService $prospectService
    ) {
        $request->validate([
            'entry_reason' => 'required|string|max:2000',
        ]);

        if ($clientTramite->status !== 'En Proceso') {
            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'Solo los trámites en proceso pueden finalizarse sin éxito.'
                );
        }

        if (empty($clientTramite->prospect_id)) {
            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'El trámite no tiene un prospecto relacionado.'
                );
        }

        $prospect = Prospect::find(
            $clientTramite->prospect_id
        );

        if (!$prospect) {
            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'No se encontró el prospecto relacionado.'
                );
        }

        $client = $clientTramite->client;

        if (!$client) {
            return redirect()
                ->route('clients.index')
                ->with(
                    'error',
                    'No se encontró el cliente relacionado.'
                );
        }

        return DB::transaction(function () use (
            $request,
            $clientTramite,
            $prospect,
            $client,
            $prospectService
        ) {

            /*
            |--------------------------------------------------------------------------
            | 1. CERRAR CLIENT_TRAMITE
            |--------------------------------------------------------------------------
            */

            $clientTramite->update([
                'status' => 'Sin Éxito',
                'finished_at' => now(),
                'result' => 'Sin Éxito',
                'notes' => $request->entry_reason,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. ACTUALIZAR CLIENTE
            |--------------------------------------------------------------------------
            */

            $client->update([
                'status' => 'Seguimiento Pendiente',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 3. ENVIAR A CARTERA
            |--------------------------------------------------------------------------
            |
            | No duplicamos una entrada activa si el prospecto
            | ya se encuentra en Cartera.
            |
            */

            $portfolio = ClientPortfolioEntry::where(
                'prospect_id',
                $prospect->id
            )->first();

            if (!$portfolio) {

                ClientPortfolioEntry::create([
                    'client_id' => $client->id,
                    'prospect_id' => $prospect->id,

                    'source_type' => 'client_tramite',
                    'source_record_id' => $clientTramite->id,

                    'previous_status' => 'En Proceso',

                    'advisor_id' => $client->user_id,

                    'entry_source' => 'Clientes / Trámites',

                    'entry_reason' => $request->entry_reason,

                    'portfolio_status' => 'Seguimiento Pendiente',

                    'notes' =>
                        'Cliente enviado a Cartera desde Clientes / Trámites por cierre sin éxito.',

                    'entered_at' => now(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 4. HISTORIAL
            |--------------------------------------------------------------------------
            */

            $prospectService->addHistory(
                $prospect,
                'Trámite finalizado sin éxito',
                'client_tramite',
                $clientTramite->id,
                'En Proceso',
                'Seguimiento Pendiente',
                $request->entry_reason,
                auth()->id()
            );

            /*
            |--------------------------------------------------------------------------
            | 5. REDIRECCIÓN A CARTERA
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('admin.cartera')
                ->with(
                    'success',
                    'El trámite fue cerrado sin éxito y el cliente fue enviado a Cartera.'
                );
        });
    }
}