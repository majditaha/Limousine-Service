<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', request()->setting);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => ['Required'],
            'value' => ['Required'],
        ];
    }
}
