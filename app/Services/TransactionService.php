<?php

namespace App\Services;

use App\Exceptions\TransactionException;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Ramsey\Uuid\Uuid;
use Exception;

class TransactionService
{
    public function store(
        $creditorAccount,
        $debtorAccount,
        $reference,
        $currency,
        $amount,
        $targetAmount,
    )
    {
        DB::beginTransaction();

        // Create the transaction
        try {
        $transaction = Transaction::create([
            'id' => Uuid::uuid4()->toString(),
            'creditor_account_id' => $creditorAccount->getId(),
            'debtor_account_id' => $debtorAccount->getId(),
            'reference' => $reference,
            'currency' => $currency,
            'amount' => $amount,
            'targetAmount' => $targetAmount
            ]);

            // Update the balances of the creditor and debtor accounts
            $creditorAccount->removeAmount($amount);
            $debtorAccount->addAmount($targetAmount);

            DB::commit();

            return response()->json(['transaction' => $transaction], 201);

        } catch (Exception $e) {
            DB::rollback();
        
            // Handle the exception or log the error
            throw TransactionException::transactionFailedException();
        }
    }

    public function getAllTransactionsByAccountId($accountId, $offset, $limit)
    {
        return Transaction::where('creditor_account_id', $accountId)
            ->orWhere('debtor_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}