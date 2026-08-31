<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSubcategory extends Model
{
    use HasFactory;

    protected $table = 'expense_subcategories';

    protected $fillable = [
        'expense_category_id',
        'name',
        'code',
        'description',
        'budget_method',
        'budget_percentage',
        'fixed_budget_amount',
        'is_budgeted',
        'is_active',
    ];

    protected $casts = [
        'budget_percentage' => 'decimal:4',
        'fixed_budget_amount' => 'decimal:2',
        'is_budgeted' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    public function budgetRules()
    {
        return $this->hasMany(
            ExpenseBudgetRule::class,
            'expense_subcategory_id'
        );
    }

    public function movements()
    {
        return $this->hasMany(
            AccountingExpenseMovement::class,
            'expense_subcategory_id'
        );
    }
}