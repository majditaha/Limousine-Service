<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ShouldHaveCorrectAnswer implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        // Check that at least one answer should be marked as correct
        return collect($value)->map->correct->filter()->isNotEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Должен быть указан хотя бы один правильный вариант ответа';
    }
}
