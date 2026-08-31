<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'email',
        'identification',
        'status',
        'first_source',
        'notes',
    ];

    public function histories()
    {
        return $this->hasMany(ProspectHistory::class);
    }

    public function portfolioEntries()
    {
        return $this->hasMany(
            ClientPortfolioEntry::class,
            'prospect_id'
        );
    }

public function contacts()
{
    return $this->hasMany(ProspectContact::class);
}

public function aliases()
{
    return $this->hasMany(ProspectAlias::class);
}

public function client()
{
    return $this->hasOne(Client::class);
}
public function tramites()
{
    return $this->hasMany(Tramite::class, 'prospect_id');
}

}

