<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'effective_from',
        'effective_to',
        'is_active',
        'default_sales_distribution',
        'allow_manual_distribution',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_active' => 'boolean',
            'allow_manual_distribution' => 'boolean',
        ];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(CommissionRule::class)
            ->orderBy('priority')
            ->orderBy('id');
    }

    public function activeRules(): HasMany
    {
        return $this->hasMany(CommissionRule::class)
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id');
    }

    /**
     * Obtiene la configuración vigente para una fecha.
     */
    public static function activeForDate($date): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->whereDate('effective_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $date);
            })
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();
    }
}