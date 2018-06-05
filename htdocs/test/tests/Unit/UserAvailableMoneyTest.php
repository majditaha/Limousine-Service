<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Transaction;

class UserAvailableMoneyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->teacher = factory(User::class)->create();
    }

    public function testFirst()
    {
        // User inputs 1000
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_INPUT,
            'amount' => 1000,
            'from_user_id' => $this->user->id,
        ]);
        $amount = $this->user->getAvailableMoney();
        $this->assertEquals($amount, 1000);

        // User inputs 2000 and withdrawals 450
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_INPUT,
            'amount' => 2000,
            'from_user_id' => $this->user->id,
        ]);
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_WITHDRAWAL,
            'amount' => 450,
            'from_user_id' => $this->user->id,
        ]);
        $amount = $this->user->getAvailableMoney();
        $this->assertEquals($amount, 2550);

        // User pays 200 for subscription and pays 200 as request to other user
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_PAYMENT,
            'amount' => 200,
            'from_user_id' => $this->user->id,
        ]);
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_PAYMENT,
            'amount' => 200,
            'from_user_id' => $this->user->id,
        ]);
        $amount = $this->user->getAvailableMoney();
        $this->assertEquals($amount, 2150);

        // Teacher receives payment
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_PAYMENT,
            'amount' => 100,
            'to_user_id' => $this->teacher->id,
        ]);
        $amount = $this->teacher->getAvailableMoney();
        $this->assertEquals($amount, 100);

        // Teacher withdraws money
        factory(Transaction::class)->create([
            'type' => Transaction::TYPE_WITHDRAWAL,
            'amount' => 80,
            'from_user_id' => $this->teacher->id,
        ]);
        $amount = $this->teacher->getAvailableMoney();
        $this->assertEquals($amount, 20);
    }
}

