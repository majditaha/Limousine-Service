<?php namespace App\Http\Controllers\Api;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\PlanReadRequest;

class PlansController extends RestController {

    public $model = 'Plan';

    protected $itemsPerPage = 999999;

    protected $order = 'asc';

    protected $indexRequest = PlanReadRequest::class;

    protected function generateMetadata() {
        return [
            'disciplines' => \App\Discipline::select('id', 'name')->get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        return $query;
    }
}
