<?php namespace App\Http\Controllers\Api;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\VariantReadRequest;
use \App\Http\Resources\Variant as VariantResource;

class VariantsController extends RestController {

    public $model = 'Variant';

    protected $resource = VariantResource::class;

    protected $allowedWith = [
        'practices.userProgresses',
        'practices.answers.userAnswers',
    ];

    protected $showRequest = VariantReadRequest::class;
}
