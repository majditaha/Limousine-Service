<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('section')) {
            return auth()->user()->can('update', request()->section);
        }
        return auth()->user()->can('create', \App\Section::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required', Rule::unique('sections')->ignore(request('id'))->whereNull('deleted_at')],
            'discipline_id' => ['Required', 'exists:disciplines,id'],
            'order' => ['Required', 'Integer'],
        ];
    }
}
