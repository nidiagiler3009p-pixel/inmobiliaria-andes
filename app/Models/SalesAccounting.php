<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAccounting extends Model
{
    use HasFactory;

    protected $table = 'sales_accountings';

    protected $fillable = [
        'property_id',
        'client_id',
        'user_id',       // Asesor que cerró la venta
        'sale_price',    // Precio total de venta
        'commission',    // Comisión de la inmobiliaria / asesor
        'sale_date',     // Fecha de la venta
        'notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}