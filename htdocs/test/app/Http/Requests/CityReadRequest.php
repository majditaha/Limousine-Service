<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('city')) {
            return auth()->user()->can('show', request()->city);
        }
        return auth()->user()->can('index', \App\City::class);
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
