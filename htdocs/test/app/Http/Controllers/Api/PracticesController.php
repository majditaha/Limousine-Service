<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetTrainingsRequest;
use App\Http\Requests\GetSmartPracticesRequest;
use App\Section;
use App\Practice;
use App\Http\Resources\Practice as PracticeResource;

class PracticesController extends Controller {

    public function getTrainings(GetTrainingsRequest $request, Section $section) {
        $practices = Practice::getTrainings($section, auth()->user());

        return response()->json(PracticeResource::collection($practices));
    }

    public function getSmart(GetSmartPracticesRequest $request) {
        $practices = Practice::getSmart(auth()->user());

        return response()->json(PracticeResource::collection($practices));
    }
}
