<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ShouldHaveCorrectAnswer;
use App\Practice;

class PracticeWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('practice')) {
            return auth()->user()->can('update', request()->practice);
        }
        return auth()->user()->can('create', \App\Practice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => ['Required'],
            'text' => ['Required_without:text_pdf'],
            'discipline_id' => ['Required'],
            'order' => ['Required', 'Integer'],
            'type' => ['Required'],
            'answer_type' => ['Required'],
            'answers' => [
                'Array',
                new ShouldHaveCorrectAnswer,
            ],
            'answers.*.value' => request('answer_type') == Practice::ANSWER_TYPE_TEXT
            ? ['Nullable']
            : ['Required'],
            'xp_gain' => ['Integer', 'Min:0'],
            'text_pdf' => ['Required_without:text'],
            'hint_pdf' => ['Nullable'],
            'solution_pdf' => ['Nullable'],
        ];

        if (request('type') == Practice::TYPE_THEORY || request('type') == Practice::TYPE_PRACTICE) {
            $rules['theory_id'] = 'Required';
        }

        if (request('type') == Practice::TYPE_TESTING || request('type') == Practice::TYPE_EGE) {
            $rules['variant_id'] = 'Required';
        }
        else {
            $rules['section_id'] = 'Required';
        }

        return $rules;
    }
}
