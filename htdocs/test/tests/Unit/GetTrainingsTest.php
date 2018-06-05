<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Practice;

class GetTrainingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->user = factory(\App\User::class)->create();

        $this->discipline = factory(\App\Discipline::class)->create();
        $this->section = factory(\App\Section::class)->create(['discipline_id' => $this->discipline->id]);
        $this->section2 = factory(\App\Section::class)->create(['discipline_id' => $this->discipline->id]);
        $this->section3 = factory(\App\Section::class)->create(['discipline_id' => $this->discipline->id]);

        $this->subtype = factory(\App\Subtype::class)->create(['section_id' => $this->section->id]);
        $this->subtype2 = factory(\App\Subtype::class)->create(['section_id' => $this->section2->id]);
        $this->subtype3 = factory(\App\Subtype::class)->create(['section_id' => $this->section2->id]);
        $this->subtype4 = factory(\App\Subtype::class)->create(['section_id' => $this->section2->id]);
        $this->subtype5 = factory(\App\Subtype::class)->create(['section_id' => $this->section3->id]);

        factory(\App\Practice::class, 10)->states('no_text')->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'type' => \App\Practice::TYPE_PRACTICE,
            'subtype_id' => $this->subtype->id,
        ]);

        factory(\App\Practice::class, 4)->states('no_text')->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section2->id,
            'type' => \App\Practice::TYPE_PRACTICE,
            'subtype_id' => $this->subtype2->id,
        ]);
        factory(\App\Practice::class, 3)->states('no_text')->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section2->id,
            'type' => \App\Practice::TYPE_PRACTICE,
            'subtype_id' => $this->subtype3->id,
        ]);
        factory(\App\Practice::class, 3)->states('no_text')->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section2->id,
            'type' => \App\Practice::TYPE_PRACTICE,
            'subtype_id' => $this->subtype4->id,
        ]);

        factory(\App\Practice::class, 10)->states('no_text')->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section3->id,
            'type' => \App\Practice::TYPE_PRACTICE,
            'subtype_id' => $this->subtype5->id,
        ]);

        $this->section->refresh();
        $this->section2->refresh();
        $this->section3->refresh();
    }

    /**
     * Test that initially user receives 5 practices from current section, if he has never passed this section before
     */
    public function testAllOneSection() {
        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertEquals($practices[2]->section_id, $this->section->id);
        $this->assertEquals($practices[3]->section_id, $this->section->id);
        $this->assertEquals($practices[4]->section_id, $this->section->id);
    }

    /**
     * Test that second time there are 5 practices from current section,
     * because there are no other finished practices from other sections
     */
    public function testOneSectionSecondTime() {

        $this->setFinished($this->section, 1);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertEquals($practices[2]->section_id, $this->section->id);
        $this->assertEquals($practices[3]->section_id, $this->section->id);
        $this->assertEquals($practices[4]->section_id, $this->section->id);
        $this->assertTrue(!isset($practices[5]));

        $practices2 = Practice::getTrainings($this->section, $this->user);

        $this->assertNotEquals($practices, $practices2);
    }

    /**
     * Test that practices for current section never get repeated (those that user has answered, are not included)
     * This data is randomly generated, so set 8 practices finished, then we should receive only 2 remaining instead of 3,
     * bacause finished practices are not included in this set
     */
    public function testAllOneSectionNoRepeat() {

        $this->setFinished($this->section, 8);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertTrue(!isset($practices[2]));
    }

    /**
     * Test that there are 4 practices from current section and 1 practice from other section,
     * that user had given wrong answer to
     * Test several options with different score to 5th practice
     * @group test
     */
    public function testOtherSectionsWrongAnswer() {

        $this->section->setFinished($this->user);

        $this->setFinished($this->section, 1);
        $this->setFinished($this->section2, 10);

        // Subtype 1
        // Total score 3
        $this->setCorrectnessTo($this->section2, 0, false);
        $this->setCorrectnessTo($this->section2, 1, false);
        $this->setCorrectnessTo($this->section2, 1, false);
        $this->setCorrectnessTo($this->section2, 2, false);
        $this->setCorrectnessTo($this->section2, 2, true);
        $this->setCorrectnessTo($this->section2, 3, true);

        // Subtype 2
        // Total score 4
        $this->setCorrectnessTo($this->section2, 4, false);
        $this->setCorrectnessTo($this->section2, 5, false);
        $this->setCorrectnessTo($this->section2, 6, false);
        $this->setCorrectnessTo($this->section2, 6, false);

        // Subtype 3
        // Total score 2
        $this->setCorrectnessTo($this->section2, 7, true);
        $this->setCorrectnessTo($this->section2, 8, false);
        $this->setCorrectnessTo($this->section2, 9, false);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertEquals($practices[2]->section_id, $this->section->id);
        $this->assertEquals($practices[3]->section_id, $this->section2->id);
        $this->assertEquals($practices[3]->subtype_id, $this->subtype3->id);
        $this->assertEquals($practices[4]->section_id, $this->section2->id);
        $this->assertTrue(!isset($practices[5]));

        // Set second practice answered wrong five times - its subtype gets more score and is presented to user
        $this->setFinishedTo($this->section2, 1);
        $this->setCorrectnessTo($this->section2, 1, false);
        $this->setCorrectnessTo($this->section2, 1, false);
        $this->setCorrectnessTo($this->section2, 1, false);
        $this->setCorrectnessTo($this->section2, 1, false);
        $this->setCorrectnessTo($this->section2, 1, false);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertEquals($practices[2]->section_id, $this->section->id);
        $this->assertEquals($practices[3]->section_id, $this->section2->id);
        $this->assertEquals($practices[3]->subtype_id, $this->subtype2->id);
        $this->assertEquals($practices[4]->section_id, $this->section2->id);
        $this->assertTrue(!isset($practices[5]));

        // Set second practice answered correctly multiple times - its subtype gets least score
        // and another subtype practice is presented to user
        $this->setFinishedTo($this->section2, 1);
        $this->setCorrectnessTo($this->section2, 1, true);
        $this->setCorrectnessTo($this->section2, 1, true);
        $this->setCorrectnessTo($this->section2, 1, true);
        $this->setCorrectnessTo($this->section2, 1, true);
        $this->setCorrectnessTo($this->section2, 1, true);
        $this->setCorrectnessTo($this->section2, 1, true);
        $this->setCorrectnessTo($this->section2, 1, true);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertEquals($practices[2]->section_id, $this->section->id);
        $this->assertEquals($practices[3]->section_id, $this->section2->id);
        $this->assertEquals($practices[3]->subtype_id, $this->subtype3->id);
        $this->assertEquals($practices[4]->section_id, $this->section2->id);
        $this->assertTrue(!isset($practices[5]));
    }

    /**
     * Test that there are 3 practices from current section,
     * 1 practice from other section, that user had given wrong answer to
     * 1 practice from other section, that user had given correct answer to
     */
    public function testOtherSectionsCorrectAnswer() {

        $this->section->setFinished($this->user);

        $this->setFinished($this->section, 1);
        $this->setFinishedTo($this->section2, 0);
        $this->setCorrectnessTo($this->section2, 0, false);
        $this->setFinishedTo($this->section3, 0);
        $this->setCorrectnessTo($this->section3, 0, true);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[0]->section_id, $this->section->id);
        $this->assertEquals($practices[1]->section_id, $this->section->id);
        $this->assertEquals($practices[2]->section_id, $this->section->id);
        $this->assertEquals($practices[3]->section_id, $this->section2->id);
        $this->assertEquals($practices[4]->section_id, $this->section3->id);
        $this->assertTrue(!isset($practices[5]));

        // Add sleep to add correctness mark on the next second
        sleep(1);

        $this->setFinishedTo($this->section3, 1);
        $this->setCorrectnessTo($this->section3, 1, true);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[4]->subtype_id, $this->section3->practices[1]->subtype_id);

        sleep(1);

        $this->setCorrectnessTo($this->section3, 0, true);

        $practices = Practice::getTrainings($this->section, $this->user);

        $this->assertEquals($practices[4]->subtype_id, $this->section3->practices[0]->subtype_id);
    }

    private function setFinished($section, $num) {
        $practices = $this->getSectionPractices($section);
        foreach ($practices->take($num) as $practice) {
            $practice->setFinished($this->user);
        }
        $section->refresh();
    }

    private function setCorrectness($section, $num, $correct) {
        $practices = $this->getSectionPractices($section);
        foreach ($practices->take($num) as $practice) {
            $practice->setCorrectness($this->user, $correct);
        }
        $section->refresh();
    }

    private function setCorrectnessTo($section, $index, $correct) {
        $practices = $this->getSectionPractices($section);
        $practices[$index]->setCorrectness($this->user, $correct);
        $section->refresh();
    }

    private function setFinishedTo($section, $index) {
        $practices = $this->getSectionPractices($section);
        $practices[$index]->setFinished($this->user);
        $section->refresh();
    }

    private function getSectionPractices($section) {
        return $section->practices()->withoutGlobalScopes()->orderBy('id', 'ASC')->get();
    }
}
