<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingExpenseMovement extends Model
{
    use HasFactory;

    protected $table = 'accounting_expense_movements';

    protected $fillable = [
        'expense_category_id',
        'expense_subcategory_id',
        'accounting_transaction_id',
        'concept',
        'amount',
        'expense_date',
        'provider',
        'document_type',
        'document_number',
        'document_path',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'was_budgeted',
        'budget_rule_id',
        'budgeted_amount',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'paid_at' => 'datetime',
        'was_budgeted' => 'boolean',
        'budgeted_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    public function subcategory()
    {
        return $this->belongsTo(
            ExpenseSubcategory::class,
            'expense_subcategory_id'
        );
    }

    public function accountingTransaction()
    {
        return $this->belongsTo(
            AccountingTransaction::class,
            'accounting_transaction_id'
        );
    }

    public function budgetRule()
    {
        return $this->belongsTo(
            ExpenseBudgetRule::class,
            'budget_rule_id'
        );
    }
}