<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTramite extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'prospect_id',
        'source_type',
        'source_id',
        'status',
        'started_at',
        'finished_at',
        'result',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | CLIENTE
    |--------------------------------------------------------------------------
    */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /*
    |--------------------------------------------------------------------------
    | PROSPECTO
    |--------------------------------------------------------------------------
    */
    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO QUE GENERÓ EL PROCESO
    |--------------------------------------------------------------------------
    */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}