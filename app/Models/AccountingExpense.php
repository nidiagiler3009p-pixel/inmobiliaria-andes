<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingExpense extends Model
{
    use HasFactory;

    protected $table = 'accounting_expenses';

    protected $fillable = [
        'expense_category_id',
        'accounting_transaction_id',

        'expense_name',
        'expense_category',
        'expense_type',
        'amount',

        'expense_date',
        'provider',
        'document_number',
        'document_path',

        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',

        'notes',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'paid_at' => 'datetime',
        'is_active' => 'boolean',
    ];

protected static function booted(): void
{
    static::saved(function (AccountingExpense $expense) {
        if ($expense->accounting_transaction_id) {
            $expense->accountingTransaction?->recalculateTotals();
        }
    });

    static::deleted(function (AccountingExpense $expense) {
        if ($expense->accounting_transaction_id) {
            $expense->accountingTransaction?->recalculateTotals();
        }
    });
}


    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    public function accountingTransaction()
    {
        return $this->belongsTo(
            AccountingTransaction::class,
            'accounting_transaction_id'
        );
    }



    }
