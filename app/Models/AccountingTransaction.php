<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingTransaction extends Model
{
    use HasFactory;

    protected $table = 'accounting_transactions';

    protected $fillable = [
        'client_id',
        'prospect_id',
        'tramite_id',
        'property_id',

        'operation_type',
        'description',

        'published_price',
        'closing_price',

        'brokerage_percentage',
        'brokerage_amount',

        'service_amount',

        'gross_income',
        'direct_expenses_total',
        'advisor_commissions_total',
        'general_expenses_prorated',
        'net_profit',

        'status',

        'origin_module',
        'source_type',
        'source_id',

        'approved_at',
        'invoiced_at',
        'closed_at',

        'notes',
    ];

    protected $casts = [
        'published_price' => 'decimal:2',
        'closing_price' => 'decimal:2',
        'brokerage_percentage' => 'decimal:4',
        'brokerage_amount' => 'decimal:2',
        'service_amount' => 'decimal:2',

        'gross_income' => 'decimal:2',
        'direct_expenses_total' => 'decimal:2',
        'advisor_commissions_total' => 'decimal:2',
        'general_expenses_prorated' => 'decimal:2',
        'net_profit' => 'decimal:2',

        'approved_at' => 'datetime',
        'invoiced_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

public function advisorCommissions()
{
    return $this->hasMany(AccountingAdvisorCommission::class);
}
public function expenses()
{
    return $this->hasMany(
        AccountingExpense::class,
        'accounting_transaction_id'
    );
}

public function recalculateTotals(): void
{
    // Gastos directos asociados a esta operación.
    // Solo contamos gastos activos.
    $directExpenses = $this->expenses()
        ->where('expense_type', 'Directo')
        ->where('is_active', true)
        ->sum('amount');

    // Comisiones de asesores asociadas a la operación.
    // Una comisión anulada no debe afectar el resultado.
    $advisorCommissions = $this->advisorCommissions()
        ->where('status', '!=', 'Anulada')
        ->sum('commission_amount');

    /*
     * El ingreso bruto puede provenir de:
     *
     * 1. Comisión de corretaje de una operación inmobiliaria.
     * 2. Valor de un servicio independiente.
     * 3. Ambos, si una operación llegara a tener los dos conceptos.
     */
    $grossIncome =
        (float) ($this->brokerage_amount ?? 0)
        + (float) ($this->service_amount ?? 0);

    /*
     * general_expenses_prorated se conserva porque posteriormente
     * definiremos desde Configuración cómo se distribuyen los gastos
     * generales entre las operaciones.
     */
    $generalExpenses = (float) ($this->general_expenses_prorated ?? 0);

    $netProfit =
        $grossIncome
        - (float) $directExpenses
        - (float) $advisorCommissions
        - $generalExpenses;

    $this->update([
        'gross_income' => $grossIncome,
        'direct_expenses_total' => $directExpenses,
        'advisor_commissions_total' => $advisorCommissions,
        'net_profit' => $netProfit,
    ]);
}
public function vehicleTrips()
{
    return $this->hasMany(
        AccountingVehicleTrip::class,
        'accounting_transaction_id'
    );
}

public function participants()
{
    return $this->hasMany(
        AccountingTransactionParticipant::class,
        'accounting_transaction_id'
    );
}
public function activeParticipants()
{
    return $this->hasMany(
        AccountingTransactionParticipant::class,
        'accounting_transaction_id'
    )->where('is_active', true);
}

public function invoice()
{
    return $this->hasOne(
        AccountingInvoice::class,
        'accounting_transaction_id'
    );
}

    }


