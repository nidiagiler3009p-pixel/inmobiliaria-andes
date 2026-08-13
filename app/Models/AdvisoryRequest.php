<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AdvisoryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_type',
        'advisor_id',
        'full_name',
        'email',
        'phone',
        'ciudad',
        'discovery_channel',
        'property_type',
        'property_location',
        'estimated_price',
        'property_details',
        'preferences_notes',
        'accepted_terms',
        'status',
    ];
}