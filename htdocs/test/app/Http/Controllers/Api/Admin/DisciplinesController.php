<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\DisciplineReadRequest;
use \App\Http\Requests\DisciplineWriteRequest;
use \App\Http\Requests\DisciplineDeleteRequest;

class DisciplinesController extends RestController {

    public $model = 'Discipline';

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
