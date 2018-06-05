<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->user) {
            return auth()->user()->can('update', request()->user);
        }
        return auth()->user()->can('create', \App\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['Required'],
            'email' => ['Required', 'Email', Rule::unique('users')->ignore(request('id'))->whereNull('deleted_at')],
            'password' => request()->has('id')
            ? ['Nullable', 'Min:6']
            : ['Required', 'Min:6'],
            // 'gender' => ['Required'],
            // 'birth_date' => ['Required'],
            'role' => [
                auth()->check() && auth()->user()->isAdmin() ? 'Required' : null,
            ],
            // 'city_id' => ['Required'],
            // 'school_id' => [
                // request('role') == 'user' ? 'Required' : null,
            // ],
            // 'grade' => [
                // request('role') == 'user' ? 'Required' : null,
            // ],
            // 'grade_name' => [
                // request('role') == 'user' ? 'Required' : null,
            // ],
        ];
    }

    public function attributes() {
        return [
            'name' => 'Имя',
        ];
    }
}
