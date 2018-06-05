<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubtypeReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('subtype')) {
            return auth()->user()->can('view', request()->subtype);
        }
        return auth()->user()->can('index', \App\Subtype::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
