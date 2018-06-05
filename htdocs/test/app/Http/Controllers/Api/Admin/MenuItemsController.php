<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\MenuItemReadRequest;
use \App\Http\Requests\MenuItemWriteRequest;
use \App\Http\Requests\MenuItemDeleteRequest;

class MenuItemsController extends RestController {

    public $model = 'MenuItem';

    protected $indexRequest = MenuItemReadRequest::class;
    protected $showRequest = MenuItemReadRequest::class;
    protected $storeRequest = MenuItemWriteRequest::class;
    protected $updateRequest = MenuItemWriteRequest::class;
    protected $destroyRequest = MenuItemDeleteRequest::class;

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        return $query;
    }
}
