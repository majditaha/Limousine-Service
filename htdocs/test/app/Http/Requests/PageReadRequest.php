<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('static_page')) {
            return auth()->user()->can('show', request()->static_page);
        }
        return auth()->user()->can('index', \App\Page::class);
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
