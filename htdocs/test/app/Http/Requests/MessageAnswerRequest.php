<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('answer', request()->message);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['Required', 'Min:'.\App\Setting::getValue('answerLength')],
        ];
    }

    public function messages() {
        return [
            'content.required' => 'Поле должно быть заполнено',
            'content.min' => 'Слишком короткое сообщение',
        ];
    }
}
