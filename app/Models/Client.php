<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospect_id',
        'user_id',
        'name',
        'last_name',
        'identification_card',
        'phone',
        'email',
        'social_media_source',
        'status',
        'review_status',
        'origin_module',
        'observations',
    ];

    // Un cliente pertenece a un asesor
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un cliente puede tener varias citas o seguimientos
    public function appointmentTrackings()
    {
        return $this->hasMany(AppointmentTracking::class);
    }

    // Un cliente puede tener varios eventos en el calendario
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Historial de entradas en Cartera
    public function portfolioEntries()
    {
        return $this->hasMany(ClientPortfolioEntry::class);
    }

    // Prospecto original relacionado
    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    // Procesos propios del módulo Clientes / Trámites
    public function clientTramites()
    {
        return $this->hasMany(ClientTramite::class);
    }
}