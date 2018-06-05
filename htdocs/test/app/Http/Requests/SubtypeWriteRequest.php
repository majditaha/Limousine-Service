<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubtypeWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('subtype')) {
            return auth()->user()->can('update', request()->subtype);
        }
        return auth()->user()->can('create', \App\Subtype::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required', Rule::unique('subtypes')->ignore(request('id')) ],
            'section_id' => ['Required', 'exists:sections,id'],
        ];
    }
}
