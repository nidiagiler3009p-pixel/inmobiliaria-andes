<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingTransactionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_transaction_id',
        'user_id',
        'participation_type',
        'distribution_percentage',
        'source',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'distribution_percentage' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(
            AccountingTransaction::class,
            'accounting_transaction_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    }
