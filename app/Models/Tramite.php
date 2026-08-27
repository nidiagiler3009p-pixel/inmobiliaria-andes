<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model {
    use HasFactory;
    protected $table = 'tramites';
    protected $fillable = ['prospect_id','first_name','last_name','identification_card','email','phone','tramite_type','ubicacion','subject','message','contact_preference','accepted_privacy_policy','status'];
    public function prospect() { return $this->belongsTo(Prospect::class); }
}