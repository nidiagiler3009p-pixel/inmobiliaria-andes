<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $invoice->document_type === 'factura'
            ? 'Factura'
            : 'Comprobante interno' }}
        {{ $invoice->invoice_number }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

.document {
    width: 190mm;
    max-width: 190mm;
    margin: 0 auto !important;

    padding: 12mm 10mm !important;

    border-radius: 0;
    box-shadow: none;

    box-sizing: border-box;
}

.toolbar {
    max-width: 980px;
    margin: 0 auto 15px;
    display: flex;
    justify-content: space-between;
    gap: 12px;
}

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: 0;
        }

        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-primary {
            background: #059669;
            color: white;
        }

        .document {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 42px;
            border-radius: 18px;
            box-shadow: 0 10px 35px rgba(15, 23, 42, .08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid #059669;
        }

        .company-name {
            font-size: 25px;
            font-weight: 800;
            margin: 0 0 7px;
        }

        .company-subtitle {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .document-box {
            min-width: 270px;
            padding: 20px;
            border: 1px solid #d1fae5;
            background: #ecfdf5;
            border-radius: 14px;
        }

        .document-type {
            color: #047857;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .document-number {
            margin-top: 8px;
            font-size: 19px;
            font-weight: 800;
        }

        .document-date {
            margin-top: 8px;
            font-size: 13px;
            color: #64748b;
        }

   .section {
    margin-top: 26px;
}

        .section-title {
            margin: 0 0 15px;
            font-size: 13px;
            color: #059669;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .customer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 30px;
        }

        .label {
            display: block;
            margin-bottom: 5px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .value {
            font-size: 14px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .concept-table {
            width: 100%;
            border-collapse: collapse;
        }

        .concept-table th {
            padding: 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
        }

        .concept-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .text-right {
            text-align: right !important;
        }

.totals {
    width: 380px;
    margin: 22px 0 0 auto;
}

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .total-row span:first-child {
            color: #64748b;
        }

        .grand-total {
            margin-top: 10px;
            padding: 16px;
            border-radius: 12px;
            background: #ecfdf5;
            color: #047857;
            font-size: 20px;
            font-weight: 800;
        }

        .notes {
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            color: #475569;
            font-size: 13px;
            line-height: 1.6;
        }

        .internal-warning {
            margin-top: 25px;
            padding: 14px 16px;
            border: 1px solid #fde68a;
            background: #fffbeb;
            border-radius: 12px;
            color: #92400e;
            font-size: 12px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 45px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.6;
        }

        @media (max-width: 700px) {
            body {
                padding: 15px;
            }

            .document {
                padding: 25px;
            }

            .header {
                flex-direction: column;
            }

            .document-box {
                min-width: 0;
            }

            .customer-grid {
                grid-template-columns: 1fr;
            }

            .totals {
                width: 100%;
            }
        }

@media print {

    @page {
        size: Letter portrait;
        margin: 8mm;
    }

    html,
    body {
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    .no-print {
        display: none !important;
    }

    .document {
        width: 190mm;
        max-width: 190mm;
        margin: 0 auto !important;
        padding: 12mm 10mm !important;
        border-radius: 0;
        box-shadow: none;
        box-sizing: border-box;
    }

    .header {
        padding-bottom: 18px;
    }

    .company-name {
        font-size: 23px;
    }

    .document-box {
        padding: 15px 18px;
    }

    .section {
        margin-top: 22px;
    }

    .customer-grid {
        gap: 14px 28px;
    }

    .concept-table th {
        padding: 10px;
    }

    .concept-table td {
        padding: 13px 10px;
    }

    .totals {
        width: 340px;
        margin-top: 18px;
    }

    .total-row {
        padding: 9px 0;
    }

    .grand-total {
        padding: 14px;
    }

    .notes {
        padding: 14px;
    }

    .internal-warning {
        margin-top: 20px;
        padding: 12px 14px;
        break-inside: avoid;
    }

    .footer {
        margin-top: 28px;
        padding-top: 16px;
    }

    .header,
    .customer-grid,
    .concept-table,
    .totals,
    .notes,
    .internal-warning,
    .footer {
        break-inside: avoid;
    }
}
    </style>
</head>

<body>

    {{-- BOTONES --}}
    <div class="toolbar no-print">

        <a href="{{ route('accounting.invoice.review', $transaction->id) }}"
           class="btn btn-secondary">
            ← Volver
        </a>

        <button type="button"
                onclick="window.print()"
                class="btn btn-primary">
            Imprimir / Guardar PDF
        </button>

    </div>


    <main class="document">

        {{-- ENCABEZADO --}}
        <header class="header">

            <div>
                <h1 class="company-name">
                    Inmobiliaria Los Andes del Ecuador
                </h1>

                <div class="company-subtitle">
                    Riobamba · Ecuador<br>
                    Servicios inmobiliarios y asesoría
                </div>
            </div>


            <div class="document-box">

                <div class="document-type">
                    {{ $invoice->document_type === 'factura'
                        ? 'Factura'
                        : 'Comprobante interno' }}
                </div>

                <div class="document-number">
                    {{ $invoice->invoice_number }}
                </div>

                <div class="document-date">
                    Emitido:
                    {{ $invoice->issued_at
                        ? $invoice->issued_at->format('d/m/Y H:i')
                        : '-' }}
                </div>

            </div>

        </header>


        {{-- CLIENTE --}}
        <section class="section">

            <h2 class="section-title">
                Datos del cliente
            </h2>

            <div class="customer-grid">

                <div>
                    <span class="label">
                        Cliente
                    </span>

                    <div class="value">
                        {{ $invoice->customer_name }}
                    </div>
                </div>


                <div>
                    <span class="label">
                        Identificación
                    </span>

                    <div class="value">
                        {{ strtoupper(
                            str_replace(
                                '_',
                                ' ',
                                $invoice->identification_type
                            )
                        ) }}

                        ·

                        {{ $invoice->identification_number ?: '-' }}
                    </div>
                </div>


                @if($invoice->business_name)
                    <div>
                        <span class="label">
                            Razón social
                        </span>

                        <div class="value">
                            {{ $invoice->business_name }}
                        </div>
                    </div>
                @endif


                <div>
                    <span class="label">
                        Dirección
                    </span>

                    <div class="value">
                        {{ $invoice->billing_address ?: '-' }}
                    </div>
                </div>


                <div>
                    <span class="label">
                        Teléfono
                    </span>

                    <div class="value">
                        {{ $invoice->phone ?: '-' }}
                    </div>
                </div>


                <div>
                    <span class="label">
                        Correo
                    </span>

                    <div class="value">
                        {{ $invoice->email ?: '-' }}
                    </div>
                </div>

            </div>

        </section>


        {{-- CONCEPTO --}}
        <section class="section">

            <h2 class="section-title">
                Detalle del documento
            </h2>

            <table class="concept-table">

                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>

                            @if($transaction->operation_type === 'Corretaje / Propiedad')

                                <strong>
                                    Servicio de corretaje inmobiliario
                                </strong>

                                @if($transaction->property)
                                    <br>
                                    <span style="color:#64748b;">
                                        {{ $transaction->property->title }}
                                    </span>
                                @endif

                            @else

                                <strong>
                                    Servicio / Trámite
                                </strong>

                                @if($transaction->description)
                                    <br>
                                    <span style="color:#64748b;">
                                        {{ $transaction->description }}
                                    </span>
                                @endif

                            @endif

                        </td>

                        <td class="text-right">
                            ${{ number_format(
                                (float) $invoice->subtotal,
                                2
                            ) }}
                        </td>

                    </tr>

                </tbody>

            </table>

        </section>


        {{-- TOTALES --}}
        <div class="totals">

            <div class="total-row">
                <span>Subtotal</span>

                <strong>
                    ${{ number_format(
                        (float) $invoice->subtotal,
                        2
                    ) }}
                </strong>
            </div>


            <div class="total-row">
                <span>
                    Impuesto
                    {{ number_format(
                        (float) $invoice->tax_percentage,
                        2
                    ) }}%
                </span>

                <strong>
                    ${{ number_format(
                        (float) $invoice->tax_amount,
                        2
                    ) }}
                </strong>
            </div>


            <div class="total-row grand-total">

                <span>
                    TOTAL
                </span>

                <span>
                    ${{ number_format(
                        (float) $invoice->total,
                        2
                    ) }}
                </span>

            </div>

        </div>


        {{-- OBSERVACIONES --}}
        @if($invoice->notes)

            <section class="section">

                <h2 class="section-title">
                    Observaciones
                </h2>

                <div class="notes">
                    {{ $invoice->notes }}
                </div>

            </section>

        @endif


        {{-- AVISO PARA COMPROBANTE INTERNO --}}
        @if($invoice->document_type === 'comprobante')

            <div class="internal-warning">

                <strong>Comprobante interno.</strong>

                Este documento corresponde al registro administrativo
                interno de la operación realizada por la inmobiliaria.

            </div>

        @endif


        {{-- PIE --}}
        <footer class="footer">

            <strong>
                Inmobiliaria Los Andes del Ecuador
            </strong>

            <br>

            Documento generado desde el módulo de Contabilidad.

            <br>

            Operación contable #{{ $transaction->id }}

        </footer>

    </main>

</body>
</html>