<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'creditor_account_id',
        'debtor_account_id',
        'amount',
        'currency',
        'reference',
        'targetAmount',
        'targetCurrency',
    ];
}
