<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseBudgetRule extends Model
{
    use HasFactory;

    protected $table = 'expense_budget_rules';

    protected $fillable = [
        'expense_category_id',
        'expense_subcategory_id',
        'calculation_method',
        'percentage',
        'fixed_amount',
        'cost_per_km',
        'percentage_base',
        'effective_from',
        'effective_until',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
        'fixed_amount' => 'decimal:2',
        'cost_per_km' => 'decimal:4',
        'effective_from' => 'date',
        'effective_until' => 'date',
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

    public function movements()
    {
        return $this->hasMany(
            AccountingExpenseMovement::class,
            'budget_rule_id'
        );
    }
}