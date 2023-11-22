<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;

class AccountModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_id():void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $accountId = $account->getId();

        $this->assertEquals($accountId, $account->id);
    }
}
