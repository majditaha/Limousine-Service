<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('school')) {
            return auth()->user()->can('update', request()->school);
        }
        return auth()->user()->can('create', \App\School::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required', Rule::unique('schools')->ignore(request('id'))->where('city_id', request('city_id'))->whereNull('deleted_at') ],
            'city_id' => ['Required', 'exists:cities,id'],
        ];
    }
}
