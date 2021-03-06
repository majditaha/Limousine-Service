<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('message')) {
            return auth()->user()->can('update', request()->message);
        }
        return auth()->user()->can('create', \App\Message::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['Required'],
        ];
    }
}
