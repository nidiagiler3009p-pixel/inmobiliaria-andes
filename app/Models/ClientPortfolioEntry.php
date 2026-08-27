<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPortfolioEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'appointment_id',

        'source_type',
        'source_record_id',
        'previous_status', 
        'property_id',
        'advisor_id',
        'entry_source',
        'contact_channel',
        'social_platform',
        'social_profile_url',
        'entry_reason',
        'portfolio_status',
        'notes',
        'entered_at',
        'prospect_id',  
    ];

    protected $casts = [
        'entered_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

     public function prospect()
{
    return $this->belongsTo(Prospect::class, 'prospect_id');
}
    public function appointment()
    {
        return $this->belongsTo(AppointmentTracking::class, 'appointment_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}