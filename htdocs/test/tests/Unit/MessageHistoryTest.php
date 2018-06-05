<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->user = factory(\App\User::class)->create();
        $this->admin = factory(\App\User::class)->states('admin')->create();
        $this->teacher = factory(\App\User::class)->states('teacher')->create();
    }

    /**
     * One message sent as a review
     */
    public function testOneReviewMessage() {
        $message = factory(\App\Message::class)->states('review')->create([
            'from_user_id' => $this->user->id,
        ]);
        $message->refresh();

        $history = [$message->id];
        $actualHistory = $message->getHistory()->pluck('id')->all();

        $this->assertEquals($history, $actualHistory);
    }

    /**
     * One message sent as a review, admin answered
     * Check history for both, should be the same
     */
    public function testOneReviewMessageWithAnswer() {
        $message1 = factory(\App\Message::class)->states('review')->create([
            'from_user_id' => $this->user->id,
        ]);

        $message2 = $message1->answer($this->admin, 'Content');

        $message1->refresh();
        $message2->refresh();

        $history = [$message1->id, $message2->id];
        $actualHistory1 = $message1->getHistory()->pluck('id')->all();
        $actualHistory2 = $message2->getHistory()->pluck('id')->all();

        $this->assertEquals($history, $actualHistory1);
        $this->assertEquals($history, $actualHistory2);
    }

    /**
     * One message sent to teacher, teacher answered, then user answered
     * Check history for all three, should be the same
     */
    public function testOneTeacherMessageWithAnswers() {
        $message1 = factory(\App\Message::class)->states('check_request')->create([
            'from_user_id' => $this->user->id,
        ]);

        $message2 = $message1->answer($this->teacher, 'Content');
        $message3 = $message2->answer($this->user, 'Content 2');

        $message1->refresh();
        $message2->refresh();
        $message3->refresh();

        $history = [$message1->id, $message2->id, $message3->id];
        $actualHistory1 = $message1->getHistory()->pluck('id')->all();
        $actualHistory2 = $message2->getHistory()->pluck('id')->all();
        $actualHistory3 = $message3->getHistory()->pluck('id')->all();

        $this->assertEquals($history, $actualHistory1);
        $this->assertEquals($history, $actualHistory2);
        $this->assertEquals($history, $actualHistory3);
    }

    /**
     * One message sent to teacher, teacher answered two consecutive messages
     * Check history for all three, should be the same
     */
    public function testTwoConsecutiveAnswers() {
        $message1 = factory(\App\Message::class)->states('check_request')->create([
            'from_user_id' => $this->user->id,
        ]);

        $message2 = $message1->answer($this->teacher, 'Content');
        $message3 = $message1->answer($this->user, 'Content 2');

        $message1->refresh();
        $message2->refresh();
        $message3->refresh();

        $history = [$message1->id, $message2->id, $message3->id];
        $actualHistory1 = $message1->getHistory()->pluck('id')->all();
        $actualHistory2 = $message2->getHistory()->pluck('id')->all();
        $actualHistory3 = $message3->getHistory()->pluck('id')->all();

        $this->assertEquals($history, $actualHistory1);
        $this->assertEquals($history, $actualHistory2);
        $this->assertEquals($history, $actualHistory3);
    }

    /**
     * Two separate review messages sent, history should be different
     * Check history for both, should be the same
     */
    public function testSeparateReviews() {
        $message1 = factory(\App\Message::class)->states('review')->create([
            'from_user_id' => $this->user->id,
        ]);

        $message2 = $message1->answer($this->admin, 'Content');

        $message3 = factory(\App\Message::class)->states('review')->create([
            'from_user_id' => $this->user->id,
        ]);

        $message1->refresh();
        $message2->refresh();
        $message3->refresh();

        $history1 = [$message1->id, $message2->id];
        $history2 = [$message3->id];
        $actualHistory1 = $message1->getHistory()->pluck('id')->all();
        $actualHistory2 = $message2->getHistory()->pluck('id')->all();
        $actualHistory3 = $message3->getHistory()->pluck('id')->all();

        $this->assertEquals($history1, $actualHistory1);
        $this->assertEquals($history1, $actualHistory2);
        $this->assertEquals($history2, $actualHistory3);
        $this->assertNotEquals($history2, $actualHistory1);
    }
}
