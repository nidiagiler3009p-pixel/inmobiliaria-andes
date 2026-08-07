<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;

    protected $table = 'social_links';

    protected $fillable = [
        'user_id',      // NULL si es de la empresa (footer), o ID si es de un asesor
        'platform',     // Red social, banco o cooperativa
        'url_or_value', // Enlace, número de cuenta o usuario
        'is_active',    // Estado activo o inactivo
    ];

    // Relación: Un enlace o cuenta puede pertenecer a un asesor específico (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}