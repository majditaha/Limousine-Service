<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\SectionReadRequest;
use \App\Http\Requests\SectionWriteRequest;
use \App\Http\Requests\SectionDeleteRequest;

class SectionsController extends RestController {

    public $model = 'Section';

    protected $indexRequest = SectionReadRequest::class;
    protected $showRequest = SectionReadRequest::class;
    protected $storeRequest = SectionWriteRequest::class;
    protected $updateRequest = SectionWriteRequest::class;
    protected $destroyRequest = SectionDeleteRequest::class;

    protected function generateMetadata() {
        return [
            'disciplines' => \App\Discipline::get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('discipline_id')) {
            $query = $query->whereDisciplineId(request('discipline_id'));
        }

        return $query;
    }
}
