<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->has('static_page')) {
            return auth()->user()->can('update', request()->static_page);
        }
        return auth()->user()->can('create', \App\Page::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $uniqueRule = Rule::unique('pages')->ignore(request('id'))->whereNull('deleted_at');
        if (request()->filled('menu_item_id')) {
            $uniqueRule = $uniqueRule->where('menu_item_id', request('menu_item_id'));
        }

        return [
            'name' => ['Required', $uniqueRule],
            'content' => ['Required'],
            'menu_item_id' => ['Nullable', 'exists:menu_items,id'],
            'order' => ['Required', 'Integer'],
        ];
    }
}
