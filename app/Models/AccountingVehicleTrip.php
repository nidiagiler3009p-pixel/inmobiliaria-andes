<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingVehicleTrip extends Model
{
    use HasFactory;

    protected $table = 'accounting_vehicle_trips';

    protected $fillable = [
        'accounting_transaction_id',
        'vehicle_cost_configuration_id',
        'trip_date',
        'concept',
        'origin',
        'destination',
        'kilometers',
        'cost_per_km',
        'calculated_cost',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'kilometers' => 'decimal:2',
        'cost_per_km' => 'decimal:6',
        'calculated_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | OPERACIÓN CONTABLE
    |--------------------------------------------------------------------------
    */

    public function accountingTransaction()
    {
        return $this->belongsTo(
            AccountingTransaction::class,
            'accounting_transaction_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN DE VEHÍCULO
    |--------------------------------------------------------------------------
    */

    public function vehicleCostConfiguration()
    {
        return $this->belongsTo(
            VehicleCostConfiguration::class,
            'vehicle_cost_configuration_id'
        );
    }
}