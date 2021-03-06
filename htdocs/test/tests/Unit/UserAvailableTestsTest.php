<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Plan;
use App\Message;

class UserAvailableTestsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();

        $this->user->addMoney(10000000);

        $this->planMain = factory(Plan::class)->create(['main' => true, 'requests' => 0]);
        $this->plan1 = factory(Plan::class)->create(['tests' => 1, 'months' => 0]);
        $this->plan2 = factory(Plan::class)->create(['tests' => 1, 'months' => 0]);
    }

    public function testFirst() {
        $this->user->purchasePlan($this->planMain, [1]);

        $this->user->purchasePlan($this->plan1, [], 3);
        $this->assertEquals($this->user->getAvailableTests(), 3);

        $this->user->purchasePlan($this->plan2, [], 5);
        $this->assertEquals($this->user->getAvailableTests(), 8);

        factory(Message::class, 4)->states('check_test')->create([
            'from_user_id' => $this->user->id,
            'to_user_id' => $this->user2->id,
        ]);
        $this->user->refresh();
        $this->assertEquals($this->user->getAvailableTests(), 4);
    }
}

