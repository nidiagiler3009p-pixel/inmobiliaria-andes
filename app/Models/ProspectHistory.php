<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectHistory extends Model
{
    use HasFactory;

    protected $table = 'prospect_histories';

    protected $fillable = [
        'prospect_id',
        'event_type',
        'source_type',
        'source_record_id',
        'previous_status',
        'new_status',
        'description',
        'user_id',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}