<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\SubtypeReadRequest;
use \App\Http\Requests\SubtypeWriteRequest;
use \App\Http\Requests\SubtypeDeleteRequest;

class SubtypesController extends RestController {

    public $model = 'Subtype';

    protected $indexRequest = SubtypeReadRequest::class;
    protected $showRequest = SubtypeReadRequest::class;
    protected $storeRequest = SubtypeWriteRequest::class;
    protected $updateRequest = SubtypeWriteRequest::class;
    protected $destroyRequest = SubtypeDeleteRequest::class;

    protected $allowedWith = ['section'];

    protected function generateMetadata() {
        return [
            'sections' => \App\Section::get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('section_id')) {
            $query = $query->whereSectionId(request('section_id'));
        }

        return $query;
    }
}
