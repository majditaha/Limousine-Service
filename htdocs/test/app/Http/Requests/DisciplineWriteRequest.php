<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisciplineWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('discipline')) {
            return auth()->user()->can('update', request()->discipline);
        }
        return auth()->user()->can('create', \App\Discipline::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required', Rule::unique('disciplines')->ignore(request('id'))->whereNull('deleted_at')],
            'icon_file' => ['Required'],
        ];
    }
}
