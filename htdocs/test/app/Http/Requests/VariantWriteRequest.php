<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VariantWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('variant')) {
            return auth()->user()->can('update', request()->variant);
        }
        return auth()->user()->can('create', \App\Variant::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required', Rule::unique('variants')->ignore(request('id'))->whereNull('deleted_at')],
            'discipline_id' => ['Required', 'exists:disciplines,id'],
            'order' => ['Required', 'Integer'],
        ];
    }
}
