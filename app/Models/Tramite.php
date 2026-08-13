<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;
    protected $table = 'tramites';

    protected $fillable = [
        // Campos para la gestión interna (Intranet)
        'client_id',
        'user_id',
        'property_id',
        'process_type',
        'requirements_notes',
        'estimated_completion_date',
        
        // Campos nuevos para el Formulario Público Web
        'first_name',
        'last_name',
        'identification_card',
        'email',
        'phone',
        'tramite_type',
        'ubicacion',
        'subject',
        'message',
        'contact_preference',
        'accepted_privacy_policy',
        
        // Estatus compartido
        'status',
    ];

    // Relaciones para la Intranet
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}