<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_transaction_id',
        'client_id',
        'identification_type',
        'identification_number',
        'customer_name',
        'business_name',
        'billing_address',
        'phone',
        'email',
        'document_type',
        'invoice_number',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'status',
        'issued_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(
            AccountingTransaction::class,
            'accounting_transaction_id'
        );
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}