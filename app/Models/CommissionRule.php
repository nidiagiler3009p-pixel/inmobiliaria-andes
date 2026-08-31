<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_configuration_id',
        'code',
        'name',
        'participation_type',
        'capture_origin',
        'percentage',
        'distribution_type',
        'is_active',
        'priority',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:4',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(
            CommissionConfiguration::class,
            'commission_configuration_id'
        );
    }

    public function getPercentageFormattedAttribute(): string
    {
        return number_format((float) $this->percentage, 2) . '%';
    }
}