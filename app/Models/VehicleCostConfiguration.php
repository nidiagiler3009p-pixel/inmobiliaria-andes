<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleCostConfiguration extends Model
{
    use HasFactory;

    protected $table = 'vehicle_cost_configurations';

    protected $fillable = [
        'name',
        'effective_from',
        'effective_until',

        'fuel_price_per_gallon',
        'vehicle_efficiency_km_per_gallon',

        'oil_change_cost',
        'oil_change_interval_km',

        'tires_total_cost',
        'tires_lifespan_km',

        'maintenance_cost',
        'maintenance_interval_km',

        'annual_insurance_cost',
        'annual_registration_cost',
        'annual_other_vehicle_costs',
        'estimated_annual_km',

        'fuel_cost_per_km',
        'oil_cost_per_km',
        'tires_cost_per_km',
        'maintenance_cost_per_km',
        'annual_costs_per_km',
        'total_cost_per_km',

        'is_active',
        'notes',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_until' => 'date',

        'fuel_price_per_gallon' => 'decimal:4',
        'vehicle_efficiency_km_per_gallon' => 'decimal:4',

        'oil_change_cost' => 'decimal:2',
        'oil_change_interval_km' => 'decimal:2',

        'tires_total_cost' => 'decimal:2',
        'tires_lifespan_km' => 'decimal:2',

        'maintenance_cost' => 'decimal:2',
        'maintenance_interval_km' => 'decimal:2',

        'annual_insurance_cost' => 'decimal:2',
        'annual_registration_cost' => 'decimal:2',
        'annual_other_vehicle_costs' => 'decimal:2',
        'estimated_annual_km' => 'decimal:2',

        'fuel_cost_per_km' => 'decimal:6',
        'oil_cost_per_km' => 'decimal:6',
        'tires_cost_per_km' => 'decimal:6',
        'maintenance_cost_per_km' => 'decimal:6',
        'annual_costs_per_km' => 'decimal:6',
        'total_cost_per_km' => 'decimal:6',

        'is_active' => 'boolean',
    ];

    public function calculateCosts(): void
    {
        $fuelCostPerKm = 0;
        $oilCostPerKm = 0;
        $tiresCostPerKm = 0;
        $maintenanceCostPerKm = 0;
        $annualCostsPerKm = 0;

        /*
        |--------------------------------------------------------------------------
        | COMBUSTIBLE
        |--------------------------------------------------------------------------
        */

        if (
            $this->fuel_price_per_gallon !== null &&
            $this->vehicle_efficiency_km_per_gallon !== null &&
            (float) $this->vehicle_efficiency_km_per_gallon > 0
        ) {
            $fuelCostPerKm =
                (float) $this->fuel_price_per_gallon /
                (float) $this->vehicle_efficiency_km_per_gallon;
        }

        /*
        |--------------------------------------------------------------------------
        | ACEITE
        |--------------------------------------------------------------------------
        */

        if (
            $this->oil_change_cost !== null &&
            $this->oil_change_interval_km !== null &&
            (float) $this->oil_change_interval_km > 0
        ) {
            $oilCostPerKm =
                (float) $this->oil_change_cost /
                (float) $this->oil_change_interval_km;
        }

        /*
        |--------------------------------------------------------------------------
        | NEUMÁTICOS
        |--------------------------------------------------------------------------
        */

        if (
            $this->tires_total_cost !== null &&
            $this->tires_lifespan_km !== null &&
            (float) $this->tires_lifespan_km > 0
        ) {
            $tiresCostPerKm =
                (float) $this->tires_total_cost /
                (float) $this->tires_lifespan_km;
        }

        /*
        |--------------------------------------------------------------------------
        | MANTENIMIENTO
        |--------------------------------------------------------------------------
        */

        if (
            $this->maintenance_cost !== null &&
            $this->maintenance_interval_km !== null &&
            (float) $this->maintenance_interval_km > 0
        ) {
            $maintenanceCostPerKm =
                (float) $this->maintenance_cost /
                (float) $this->maintenance_interval_km;
        }

        /*
        |--------------------------------------------------------------------------
        | COSTOS ANUALES
        |--------------------------------------------------------------------------
        */

        $annualCosts =
            (float) ($this->annual_insurance_cost ?? 0) +
            (float) ($this->annual_registration_cost ?? 0) +
            (float) ($this->annual_other_vehicle_costs ?? 0);

        if (
            $annualCosts > 0 &&
            $this->estimated_annual_km !== null &&
            (float) $this->estimated_annual_km > 0
        ) {
            $annualCostsPerKm =
                $annualCosts /
                (float) $this->estimated_annual_km;
        }

        /*
        |--------------------------------------------------------------------------
        | GUARDAR RESULTADOS
        |--------------------------------------------------------------------------
        */

        $this->fuel_cost_per_km = round($fuelCostPerKm, 6);
        $this->oil_cost_per_km = round($oilCostPerKm, 6);
        $this->tires_cost_per_km = round($tiresCostPerKm, 6);
        $this->maintenance_cost_per_km = round($maintenanceCostPerKm, 6);
        $this->annual_costs_per_km = round($annualCostsPerKm, 6);

        $this->total_cost_per_km = round(
            $fuelCostPerKm +
            $oilCostPerKm +
            $tiresCostPerKm +
            $maintenanceCostPerKm +
            $annualCostsPerKm,
            6
        );
    }
    public function vehicleTrips()
    {
        return $this->hasMany(
            AccountingVehicleTrip::class,
            'vehicle_cost_configuration_id'
        );
    }

    
}
