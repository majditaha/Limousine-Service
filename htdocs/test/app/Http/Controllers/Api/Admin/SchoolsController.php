<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\SchoolReadRequest;
use \App\Http\Requests\SchoolWriteRequest;
use \App\Http\Requests\SchoolDeleteRequest;

class SchoolsController extends RestController {

    public $model = 'School';

    protected $indexRequest = SchoolReadRequest::class;
    protected $showRequest = SchoolReadRequest::class;
    protected $storeRequest = SchoolWriteRequest::class;
    protected $updateRequest = SchoolWriteRequest::class;
    protected $destroyRequest = SchoolDeleteRequest::class;

    protected function generateMetadata() {
        return [
            'cities' => \App\City::get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('city_id')) {
            $query = $query->whereCityId(request('city_id'));
        }

        return $query;
    }
}
