<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\TransactionException;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function create(StoreTransactionRequest $request,)
    {
        $user = Auth::user();

        validator($request->all());

        $currency = $request->input('currency');
        $amount = $request->input('amount');
        $reference = $request->input('reference');

        $creditorAccount = Account::getAccount($request->input('creditor_account_id'), $user->id);
        $debtorAccount = Account::getDebtorAccount($request->input('debtor_account_id'));

        $this->checkIfDiffAccounts($creditorAccount->getId(), $debtorAccount->getId());

        $creditorAccount->checkFunds($amount);

        if ($debtorAccount->checkCurrency($currency)) {
            $currencyFrom = $creditorAccount->getCurrency();
            if($currency != $currencyFrom) {
                $targetAmount = (new CurrencyExchangeController)->getTargetAmount($currency, $currencyFrom, $amount, date("Y-m-d"));
            }
            $targetAmount = $amount;
        } 

        return $this->transactionService->store(
            $creditorAccount,
            $debtorAccount,
            $reference,
            $currency,
            $amount,
            $targetAmount
        );
    }

    public function getTransactionsForAccount($accountId, Request $request)
    {
        $user = Auth::user();

        // Validate the account ID
        $validator = Validator::make(['account_id' => $accountId], [
            'account_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        Account::getAccount($accountId, $user->id);

        $offset = $request->query('offset', 0);
        $limit = $request->query('limit', 10);

        $transactions = (new TransactionService)->getAllTransactionsByAccountId($accountId, $offset, $limit);
        
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

    private function checkIfDiffAccounts($creditorAccountId, $debtorAccountId)
    {
        if($creditorAccountId === $debtorAccountId) {
            throw TransactionException::sameAccountException();
        }
        return true;
    }
}
