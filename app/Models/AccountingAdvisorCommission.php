<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingAdvisorCommission extends Model
{
    use HasFactory;

    protected $table = 'accounting_advisor_commissions';

    protected $fillable = [
        'accounting_transaction_id',
        'user_id',
        'role_in_transaction',
        'percentage',
        'calculation_base',
        'commission_amount',
        'status',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
        'calculation_base' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
{
    static::saved(function (AccountingAdvisorCommission $commission) {
        if ($commission->accounting_transaction_id) {
            $commission->accountingTransaction?->recalculateTotals();
        }
    });

    static::deleted(function (AccountingAdvisorCommission $commission) {
        if ($commission->accounting_transaction_id) {
            $commission->accountingTransaction?->recalculateTotals();
        }
    });
}


    public function accountingTransaction()
    {
        return $this->belongsTo(AccountingTransaction::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}