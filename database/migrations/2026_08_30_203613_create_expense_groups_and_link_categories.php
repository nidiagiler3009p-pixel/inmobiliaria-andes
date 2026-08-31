<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Crear tabla de grupos de gasto
        |--------------------------------------------------------------------------
        */
        Schema::create('expense_groups', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);

            $table->string('code', 80)->unique();

            $table->string('description', 500)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 2. Relacionar categorías con grupos
        |--------------------------------------------------------------------------
        */
        Schema::table('expense_categories', function (Blueprint $table) {

            $table->foreignId('expense_group_id')
                ->nullable()
                ->after('id')
                ->constrained('expense_groups')
                ->nullOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | 3. Crear grupos actuales
        |--------------------------------------------------------------------------
        */
        $now = now();

        DB::table('expense_groups')->insert([
            [
                'name' => 'Publicidad',
                'code' => 'PUBLICIDAD',
                'description' => 'Gastos relacionados con publicidad, marketing, promoción y contenido audiovisual.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Administrativos',
                'code' => 'ADMINISTRATIVOS',
                'description' => 'Gastos administrativos y de funcionamiento interno.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Gastos generales',
                'code' => 'GENERALES',
                'description' => 'Gastos generales necesarios para el funcionamiento de la inmobiliaria.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Movilización',
                'code' => 'MOVILIZACION',
                'description' => 'Gastos relacionados con transporte, vehículos y desplazamientos.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 4. Obtener IDs de los grupos
        |--------------------------------------------------------------------------
        */
        $publicidadId = DB::table('expense_groups')
            ->where('code', 'PUBLICIDAD')
            ->value('id');

        $administrativosId = DB::table('expense_groups')
            ->where('code', 'ADMINISTRATIVOS')
            ->value('id');

        $generalesId = DB::table('expense_groups')
            ->where('code', 'GENERALES')
            ->value('id');

        $movilizacionId = DB::table('expense_groups')
            ->where('code', 'MOVILIZACION')
            ->value('id');


        /*
        |--------------------------------------------------------------------------
        | 5. PUBLICIDAD
        |--------------------------------------------------------------------------
        */
        DB::table('expense_categories')
            ->whereIn('code', [
                'PUBLICIDAD',
                'FOTO_DRONE',
            ])
            ->update([
                'expense_group_id' => $publicidadId,
            ]);


        /*
        |--------------------------------------------------------------------------
        | 6. ADMINISTRATIVOS
        |--------------------------------------------------------------------------
        */
        DB::table('expense_categories')
            ->whereIn('code', [
                'ADMIN',
                'PAPELERIA',
                'SOFTWARE',
                'DOCUMENTACION',
                'HONORARIOS',
            ])
            ->update([
                'expense_group_id' => $administrativosId,
            ]);


        /*
        |--------------------------------------------------------------------------
        | 7. GASTOS GENERALES
        |--------------------------------------------------------------------------
        */
        DB::table('expense_categories')
            ->whereIn('code', [
                'SERV_BASICOS',
                'ALQUILER',
                'INTERNET_TELEFONO',
                'MANTENIMIENTO',
                'IMPUESTOS_TASAS',
                'OTROS',
            ])
            ->update([
                'expense_group_id' => $generalesId,
            ]);


        /*
        |--------------------------------------------------------------------------
        | 8. MOVILIZACIÓN
        |--------------------------------------------------------------------------
        */
        DB::table('expense_categories')
            ->whereIn('code', [
                'TRANSPORTE',
            ])
            ->update([
                'expense_group_id' => $movilizacionId,
            ]);
    }


    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {

            $table->dropForeign([
                'expense_group_id'
            ]);

            $table->dropColumn(
                'expense_group_id'
            );
        });


        Schema::dropIfExists(
            'expense_groups'
        );
    }
};