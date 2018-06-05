<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('user')) {
            return auth()->user()->can('show', request()->user);
        }
        return auth()->user()->can('index', \App\User::class);
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
