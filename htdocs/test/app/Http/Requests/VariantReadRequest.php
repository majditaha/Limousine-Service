<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->variant) {
            return auth()->user()->can('view', request()->variant);
        }
        return auth()->user()->can('index', \App\Variant::class);
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
