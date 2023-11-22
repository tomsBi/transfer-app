<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Account;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class AccountControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_get_multiple_user_accounts(): void
    {
        $user = User::factory()->create();
        $accounts = Account::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('/api/accounts');

        $response->assertStatus(200)
        ->assertJsonStructure(['user_accounts'])
        ->assertJsonCount(3, 'user_accounts')
        ->assertJsonFragment(['id' => $accounts[0]->id]);
    }

    public function test_get_user_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('/api/accounts');

        $response
        ->assertStatus(200)
        ->assertJsonStructure(['user_accounts'])
        ->assertJsonCount(1, 'user_accounts')
        ->assertJsonFragment(['id' => $account->id]);
    }

    public function test_get_user_account_2(): void
    {
        $user = User::factory()->create();
        $actingUser = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($actingUser);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('/api/accounts');
        

        $response
        ->assertStatus(200)
        ->assertJsonStructure(['user_accounts'])
        ->assertJsonCount(0, 'user_accounts');
    }

    public function test_create_user_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('api/accounts/create',[
            'currency' => 'USD',
            'balance' => 123.12,
        ]);

        $response
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('account.user_id', $user->id)
                ->where('account.currency', 'USD')
                ->where('account.balance', 123.12)
                ->etc()
        );
    }

    public function test_get_user_accounts_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('/api/accounts');

        $response->assertStatus(401);
    }
}
