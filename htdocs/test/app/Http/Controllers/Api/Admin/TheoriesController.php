<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\TheoryReadRequest;
use \App\Http\Requests\TheoryWriteRequest;
use \App\Http\Requests\TheoryDeleteRequest;

class TheoriesController extends RestController {

    public $model = 'Theory';

    protected $indexRequest = TheoryReadRequest::class;
    protected $showRequest = TheoryReadRequest::class;
    protected $storeRequest = TheoryWriteRequest::class;
    protected $updateRequest = TheoryWriteRequest::class;
    protected $destroyRequest = TheoryDeleteRequest::class;

    protected $allowedWith = ['section'];

    protected function generateMetadata() {
        return [
            'disciplines' => \App\Discipline::with('sections')->get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        $query = $query->whereHas('section');

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('text')) {
            $text = request('text');
            $query = $query->where('text', 'ilike', "%{$text}%");
        }

        if (request()->filled('discipline_id')) {
            $query = $query->whereDisciplineId(request('discipline_id'));
        }

        if (request()->filled('section_id')) {
            $query = $query->whereSectionId(request('section_id'));
        }

        return $query;
    }
}
