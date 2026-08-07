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

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointmentTrackings()
    {
        return $this->hasMany(AppointmentTracking::class);
    }
}