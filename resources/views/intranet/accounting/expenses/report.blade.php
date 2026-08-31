<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reporte de Gastos</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: #f1f5f9;
            color: #1e293b;
            font-family: Arial, Helvetica, sans-serif;
        }

        .actions {
            max-width: 1050px;
            margin: 0 auto 18px auto;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 11px 18px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-back {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
        }

        .btn-print {
            background: #059669;
            color: #ffffff;
        }

        .report {
            width: 100%;
            max-width: 1050px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
            padding-bottom: 22px;
            border-bottom: 3px solid #059669;
        }

        .company h1 {
            margin: 0;
            font-size: 23px;
            color: #064e3b;
        }

        .company p {
            margin: 6px 0 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .report-title {
            text-align: right;
        }

        .report-title h2 {
            margin: 0;
            font-size: 21px;
            color: #0f172a;
        }

        .report-title p {
            margin: 7px 0 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .period {
            margin-top: 22px;
            padding: 14px 18px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
        }

        .period-label {
            color: #047857;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .period-value {
            margin-top: 5px;
            font-size: 16px;
            font-weight: bold;
            color: #064e3b;
        }

        .summary-title,
        .detail-title {
            margin-top: 28px;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }

        .summary-card .label {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }

        .summary-card .value {
            margin-top: 7px;
            font-size: 21px;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-card.total {
            border-color: #6ee7b7;
            background: #ecfdf5;
        }

        .summary-card.total .value {
            color: #047857;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 10px 8px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            text-align: left;
            font-size: 10px;
            color: #475569;
            text-transform: uppercase;
        }

        td {
            padding: 9px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 11px;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
            font-weight: bold;
        }

        .status {
            font-weight: bold;
        }

        .paid {
            color: #047857;
        }

        .pending {
            color: #b45309;
        }

        .category {
            font-weight: bold;
        }

        .subcategory {
            color: #475569;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .grand-total {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .grand-total-box {
            min-width: 300px;
            background: #0f172a;
            color: #ffffff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            color: #94a3b8;
            font-size: 10px;
        }

        @page {
            size: Letter landscape;
            margin: 8mm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background: #ffffff;
            }

            .no-print {
                display: none !important;
            }

            .report {
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 8mm;
                border-radius: 0;
                box-shadow: none;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            .summary-card {
                break-inside: avoid;
            }

            .grand-total {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>

@php
    $monthNames = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    /*
    |--------------------------------------------------------------------------
    | Grupos visuales
    |--------------------------------------------------------------------------
    */
    $expenseGroups = [
        'Publicidad' => [
            'PUBLICIDAD',
            'FOTO_DRONE',
        ],

        'Administrativos' => [
            'ADMIN',
            'PAPELERIA',
            'SOFTWARE',
            'DOCUMENTACION',
            'HONORARIOS',
        ],

        'Generales' => [
            'SERV_BASICOS',
            'ALQUILER',
            'INTERNET_TELEFONO',
            'MANTENIMIENTO',
            'IMPUESTOS_TASAS',
            'OTROS',
        ],

        'Movilización' => [
            'TRANSPORTE',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Total por grupo
    |--------------------------------------------------------------------------
    */
    $groupTotals = [];

    foreach ($expenseGroups as $groupName => $codes) {

        $groupTotals[$groupName] = $movements
            ->filter(function ($movement) use ($codes) {
                return $movement->category
                    && in_array($movement->category->code, $codes);
            })
            ->sum('amount');
    }
@endphp


{{-- BOTONES --}}
<div class="actions no-print">

    <a
        href="{{ route('accounting.expenses', ($filterType ?? 'month') === 'range'
            ? [
                'from_date' => $fromDate,
                'to_date' => $toDate
            ]
            : [
                'month' => $month,
                'year' => $year
            ]
        ) }}"
        class="btn btn-back"
    >
        ← Volver a Gastos
    </a>

    <button
        type="button"
        onclick="window.print()"
        class="btn btn-print"
    >
        Imprimir / Guardar PDF
    </button>

</div>


<div class="report">

    {{-- ENCABEZADO --}}
    <div class="header">

        <div class="company">

            <h1>
                Inmobiliaria Los Andes del Ecuador
            </h1>

            <p>
                Riobamba - Ecuador
            </p>

            <p>
                Sistema de Gestión Contable
            </p>

        </div>


        <div class="report-title">

            <h2>
                REPORTE DE GASTOS
            </h2>

            <p>
                Generado:
                {{ now()->format('d/m/Y H:i') }}
            </p>

        </div>

    </div>


    {{-- PERÍODO --}}
    <div class="period">

        <div class="period-label">
            Período del reporte
        </div>

        <div class="period-value">

            @if(($filterType ?? 'month') === 'range')

                {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}

                al

                {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}

            @else

                {{ $monthNames[(int) $month] }}
                {{ $year }}

            @endif

        </div>

    </div>


    {{-- RESUMEN GENERAL --}}
    <div class="summary-title">
        Resumen general
    </div>

    <div class="summary">

        <div class="summary-card total">

            <div class="label">
                Total gastos
            </div>

            <div class="value">
                ${{ number_format((float) $totalExpenses, 2) }}
            </div>

        </div>


        <div class="summary-card">

            <div class="label">
                Pagado
            </div>

            <div class="value">
                ${{ number_format((float) $paidExpenses, 2) }}
            </div>

        </div>


        <div class="summary-card">

            <div class="label">
                Pendiente
            </div>

            <div class="value">
                ${{ number_format((float) $pendingExpenses, 2) }}
            </div>

        </div>

    </div>


    {{-- RESUMEN POR GRUPO --}}
    <div class="summary-title">
        Distribución por grupo
    </div>

    <table>

        <thead>
            <tr>
                <th>Grupo</th>
                <th style="text-align:right;">
                    Total
                </th>
                <th style="text-align:right;">
                    Participación
                </th>
            </tr>
        </thead>

        <tbody>

            @foreach($groupTotals as $groupName => $groupTotal)

                @php
                    $percentage = $totalExpenses > 0
                        ? ($groupTotal / $totalExpenses) * 100
                        : 0;
                @endphp

                <tr>

                    <td class="category">
                        {{ $groupName }}
                    </td>

                    <td class="amount">
                        ${{ number_format((float) $groupTotal, 2) }}
                    </td>

                    <td class="amount">
                        {{ number_format($percentage, 2) }}%
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    {{-- DETALLE --}}
    <div class="detail-title">
        Detalle de movimientos
    </div>


    @if($movements->isEmpty())

        <div class="empty">
            No existen gastos registrados para el período seleccionado.
        </div>

    @else

        <table>

            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Subcategoría</th>
                    <th>Concepto</th>
                    <th>Proveedor</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th style="text-align:right;">
                        Valor
                    </th>
                </tr>
            </thead>

            <tbody>

                @foreach($movements as $movement)

                    <tr>

                        <td style="white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($movement->expense_date)->format('d/m/Y') }}
                        </td>


                        <td class="category">
                            {{ $movement->category?->name ?? 'Sin categoría' }}
                        </td>


                        <td class="subcategory">
                            {{ $movement->subcategory?->name ?? '—' }}
                        </td>


                        <td>
                            {{ $movement->concept }}
                        </td>


                        <td>
                            {{ $movement->provider ?: '—' }}
                        </td>


                        <td>

                            @if($movement->document_type || $movement->document_number)

                                {{ $movement->document_type ?: '' }}

                                @if($movement->document_number)
                                    {{ $movement->document_number }}
                                @endif

                            @else
                                —
                            @endif

                        </td>


                        <td>

                            @if($movement->payment_status === 'Pagado')

                                <span class="status paid">
                                    Pagado
                                </span>

                            @else

                                <span class="status pending">
                                    Pendiente
                                </span>

                            @endif

                        </td>


                        <td class="amount">
                            ${{ number_format((float) $movement->amount, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>


        <div class="grand-total">

            <div class="grand-total-box">

                <span>
                    TOTAL
                </span>

                <span>
                    ${{ number_format((float) $totalExpenses, 2) }}
                </span>

            </div>

        </div>

    @endif


    {{-- PIE --}}
    <div class="footer">

        <div>
            Inmobiliaria Los Andes del Ecuador
        </div>

        <div>
            Reporte generado por el sistema contable
        </div>

    </div>

</div>

</body>
</html>