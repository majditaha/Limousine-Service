<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\PageReadRequest;
use \App\Http\Requests\PageWriteRequest;
use \App\Http\Requests\PageDeleteRequest;

class PagesController extends RestController {

    public $model = 'Page';

    protected $indexRequest = PageReadRequest::class;
    protected $showRequest = PageReadRequest::class;
    protected $storeRequest = PageWriteRequest::class;
    protected $updateRequest = PageWriteRequest::class;
    protected $destroyRequest = PageDeleteRequest::class;

    protected function generateMetadata() {
        return [
            'menu_items' => \App\MenuItem::get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('menu_item_id')) {
            $query = $query->whereMenuItemId(request('menu_item_id'));
        }

        if (request()->filled('content')) {
            $content = request('content');
            $query = $query->where('content', 'ilike', "%{$content}%");
        }

        return $query;
    }
}
