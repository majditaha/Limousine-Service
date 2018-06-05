<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PracticeCorrectnessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();

        $this->discipline = factory(\App\Discipline::class)->create();
        $this->section = factory(\App\Section::class)->create(['discipline_id' => $this->discipline->id]);
    }

    /**
     * Single choice
     */
    public function testSingleChoice()
    {
        $practice = factory(\App\Practice::class)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'answer_type' => 'single_choice',
        ]);

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

        $practice->syncAnswers($answersData);
        $practice->refresh();

        $correctAnswers = [
            $practice->answers[0]->id => 'Answer 1',
        ];
        $correct = $practice->isCorrectAnswer($correctAnswers);
        $this->assertTrue($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 2',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);
    }

    /**
     * Multiple choice
     */
    public function testMultipleChoice()
    {
        $practice = factory(\App\Practice::class)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'answer_type' => 'multiple_choice',
        ]);

        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
            [
                'order' => 2,
                'value' => 'Answer 2',
                'correct' => true,
            ],
            [
                'order' => 3,
                'value' => 'Answer 3',
                'correct' => false,
            ],
        ];

        $practice->syncAnswers($answersData);
        $practice->refresh();

        $correctAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[1]->id => 'Answer 2',
        ];
        $correct = $practice->isCorrectAnswer($correctAnswers);
        $this->assertTrue($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[2]->id => 'Answer 3',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 1',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);
    }

    /**
     * Single value
     */
    public function testSingleValue()
    {
        $practice = factory(\App\Practice::class)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'answer_type' => 'single_value',
        ]);

        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
        ];

        $practice->syncAnswers($answersData);
        $practice->refresh();

        $correctAnswers = [
            $practice->answers[0]->id => 'Answer 1',
        ];
        $correct = $practice->isCorrectAnswer($correctAnswers);
        $this->assertTrue($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 2',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);
    }

    /**
     * Multiple value
     */
    public function testMultipleValue()
    {
        $practice = factory(\App\Practice::class)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'answer_type' => 'multiple_value',
        ]);

        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
            [
                'order' => 2,
                'value' => 'Answer 2',
                'correct' => true,
            ],
        ];

        $practice->syncAnswers($answersData);
        $practice->refresh();

        $correctAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[1]->id => 'Answer 2',
        ];
        $correct = $practice->isCorrectAnswer($correctAnswers);
        $this->assertTrue($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[1]->id => 'Answer 3',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 1',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);
    }

    /**
     * Text
     */
    public function testText()
    {
        $practice = factory(\App\Practice::class)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'answer_type' => 'text',
        ]);

        $answersData = [
            [
                'order' => 1,
                'value' => '',
                'correct' => true,
            ],
        ];

        $practice->syncAnswers($answersData);
        $practice->refresh();

        $correctAnswers = [
            $practice->answers[0]->id => 'Answer 1',
        ];
        $correct = $practice->isCorrectAnswer($correctAnswers);
        $this->assertTrue($correct);

        $wrongAnswers = [];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);
    }

    /**
     * Matching
     */
    public function testMatching()
    {
        $practice = factory(\App\Practice::class)->create([
            'discipline_id' => $this->discipline->id,
            'section_id' => $this->section->id,
            'answer_type' => 'matching',
        ]);

        $answersData = [
            [
                'order' => 1,
                'value' => 'Answer 1',
                'correct' => true,
            ],
            [
                'order' => 2,
                'value' => 'Answer 2',
                'correct' => true,
            ],
            [
                'order' => 3,
                'value' => 'Answer 3',
                'correct' => true,
            ],
        ];

        $practice->syncAnswers($answersData);
        $practice->refresh();

        $correctAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[1]->id => 'Answer 2',
            $practice->answers[2]->id => 'Answer 3',
        ];
        $correct = $practice->isCorrectAnswer($correctAnswers);
        $this->assertTrue($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[1]->id => 'Answer 3',
            $practice->answers[2]->id => 'Answer 2',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [
            $practice->answers[0]->id => 'Answer 1',
            $practice->answers[1]->id => 'Answer 2',
        ];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);

        $wrongAnswers = [];
        $correct = $practice->isCorrectAnswer($wrongAnswers);
        $this->assertFalse($correct);
    }

}
