<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PracticeSyncAnswersTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $discipline = factory(\App\Discipline::class)->create();
        $section = factory(\App\Section::class)->create(['discipline_id' => $discipline->id]);
        $theory = factory(\App\Theory::class)->create(['section_id' => $section->id]);

        $this->practice = factory(\App\Practice::class)->create([
            'discipline_id' => $discipline->id,
            'section_id' => $section->id,
            'theory_id' => $theory->id,
            'answer_type' => 'single_choice',
        ]);
    }

    /**
     * Practice has no answers, add two more
     */
    public function testNoAnswers()
    {
        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
            [
                'order' => 2,
                'value' => 'Answer 2',
                'correct' => false,
            ],
        ];

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answers = $this->mapAnswers($this->practice->answers);

        $this->assertEquals($answersData, $answers);
    }

    /**
     * Practice has one answer, add two more
     */
    public function testOneAnswer()
    {
        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
        ];

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answersData = array_merge($answersData, [
            [
                'order' => 2,
                'value' => 'Answer 2',
                'correct' => false,
            ],
            [
                'order' => 3,
                'value' => 'Answer 3',
                'correct' => true,
            ],
        ]);

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answers = $this->mapAnswers($this->practice->answers);

        $this->assertEquals($answersData, $answers);
    }

    /**
     * Practice has one answer, remove it and add two more
     */
    public function testOneAnswerRemove()
    {
        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
        ];

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 2',
                'correct' => false,
            ],
            [
                'order' => 2,
                'value' => 'Answer 3',
                'correct' => true,
            ],
        ];

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answers = $this->mapAnswers($this->practice->answers);

        $this->assertEquals($answersData, $answers);
    }

    /**
     * Practice has two answers, remove one, edit one and add one more
     */
    public function testTwoAnswersRemove()
    {
        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
            [
                'order' => 2,
                'value' => 'Answer 2',
                'correct' => false,
            ],
        ];

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answersData = $this->practice->answers->toArray();
        unset($answersData[1]);
        $answersData[0]['value'] = 'Answer ololo';

        $answersData = array_merge($answersData, [
            [
                'order' => 3,
                'value' => 'Answer 3',
                'correct' => true,
            ],
            [
                'order' => 4,
                'value' => 'Answer 2',
                'correct' => false,
            ],
        ]);

        $this->practice->syncAnswers($answersData);
        $this->practice->refresh();

        $answers = $this->mapAnswers($this->practice->answers);

        $this->assertEquals($this->mapAnswers($answersData), $answers);
    }

    private function mapAnswers($answers) {
        return collect($answers)->map(function ($answer) {
            return [
                'order' => $answer['order'],
                'value' => $answer['value'],
                'correct' => $answer['correct'],
            ];
        })->all();
    }
}
