<?php namespace App\Http\Controllers\Api;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\CityReadRequest;

class CitiesController extends RestController {

    public $model = 'City';

    protected $allowedWith = ['schools'];

    protected $itemsPerPage = 999999;

    protected $indexRequest = CityReadRequest::class;

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        return $query;
    }
}
