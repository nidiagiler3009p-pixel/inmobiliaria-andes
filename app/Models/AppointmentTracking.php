<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentTracking extends Model
{
    use HasFactory;
    protected $table = 'appointments_tracking';
    protected $fillable = ['client_id','prospect_id','user_id','property_id','registration_date','appointment_date','is_notified','location_reference','status','notes','type','priority','source_channel','cancellation_reason','rescued_to_portfolio','cancelled_at'];
    protected $casts = ['is_notified' => 'boolean','rescued_to_portfolio' => 'boolean','appointment_date' => 'datetime','registration_date' => 'datetime','cancelled_at' => 'datetime'];
    public function prospect() { return $this->belongsTo(Prospect::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function property() { return $this->belongsTo(Property::class); }
    public function portfolioEntry() { return $this->hasOne(ClientPortfolioEntry::class, 'appointment_id'); }
}