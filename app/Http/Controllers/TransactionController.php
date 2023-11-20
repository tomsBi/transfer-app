<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        // Retrieve all transactions
        $transactions = Transaction::all();

        return response()->json(['transactions' => $transactions]);
    }

    public function store(Request $request)
    {
        $currency = $request->input('currency');
        $amount = $request->input('amount');
        $reference = $request->input('reference');
        
        $validator = $this->validateTransactionRequest($request);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $creditorAccount = Account::getAccount($request->input('creditor_account_id'));
        $debtorAccount = Account::getAccount($request->input('debtor_account_id'));

        if ($creditorAccount->getId() === $debtorAccount->getId()) {
            return response()->json(['error' => 'Transaction between the same account is not allowed.'], 403);
        }

        if (!$creditorAccount || !$debtorAccount) {
            return response()->json(['error' => 'One or more accounts not found.'], 404);
        }

        if (!$creditorAccount->checkFunds($amount)) {
            return response()->json(['error' => 'Insufficient funds in the creditor account.'], 400);
        }

        if (!$debtorAccount->checkCurrency($currency)) {
            $targetCurrency = $debtorAccount->getCurrency();
            $targetAmount = (new CurrencyExchangeController)->getTargetAmount($currency, $targetCurrency, $amount, date("Y-m-d"));
        } else {
            $targetAmount = $amount;
            $targetCurrency = $currency;
        }

        return $this->commitTransaction($creditorAccount, $debtorAccount, $reference, $currency, $amount, $targetAmount, $targetCurrency);
    }

    private function validateTransactionRequest(Request $request)
    {
        return validator($request->all(), [
            'creditor_account_id' => 'required|uuid',
            'debtor_account_id' => 'required|uuid',
            'reference' => 'required|string',
            'currency' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);
    }

    private function commitTransaction(
        $creditorAccount,
        $debtorAccount,
        $reference,
        $currency,
        $amount,
        $targetAmount,
        $targetCurrency
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
            'targetAmount' => $targetAmount,
            'targetCurrency' => $targetCurrency
            ]);

            // Update the balances of the creditor and debtor accounts
            $creditorAccount->removeAmount($amount);
            $debtorAccount->addAmount($targetAmount);

            DB::commit();

            return response()->json(['transaction' => $transaction], 201);

        } catch (Exception $e) {
            DB::rollback();
        
            // Handle the exception or log the error
            return response()->json(['error' => 'Transaction failed.'], 500);
        }
    }

    public function getTransactionsForAccount($accountId, Request $request)
    {
        // Validate the account ID
        $validator = Validator::make(['account_id' => $accountId], [
            'account_id' => 'required|uuid',
        ]);

        $accountExists = Account::where('id', $accountId)->exists();
        if (!$accountExists){
            return response()->json(['error' => 'Account not found.'], 404);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $offset = $request->query('offset', 0);
        $limit = $request->query('limit', 10);

        // Retrieve transactions for the specified account
        $transactions = Transaction::where('creditor_account_id', $accountId)
            ->orWhere('debtor_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        $outgoingTransactions = $transactions->filter(function ($transaction) use ($accountId) {
            return $transaction->creditor_account_id == $accountId;
        });
            
        $incomingTransactions = $transactions->filter(function ($transaction) use ($accountId) {
            return $transaction->debtor_account_id == $accountId;
        });

        return response()->json([
            'outgoingTransactions' => $outgoingTransactions,
            'incomingTransactions' => $incomingTransactions,
        ]);
    }
}
