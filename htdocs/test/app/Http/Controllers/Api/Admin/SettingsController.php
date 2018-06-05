<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\SettingReadRequest;
use \App\Http\Requests\SettingWriteRequest;

class SettingsController extends RestController {

    public $model = 'Setting';

    protected $indexRequest = SettingReadRequest::class;
    protected $updateRequest = SettingWriteRequest::class;

}
