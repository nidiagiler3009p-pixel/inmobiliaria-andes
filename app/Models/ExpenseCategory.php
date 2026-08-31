<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expense_categories';

    protected $fillable = [
        'expense_group_id',
        'name',
        'code',
        'expense_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(
            ExpenseGroup::class,
            'expense_group_id'
        );
    }

    public function subcategories()
    {
        return $this->hasMany(
            ExpenseSubcategory::class,
            'expense_category_id'
        );
    }
}