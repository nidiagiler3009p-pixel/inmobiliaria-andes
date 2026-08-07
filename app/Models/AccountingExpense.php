<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingExpense extends Model
{
    use HasFactory;

    protected $table = 'accounting_expenses';

    protected $fillable = [
        'user_id',     // Quién registró el gasto
        'concept',     // Concepto o descripción del gasto
        'amount',      // Monto
        'expense_date',// Fecha del gasto
        'category',    // Categoría (servicios, mantenimiento, publicidad, etc.)
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}