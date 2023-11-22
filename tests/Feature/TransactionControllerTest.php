<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Testing\Fluent\AssertableJson;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_transaction_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => '8c689138-5523-4af0-9f70-961a1c0b97e6',
            'debtor_account_id' => 'a83f2422-20cd-46c9-bf34-8a184edf009d',
            'reference' => 'test_reference',
            'amount' => 0.44,
            'currency' => 'USD',
        ]);

        $response->assertStatus(401);
    }

    public function test_create_transaction_with_same_currency(): void
    {
        $creditorUser = User::factory()->create();
        $debtorUser = User::factory()->create();
        $creditorAccount = Account::factory()->create([
            'user_id' => $creditorUser->id,
            'currency' => 'USD',
            'balance' => 77.77,
        ]);
        $debtorAccount = Account::factory()->create([
            'user_id' => $debtorUser->id,
            'currency' => 'USD',
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => $creditorAccount->id,
            'debtor_account_id' => $debtorAccount->id,
            'reference' => 'test_reference',
            'amount' => 0.44,
            'currency' => 'USD',
        ]);

        $response
        ->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('transaction.creditor_account_id', $creditorAccount->id)
                ->where('transaction.debtor_account_id', $debtorAccount->id)
                ->where('transaction.currency', 'USD')
                ->where('transaction.amount', 0.44)
                ->where('transaction.reference', 'test_reference')
                ->etc()
        );

    }

    public function test_create_transaction_with_different_currency(): void
    {
        $creditorUser = User::factory()->create();
        $debtorUser = User::factory()->create();
        $creditorAccount = Account::factory()->create([
            'user_id' => $creditorUser->id,
            'currency' => 'USD',
            'balance' => 77.77,
        ]);
        $debtorAccount = Account::factory()->create([
            'user_id' => $debtorUser->id,
            'currency' => 'GBP',
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => $creditorAccount->id,
            'debtor_account_id' => $debtorAccount->id,
            'reference' => 'test_reference',
            'amount' => 4.44,
            'currency' => 'GBP',
        ]);

        $response
        ->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('transaction.creditor_account_id', $creditorAccount->id)
                ->where('transaction.debtor_account_id', $debtorAccount->id)
                ->where('transaction.currency', 'GBP')
                ->where('transaction.amount', 4.44)
                ->where('transaction.reference', 'test_reference')
                ->etc()
        );

    }

    public function test_create_transaction_with_different_currency_2(): void
    {
        $creditorUser = User::factory()->create();
        $debtorUser = User::factory()->create();
        $creditorAccount = Account::factory()->create([
            'user_id' => $creditorUser->id,
            'currency' => 'USD',
            'balance' => 77.77,
        ]);
        $debtorAccount = Account::factory()->create([
            'user_id' => $debtorUser->id,
            'currency' => 'GBP',
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => $creditorAccount->id,
            'debtor_account_id' => $debtorAccount->id,
            'reference' => 'test_reference',
            'amount' => 4.44,
            'currency' => 'EUR',
        ]);

        $response->assertBadRequest();
    }

    public function test_create_transaction_with_no_debtor_account(): void
    {
        $creditorUser = User::factory()->create();
        $creditorAccount = Account::factory()->create([
            'user_id' => $creditorUser->id,
            'currency' => 'USD',
            'balance' => 77.77,
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => $creditorAccount->id,
            'debtor_account_id' => '5a495ca4-5e01-4706-b23c-c458f58c67df',
            'reference' => 'test_reference',
            'amount' => 4.44,
            'currency' => 'EUR',
        ]);

        $response->assertNotFound();
    }

    public function test_create_transaction_with_no_creditor_account(): void
    {
        $creditorUser = User::factory()->create();
        $debtorUser = User::factory()->create();
        $debtorAccount = Account::factory()->create([
            'user_id' => $debtorUser->id,
            'currency' => 'GBP',
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => '5a495ca4-5e01-4706-b23c-c458f58c67df',
            'debtor_account_id' => $debtorAccount->id,
            'reference' => 'test_reference',
            'amount' => 4.44,
            'currency' => 'EUR',
        ]);

        $response->assertNotFound();
    }

    public function test_create_transaction_with_insufficient_funds(): void
    {
        $creditorUser = User::factory()->create();
        $debtorUser = User::factory()->create();
        $creditorAccount = Account::factory()->create([
            'user_id' => $creditorUser->id,
            'currency' => 'USD',
            'balance' => 77.77,
        ]);
        $debtorAccount = Account::factory()->create([
            'user_id' => $debtorUser->id,
            'currency' => 'GBP',
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => $creditorAccount->id,
            'debtor_account_id' => $debtorAccount->id,
            'reference' => 'test_reference',
            'amount' => 78.44,
            'currency' => 'GBP',
        ]);

        $response->assertBadRequest();
    }

    public function test_create_transaction_beetween_same_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 77.77,
        ]);

        $this->actingAs($user);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/transactions',[
            'creditor_account_id' => $account->id,
            'debtor_account_id' => $account->id,
            'reference' => 'test_reference',
            'amount' => 78.44,
            'currency' => 'GBP',
        ]);

        $response->assertForbidden();
    }

    public function test_get_all_transactions_by_account_id(): void
    {
        $creditorUser = User::factory()->create();
        $debtorUser = User::factory()->create();

        $creditorAccount = Account::factory()->create([
            'user_id' => $creditorUser->id,
        ]);
        $debtorAccount = Account::factory()->create([
            'user_id' => $debtorUser->id,
        ]);

        $transactions = Transaction::factory(5)->create([
            'creditor_account_id' => $creditorAccount->id,
            'debtor_account_id' => $debtorAccount->id,
        ]);

        $this->actingAs($creditorUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get("api/transactions/$creditorAccount->id");

        $response->assertStatus(200)
        ->assertJsonStructure(['incomingTransactions', 'outgoingTransactions'])
        ->assertJsonCount(5, 'outgoingTransactions')
        ->assertJsonCount(0, 'incomingTransactions')
        ->assertJsonFragment(['id' => $transactions[0]->id]);
    }
}
