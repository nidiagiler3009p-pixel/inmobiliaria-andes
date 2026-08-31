<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. SERVICIOS BÁSICOS
        |--------------------------------------------------------------------------
        */
        $serviciosBasicosId = DB::table('expense_categories')
            ->where('code', 'SERV_BASICOS')
            ->value('id');

        if ($serviciosBasicosId) {
            $this->createSubcategory(
                $serviciosBasicosId,
                'Agua',
                'Pago de consumo de agua potable.'
            );

            $this->createSubcategory(
                $serviciosBasicosId,
                'Luz',
                'Pago de energía eléctrica.'
            );

            $this->createSubcategory(
                $serviciosBasicosId,
                'Otros servicios básicos',
                'Otros pagos relacionados con servicios básicos.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. INTERNET Y TELEFONÍA
        |--------------------------------------------------------------------------
        */
        $internetTelefoniaId = DB::table('expense_categories')
            ->where('code', 'INTERNET_TELEFONO')
            ->value('id');

        if ($internetTelefoniaId) {
            $this->createSubcategory(
                $internetTelefoniaId,
                'Internet',
                'Planes y servicios de internet.'
            );

            $this->createSubcategory(
                $internetTelefoniaId,
                'Telefonía',
                'Planes y servicios de telefonía fija o móvil.'
            );

            $this->createSubcategory(
                $internetTelefoniaId,
                'Otros servicios de comunicación',
                'Otros gastos relacionados con comunicación.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. ALQUILER
        |--------------------------------------------------------------------------
        */
        $alquilerId = DB::table('expense_categories')
            ->where('code', 'ALQUILER')
            ->value('id');

        if ($alquilerId) {
            $this->createSubcategory(
                $alquilerId,
                'Arriendo de oficina',
                'Pago de arriendo de oficina o establecimiento.'
            );

            $this->createSubcategory(
                $alquilerId,
                'Otros alquileres',
                'Otros pagos relacionados con alquileres.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. PUBLICIDAD Y MARKETING
        |--------------------------------------------------------------------------
        */
        $publicidadId = DB::table('expense_categories')
            ->where('code', 'PUBLICIDAD')
            ->value('id');

        if ($publicidadId) {
            /*
             * Ya existen:
             * - Meta Ads
             * - Radio
             * - Material promocional
             *
             * No los eliminamos para conservar cualquier relación existente.
             */

            $this->createSubcategory(
                $publicidadId,
                'Tarjetas de presentación',
                'Diseño e impresión de tarjetas de presentación.'
            );

            $this->createSubcategory(
                $publicidadId,
                'Publicidad general',
                'Publicidad institucional o comercial general.'
            );

            $this->createSubcategory(
                $publicidadId,
                'Open House / Eventos',
                'Organización y promoción de Open House, ferias y eventos.'
            );

            $this->createSubcategory(
                $publicidadId,
                'Redes sociales / Influencer',
                'Publicidad pagada en redes sociales, creadores de contenido o influencers.'
            );

            $this->createSubcategory(
                $publicidadId,
                'Banners / Pancartas',
                'Diseño, impresión o instalación de banners y pancartas.'
            );

            $this->createSubcategory(
                $publicidadId,
                'Otros gastos de publicidad',
                'Otros gastos relacionados con publicidad y marketing.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. ADMINISTRATIVO
        |--------------------------------------------------------------------------
        */
        $administrativoId = DB::table('expense_categories')
            ->where('code', 'ADMIN')
            ->value('id');

        if ($administrativoId) {
            /*
             * IMPORTANTE:
             * Secretaría ya existe y tiene movimientos contables.
             * No se elimina ni se reemplaza.
             */

            $this->createSubcategory(
                $administrativoId,
                'Personal administrativo',
                'Pagos de personal administrativo de la empresa.'
            );

            $this->createSubcategory(
                $administrativoId,
                'Suministros de oficina',
                'Papelería, impresiones y otros suministros de oficina.'
            );

            $this->createSubcategory(
                $administrativoId,
                'Uniformes e indumentaria',
                'Uniformes, prendas e indumentaria utilizada por el personal.'
            );

            $this->createSubcategory(
                $administrativoId,
                'Trámites',
                'Pagos administrativos relacionados con trámites.'
            );

            $this->createSubcategory(
                $administrativoId,
                'Asesorías',
                'Pagos por asesorías profesionales.'
            );

            $this->createSubcategory(
                $administrativoId,
                'Legal',
                'Servicios y gastos legales.'
            );

            $this->createSubcategory(
                $administrativoId,
                'Otros gastos administrativos',
                'Otros gastos administrativos no clasificados.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. TRANSPORTE / MOVILIZACIÓN
        |--------------------------------------------------------------------------
        */
        $transporteId = DB::table('expense_categories')
            ->where('code', 'TRANSPORTE')
            ->value('id');

        if ($transporteId) {
            /*
             * Combustible y Parqueadero ya existen.
             * Combustible ya tiene un movimiento real registrado,
             * por lo que conservamos exactamente esa subcategoría.
             */

            $this->createSubcategory(
                $transporteId,
                'Mantenimiento de vehículo',
                'Mantenimiento preventivo y correctivo del vehículo.'
            );

            $this->createSubcategory(
                $transporteId,
                'Llantas',
                'Compra, reparación o mantenimiento de llantas.'
            );

            $this->createSubcategory(
                $transporteId,
                'Matrícula',
                'Pago de matrícula y valores relacionados con matriculación vehicular.'
            );

            $this->createSubcategory(
                $transporteId,
                'Seguro',
                'Pago de seguro del vehículo.'
            );

            $this->createSubcategory(
                $transporteId,
                'Lubricantes / Aceite',
                'Aceites, lubricantes y productos relacionados con el vehículo.'
            );

            $this->createSubcategory(
                $transporteId,
                'Reparaciones',
                'Reparaciones mecánicas, eléctricas u otras reparaciones del vehículo.'
            );

            $this->createSubcategory(
                $transporteId,
                'Otros gastos de movilización',
                'Otros gastos relacionados con vehículo, transporte o movilización.'
            );
        }
    }

    /**
     * Crea una subcategoría únicamente si todavía no existe.
     */
    private function createSubcategory(
        int $categoryId,
        string $name,
        string $description
    ): void {
        $exists = DB::table('expense_subcategories')
            ->where('expense_category_id', $categoryId)
            ->where('name', $name)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('expense_subcategories')->insert([
            'expense_category_id' => $categoryId,
            'name' => $name,
            'code' => null,
            'description' => $description,
            'budget_method' => 'Ninguno',
            'budget_percentage' => null,
            'fixed_budget_amount' => null,
            'is_budgeted' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * No eliminamos automáticamente estas subcategorías.
         *
         * Después de ejecutar esta migración podrían existir movimientos
         * contables relacionados con ellas. Eliminarlas durante un rollback
         * podría romper el historial de gastos.
         */
    }
};