<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Section as SectionResource;
use App\Http\Requests\GetLatestSectionInTrainingRequest;
use \App\Http\Requests\SectionReadRequest;
use \App\Http\Requests\DropSectionProgressRequest;
use App\Discipline;
use App\Section;

class SectionsController extends Controller {

    public function getFinished(SectionReadRequest $request, Discipline $discipline) {
        $finishedSectionIds = auth()->user()->finishedSections->pluck('section_id');
        $sections = $discipline->sections()->whereIn('id', $finishedSectionIds)->get();
        $sections->load(
            'practicesOfTheoryType.userProgresses',
            'trainings.userProgresses',
            'theoriesWithTheoryPractices.userTrainingProgresses'
        );
        return response()->json(SectionResource::collection($sections));
    }

    public function show(SectionReadRequest $request, Section $section) {
        $section->load(
            'theoriesWithTheoryPractices.userTrainingProgresses',
            'theoriesWithTheoryPractices.theoryPractices.userProgresses',
            'theoriesWithTheoryPractices.theoryPractices.answers.userAnswers',
            'practicesOfTheoryType.userProgresses'
        );

        return response()->json(new SectionResource($section));
    }

    public function getLatestInTraining(GetLatestSectionInTrainingRequest $request, Discipline $discipline) {
        $section = $discipline->getFirstUnfinishedSection(auth()->user());

        if (!$section) {
            return response()->json(null);
        }

        $section->load(
            'theoriesWithTheoryPractices.userTrainingProgresses',
            'theoriesWithTheoryPractices.theoryPractices.userProgresses',
            'theoriesWithTheoryPractices.theoryPractices.answers.userAnswers',
            'practicesOfTheoryType.userProgresses'
        );

        return response()->json(new SectionResource($section));
    }

    public function dropProgress(DropSectionProgressRequest $request, Section $section) {
        auth()->user()->dropSectionProgress($section);

        return response()->json('ok');
    }

}
