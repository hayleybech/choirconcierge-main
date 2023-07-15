<?php

namespace App\Rules;

use App\Models\Singer;
use Illuminate\Contracts\Validation\Rule;

class UserUniqueForOrganisation implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return ! Singer::query()->where('user_id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'That user has already been added to your organisation. ';
    }
}
