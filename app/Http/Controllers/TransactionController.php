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
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'creditor_account_id' => 'required|uuid',
            'debtor_account_id' => 'required|uuid',
            'reference' => 'required|string',
            'currency' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $creditorAccount = Account::retrieveAccount($request->input('creditor_account_id'));
        $debtorAccount = Account::retrieveAccount($request->input('debtor_account_id'));

        if (!$creditorAccount || !$debtorAccount) {
            return response()->json(['error' => 'One or more accounts not found.'], 404);
        }

        if (!$creditorAccount->checkFunds($request->input('amount'))) {
            return response()->json(['error' => 'Insufficient funds in the creditor account.'], 400);
        }

        DB::beginTransaction();

        // Create the transaction
        try {
        $transaction = Transaction::create([
            'id' => Uuid::uuid4()->toString(),
            'creditor_account_id' => $request->input('creditor_account_id'),
            'debtor_account_id' => $request->input('debtor_account_id'),
            'reference' => $request->input('reference'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            ]);

            // Update the balances of the creditor and debtor accounts
            $creditorAccount->removeMoney($request->input('amount'));
            $debtorAccount->addMoney($request->input('amount'));

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

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $offset = $request->query('offset', 0);
        $limit = $request->query('limit', 10);

        // Retrieve transactions for the specified account
        $transactions = Transaction::where('creditor_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json(['transactions' => $transactions]);
    }
}
