<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->section) {
            return auth()->user()->can('view', request()->section);
        }
        return auth()->user()->can('index', \App\Section::class);
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
