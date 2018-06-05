<?php namespace App\Http\Controllers\Api\Admin;

use \Staskjs\Rest\RestController;
use \App\Http\Requests\PracticeReadRequest;
use \App\Http\Requests\PracticeWriteRequest;
use \App\Http\Requests\PracticeDeleteRequest;

class PracticesController extends RestController {

    public $model = 'Practice';

    protected $indexRequest = PracticeReadRequest::class;
    protected $showRequest = PracticeReadRequest::class;
    protected $storeRequest = PracticeWriteRequest::class;
    protected $updateRequest = PracticeWriteRequest::class;
    protected $destroyRequest = PracticeDeleteRequest::class;

    protected $allowedWith = ['answers'];

    protected function generateMetadata() {
        return [
            'disciplines' => \App\Discipline::get(),
            'sections' => \App\Section::get(),
            'variants' => \App\Variant::get(),
            'theories' => \App\Theory::get(),
            'subtypes' => \App\Subtype::get(),
        ];
    }

    protected function getFiltered() {
        $query = parent::getFiltered();

        if (request()->filled('name')) {
            $name = request('name');
            $query = $query->where('name', 'ilike', "%{$name}%");
        }

        if (request()->filled('text')) {
            $text = request('text');
            $query = $query->where('text', 'ilike', "%{$text}%");
        }

        if (request()->filled('discipline_id')) {
            $query = $query->whereDisciplineId(request('discipline_id'));
        }

        if (request()->filled('section_id')) {
            $query = $query->whereSectionId(request('section_id'));
        }

        if (request()->filled('variant_id')) {
            $query = $query->whereVariantId(request('variant_id'));
        }

        if (request()->filled('theory_id')) {
            $query = $query->whereTheoryId(request('theory_id'));
        }

        if (request()->filled('type')) {
            $query = $query->whereType(request('type'));
        }

        if (request()->filled('subtype_id')) {
            $query = $query->whereType(request('subtype_id'));
        }

        return $query;
    }

    protected function afterSave($object) {
        $answers = request('answers');

        // Text answer type should have one answer
        if ($object->answer_type == \App\Practice::ANSWER_TYPE_TEXT) {
            if (!empty($answers)) {
                $answer = $answers[0];
                $answer['correct'] = true;
                $answer['value'] = '';
                $answer['order'] = 1;
            }
            else {
                $answer = [
                    'correct' => true,
                    'value' => '',
                    'order' => 1,
                ];
            }

            $answers = [$answer];
        }
        $object->syncAnswers($answers);
        return $object;
    }
}
