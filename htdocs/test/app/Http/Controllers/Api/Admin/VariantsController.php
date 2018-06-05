<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\VariantReadRequest;
use \App\Http\Requests\VariantWriteRequest;
use \App\Http\Requests\VariantDeleteRequest;

class VariantsController extends RestController {

    public $model = 'Variant';

    protected $indexRequest = VariantReadRequest::class;
    protected $showRequest = VariantReadRequest::class;
    protected $storeRequest = VariantWriteRequest::class;
    protected $updateRequest = VariantWriteRequest::class;
    protected $destroyRequest = VariantDeleteRequest::class;

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
