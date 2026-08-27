<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospect_id',
        'alias_name',
        'notes',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}