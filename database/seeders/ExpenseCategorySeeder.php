<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Servicios básicos',
                'code' => 'SERV_BASICOS',
                'expense_type' => 'General',
                'description' => 'Agua, energía eléctrica y otros servicios básicos.',
            ],
            [
                'name' => 'Alquiler',
                'code' => 'ALQUILER',
                'expense_type' => 'General',
                'description' => 'Alquiler de oficina, local u otros espacios.',
            ],
            [
                'name' => 'Internet y telefonía',
                'code' => 'INTERNET_TELEFONO',
                'expense_type' => 'General',
                'description' => 'Internet, telefonía móvil y comunicaciones.',
            ],
            [
                'name' => 'Transporte y combustible',
                'code' => 'TRANSPORTE',
                'expense_type' => 'Directo',
                'description' => 'Combustible, movilización y transporte relacionado con operaciones.',
            ],
            [
                'name' => 'Publicidad y marketing',
                'code' => 'PUBLICIDAD',
                'expense_type' => 'Directo',
                'description' => 'Publicidad, campañas, banners y promoción de propiedades o servicios.',
            ],
            [
                'name' => 'Fotografía y drone',
                'code' => 'FOTO_DRONE',
                'expense_type' => 'Directo',
                'description' => 'Fotografía, video, drone y producción audiovisual.',
            ],
            [
                'name' => 'Documentación y trámites',
                'code' => 'DOCUMENTACION',
                'expense_type' => 'Directo',
                'description' => 'Documentos, certificados, copias y gastos relacionados con trámites.',
            ],
            [
                'name' => 'Papelería y suministros',
                'code' => 'PAPELERIA',
                'expense_type' => 'General',
                'description' => 'Materiales de oficina, impresiones y suministros.',
            ],
            [
                'name' => 'Software y tecnología',
                'code' => 'SOFTWARE',
                'expense_type' => 'General',
                'description' => 'Hosting, dominio, software, plataformas y herramientas tecnológicas.',
            ],
            [
                'name' => 'Mantenimiento',
                'code' => 'MANTENIMIENTO',
                'expense_type' => 'General',
                'description' => 'Mantenimiento de equipos, instalaciones y otros activos.',
            ],
            [
                'name' => 'Honorarios profesionales',
                'code' => 'HONORARIOS',
                'expense_type' => 'Directo',
                'description' => 'Servicios profesionales externos relacionados con una operación.',
            ],
            [
                'name' => 'Impuestos y tasas',
                'code' => 'IMPUESTOS_TASAS',
                'expense_type' => 'General',
                'description' => 'Impuestos, tasas y otros cargos administrativos.',
            ],
            [
                'name' => 'Otros',
                'code' => 'OTROS',
                'expense_type' => 'General',
                'description' => 'Otros gastos no clasificados.',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}