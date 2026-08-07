<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'client_id',
        'event_type', // Cita, Asignación
        'title',
        'start_time',
        'end_time',
        'status',     // Pendiente, Confirmada, Completada, Cancelada
        'alert_sent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
