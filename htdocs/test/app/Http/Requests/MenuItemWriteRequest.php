<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('menu_item')) {
            return auth()->user()->can('update', request()->menu_item);
        }
        return auth()->user()->can('create', \App\MenuItem::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required', Rule::unique('menu_items')->ignore(request('id')) ],
            'order' => ['Required', 'Integer'],
            'alias' => ['Required', 'alpha_num', Rule::unique('menu_items')->ignore(request('id')) ],
        ];
    }
}
