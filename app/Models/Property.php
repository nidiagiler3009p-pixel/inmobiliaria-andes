<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'service_type',
        'user_id',
        'property_type',
        'title',
        'owner_name',
        'owner_phone',
        'owner_dni',
        'owner_email',
        'badge_left',
        'location',
        'address',
        'badge_right',
        'google_maps_url',
        'bedrooms',
        'bedrooms_detail',
        'bathrooms_full',
        'bathrooms_half',
        'garages',
        'garages_detail',
        'social_areas',
        'kitchen',
        'exteriors',
        'study_room',
        // Características y Servicios Booleanos (Checkboxes)
        'has_jardin',
        'has_balcon',
        'has_seguridad',
        'has_agua',
        'has_luz',
        'has_alcantarillado',
        'has_internet',
        'has_piscina',
        'has_bbq',
        'has_amoblado',
        'has_mascotas',
        'price_dropped',
        // Precios y detalles
        'price',
        'price_condition',
        'documentation_status',
        'antiquity_years',
        'land_area_m2',
        'construction_area_m2',
        'basic_services',
        'description',
        'virtual_tour_url',
        'contact_phone',
        'contact_email',
        'url_youtube',
        'url_instagram',
        'url_tiktok',
        'url_facebook',
        'status',
        'social_info_completed'
    ];

    /**
     * Conversión de tipos de atributos (Casting)
     */
    protected $casts = [
        'has_jardin' => 'boolean',
        'has_balcon' => 'boolean',
        'has_seguridad' => 'boolean',
        'has_agua' => 'boolean',
        'has_luz' => 'boolean',
        'has_alcantarillado' => 'boolean',
        'has_internet' => 'boolean',
        'has_piscina' => 'boolean',
        'has_bbq' => 'boolean',
        'has_amoblado' => 'boolean',
        'has_mascotas' => 'boolean',
        'price_dropped' => 'boolean',
        'price' => 'decimal:2',
        'land_area_m2' => 'decimal:2',
        'construction_area_m2' => 'decimal:2',
    ];

  public function images()
{
    return $this->hasMany(PropertyImage::class)->orderBy('position', 'asc');
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointmentTrackings()
    {
        return $this->hasMany(AppointmentTracking::class);
    }

    public function portfolioEntries()
{
    return $this->hasMany(ClientPortfolioEntry::class);
}
}