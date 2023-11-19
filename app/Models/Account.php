<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Account extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'user_id', 'currency', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

     /**
     * Check if the account has sufficient funds for a given amount.
     *
     * @param float $amount
     * @return bool
     */
    public function checkFunds($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Retrieve an account based on user ID and currency.
     *
     * @param string $userId
     * @param string $currency
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function retrieveAccount($id)
    {
        return self::where('id', $id)
            ->first();
    }

     /**
     * Add money to the account balance.
     *
     * @param float $amount
     * @return void
     */
    public function addMoney($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Remove money from the account balance.
     *
     * @param float $amount
     * @return void
     */
    public function removeMoney($amount)
    {
        $this->balance -= $amount;
        $this->save();
    }

    /**
     * Check if the account has the specified currency.
     *
     * @param string $currency
     * @return bool
     */
    public function checkCurrency($currency)
    {
        return $this->currency === $currency;
    }
}
