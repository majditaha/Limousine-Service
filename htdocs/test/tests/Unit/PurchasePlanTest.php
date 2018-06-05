<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Plan;
use App\Discipline;

class PurchasePlanTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->discipline = factory(Discipline::class)->create();
        $this->discipline2 = factory(Discipline::class)->create();
        $this->discipline3 = factory(Discipline::class)->create();
        $this->discipline4 = factory(Discipline::class)->create();

        $this->user = factory(User::class)->create();
        $this->plan1 = factory(Plan::class)->create([
            'price' => 1000,
            'disciplines' => 2,
            'main' => true,
            'requests' => 5,
            'tests' => 10,
            'months' => 3,
        ]);
        $this->plan2 = factory(Plan::class)->create([
            'price' => 2000,
            'disciplines' => 3,
            'main' => true,
            'requests' => 10,
            'tests' => 15,
            'months' => 6,
        ]);
        $this->planAdditional = factory(Plan::class)->create([
            'price' => 2000,
            'disciplines' => 0,
            'main' => false,
            'requests' => 1,
            'tests' => 0,
            'months' => 0,
        ]);
        $this->planAdditional2 = factory(Plan::class)->create([
            'price' => 2000,
            'disciplines' => 0,
            'main' => false,
            'requests' => 0,
            'tests' => 1,
            'months' => 0,
        ]);
        $this->planAdditional3 = factory(Plan::class)->create([
            'price' => 2000,
            'disciplines' => 0,
            'main' => false,
            'requests' => 0,
            'tests' => 0,
            'months' => 1,
        ]);
    }

    /**
     * No subscription, purchase new plan, no money
     * @expectedException \App\Exceptions\NotEnoughMoneyException
     */
    public function testFirst() {
        $this->user->purchasePlan($this->plan1, [$this->discipline->id, $this->discipline2->id]);
    }

    /**
     * No subscription, purchase new plan, success, should add disciplines
     */
    public function testSecond() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);
        $this->user->refresh();

        $this->assertEquals($this->user->discipline_ids->all(), $disciplineIds);
        $this->assertTrue($this->user->isSubscriptionActive());
        $this->assertEquals($this->user->getAvailableRequests(), 5);
        $this->assertEquals($this->user->getAvailableTests(), 10);
        $this->assertEquals($this->user->getAvailableMoney(), $money * 100 - $this->plan1->getPrice(2));
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[0]->subscription_ends_at, 3);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[1]->subscription_ends_at, 3);
    }

    /**
     * Has subscription, purchase same plan, should update months, add requests and tests
     */
    public function testThird() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);
        $this->user->purchasePlan($this->plan1, $disciplineIds);
        $this->user->refresh();

        $this->assertEquals($this->user->discipline_ids->all(), $disciplineIds);
        $this->assertTrue($this->user->isSubscriptionActive());
        $this->assertEquals($this->user->getAvailableRequests(), 10);
        $this->assertEquals($this->user->getAvailableTests(), 20);
        $this->assertEquals($this->user->getAvailableMoney(), $money * 100 - $this->plan1->getPrice(2) * 2);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[0]->subscription_ends_at, 6);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[1]->subscription_ends_at, 6);
    }

    /**
     * Has subscription, purchase other plan with existing and with new disciplines
     * Existing should prolongate, new should be added
     */
    public function testFourth() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);

        $disciplineIds = [$this->discipline2->id, $this->discipline3->id, $this->discipline4->id];
        $this->user->purchasePlan($this->plan2, $disciplineIds);
        $this->user->refresh();

        $allDisciplineIds = [$this->discipline->id, $this->discipline2->id, $this->discipline3->id, $this->discipline4->id];

        $this->assertEquals($this->user->discipline_ids->all(), $allDisciplineIds);
        $this->assertTrue($this->user->isSubscriptionActive());
        $this->assertEquals($this->user->getAvailableRequests(), 15);
        $this->assertEquals($this->user->getAvailableTests(), 25);
        $this->assertEquals($this->user->getAvailableMoney(), $money * 100 - $this->plan1->getPrice(2) - $this->plan2->getPrice(3));
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[0]->subscription_ends_at, 3);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[1]->subscription_ends_at, 9);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[2]->subscription_ends_at, 6);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[3]->subscription_ends_at, 6);
    }

    /**
     * No subscription, purchase new plan, no discipline selected
     * @expectedException \App\Exceptions\DisciplinesNotSelectedException
     */
    public function testNoDisciplinesSelected() {
        $this->user->purchasePlan($this->plan1, []);
    }

    /**
     * No subscription, purchase additional plan
     * @expectedException \App\Exceptions\SubscriptionNeededException
     */
    public function testAdditionalNoSubscription() {
        $this->user->purchasePlan($this->planAdditional);
    }

    /**
     * Has subscription, purchase additional plan, zero items selected
     * @expectedException \App\Exceptions\AtLeastOneItemShouldBePurchasedException
     */
    public function testAdditionalZero() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);

        $this->user->purchasePlan($this->planAdditional);
    }

    /**
     * Has subscription, purchase additional plan, 2 requests selected
     */
    public function testAdditionalRequests() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);

        $this->user->purchasePlan($this->planAdditional, [], 2);
        $this->user->refresh();
        $this->assertEquals($this->user->getAvailableRequests(), 7);
        $this->assertEquals($this->user->getAvailableTests(), 10);
    }

    /**
     * Has subscription, purchase additional plan, 3 tests selected
     */
    public function testAdditionalTests() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);

        $this->user->purchasePlan($this->planAdditional2, [], 3);
        $this->user->refresh();
        $this->assertEquals($this->user->getAvailableRequests(), 5);
        $this->assertEquals($this->user->getAvailableTests(), 13);
    }

    /**
     * Has subscription, purchase additional plan, 3 tests selected
     */
    public function testAdditionalMonths() {
        $money = 1000000;
        $this->user->addMoney($money);
        $disciplineIds = [$this->discipline->id, $this->discipline2->id];
        $this->user->purchasePlan($this->plan1, $disciplineIds);

        // Can only by 1 month at a time
        $this->user->purchasePlan($this->planAdditional3, [], 0);
        $this->user->refresh();
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[0]->subscription_ends_at, 4);
        $this->assertDateDiffInMonths($this->user->disciplineSubscriptions[1]->subscription_ends_at, 4);
    }

    private function assertDateDiffInMonths($date, $months) {
        $diff = \Carbon\Carbon::parse($date)->diffInMonths(\Carbon\Carbon::now());
        $this->assertEquals($diff, $months);
    }
}

