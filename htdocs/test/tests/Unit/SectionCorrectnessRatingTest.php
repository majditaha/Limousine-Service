<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionCorrectnessRatingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->user = factory(\App\User::class)->create();
        $this->discipline = factory(\App\Discipline::class)->create();
        $this->section = factory(\App\Section::class)->create(['discipline_id' => $this->discipline->id]);

        $this->practices = factory(\App\Practice::class, 10)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->practices[$i]->setFinished($this->user);
        }
    }

    public function testFirst()
    {
        $this->practices[0]->setCorrectness($this->user, true);
        $this->practices[1]->setCorrectness($this->user, true);
        $this->practices[2]->setCorrectness($this->user, true);
        $this->practices[3]->setCorrectness($this->user, false);
        $this->practices[4]->setCorrectness($this->user, false);

        $rating = $this->section->getCorrectnessRating($this->practices, $this->user);

        $this->assertEquals($rating, 3);
    }

    public function testSecond()
    {
        $this->practices[0]->setCorrectness($this->user, false);
        $this->practices[1]->setCorrectness($this->user, false);
        $this->practices[2]->setCorrectness($this->user, false);
        $this->practices[3]->setCorrectness($this->user, false);
        $this->practices[4]->setCorrectness($this->user, false);

        $rating = $this->section->getCorrectnessRating($this->practices, $this->user);

        $this->assertEquals($rating, 0);
    }

    public function testThird()
    {
        $this->practices[0]->setCorrectness($this->user, true);
        $this->practices[1]->setCorrectness($this->user, true);
        $this->practices[2]->setCorrectness($this->user, true);
        $this->practices[3]->setCorrectness($this->user, true);
        $this->practices[4]->setCorrectness($this->user, true);

        $rating = $this->section->getCorrectnessRating($this->practices, $this->user);

        $this->assertEquals($rating, 5);
    }
}
