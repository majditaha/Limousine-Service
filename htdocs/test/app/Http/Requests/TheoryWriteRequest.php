<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TheoryWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('theory')) {
            return auth()->user()->can('update', request()->theory);
        }
        return auth()->user()->can('create', \App\Theory::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required'],
            'text' => ['Required_without:text_pdf'],
            'section_id' => ['Required', 'exists:sections,id'],
            'order' => ['Required', 'Integer'],
            'text_pdf' => ['Required_without:text'],
        ];
    }
}
