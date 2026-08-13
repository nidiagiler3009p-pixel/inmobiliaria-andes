<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentTracking extends Model
{
    use HasFactory;

    protected $table = 'appointments_tracking';

    protected $fillable = [
        'client_id',
        'user_id',
        'property_id',
        'registration_date',
        'appointment_date',
        'is_notified',
        'location_reference',
        'status',
        'notes',
        'type',
        'priority',
        // Nuevos campos agregados
        'source_channel',
        'cancellation_reason',
        'rescued_to_portfolio',
        'cancelled_at',
    ];

    /**
     * Conversión de tipos de datos (Casting)
     */
    protected $casts = [
        'is_notified' => 'boolean',
        'rescued_to_portfolio' => 'boolean',
        'appointment_date' => 'datetime',
        'registration_date' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relación: La cita pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación: La cita pertenece a un asesor (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: La cita puede estar vinculada a una propiedad
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}