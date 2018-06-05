<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\CityReadRequest;
use \App\Http\Requests\CityWriteRequest;
use \App\Http\Requests\CityDeleteRequest;

class CitiesController extends RestController {

    public $model = 'City';

    protected $indexRequest = CityReadRequest::class;
    protected $showRequest = CityReadRequest::class;
    protected $storeRequest = CityWriteRequest::class;
    protected $updateRequest = CityWriteRequest::class;
    protected $destroyRequest = CityDeleteRequest::class;

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        return $query;
    }
}
