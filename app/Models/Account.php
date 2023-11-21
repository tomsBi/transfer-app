<?php

namespace App\Models;

use App\Exceptions\AccountException;
use App\Exceptions\TransactionException;
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
     * @throws AccountException if insufficient funds for Account
     */
    public function checkFunds($amount)
    {
        if($this->balance >= $amount){
            return true;
        }
        throw AccountException::insufficientFundsException();
    }

    /**
     * Retrieve an account based on ID.
     *
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws AccountException if no Account found
     */
    public static function getAccount($id)
    {
        $account = self::where('id', $id)->first();
        if($account){
            return $account;
        }
        throw AccountException::noAccountsFoundException($id);
    }

     /**
     * Add money to the account balance.
     *
     * @param float $amount
     * @return void
     */
    public function addAmount($amount)
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
    public function removeAmount($amount)
    {
        $this->balance -= $amount;
        $this->save();
    }

    /**
     * Check if the account has the specified currency.
     *
     * @param string $currency
     * @return bool
     * @throws TransactionException if currency is wrong
     */
    public function checkCurrency($currency)
    {
        if($this->currency === $currency) {
            return true;
        }
        throw TransactionException::wrongCurrencyException();
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }

    public function getId()
    {
        return $this->id;
    }


}
