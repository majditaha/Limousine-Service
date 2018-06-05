<?php namespace App\Http\Controllers\Api;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\DisciplineReadRequest;
use \App\Http\Requests\DisciplineWriteRequest;
use \App\Http\Requests\DisciplineDeleteRequest;

class DisciplinesController extends RestController {

    public $model = 'Discipline';

    protected $resource = \App\Http\Resources\Discipline::class;

    protected $allowedWith = [
        'sections',
        'sections.userProgresses',
        'sections.theories.userProgresses',
        'sections.theoriesWithoutPractices.userProgresses',
        'sections.theoriesWithTheoryPractices.userProgresses',
        'sections.theoriesWithTheoryPractices.theoryPractices.answers',
        'sections.theoriesWithTheoryPractices.theoryPractices.answers.userAnswers',
        'sections.practicesOfPracticeType.userProgresses',
        'sections.practicesOfPracticeType.answers',
        'sections.practicesOfTheoryType.userProgresses',
        'sections.practices.answers.userAnswers',
        'variants',
        'variants.practices.userProgresses',
        'variants.practices.answers.userAnswers',
    ];

    protected $indexRequest = DisciplineReadRequest::class;
    protected $showRequest = DisciplineReadRequest::class;
    protected $storeRequest = DisciplineWriteRequest::class;
    protected $updateRequest = DisciplineWriteRequest::class;
    protected $destroyRequest = DisciplineDeleteRequest::class;

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        return $query;
    }

}
