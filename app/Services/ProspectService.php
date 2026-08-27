<?php

namespace App\Services;

use App\Models\Prospect;
use App\Models\ProspectHistory;

class ProspectService
{
    /**
     * Busca un prospecto existente o crea uno nuevo.
     */
    public function findOrCreate(
        ?string $name,
        ?string $lastName,
        ?string $phone,
        ?string $email,
        ?string $identification,
        string $source
    ): Prospect {

        $phone = $this->normalizePhone($phone);
        $email = $email ? strtolower(trim($email)) : null;
        $identification = $identification
            ? trim($identification)
            : null;

        /*
        |--------------------------------------------------------------------------
        | 1. BUSCAR POR IDENTIFICACIÓN
        |--------------------------------------------------------------------------
        */

        $prospect = null;

        if (!empty($identification)) {
            $prospect = Prospect::where(
                'identification',
                $identification
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | 2. BUSCAR POR TELÉFONO
        |--------------------------------------------------------------------------
        */

        if (!$prospect && !empty($phone)) {
            $prospect = Prospect::where(
                'phone',
                $phone
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | 3. BUSCAR POR EMAIL
        |--------------------------------------------------------------------------
        */

        if (!$prospect && !empty($email)) {
            $prospect = Prospect::where(
                'email',
                $email
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | 4. SI NO EXISTE → CREAR
        |--------------------------------------------------------------------------
        */

        if (!$prospect) {

            $prospect = Prospect::create([
                'name' => $name ?: 'Sin nombre',
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'identification' => $identification,
                'status' => 'Prospecto',
                'first_source' => $source,
            ]);

            return $prospect;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. COMPLETAR INFORMACIÓN FALTANTE
        |--------------------------------------------------------------------------
        |
        | Si Juan primero ingresó solamente con teléfono y después
        | proporciona correo o identificación, completamos su ficha.
        |
        */

        $changed = false;

        if (empty($prospect->name) && !empty($name)) {
            $prospect->name = $name;
            $changed = true;
        }

        if (empty($prospect->last_name) && !empty($lastName)) {
            $prospect->last_name = $lastName;
            $changed = true;
        }

        if (empty($prospect->phone) && !empty($phone)) {
            $prospect->phone = $phone;
            $changed = true;
        }

        if (empty($prospect->email) && !empty($email)) {
            $prospect->email = $email;
            $changed = true;
        }

        if (
            empty($prospect->identification)
            && !empty($identification)
        ) {
            $prospect->identification = $identification;
            $changed = true;
        }

        if ($changed) {
            $prospect->save();
        }

        return $prospect;
    }


    /**
     * Guarda una interacción en el historial.
     */
    public function addHistory(
        Prospect $prospect,
        string $eventType,
        ?string $sourceType = null,
        ?int $sourceRecordId = null,
        ?string $previousStatus = null,
        ?string $newStatus = null,
        ?string $description = null,
        ?int $userId = null
    ): ProspectHistory {

        return ProspectHistory::create([
            'prospect_id' => $prospect->id,
            'event_type' => $eventType,
            'source_type' => $sourceType,
            'source_record_id' => $sourceRecordId,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'description' => $description,
            'user_id' => $userId,
        ]);
    }


    /**
     * Normaliza el teléfono para mejorar la detección.
     */
    private function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        return !empty($phone)
            ? $phone
            : null;
    }
}